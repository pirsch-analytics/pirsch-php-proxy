import {getScript, ignore} from "./common";
import {addExtendSession} from "./sessions";

(function () {
    "use strict";

    const script = getScript("#pirschsessionsjs");

    if (ignore(script)) {
        return;
    }

    const endpoint = script.getAttribute("data-endpoint") || "https://api.pirsch.io/session";
    const identificationCode = script.getAttribute("data-code") || "not-set";
    const domains = script.getAttribute("data-domain") ? script.getAttribute("data-domain").split(",") || [] : [];
    const disableQueryParams = script.hasAttribute("data-disable-query");
    const rewrite = script.getAttribute("data-dev");
    const pathPrefix = script.getAttribute("data-path-prefix") ? script.getAttribute("data-path-prefix").split(",") || [] : [];
    const pathSuffix = script.getAttribute("data-path-suffix") ? script.getAttribute("data-path-suffix").split(",") || [] : [];
    const titlePrefix = script.getAttribute("data-title-prefix") ? script.getAttribute("data-title-prefix").split(",") || [] : [];
    const titleSuffix = script.getAttribute("data-title-suffix") ? script.getAttribute("data-title-suffix").split(",") || [] : [];
    addExtendSession({
        script,
        domains,
        rewrite,
        pathPrefix,
        pathSuffix,
        titlePrefix,
        titleSuffix,
        identificationCode,
        endpoint,
        disableQueryParams
    });
})();
