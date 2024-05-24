import {getPathOrTitleSuffixOrPrefix, rewriteHostname, rewritePath} from "./common";

export function addExtendSession(params) {
    const {
        script,
        domains,
        rewrite,
        pathPrefix,
        pathSuffix,
        identificationCode,
        endpoint,
        disableQueryParams
    } = params;
    const interval = Number.parseInt(script.getAttribute("data-interval-ms"), 10) || 60_000;

    const intervalHandler = setInterval(() => {
        extendSession({
            domains,
            rewrite,
            pathPrefix,
            pathSuffix,
            identificationCode,
            endpoint,
            disableQueryParams
        });
    }, interval);

    window.pirschClearSession = () => {
        clearInterval(intervalHandler);
    }
}

function extendSession(params) {
    const {
        domains,
        rewrite,
        pathPrefix,
        pathSuffix,
        identificationCode,
        endpoint,
        disableQueryParams
    } = params;
    sendExtendSession({
        hostname: rewrite,
        pathPrefix: domains.length ? "" : getPathOrTitleSuffixOrPrefix(pathPrefix, 0),
        pathSuffix: domains.length ? "" : getPathOrTitleSuffixOrPrefix(pathSuffix, 0),
        identificationCode,
        endpoint,
        disableQueryParams
    });

    for (let i = 0; i < domains.length; i++) {
        sendExtendSession({
            hostname: domains[i],
            pathPrefix: getPathOrTitleSuffixOrPrefix(pathPrefix, i),
            pathSuffix: getPathOrTitleSuffixOrPrefix(pathSuffix, i),
            identificationCode,
            endpoint,
            disableQueryParams
        });
    }
}

function sendExtendSession(params) {
    let {
        hostname,
        pathPrefix,
        pathSuffix,
        identificationCode,
        endpoint,
        disableQueryParams
    } = params;
    hostname = rewriteHostname(hostname);
    hostname = rewritePath(hostname, pathPrefix, pathSuffix);

    if (disableQueryParams) {
        hostname = (hostname.includes('?') ? hostname.split('?')[0] : hostname);
    }

    const url = endpoint +
        "?nc=" + new Date().getTime() +
        "&code=" + identificationCode +
        "&url=" + encodeURIComponent(hostname.substring(0, 1800));
    const req = new XMLHttpRequest();
    req.open("POST", url);
    req.send();
}
