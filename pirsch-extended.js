import {getScript, ignore} from "./common";
import {addPageViews} from "./pageviews";
import {addEvents, addEventsPlaceholder} from "./events";
import {addExtendSession} from "./sessions";

(function () {
    "use strict";

    addEventsPlaceholder();
    const script = getScript("#pirschextendedjs");

    if (ignore(script)) {
        return;
    }

    const fileExtensions = [
        "7z",
        "avi",
        "csv",
        "docx",
        "exe",
        "gz",
        "key",
        "midi",
        "mov",
        "mp3",
        "mp4",
        "mpeg",
        "pdf",
        "pkg",
        "pps",
        "ppt",
        "pptx",
        "rar",
        "rtf",
        "txt",
        "wav",
        "wma",
        "wmv",
        "xlsx",
        "zip"
    ].concat(script.getAttribute("data-download-extensions")?.split(",") || []);

    const hitEndpoint = script.getAttribute("data-hit-endpoint") || "https://api.pirsch.io/hit";
    const eventEndpoint = script.getAttribute("data-event-endpoint") || "https://api.pirsch.io/event";
    const sessionEndpoint = script.getAttribute("data-session-endpoint") || "https://api.pirsch.io/session";
    const identificationCode = script.getAttribute("data-code") || "not-set";
    const domains = script.getAttribute("data-domain") ? script.getAttribute("data-domain").split(",") || [] : [];
    const disablePageViews = script.hasAttribute("data-disable-page-views");
    const disableQueryParams = script.hasAttribute("data-disable-query");
    const disableReferrer = script.hasAttribute("data-disable-referrer");
    const disableResolution = script.hasAttribute("data-disable-resolution");
    const disableHistory = script.hasAttribute("data-disable-history");
    const disableOutboundLinks = script.hasAttribute("data-disable-outbound-links");
    const disableDownloads = script.hasAttribute("data-disable-downloads");
    const enableSessions = script.hasAttribute("data-enable-sessions");
    const rewrite = script.getAttribute("data-dev");
    const pathPrefix = script.getAttribute("data-path-prefix") ? script.getAttribute("data-path-prefix").split(",") || [] : [];
    const pathSuffix = script.getAttribute("data-path-suffix") ? script.getAttribute("data-path-suffix").split(",") || [] : [];
    const titlePrefix = script.getAttribute("data-title-prefix") ? script.getAttribute("data-title-prefix").split(",") || [] : [];
    const titleSuffix = script.getAttribute("data-title-suffix") ? script.getAttribute("data-title-suffix").split(",") || [] : [];
    const outboundLinkEventName = script.getAttribute("data-outbound-link-event-name") || "Outbound Link Click";
    const downloadEventName = script.getAttribute("data-download-event-name") || "File Download";
    const notFoundEventName = script.getAttribute("data-not-found-event-name") || "404 Page Not Found";

    if (!disablePageViews) {
        addPageViews({
            script,
            domains,
            rewrite,
            pathPrefix,
            pathSuffix,
            titlePrefix,
            titleSuffix,
            identificationCode,
            endpoint: hitEndpoint,
            disableQueryParams,
            disableReferrer,
            disableResolution,
            disableHistory
        });
    }

    if (enableSessions) {
        addExtendSession({
            script,
            domains,
            rewrite,
            pathPrefix,
            pathSuffix,
            titlePrefix,
            titleSuffix,
            identificationCode,
            endpoint: sessionEndpoint,
            disableQueryParams
        });
    }

    addEvents({
        script,
        domains,
        rewrite,
        pathPrefix,
        pathSuffix,
        titlePrefix,
        titleSuffix,
        identificationCode,
        endpoint: eventEndpoint,
        disableQueryParams,
        disableReferrer,
        disableResolution
    });

    window.pirschInit = function () {
        addHTMLEvents("[data-pirsch-event]");
        addHTMLEvents("[pirsch-event]");
        addCSSEvents();
        addLinks();
        addPageNotFound();
    }
    document.addEventListener("DOMContentLoaded", pirschInit);

    function addHTMLEvents(selector) {
        const elements = document.querySelectorAll(selector);

        for (const element of elements) {
            element.addEventListener("click", () => {
                htmlClickEvent(element);
            });
            element.addEventListener("auxclick", () => {
                htmlClickEvent(element);
            });
        }
    }

    function htmlClickEvent(element) {
        const name = element.getAttribute("pirsch-event") ?? element.getAttribute("data-pirsch-event");

        if (!name) {
            console.error("Pirsch event attribute name must not be empty!", element);
            return;
        }

        const meta = {};
        let duration;

        for (const attribute of element.attributes) {
            if (attribute.name.startsWith("data-pirsch-meta-")) {
                meta[attribute.name.substring("data-pirsch-meta-".length)] = attribute.value;
            } else if (attribute.name.startsWith("pirsch-meta-")) {
                meta[attribute.name.substring("pirsch-meta-".length)] = attribute.value;
            } else if (attribute.name.startsWith("data-pirsch-duration")) {
                duration = Number.parseInt(attribute.value, 10) ?? 0;
            } else if (attribute.name.startsWith("pirsch-duration")) {
                duration = Number.parseInt(attribute.value, 10) ?? 0;
            }
        }

        pirsch(name, {meta, duration});
    }

    function addCSSEvents() {
        const elements = document.querySelectorAll("[class*='pirsch-event=']");

        for (const element of elements) {
            element.addEventListener("click", () => {
                cssClickEvent(element);
            });
            element.addEventListener("auxclick", () => {
                cssClickEvent(element);
            });
        }
    }

    function cssClickEvent(element) {
        let name = "";
        const meta = {};
        let duration;

        for (const className of element.classList) {
            if (className.startsWith("pirsch-event=")) {
                name = className.substring("pirsch-event=".length).replaceAll("+", " ");

                if (!name) {
                    console.error("Pirsch event class name must not be empty!", element);
                    return;
                }
            } else if (className.startsWith("pirsch-meta-")) {
                const metaKeyValue = className.substring("pirsch-meta-".length);

                if (metaKeyValue) {
                    const parts = metaKeyValue.split("=");

                    if (parts.length === 2 && parts[1] !== "") {
                        meta[parts[0]] = parts[1].replaceAll("+", " ");
                    }
                }
            } else if (className.startsWith("pirsch-duration=")) {
                duration = Number.parseInt(className.substring("pirsch-duration=".length)) ?? 0;
            }
        }

        pirsch(name, {meta, duration});
    }

    function addLinks() {
        const links = document.getElementsByTagName("a");

        for (const link of links) {
            if (!link.hasAttribute("pirsch-ignore") &&
                !link.hasAttribute("data-pirsch-ignore") &&
                !link.classList.contains("pirsch-ignore")) {
                if (isFileDownload(link.href)) {
                    if (!disableDownloads) {
                        addFileDownload(link);
                    }
                } else if (!disableOutboundLinks) {
                    addOutboundLink(link);
                }
            }
        }
    }

    function addOutboundLink(link) {
        const url = getURL(link.href);

        if (url !== null && url.hostname !== location.hostname) {
            link.addEventListener("click", () => pirsch(outboundLinkEventName, {meta: {url: url.href}}));
            link.addEventListener("auxclick", () => pirsch(outboundLinkEventName, {meta: {url: url.href}}));
        }
    }

    function addFileDownload(link) {
        const file = getPathname(link.href);
        link.addEventListener("click", () => pirsch(downloadEventName, {meta: {file}}));
        link.addEventListener("auxclick", () => pirsch(downloadEventName, {meta: {file}}));
    }

    function isFileDownload(url) {
        const ext = url.split(".").pop().toLowerCase();
        return fileExtensions.includes(ext);
    }

    function getURL(href) {
        try {
            return new URL(href);
        } catch {
            return null;
        }
    }

    function getPathname(href) {
        try {
            if (href.toLowerCase().startsWith("http")) {
                const url = new URL(href);
                return url.pathname;
            }

            return href ?? "(empty)";
        } catch {
            return "(error)";
        }
    }

    function addPageNotFound() {
        window.pirschNotFound = function () {
            pirsch(notFoundEventName, {
                meta: {
                    path: location.pathname
                }
            });
        }
    }
})();
