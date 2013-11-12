## Redirect requests to Apache, running on port 8080 on localhost
include "devicedetect.vcl";
import std;
backend apache {
        .host = "127.0.0.1";
        .port = "8080";
}

acl local {
    "localhost";
}

sub vcl_recv {
    if (req.request == "PURGE") {
        if (client.ip ~ local ) {
            return (lookup);
        }
    }
    call devicedetect;
    if (req.http.Accept-Encoding) {
       if (req.url ~ "\.(jpg|png|gif|gz|tgz|bz2|tbz|mp3|ogg)$") {
           # No point in compressing these
           remove req.http.Accept-Encoding;
       } elsif (req.http.Accept-Encoding ~ "gzip") {
           set req.http.Accept-Encoding = "gzip";
       } elsif (req.http.Accept-Encoding ~ "deflate") {
           set req.http.Accept-Encoding = "deflate";
       } else {
           # unknown algorithm
           remove req.http.Accept-Encoding;
       }
    }

    // Add a Surrogate-Capability header to announce ESI support.
    set req.http.Surrogate-Capability = "abc=ESI/1.0";
    if (req.http.X-Forwarded-Proto == "https" ) {
        set req.http.X-Forwarded-Port = "443";
    } else {
        set req.http.X-Forwarded-Port = "80";
    }
    if (req.restarts == 0) {
        if (req.http.x-forwarded-for) {
            set req.http.X-Forwarded-For =
            req.http.X-Forwarded-For + ", " + client.ip;
        } else {
            set req.http.X-Forwarded-For = client.ip;
        }
    }
    // Remove has_js and Google Analytics __* cookies.
    set req.http.Cookie = regsuball(req.http.Cookie, "(^|;\s*)(_[_a-z]+|has_js)=[^;]*", "");
    // Remove a ";" prefix, if present.
    set req.http.Cookie = regsub(req.http.Cookie, "^;\s*", "");

    if (req.request != "GET" &&
      req.request != "HEAD" &&
      req.request != "PUT" &&
      req.request != "POST" &&
      req.request != "TRACE" &&
      req.request != "OPTIONS" &&
      req.request != "DELETE") {
        /* Non-RFC2616 or CONNECT which is weird. */
        return (pipe);
    }
    if (req.request != "GET" && req.request != "HEAD") {
        /* We only deal with GET and HEAD by default */
        return (pass);
    }

    if (!req.http.Cookie ~ "logged_in=true" && req.url ~ "^/$") {
        unset req.http.Cookie;
        return (lookup);
    }
    if (req.url ~ "^/api/users.json" || req.url ~ "^/api/tags.json") {
        unset req.http.Cookie;
        return (lookup);
    }
    if (req.url ~ "\.(png|gif|jpg|js|css|ico)") {
        unset req.http.Cookie;
        return (lookup);
    }
    if (req.http.Authorization || req.http.Cookie) {
        /* Not cacheable by default */
        return (pass);
    }
    if (req.url ~ "^/w00tw00t") {
            error 403 "Not permitted";
    }

    return (lookup);
}

## Fetch
sub vcl_fetch {
    if (req.http.X-UA-Device) {
        if (!beresp.http.Vary) { # no Vary at all
            set beresp.http.Vary = "X-UA-Device";
        } elseif (beresp.http.Vary !~ "X-UA-Device") {
            if (beresp.http.Vary ~ "User-Agent") {
                set beresp.http.Vary = regsub(beresp.http.Vary, "User-Agent", "X-UA-Device");
            } else {
                set beresp.http.Vary = beresp.http.Vary + ", X-UA-Device";
            }
        }
    }
    if (req.url ~ "\.(png|gif|jpg|js|css|ico)") {
        set beresp.http.Vary = regsub(beresp.http.Vary, "(,)?X-UA-Device", "");
    }
    # comment this out if you don't want the client to know your classification
    # set beresp.http.X-UA-Device = req.http.X-UA-Device;
    /*
    Check for ESI acknowledgement
    and remove Surrogate-Control header
    */
    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;

        // For Varnish >= 3.0
        set beresp.do_esi = true;
        // For Varnish < 3.0
        // esi;
    }
    ## Remove the X-Forwarded-For header if it exists.
    remove req.http.X-Forwarded-For;

    if (!req.url ~ "^/(login|auth|connect|register|logout)") {
        remove beresp.http.Set-Cookie;
    }
    if (beresp.ttl <= 0s ||
        beresp.http.Set-Cookie ||
       beresp.http.Vary == "*") {
        /*
         * Mark as "Hit-For-Pass" for the next 2 minutes
         */
        set beresp.ttl = 120 s;
        return (hit_for_pass);
   }

   ## insert the client IP address as X-Forwarded-For. This is the normal IP address of the user.
   set    req.http.X-Forwarded-For = req.http.rlnclientipaddr;

   ## Deliver the content
   return (deliver);
}

## Deliver
sub vcl_deliver {
    ## We'll be hiding some headers added by Varnish. We want to make sure people are not seeing we're using Varnish.
    ## Since we're not caching (yet), why bother telling people we use it?
    remove resp.http.X-Varnish;
    remove resp.http.Via;
    remove resp.http.Age;

    ## We'd like to hide the X-Powered-By headers. Nobody has to know we can run PHP and have version xyz of it.
    remove resp.http.X-Powered-By;

    # to keep any caches in the wild from serving wrong content to client #2 behind them, we need to
    # transform the Vary on the way out.
    if ((req.http.X-UA-Device) && (resp.http.Vary)) {
        set resp.http.Vary = regsub(resp.http.Vary, "X-UA-Device", "User-Agent");
    }

    return (deliver);
}

sub vcl_hit {
    if (req.request == "PURGE") {
        set obj.ttl = 0s;
        error 200 "Purged.";
    }
    return (deliver);
}

sub vcl_miss {
    if (req.request == "PURGE") {
        error 404 "Not in cache.";
    }
    return (fetch);
}

sub vcl_pipe {
    set bereq.http.connection = "close";

    return (pipe);
}

sub vcl_pass {
    return (pass);
}

sub vcl_hash {
    hash_data(req.url);
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }
    return (hash);
}

sub vcl_init {
        return (ok);
}

sub vcl_fini {
        return (ok);
}
