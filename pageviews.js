import {
    getPathOrTitleSuffixOrPrefix,
    getTags,
    rewriteHostname,
    rewritePath,
    rewriteReferrer,
    rewriteTitle
} from "./common";

export function addPageViews(params) {
    if (history.pushState && !params.disableHistory) {
        const pushState = history["pushState"];
        history.pushState = function(state, unused, url) {
            pushState.apply(this, [state, unused, url]);
            pageView(params);
        }
        window.addEventListener("popstate", () => pageView(params));
    }

    if (!document.body) {
        window.addEventListener("DOMContentLoaded", () => pageView(params));
    } else {
        pageView(params);
    }
}

function pageView(params) {
    const {
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
    } = params;
    send({
        script,
        hostname: rewrite,
        pathPrefix: domains.length ? "" : getPathOrTitleSuffixOrPrefix(pathPrefix, 0),
        pathSuffix: domains.length ? "" : getPathOrTitleSuffixOrPrefix(pathSuffix, 0),
        titlePrefix: domains.length ? "" : getPathOrTitleSuffixOrPrefix(titlePrefix, 0),
        titleSuffix: domains.length ? "" : getPathOrTitleSuffixOrPrefix(titleSuffix, 0),
        identificationCode,
        endpoint,
        disableQueryParams,
        disableReferrer,
        disableResolution
    });

    for (let i = 0; i < domains.length; i++) {
        send({
            script,
            hostname: domains[i],
            pathPrefix: getPathOrTitleSuffixOrPrefix(pathPrefix, i),
            pathSuffix: getPathOrTitleSuffixOrPrefix(pathSuffix, i),
            titlePrefix: getPathOrTitleSuffixOrPrefix(titlePrefix, i),
            titleSuffix: getPathOrTitleSuffixOrPrefix(titleSuffix, i),
            identificationCode,
            endpoint,
            disableQueryParams,
            disableReferrer,
            disableResolution
        });
    }
}

function send(params) {
    let {
        script,
        hostname,
        pathPrefix,
        pathSuffix,
        titlePrefix,
        titleSuffix,
        identificationCode,
        endpoint,
        disableQueryParams,
        disableReferrer,
        disableResolution
    } = params;
    const referrer = rewriteReferrer(hostname);
    hostname = rewriteHostname(hostname);
    hostname = rewritePath(hostname, pathPrefix, pathSuffix);

    if (disableQueryParams) {
        hostname = (hostname.includes('?') ? hostname.split('?')[0] : hostname);
    }

    const tags = getTags(script);
    const url = endpoint +
        "?nc=" + new Date().getTime() +
        "&code=" + identificationCode +
        "&url=" + encodeURIComponent(hostname.substring(0, 1800)) +
        "&t=" + encodeURIComponent(rewriteTitle(titlePrefix, titleSuffix)) +
        "&ref=" + (disableReferrer ? '' : encodeURIComponent(referrer)) +
        "&w=" + (disableResolution ? '' : screen.width) +
        "&h=" + (disableResolution ? '' : screen.height) +
        (Object.keys(tags).length ? "&" + Object.entries(tags).map(([key, value]) => `tag_${key.replaceAll("-", " ")}=${value || 1}`).join("&") : "");
    const req = new XMLHttpRequest();
    req.open("GET", url);
    req.send();
}
