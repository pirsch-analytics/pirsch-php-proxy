import {getScript, ignore} from "./common";
import {addPageViews} from "./pageviews";

(function () {
    "use strict";

    const script = getScript("#pirschjs");

    if (ignore(script) || document.querySelector("#pirschextendedjs")) {
        return;
    }

    const endpoint = script.getAttribute("data-endpoint") || "https://api.pirsch.io/hit";
    const identificationCode = script.getAttribute("data-code") || "not-set";
    const domains = script.getAttribute("data-domain") ? script.getAttribute("data-domain").split(",") || [] : [];
    const disableQueryParams = script.hasAttribute("data-disable-query");
    const disableReferrer = script.hasAttribute("data-disable-referrer");
    const disableResolution = script.hasAttribute("data-disable-resolution");
    const disableHistory = script.hasAttribute("data-disable-history");
    const rewrite = script.getAttribute("data-dev");
    const pathPrefix = script.getAttribute("data-path-prefix") ? script.getAttribute("data-path-prefix").split(",") || [] : [];
    const pathSuffix = script.getAttribute("data-path-suffix") ? script.getAttribute("data-path-suffix").split(",") || [] : [];
    const titlePrefix = script.getAttribute("data-title-prefix") ? script.getAttribute("data-title-prefix").split(",") || [] : [];
    const titleSuffix = script.getAttribute("data-title-suffix") ? script.getAttribute("data-title-suffix").split(",") || [] : [];
    addPageViews({
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
        disableResolution,
        disableHistory
    });
})();
