import {getScript, ignore} from "./common";
import {addEvents, addEventsPlaceholder} from "./events";

(function () {
    "use strict";

    addEventsPlaceholder();
    const script = getScript("#pirscheventsjs");

    if (ignore(script) || document.querySelector("#pirschextendedjs")) {
        return;
    }

    const endpoint = script.getAttribute("data-endpoint") || "https://api.pirsch.io/event";
    const identificationCode = script.getAttribute("data-code");
    const domains = script.getAttribute("data-domain") ? script.getAttribute("data-domain").split(",") || [] : [];
    const disableQueryParams = script.hasAttribute("data-disable-query");
    const disableReferrer = script.hasAttribute("data-disable-referrer");
    const disableResolution = script.hasAttribute("data-disable-resolution");
    const rewrite = script.getAttribute("data-dev");
    const pathPrefix = script.getAttribute("data-path-prefix") ? script.getAttribute("data-path-prefix").split(",") || [] : [];
    const pathSuffix = script.getAttribute("data-path-suffix") ? script.getAttribute("data-path-suffix").split(",") || [] : [];
    const titlePrefix = script.getAttribute("data-title-prefix") ? script.getAttribute("data-title-prefix").split(",") || [] : [];
    const titleSuffix = script.getAttribute("data-title-suffix") ? script.getAttribute("data-title-suffix").split(",") || [] : [];
    addEvents({
        script,
        domains,
        rewrite,
        pathPrefix,
        pathSuffix,
        titlePrefix,
        titleSuffix,
        identificationCode,
        endpoint,
        disableQueryParams,
        disableReferrer,
        disableResolution
    });
})();
