export function getScript(id) {
    const script = document.querySelector(id);

    if (!script) {
        throw `Pirsch script ${id} tag not found!`;
    }

    return script;
}

export function getPathOrTitleSuffixOrPrefix(list, index) {
    let entry = "";

    if (list.length > 0) {
        if (index < list.length) {
            entry = list[index];
        } else {
            entry = list[list.length-1];
        }
    }

    return entry;
}

export function getTags(script) {
    const tags = {};

    for (const attribute of script.attributes) {
        if (attribute.name.startsWith("data-tag-")) {
            tags[attribute.name.substring("data-tag-".length).replaceAll("-", " ")] = attribute.value || "1";
        } else if (attribute.name.startsWith("data-tag") && attribute.value) {
            attribute.value.split(",").forEach(t => {
                t = t.trim().replaceAll("-", " ");

                if (t) {
                    tags[t] = "1";
                }
            });
        }
    }

    return tags;
}

export function ignore(script) {
    return localStorage.getItem("disable_pirsch") || isLocalhost(script) || !includePage(script) || excludePage(script);
}

export function rewriteHostname(hostname) {
    if (!hostname) {
        hostname = location.href;
    } else {
        hostname = location.href.replace(location.hostname, hostname);
    }

    return hostname;
}

export function rewritePath(url, prefix, suffix) {
    if (!url) {
        url = location.href;
    }

    if (!prefix) {
        prefix = "";
    }

    if (!suffix) {
        suffix = "";
    }

    const u = new URL(url);
    u.pathname = prefix+u.pathname+suffix;
    return u.toString();
}

export function rewriteTitle(prefix, suffix) {
    let title = document.title;

    if (!prefix) {
        prefix = "";
    }

    if (!suffix) {
        suffix = "";
    }

    return prefix+title+suffix;
}

export function rewriteReferrer(hostname) {
    let referrer = document.referrer;

    if (hostname) {
        referrer = referrer.replace(location.hostname, hostname);
    }

    return referrer;
}

function isLocalhost(script) {
    const dev = script.hasAttribute("data-dev");

    if (!dev && (/^localhost(.*)$|^127(\.[0-9]{1,3}){3}$/is.test(location.hostname) || location.protocol === "file:")) {
        console.info("Pirsch is ignored on localhost. Add the data-dev attribute to enable it.");
        return true;
    }

    return false;
}

function includePage(script) {
    try {
        const include = script.getAttribute("data-include");
        const paths = include ? include.split(",") : [];

        if (paths.length) {
            let found = false;

            for (let i = 0; i < paths.length; i++) {
                if (new RegExp(paths[i]).test(location.pathname)) {
                    found = true;
                    break;
                }
            }

            if (!found) {
                return false;
            }
        }
    } catch (e) {
        console.error(e);
    }

    return true;
}

function excludePage(script) {
    try {
        const exclude = script.getAttribute("data-exclude");
        const paths = exclude ? exclude.split(",") : [];

        for (let i = 0; i < paths.length; i++) {
            if (new RegExp(paths[i]).test(location.pathname)) {
                return true;
            }
        }
    } catch (e) {
        console.error(e);
    }

    return false;
}
