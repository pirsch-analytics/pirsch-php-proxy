import {
    getPathOrTitleSuffixOrPrefix,
    getTags,
    rewriteHostname,
    rewritePath,
    rewriteReferrer,
    rewriteTitle
} from "./common";

export function addEventsPlaceholder() {
    window.pirsch = function (name, options) {
        console.log(`Pirsch event: ${name}${options ? " " + JSON.stringify(options) : ""}`);
        return Promise.resolve(null);
    }
}

export function addEvents(params) {
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

    window.pirsch = function (name, options) {
        if (typeof name !== "string" || !name) {
            return Promise.reject("The event name for Pirsch is invalid (must be a non-empty string)! Usage: pirsch('event name', {duration: 42, meta: {key: 'value'}})");
        }

        return new Promise((resolve, reject) => {
            const meta = options && options.meta ? options.meta : {};

            for (let key in meta) {
                if (meta.hasOwnProperty(key)) {
                    meta[key] = String(meta[key]);
                }
            }

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
                disableResolution,
                name,
                options,
                meta,
                resolve,
                reject
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
                    disableResolution,
                    name,
                    options,
                    meta,
                    resolve,
                    reject
                });
            }
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
        disableResolution,
        name,
        options,
        meta,
        resolve,
        reject
    } = params;
    const referrer = rewriteReferrer(hostname);
    const tags = getTags(script);
    hostname = rewriteHostname(hostname);
    hostname = rewritePath(hostname, pathPrefix, pathSuffix);

    if (disableQueryParams) {
        hostname = (hostname.includes('?') ? hostname.split('?')[0] : hostname);
    }

    if (navigator.sendBeacon(endpoint, JSON.stringify({
        identification_code: identificationCode,
        url: hostname.substring(0, 1800),
        title: rewriteTitle(titlePrefix, titleSuffix),
        referrer: (disableReferrer ? '' : encodeURIComponent(referrer)),
        screen_width: (disableResolution ? 0 : screen.width),
        screen_height: (disableResolution ? 0 : screen.height),
        tags,
        event_name: name,
        event_duration: options && options.duration && typeof options.duration === "number" ? options.duration : 0,
        event_meta: meta
    }))) {
        resolve();
    } else {
        reject("error queuing event request");
    }
}
