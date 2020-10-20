/*!
 * AgeGate Popup v0.0.11
 * https://github.com/nicknish/agegate-popup
 *
 * Copyright 2017 Nicholas Nishiguchi
 * Released under the MIT license
 */
! function(e) {
    "use strict";
    e.Agegate = function(e) {
            var t = {
                cookie: "popup_agreed",
                body: "Are you 21 or older?",
                yesButton: 'Yes',
                noButton: 'No',
                cancelUrl: "/",
                expires: 7,
                success: function() {},
                fail: function() {}
            };
            if (e = void 0 !== e ? e : {}, this.setOptions(e, t), !this.checkCookie(this.options.cookie)) return this.createPopup(), this.setEventHandlers(), this.freezeScrolling(), this
        }, Agegate.prototype = {
            createPopup: function() {
                this.container = document.createElement("div"), this.container.innerHTML = ['<div class="popup-backdrop" style="z-index: 1000; position: fixed; top: 0; right: 0; left: 0; bottom: 0; background-color: #242629; opacity: 0.78; overflow-x: hidden; overflow-y: auto;"></div>', '<div class="popup-modal" style="z-index: 1001; position: fixed; top: 0; right: 0; left: 0; bottom: 0; padding: 10px;">', '<div class="popup-container">', '<div class="popup-body">', this.options.body, "</div>", '<div class="popup-actions">', '<button class="popup-btn popup-agree">', this.options.yesButton, '</button>', '<button class="popup-btn popup-cancel">', this.options.noButton, '</button>', "</div>", "</div>", "</div>"].join(""), document.body.insertBefore(this.container, document.body.firstChild), this.agreeBtn = this.container.querySelector(".popup-agree"), this.cancelBtn = this.container.querySelector(".popup-cancel")
            },
            destroyPopup: function() {
                this.container.parentNode.removeChild(this.container), this.allowScrolling()
            },
            successHandler: function() {
                this.setCookie(), this.destroyPopup(), this.options.success()
            },
            failHandler: function() {
                this.options.fail(), e.location.assign(this.options.cancelUrl)
            },
            setOptions: function(e, t) {
                this.options = Object.assign(t, e, {
                    defaults: t
                })
            },
            checkCookie: function(e) {
                return Cookies.get(e)
            },
            setCookie: function() {
                Cookies.set(this.options.cookie, !0, {
                    expires: this.options.expires
                })
            },
            setEventHandlers: function() {
                this.agreeBtn.addEventListener("click", this.successHandler.bind(this), !1), this.cancelBtn.addEventListener("click", this.failHandler.bind(this), !1)
            },
            freezeScrolling: function() {
                document.body.style.overflowY = "hidden"
            },
            allowScrolling: function() {
                document.body.style.overflowY = "auto"
            }
        }, "function" != typeof Object.assign && Object.defineProperty(Object, "assign", {
            value: function(e, t) {
                if (null == e) throw new TypeError("Cannot convert undefined or null to object");
                for (var o = Object(e), n = 1; n < arguments.length; n++) {
                    var i = arguments[n];
                    if (null != i)
                        for (var r in i) Object.prototype.hasOwnProperty.call(i, r) && (o[r] = i[r])
                }
                return o
            },
            writable: !0,
            configurable: !0
        }),
        function(t) {
            var o = !1;
            if ("function" == typeof define && define.amd && (define(t), o = !0), "object" == typeof exports && (module.exports = t(), o = !0), !o) {
                var n = e.Cookies,
                    i = e.Cookies = t();
                i.noConflict = function() {
                    return e.Cookies = n, i
                }
            }
        }(function() {
            function e() {
                for (var e = 0, t = {}; e < arguments.length; e++) {
                    var o = arguments[e];
                    for (var n in o) t[n] = o[n]
                }
                return t
            }

            function t(o) {
                function n(t, i, r) {
                    var c;
                    if ("undefined" != typeof document) {
                        if (arguments.length > 1) {
                            if ("number" == typeof(r = e({
                                    path: "/"
                                }, n.defaults, r)).expires) {
                                var s = new Date;
                                s.setMilliseconds(s.getMilliseconds() + 864e5 * r.expires), r.expires = s
                            }
                            r.expires = r.expires ? r.expires.toUTCString() : "";
                            try {
                                c = JSON.stringify(i), /^[\{\[]/.test(c) && (i = c)
                            } catch (e) {}
                            i = o.write ? o.write(i, t) : encodeURIComponent(String(i)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent), t = (t = (t = encodeURIComponent(String(t))).replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent)).replace(/[\(\)]/g, escape);
                            var p = "";
                            for (var a in r) r[a] && (p += "; " + a, !0 !== r[a] && (p += "=" + r[a]));
                            return document.cookie = t + "=" + i + p
                        }
                        t || (c = {});
                        for (var l = document.cookie ? document.cookie.split("; ") : [], u = /(%[0-9A-Z]{2})+/g, d = 0; d < l.length; d++) {
                            var f = l[d].split("="),
                                h = f.slice(1).join("=");
                            '"' === h.charAt(0) && (h = h.slice(1, -1));
                            try {
                                var v = f[0].replace(u, decodeURIComponent);
                                if (h = o.read ? o.read(h, v) : o(h, v) || h.replace(u, decodeURIComponent), this.json) try {
                                    h = JSON.parse(h)
                                } catch (e) {}
                                if (t === v) {
                                    c = h;
                                    break
                                }
                                t || (c[v] = h)
                            } catch (e) {}
                        }
                        return c
                    }
                }
                return n.set = n, n.get = function(e) {
                    return n.call(n, e)
                }, n.getJSON = function() {
                    return n.apply({
                        json: !0
                    }, [].slice.call(arguments))
                }, n.defaults = {}, n.remove = function(t, o) {
                    n(t, "", e(o, {
                        expires: -1
                    }))
                }, n.withConverter = t, n
            }
            return t(function() {})
        })
}(window);