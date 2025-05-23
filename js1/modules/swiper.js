/**
 * Swiper 4.1.0
 * Most modern mobile touch slider and framework with hardware accelerated transitions
 * http://www.idangero.us/swiper/
 *
 * Copyright 2014-2018 Vladimir Kharlampidi
 *
 * Released under the MIT License
 *
 * Released on: January 13, 2018
 */
!(function (e, t) {
    "object" == typeof exports && "undefined" != typeof module
        ? (module.exports = t())
        : "function" == typeof define && define.amd
        ? define(t)
        : (e.Swiper = t());
})(this, function () {
    "use strict";
    var e = function (e) {
        for (var t = 0; t < e.length; t += 1) this[t] = e[t];
        return (this.length = e.length), this;
    };
    function t(t, i) {
        var s = [],
            a = 0;
        if (t && !i && t instanceof e) return t;
        if (t)
            if ("string" == typeof t) {
                var r,
                    n,
                    o = t.trim();
                if (o.indexOf("<") >= 0 && o.indexOf(">") >= 0) {
                    var l = "div";
                    for (
                        0 === o.indexOf("<li") && (l = "ul"),
                            0 === o.indexOf("<tr") && (l = "tbody"),
                            (0 !== o.indexOf("<td") &&
                                0 !== o.indexOf("<th")) ||
                                (l = "tr"),
                            0 === o.indexOf("<tbody") && (l = "table"),
                            0 === o.indexOf("<option") && (l = "select"),
                            (n = document.createElement(l)).innerHTML = o,
                            a = 0;
                        a < n.childNodes.length;
                        a += 1
                    )
                        s.push(n.childNodes[a]);
                } else
                    for (
                        r =
                            i || "#" !== t[0] || t.match(/[ .<>:~]/)
                                ? (i || document).querySelectorAll(t.trim())
                                : [
                                      document.getElementById(
                                          t.trim().split("#")[1]
                                      ),
                                  ],
                            a = 0;
                        a < r.length;
                        a += 1
                    )
                        r[a] && s.push(r[a]);
            } else if (t.nodeType || t === window || t === document) s.push(t);
            else if (t.length > 0 && t[0].nodeType)
                for (a = 0; a < t.length; a += 1) s.push(t[a]);
        return new e(s);
    }
    function i(e) {
        for (var t = [], i = 0; i < e.length; i += 1)
            -1 === t.indexOf(e[i]) && t.push(e[i]);
        return t;
    }
    (t.fn = e.prototype), (t.Class = e), (t.Dom7 = e);
    "resize scroll".split(" ");
    var s = {
        addClass: function (e) {
            if (void 0 === e) return this;
            for (var t = e.split(" "), i = 0; i < t.length; i += 1)
                for (var s = 0; s < this.length; s += 1)
                    void 0 !== this[s].classList && this[s].classList.add(t[i]);
            return this;
        },
        removeClass: function (e) {
            for (var t = e.split(" "), i = 0; i < t.length; i += 1)
                for (var s = 0; s < this.length; s += 1)
                    void 0 !== this[s].classList &&
                        this[s].classList.remove(t[i]);
            return this;
        },
        hasClass: function (e) {
            return !!this[0] && this[0].classList.contains(e);
        },
        toggleClass: function (e) {
            for (var t = e.split(" "), i = 0; i < t.length; i += 1)
                for (var s = 0; s < this.length; s += 1)
                    void 0 !== this[s].classList &&
                        this[s].classList.toggle(t[i]);
            return this;
        },
        attr: function (e, t) {
            var i = arguments;
            if (1 === arguments.length && "string" == typeof e)
                return this[0] ? this[0].getAttribute(e) : void 0;
            for (var s = 0; s < this.length; s += 1)
                if (2 === i.length) this[s].setAttribute(e, t);
                else
                    for (var a in e)
                        (this[s][a] = e[a]), this[s].setAttribute(a, e[a]);
            return this;
        },
        removeAttr: function (e) {
            for (var t = 0; t < this.length; t += 1) this[t].removeAttribute(e);
            return this;
        },
        data: function (e, t) {
            var i;
            if (void 0 !== t) {
                for (var s = 0; s < this.length; s += 1)
                    (i = this[s]).dom7ElementDataStorage ||
                        (i.dom7ElementDataStorage = {}),
                        (i.dom7ElementDataStorage[e] = t);
                return this;
            }
            if ((i = this[0])) {
                if (i.dom7ElementDataStorage && e in i.dom7ElementDataStorage)
                    return i.dom7ElementDataStorage[e];
                var a = i.getAttribute("data-" + e);
                return a || void 0;
            }
        },
        transform: function (e) {
            for (var t = 0; t < this.length; t += 1) {
                var i = this[t].style;
                (i.webkitTransform = e), (i.transform = e);
            }
            return this;
        },
        transition: function (e) {
            "string" != typeof e && (e += "ms");
            for (var t = 0; t < this.length; t += 1) {
                var i = this[t].style;
                (i.webkitTransitionDuration = e), (i.transitionDuration = e);
            }
            return this;
        },
        on: function () {
            for (var e = [], i = arguments.length; i--; ) e[i] = arguments[i];
            var s,
                a = e[0],
                r = e[1],
                n = e[2],
                o = e[3];
            function l(e) {
                var i = e.target;
                if (i) {
                    var s = e.target.dom7EventData || [];
                    if ((s.unshift(e), t(i).is(r))) n.apply(i, s);
                    else
                        for (
                            var a = t(i).parents(), o = 0;
                            o < a.length;
                            o += 1
                        )
                            t(a[o]).is(r) && n.apply(a[o], s);
                }
            }
            function d(e) {
                var t = e && e.target ? e.target.dom7EventData || [] : [];
                t.unshift(e), n.apply(this, t);
            }
            "function" == typeof e[1] &&
                ((a = (s = e)[0]), (n = s[1]), (o = s[2]), (r = void 0)),
                o || (o = !1);
            for (var h, p = a.split(" "), c = 0; c < this.length; c += 1) {
                var u = this[c];
                if (r)
                    for (h = 0; h < p.length; h += 1)
                        u.dom7LiveListeners || (u.dom7LiveListeners = []),
                            u.dom7LiveListeners.push({
                                type: a,
                                listener: n,
                                proxyListener: l,
                            }),
                            u.addEventListener(p[h], l, o);
                else
                    for (h = 0; h < p.length; h += 1)
                        u.dom7Listeners || (u.dom7Listeners = []),
                            u.dom7Listeners.push({
                                type: a,
                                listener: n,
                                proxyListener: d,
                            }),
                            u.addEventListener(p[h], d, o);
            }
            return this;
        },
        off: function () {
            for (var e = [], t = arguments.length; t--; ) e[t] = arguments[t];
            var i,
                s = e[0],
                a = e[1],
                r = e[2],
                n = e[3];
            "function" == typeof e[1] &&
                ((s = (i = e)[0]), (r = i[1]), (n = i[2]), (a = void 0)),
                n || (n = !1);
            for (var o = s.split(" "), l = 0; l < o.length; l += 1)
                for (var d = 0; d < this.length; d += 1) {
                    var h = this[d];
                    if (a) {
                        if (h.dom7LiveListeners)
                            for (
                                var p = 0;
                                p < h.dom7LiveListeners.length;
                                p += 1
                            )
                                r
                                    ? h.dom7LiveListeners[p].listener === r &&
                                      h.removeEventListener(
                                          o[l],
                                          h.dom7LiveListeners[p].proxyListener,
                                          n
                                      )
                                    : h.dom7LiveListeners[p].type === o[l] &&
                                      h.removeEventListener(
                                          o[l],
                                          h.dom7LiveListeners[p].proxyListener,
                                          n
                                      );
                    } else if (h.dom7Listeners)
                        for (var c = 0; c < h.dom7Listeners.length; c += 1)
                            r
                                ? h.dom7Listeners[c].listener === r &&
                                  h.removeEventListener(
                                      o[l],
                                      h.dom7Listeners[c].proxyListener,
                                      n
                                  )
                                : h.dom7Listeners[c].type === o[l] &&
                                  h.removeEventListener(
                                      o[l],
                                      h.dom7Listeners[c].proxyListener,
                                      n
                                  );
                }
            return this;
        },
        trigger: function () {
            for (var e = [], t = arguments.length; t--; ) e[t] = arguments[t];
            for (var i = e[0].split(" "), s = e[1], a = 0; a < i.length; a += 1)
                for (var r = 0; r < this.length; r += 1) {
                    var n = void 0;
                    try {
                        n = new window.CustomEvent(i[a], {
                            detail: s,
                            bubbles: !0,
                            cancelable: !0,
                        });
                    } catch (e) {
                        (n = document.createEvent("Event")).initEvent(
                            i[a],
                            !0,
                            !0
                        ),
                            (n.detail = s);
                    }
                    (this[r].dom7EventData = e.filter(function (e, t) {
                        return t > 0;
                    })),
                        this[r].dispatchEvent(n),
                        (this[r].dom7EventData = []),
                        delete this[r].dom7EventData;
                }
            return this;
        },
        transitionEnd: function (e) {
            var t,
                i = ["webkitTransitionEnd", "transitionend"],
                s = this;
            function a(r) {
                if (r.target === this)
                    for (e.call(this, r), t = 0; t < i.length; t += 1)
                        s.off(i[t], a);
            }
            if (e) for (t = 0; t < i.length; t += 1) s.on(i[t], a);
            return this;
        },
        outerWidth: function (e) {
            if (this.length > 0) {
                if (e) {
                    var t = this.styles();
                    return (
                        this[0].offsetWidth +
                        parseFloat(t.getPropertyValue("margin-right")) +
                        parseFloat(t.getPropertyValue("margin-left"))
                    );
                }
                return this[0].offsetWidth;
            }
            return null;
        },
        outerHeight: function (e) {
            if (this.length > 0) {
                if (e) {
                    var t = this.styles();
                    return (
                        this[0].offsetHeight +
                        parseFloat(t.getPropertyValue("margin-top")) +
                        parseFloat(t.getPropertyValue("margin-bottom"))
                    );
                }
                return this[0].offsetHeight;
            }
            return null;
        },
        offset: function () {
            if (this.length > 0) {
                var e = this[0],
                    t = e.getBoundingClientRect(),
                    i = document.body,
                    s = e.clientTop || i.clientTop || 0,
                    a = e.clientLeft || i.clientLeft || 0,
                    r = e === window ? window.scrollY : e.scrollTop,
                    n = e === window ? window.scrollX : e.scrollLeft;
                return { top: t.top + r - s, left: t.left + n - a };
            }
            return null;
        },
        css: function (e, t) {
            var i;
            if (1 === arguments.length) {
                if ("string" != typeof e) {
                    for (i = 0; i < this.length; i += 1)
                        for (var s in e) this[i].style[s] = e[s];
                    return this;
                }
                if (this[0])
                    return window
                        .getComputedStyle(this[0], null)
                        .getPropertyValue(e);
            }
            if (2 === arguments.length && "string" == typeof e) {
                for (i = 0; i < this.length; i += 1) this[i].style[e] = t;
                return this;
            }
            return this;
        },
        each: function (e) {
            if (!e) return this;
            for (var t = 0; t < this.length; t += 1)
                if (!1 === e.call(this[t], t, this[t])) return this;
            return this;
        },
        html: function (e) {
            if (void 0 === e) return this[0] ? this[0].innerHTML : void 0;
            for (var t = 0; t < this.length; t += 1) this[t].innerHTML = e;
            return this;
        },
        text: function (e) {
            if (void 0 === e)
                return this[0] ? this[0].textContent.trim() : null;
            for (var t = 0; t < this.length; t += 1) this[t].textContent = e;
            return this;
        },
        is: function (i) {
            var s,
                a,
                r = this[0];
            if (!r || void 0 === i) return !1;
            if ("string" == typeof i) {
                if (r.matches) return r.matches(i);
                if (r.webkitMatchesSelector) return r.webkitMatchesSelector(i);
                if (r.msMatchesSelector) return r.msMatchesSelector(i);
                for (s = t(i), a = 0; a < s.length; a += 1)
                    if (s[a] === r) return !0;
                return !1;
            }
            if (i === document) return r === document;
            if (i === window) return r === window;
            if (i.nodeType || i instanceof e) {
                for (s = i.nodeType ? [i] : i, a = 0; a < s.length; a += 1)
                    if (s[a] === r) return !0;
                return !1;
            }
            return !1;
        },
        index: function () {
            var e,
                t = this[0];
            if (t) {
                for (e = 0; null !== (t = t.previousSibling); )
                    1 === t.nodeType && (e += 1);
                return e;
            }
        },
        eq: function (t) {
            if (void 0 === t) return this;
            var i,
                s = this.length;
            return new e(
                t > s - 1
                    ? []
                    : t < 0
                    ? (i = s + t) < 0
                        ? []
                        : [this[i]]
                    : [this[t]]
            );
        },
        append: function () {
            for (var t, i = [], s = arguments.length; s--; )
                i[s] = arguments[s];
            for (var a = 0; a < i.length; a += 1) {
                t = i[a];
                for (var r = 0; r < this.length; r += 1)
                    if ("string" == typeof t) {
                        var n = document.createElement("div");
                        for (n.innerHTML = t; n.firstChild; )
                            this[r].appendChild(n.firstChild);
                    } else if (t instanceof e)
                        for (var o = 0; o < t.length; o += 1)
                            this[r].appendChild(t[o]);
                    else this[r].appendChild(t);
            }
            return this;
        },
        prepend: function (t) {
            var i, s;
            for (i = 0; i < this.length; i += 1)
                if ("string" == typeof t) {
                    var a = document.createElement("div");
                    for (
                        a.innerHTML = t, s = a.childNodes.length - 1;
                        s >= 0;
                        s -= 1
                    )
                        this[i].insertBefore(
                            a.childNodes[s],
                            this[i].childNodes[0]
                        );
                } else if (t instanceof e)
                    for (s = 0; s < t.length; s += 1)
                        this[i].insertBefore(t[s], this[i].childNodes[0]);
                else this[i].insertBefore(t, this[i].childNodes[0]);
            return this;
        },
        next: function (i) {
            return this.length > 0
                ? i
                    ? this[0].nextElementSibling &&
                      t(this[0].nextElementSibling).is(i)
                        ? new e([this[0].nextElementSibling])
                        : new e([])
                    : this[0].nextElementSibling
                    ? new e([this[0].nextElementSibling])
                    : new e([])
                : new e([]);
        },
        nextAll: function (i) {
            var s = [],
                a = this[0];
            if (!a) return new e([]);
            for (; a.nextElementSibling; ) {
                var r = a.nextElementSibling;
                i ? t(r).is(i) && s.push(r) : s.push(r), (a = r);
            }
            return new e(s);
        },
        prev: function (i) {
            if (this.length > 0) {
                var s = this[0];
                return i
                    ? s.previousElementSibling &&
                      t(s.previousElementSibling).is(i)
                        ? new e([s.previousElementSibling])
                        : new e([])
                    : s.previousElementSibling
                    ? new e([s.previousElementSibling])
                    : new e([]);
            }
            return new e([]);
        },
        prevAll: function (i) {
            var s = [],
                a = this[0];
            if (!a) return new e([]);
            for (; a.previousElementSibling; ) {
                var r = a.previousElementSibling;
                i ? t(r).is(i) && s.push(r) : s.push(r), (a = r);
            }
            return new e(s);
        },
        parent: function (e) {
            for (var s = [], a = 0; a < this.length; a += 1)
                null !== this[a].parentNode &&
                    (e
                        ? t(this[a].parentNode).is(e) &&
                          s.push(this[a].parentNode)
                        : s.push(this[a].parentNode));
            return t(i(s));
        },
        parents: function (e) {
            for (var s = [], a = 0; a < this.length; a += 1)
                for (var r = this[a].parentNode; r; )
                    e ? t(r).is(e) && s.push(r) : s.push(r), (r = r.parentNode);
            return t(i(s));
        },
        closest: function (t) {
            var i = this;
            return void 0 === t
                ? new e([])
                : (i.is(t) || (i = i.parents(t).eq(0)), i);
        },
        find: function (t) {
            for (var i = [], s = 0; s < this.length; s += 1)
                for (
                    var a = this[s].querySelectorAll(t), r = 0;
                    r < a.length;
                    r += 1
                )
                    i.push(a[r]);
            return new e(i);
        },
        children: function (s) {
            for (var a = [], r = 0; r < this.length; r += 1)
                for (var n = this[r].childNodes, o = 0; o < n.length; o += 1)
                    s
                        ? 1 === n[o].nodeType && t(n[o]).is(s) && a.push(n[o])
                        : 1 === n[o].nodeType && a.push(n[o]);
            return new e(i(a));
        },
        remove: function () {
            for (var e = 0; e < this.length; e += 1)
                this[e].parentNode && this[e].parentNode.removeChild(this[e]);
            return this;
        },
        add: function () {
            for (var e = [], i = arguments.length; i--; ) e[i] = arguments[i];
            var s, a;
            for (s = 0; s < e.length; s += 1) {
                var r = t(e[s]);
                for (a = 0; a < r.length; a += 1)
                    (this[this.length] = r[a]), (this.length += 1);
            }
            return this;
        },
        styles: function () {
            return this[0] ? window.getComputedStyle(this[0], null) : {};
        },
    };
    Object.keys(s).forEach(function (e) {
        t.fn[e] = s[e];
    });
    var a,
        r,
        n,
        o =
            "undefined" == typeof window
                ? {
                      navigator: { userAgent: "" },
                      location: {},
                      history: {},
                      addEventListener: function () {},
                      removeEventListener: function () {},
                      getComputedStyle: function () {
                          return {};
                      },
                      Image: function () {},
                      Date: function () {},
                      screen: {},
                  }
                : window,
        l = {
            deleteProps: function (e) {
                var t = e;
                Object.keys(t).forEach(function (e) {
                    try {
                        t[e] = null;
                    } catch (e) {}
                    try {
                        delete t[e];
                    } catch (e) {}
                });
            },
            nextTick: function (e, t) {
                return void 0 === t && (t = 0), setTimeout(e, t);
            },
            now: function () {
                return Date.now();
            },
            getTranslate: function (e, t) {
                var i, s, a;
                void 0 === t && (t = "x");
                var r = o.getComputedStyle(e, null);
                return (
                    o.WebKitCSSMatrix
                        ? ((s = r.transform || r.webkitTransform).split(",")
                              .length > 6 &&
                              (s = s
                                  .split(", ")
                                  .map(function (e) {
                                      return e.replace(",", ".");
                                  })
                                  .join(", ")),
                          (a = new o.WebKitCSSMatrix("none" === s ? "" : s)))
                        : (i = (a =
                              r.MozTransform ||
                              r.OTransform ||
                              r.MsTransform ||
                              r.msTransform ||
                              r.transform ||
                              r
                                  .getPropertyValue("transform")
                                  .replace("translate(", "matrix(1, 0, 0, 1,"))
                              .toString()
                              .split(",")),
                    "x" === t &&
                        (s = o.WebKitCSSMatrix
                            ? a.m41
                            : 16 === i.length
                            ? parseFloat(i[12])
                            : parseFloat(i[4])),
                    "y" === t &&
                        (s = o.WebKitCSSMatrix
                            ? a.m42
                            : 16 === i.length
                            ? parseFloat(i[13])
                            : parseFloat(i[5])),
                    s || 0
                );
            },
            parseUrlQuery: function (e) {
                var t,
                    i,
                    s,
                    a,
                    r = {},
                    n = e || o.location.href;
                if ("string" == typeof n && n.length)
                    for (
                        a = (i = (n =
                            n.indexOf("?") > -1 ? n.replace(/\S*\?/, "") : "")
                            .split("&")
                            .filter(function (e) {
                                return "" !== e;
                            })).length,
                            t = 0;
                        t < a;
                        t += 1
                    )
                        (s = i[t].replace(/#\S+/g, "").split("=")),
                            (r[decodeURIComponent(s[0])] =
                                void 0 === s[1]
                                    ? void 0
                                    : decodeURIComponent(s[1]) || "");
                return r;
            },
            isObject: function (e) {
                return (
                    "object" == typeof e &&
                    null !== e &&
                    e.constructor &&
                    e.constructor === Object
                );
            },
            extend: function () {
                for (var e = [], t = arguments.length; t--; )
                    e[t] = arguments[t];
                for (var i = Object(e[0]), s = 1; s < e.length; s += 1) {
                    var a = e[s];
                    if (void 0 !== a && null !== a)
                        for (
                            var r = Object.keys(Object(a)), n = 0, o = r.length;
                            n < o;
                            n += 1
                        ) {
                            var d = r[n],
                                h = Object.getOwnPropertyDescriptor(a, d);
                            void 0 !== h &&
                                h.enumerable &&
                                (l.isObject(i[d]) && l.isObject(a[d])
                                    ? l.extend(i[d], a[d])
                                    : !l.isObject(i[d]) && l.isObject(a[d])
                                    ? ((i[d] = {}), l.extend(i[d], a[d]))
                                    : (i[d] = a[d]));
                        }
                }
                return i;
            },
        },
        d =
            "undefined" == typeof document
                ? {
                      addEventListener: function () {},
                      removeEventListener: function () {},
                      activeElement: { blur: function () {}, nodeName: "" },
                      querySelector: function () {
                          return {};
                      },
                      querySelectorAll: function () {
                          return [];
                      },
                      createElement: function () {
                          return {
                              style: {},
                              setAttribute: function () {},
                              getElementsByTagName: function () {
                                  return [];
                              },
                          };
                      },
                      location: { hash: "" },
                  }
                : document,
        h =
            ((n = d.createElement("div")),
            {
                touch:
                    (o.Modernizr && !0 === o.Modernizr.touch) ||
                    !!(
                        "ontouchstart" in o ||
                        (o.DocumentTouch && d instanceof o.DocumentTouch)
                    ),
                pointerEvents: !(
                    !o.navigator.pointerEnabled && !o.PointerEvent
                ),
                prefixedPointerEvents: !!o.navigator.msPointerEnabled,
                transition:
                    ((r = n.style),
                    "transition" in r ||
                        "webkitTransition" in r ||
                        "MozTransition" in r),
                transforms3d:
                    (o.Modernizr && !0 === o.Modernizr.csstransforms3d) ||
                    ((a = n.style),
                    "webkitPerspective" in a ||
                        "MozPerspective" in a ||
                        "OPerspective" in a ||
                        "MsPerspective" in a ||
                        "perspective" in a),
                flexbox: (function () {
                    for (
                        var e = n.style,
                            t =
                                "alignItems webkitAlignItems webkitBoxAlign msFlexAlign mozBoxAlign webkitFlexDirection msFlexDirection mozBoxDirection mozBoxOrient webkitBoxDirection webkitBoxOrient".split(
                                    " "
                                ),
                            i = 0;
                        i < t.length;
                        i += 1
                    )
                        if (t[i] in e) return !0;
                    return !1;
                })(),
                observer:
                    "MutationObserver" in o || "WebkitMutationObserver" in o,
                passiveListener: (function () {
                    var e = !1;
                    try {
                        var t = Object.defineProperty({}, "passive", {
                            get: function () {
                                e = !0;
                            },
                        });
                        o.addEventListener("testPassiveListener", null, t);
                    } catch (e) {}
                    return e;
                })(),
                gestures: "ongesturestart" in o,
            }),
        p = function (e) {
            void 0 === e && (e = {});
            var t = this;
            (t.params = e),
                (t.eventsListeners = {}),
                t.params &&
                    t.params.on &&
                    Object.keys(t.params.on).forEach(function (e) {
                        t.on(e, t.params.on[e]);
                    });
        },
        c = { components: { configurable: !0 } };
    (p.prototype.on = function (e, t) {
        var i = this;
        return "function" != typeof t
            ? i
            : (e.split(" ").forEach(function (e) {
                  i.eventsListeners[e] || (i.eventsListeners[e] = []),
                      i.eventsListeners[e].push(t);
              }),
              i);
    }),
        (p.prototype.once = function (e, t) {
            var i = this;
            if ("function" != typeof t) return i;
            return i.on(e, function s() {
                for (var a = [], r = arguments.length; r--; )
                    a[r] = arguments[r];
                t.apply(i, a), i.off(e, s);
            });
        }),
        (p.prototype.off = function (e, t) {
            var i = this;
            return (
                e.split(" ").forEach(function (e) {
                    void 0 === t
                        ? (i.eventsListeners[e] = [])
                        : i.eventsListeners[e].forEach(function (s, a) {
                              s === t && i.eventsListeners[e].splice(a, 1);
                          });
                }),
                i
            );
        }),
        (p.prototype.emit = function () {
            for (var e = [], t = arguments.length; t--; ) e[t] = arguments[t];
            var i,
                s,
                a,
                r = this;
            return r.eventsListeners
                ? ("string" == typeof e[0] || Array.isArray(e[0])
                      ? ((i = e[0]), (s = e.slice(1, e.length)), (a = r))
                      : ((i = e[0].events),
                        (s = e[0].data),
                        (a = e[0].context || r)),
                  (Array.isArray(i) ? i : i.split(" ")).forEach(function (e) {
                      if (r.eventsListeners[e]) {
                          var t = [];
                          r.eventsListeners[e].forEach(function (e) {
                              t.push(e);
                          }),
                              t.forEach(function (e) {
                                  e.apply(a, s);
                              });
                      }
                  }),
                  r)
                : r;
        }),
        (p.prototype.useModulesParams = function (e) {
            var t = this;
            t.modules &&
                Object.keys(t.modules).forEach(function (i) {
                    var s = t.modules[i];
                    s.params && l.extend(e, s.params);
                });
        }),
        (p.prototype.useModules = function (e) {
            void 0 === e && (e = {});
            var t = this;
            t.modules &&
                Object.keys(t.modules).forEach(function (i) {
                    var s = t.modules[i],
                        a = e[i] || {};
                    s.instance &&
                        Object.keys(s.instance).forEach(function (e) {
                            var i = s.instance[e];
                            t[e] = "function" == typeof i ? i.bind(t) : i;
                        }),
                        s.on &&
                            t.on &&
                            Object.keys(s.on).forEach(function (e) {
                                t.on(e, s.on[e]);
                            }),
                        s.create && s.create.bind(t)(a);
                });
        }),
        (c.components.set = function (e) {
            this.use && this.use(e);
        }),
        (p.installModule = function (e) {
            for (var t = [], i = arguments.length - 1; i-- > 0; )
                t[i] = arguments[i + 1];
            var s = this;
            s.prototype.modules || (s.prototype.modules = {});
            var a =
                e.name ||
                Object.keys(s.prototype.modules).length + "_" + l.now();
            return (
                (s.prototype.modules[a] = e),
                e.proto &&
                    Object.keys(e.proto).forEach(function (t) {
                        s.prototype[t] = e.proto[t];
                    }),
                e.static &&
                    Object.keys(e.static).forEach(function (t) {
                        s[t] = e.static[t];
                    }),
                e.install && e.install.apply(s, t),
                s
            );
        }),
        (p.use = function (e) {
            for (var t = [], i = arguments.length - 1; i-- > 0; )
                t[i] = arguments[i + 1];
            var s = this;
            return Array.isArray(e)
                ? (e.forEach(function (e) {
                      return s.installModule(e);
                  }),
                  s)
                : s.installModule.apply(s, [e].concat(t));
        }),
        Object.defineProperties(p, c);
    var u = {
            updateSize: function () {
                var e,
                    t,
                    i = this.$el;
                (e =
                    void 0 !== this.params.width
                        ? this.params.width
                        : i[0].clientWidth),
                    (t =
                        void 0 !== this.params.height
                            ? this.params.height
                            : i[0].clientHeight),
                    (0 === e && this.isHorizontal()) ||
                        (0 === t && this.isVertical()) ||
                        ((e =
                            e -
                            parseInt(i.css("padding-left"), 10) -
                            parseInt(i.css("padding-right"), 10)),
                        (t =
                            t -
                            parseInt(i.css("padding-top"), 10) -
                            parseInt(i.css("padding-bottom"), 10)),
                        l.extend(this, {
                            width: e,
                            height: t,
                            size: this.isHorizontal() ? e : t,
                        }));
            },
            updateSlides: function () {
                var e = this.params,
                    t = this.$wrapperEl,
                    i = this.size,
                    s = this.rtl,
                    a = this.wrongRTL,
                    r = t.children("." + this.params.slideClass),
                    n =
                        this.virtual && e.virtual.enabled
                            ? this.virtual.slides.length
                            : r.length,
                    o = [],
                    d = [],
                    p = [],
                    c = e.slidesOffsetBefore;
                "function" == typeof c && (c = e.slidesOffsetBefore.call(this));
                var u = e.slidesOffsetAfter;
                "function" == typeof u && (u = e.slidesOffsetAfter.call(this));
                var f = n,
                    v = this.snapGrid.length,
                    m = this.snapGrid.length,
                    g = e.spaceBetween,
                    b = -c,
                    w = 0,
                    y = 0;
                if (void 0 !== i) {
                    var x, T;
                    "string" == typeof g &&
                        g.indexOf("%") >= 0 &&
                        (g = (parseFloat(g.replace("%", "")) / 100) * i),
                        (this.virtualSize = -g),
                        s
                            ? r.css({ marginLeft: "", marginTop: "" })
                            : r.css({ marginRight: "", marginBottom: "" }),
                        e.slidesPerColumn > 1 &&
                            ((x =
                                Math.floor(n / e.slidesPerColumn) ===
                                n / this.params.slidesPerColumn
                                    ? n
                                    : Math.ceil(n / e.slidesPerColumn) *
                                      e.slidesPerColumn),
                            "auto" !== e.slidesPerView &&
                                "row" === e.slidesPerColumnFill &&
                                (x = Math.max(
                                    x,
                                    e.slidesPerView * e.slidesPerColumn
                                )));
                    for (
                        var E,
                            S = e.slidesPerColumn,
                            C = x / S,
                            M = C - (e.slidesPerColumn * C - n),
                            z = 0;
                        z < n;
                        z += 1
                    ) {
                        T = 0;
                        var P = r.eq(z);
                        if (e.slidesPerColumn > 1) {
                            var k = void 0,
                                $ = void 0,
                                L = void 0;
                            "column" === e.slidesPerColumnFill
                                ? ((L = z - ($ = Math.floor(z / S)) * S),
                                  ($ > M || ($ === M && L === S - 1)) &&
                                      (L += 1) >= S &&
                                      ((L = 0), ($ += 1)),
                                  (k = $ + (L * x) / S),
                                  P.css({
                                      "-webkit-box-ordinal-group": k,
                                      "-moz-box-ordinal-group": k,
                                      "-ms-flex-order": k,
                                      "-webkit-order": k,
                                      order: k,
                                  }))
                                : ($ = z - (L = Math.floor(z / C)) * C),
                                P.css(
                                    "margin-" +
                                        (this.isHorizontal() ? "top" : "left"),
                                    0 !== L &&
                                        e.spaceBetween &&
                                        e.spaceBetween + "px"
                                )
                                    .attr("data-swiper-column", $)
                                    .attr("data-swiper-row", L);
                        }
                        "none" !== P.css("display") &&
                            ("auto" === e.slidesPerView
                                ? ((T = this.isHorizontal()
                                      ? P.outerWidth(!0)
                                      : P.outerHeight(!0)),
                                  e.roundLengths && (T = Math.floor(T)))
                                : ((T =
                                      (i - (e.slidesPerView - 1) * g) /
                                      e.slidesPerView),
                                  e.roundLengths && (T = Math.floor(T)),
                                  r[z] &&
                                      (this.isHorizontal()
                                          ? (r[z].style.width = T + "px")
                                          : (r[z].style.height = T + "px"))),
                            r[z] && (r[z].swiperSlideSize = T),
                            p.push(T),
                            e.centeredSlides
                                ? ((b = b + T / 2 + w / 2 + g),
                                  0 === w && 0 !== z && (b = b - i / 2 - g),
                                  0 === z && (b = b - i / 2 - g),
                                  Math.abs(b) < 0.001 && (b = 0),
                                  y % e.slidesPerGroup == 0 && o.push(b),
                                  d.push(b))
                                : (y % e.slidesPerGroup == 0 && o.push(b),
                                  d.push(b),
                                  (b = b + T + g)),
                            (this.virtualSize += T + g),
                            (w = T),
                            (y += 1));
                    }
                    if (
                        ((this.virtualSize = Math.max(this.virtualSize, i) + u),
                        s &&
                            a &&
                            ("slide" === e.effect ||
                                "coverflow" === e.effect) &&
                            t.css({
                                width: this.virtualSize + e.spaceBetween + "px",
                            }),
                        (h.flexbox && !e.setWrapperSize) ||
                            (this.isHorizontal()
                                ? t.css({
                                      width:
                                          this.virtualSize +
                                          e.spaceBetween +
                                          "px",
                                  })
                                : t.css({
                                      height:
                                          this.virtualSize +
                                          e.spaceBetween +
                                          "px",
                                  })),
                        e.slidesPerColumn > 1 &&
                            ((this.virtualSize = (T + e.spaceBetween) * x),
                            (this.virtualSize =
                                Math.ceil(
                                    this.virtualSize / e.slidesPerColumn
                                ) - e.spaceBetween),
                            this.isHorizontal()
                                ? t.css({
                                      width:
                                          this.virtualSize +
                                          e.spaceBetween +
                                          "px",
                                  })
                                : t.css({
                                      height:
                                          this.virtualSize +
                                          e.spaceBetween +
                                          "px",
                                  }),
                            e.centeredSlides))
                    ) {
                        E = [];
                        for (var I = 0; I < o.length; I += 1)
                            o[I] < this.virtualSize + o[0] && E.push(o[I]);
                        o = E;
                    }
                    if (!e.centeredSlides) {
                        E = [];
                        for (var D = 0; D < o.length; D += 1)
                            o[D] <= this.virtualSize - i && E.push(o[D]);
                        (o = E),
                            Math.floor(this.virtualSize - i) -
                                Math.floor(o[o.length - 1]) >
                                1 && o.push(this.virtualSize - i);
                    }
                    0 === o.length && (o = [0]),
                        0 !== e.spaceBetween &&
                            (this.isHorizontal()
                                ? s
                                    ? r.css({ marginLeft: g + "px" })
                                    : r.css({ marginRight: g + "px" })
                                : r.css({ marginBottom: g + "px" })),
                        l.extend(this, {
                            slides: r,
                            snapGrid: o,
                            slidesGrid: d,
                            slidesSizesGrid: p,
                        }),
                        n !== f && this.emit("slidesLengthChange"),
                        o.length !== v &&
                            (this.params.watchOverflow && this.checkOverflow(),
                            this.emit("snapGridLengthChange")),
                        d.length !== m && this.emit("slidesGridLengthChange"),
                        (e.watchSlidesProgress || e.watchSlidesVisibility) &&
                            this.updateSlidesOffset();
                }
            },
            updateAutoHeight: function () {
                var e,
                    t = [],
                    i = 0;
                if (
                    "auto" !== this.params.slidesPerView &&
                    this.params.slidesPerView > 1
                )
                    for (
                        e = 0;
                        e < Math.ceil(this.params.slidesPerView);
                        e += 1
                    ) {
                        var s = this.activeIndex + e;
                        if (s > this.slides.length) break;
                        t.push(this.slides.eq(s)[0]);
                    }
                else t.push(this.slides.eq(this.activeIndex)[0]);
                for (e = 0; e < t.length; e += 1)
                    if (void 0 !== t[e]) {
                        var a = t[e].offsetHeight;
                        i = a > i ? a : i;
                    }
                i && this.$wrapperEl.css("height", i + "px");
            },
            updateSlidesOffset: function () {
                for (var e = this.slides, t = 0; t < e.length; t += 1)
                    e[t].swiperSlideOffset = this.isHorizontal()
                        ? e[t].offsetLeft
                        : e[t].offsetTop;
            },
            updateSlidesProgress: function (e) {
                void 0 === e && (e = this.translate || 0);
                var t = this.params,
                    i = this.slides,
                    s = this.rtl;
                if (0 !== i.length) {
                    void 0 === i[0].swiperSlideOffset &&
                        this.updateSlidesOffset();
                    var a = -e;
                    s && (a = e), i.removeClass(t.slideVisibleClass);
                    for (var r = 0; r < i.length; r += 1) {
                        var n = i[r],
                            o =
                                (a +
                                    (t.centeredSlides
                                        ? this.minTranslate()
                                        : 0) -
                                    n.swiperSlideOffset) /
                                (n.swiperSlideSize + t.spaceBetween);
                        if (t.watchSlidesVisibility) {
                            var l = -(a - n.swiperSlideOffset),
                                d = l + this.slidesSizesGrid[r];
                            ((l >= 0 && l < this.size) ||
                                (d > 0 && d <= this.size) ||
                                (l <= 0 && d >= this.size)) &&
                                i.eq(r).addClass(t.slideVisibleClass);
                        }
                        n.progress = s ? -o : o;
                    }
                }
            },
            updateProgress: function (e) {
                void 0 === e && (e = this.translate || 0);
                var t = this.params,
                    i = this.maxTranslate() - this.minTranslate(),
                    s = this.progress,
                    a = this.isBeginning,
                    r = this.isEnd,
                    n = a,
                    o = r;
                0 === i
                    ? ((s = 0), (a = !0), (r = !0))
                    : ((a = (s = (e - this.minTranslate()) / i) <= 0),
                      (r = s >= 1)),
                    l.extend(this, { progress: s, isBeginning: a, isEnd: r }),
                    (t.watchSlidesProgress || t.watchSlidesVisibility) &&
                        this.updateSlidesProgress(e),
                    a && !n && this.emit("reachBeginning toEdge"),
                    r && !o && this.emit("reachEnd toEdge"),
                    ((n && !a) || (o && !r)) && this.emit("fromEdge"),
                    this.emit("progress", s);
            },
            updateSlidesClasses: function () {
                var e,
                    t = this.slides,
                    i = this.params,
                    s = this.$wrapperEl,
                    a = this.activeIndex,
                    r = this.realIndex,
                    n = this.virtual && i.virtual.enabled;
                t.removeClass(
                    i.slideActiveClass +
                        " " +
                        i.slideNextClass +
                        " " +
                        i.slidePrevClass +
                        " " +
                        i.slideDuplicateActiveClass +
                        " " +
                        i.slideDuplicateNextClass +
                        " " +
                        i.slideDuplicatePrevClass
                ),
                    (e = n
                        ? this.$wrapperEl.find(
                              "." +
                                  i.slideClass +
                                  '[data-swiper-slide-index="' +
                                  a +
                                  '"]'
                          )
                        : t.eq(a)).addClass(i.slideActiveClass),
                    i.loop &&
                        (e.hasClass(i.slideDuplicateClass)
                            ? s
                                  .children(
                                      "." +
                                          i.slideClass +
                                          ":not(." +
                                          i.slideDuplicateClass +
                                          ')[data-swiper-slide-index="' +
                                          r +
                                          '"]'
                                  )
                                  .addClass(i.slideDuplicateActiveClass)
                            : s
                                  .children(
                                      "." +
                                          i.slideClass +
                                          "." +
                                          i.slideDuplicateClass +
                                          '[data-swiper-slide-index="' +
                                          r +
                                          '"]'
                                  )
                                  .addClass(i.slideDuplicateActiveClass));
                var o = e
                    .nextAll("." + i.slideClass)
                    .eq(0)
                    .addClass(i.slideNextClass);
                i.loop &&
                    0 === o.length &&
                    (o = t.eq(0)).addClass(i.slideNextClass);
                var l = e
                    .prevAll("." + i.slideClass)
                    .eq(0)
                    .addClass(i.slidePrevClass);
                i.loop &&
                    0 === l.length &&
                    (l = t.eq(-1)).addClass(i.slidePrevClass),
                    i.loop &&
                        (o.hasClass(i.slideDuplicateClass)
                            ? s
                                  .children(
                                      "." +
                                          i.slideClass +
                                          ":not(." +
                                          i.slideDuplicateClass +
                                          ')[data-swiper-slide-index="' +
                                          o.attr("data-swiper-slide-index") +
                                          '"]'
                                  )
                                  .addClass(i.slideDuplicateNextClass)
                            : s
                                  .children(
                                      "." +
                                          i.slideClass +
                                          "." +
                                          i.slideDuplicateClass +
                                          '[data-swiper-slide-index="' +
                                          o.attr("data-swiper-slide-index") +
                                          '"]'
                                  )
                                  .addClass(i.slideDuplicateNextClass),
                        l.hasClass(i.slideDuplicateClass)
                            ? s
                                  .children(
                                      "." +
                                          i.slideClass +
                                          ":not(." +
                                          i.slideDuplicateClass +
                                          ')[data-swiper-slide-index="' +
                                          l.attr("data-swiper-slide-index") +
                                          '"]'
                                  )
                                  .addClass(i.slideDuplicatePrevClass)
                            : s
                                  .children(
                                      "." +
                                          i.slideClass +
                                          "." +
                                          i.slideDuplicateClass +
                                          '[data-swiper-slide-index="' +
                                          l.attr("data-swiper-slide-index") +
                                          '"]'
                                  )
                                  .addClass(i.slideDuplicatePrevClass));
            },
            updateActiveIndex: function (e) {
                var t,
                    i = this.rtl ? this.translate : -this.translate,
                    s = this.slidesGrid,
                    a = this.snapGrid,
                    r = this.params,
                    n = this.activeIndex,
                    o = this.realIndex,
                    d = this.snapIndex,
                    h = e;
                if (void 0 === h) {
                    for (var p = 0; p < s.length; p += 1)
                        void 0 !== s[p + 1]
                            ? i >= s[p] && i < s[p + 1] - (s[p + 1] - s[p]) / 2
                                ? (h = p)
                                : i >= s[p] && i < s[p + 1] && (h = p + 1)
                            : i >= s[p] && (h = p);
                    r.normalizeSlideIndex && (h < 0 || void 0 === h) && (h = 0);
                }
                if (
                    ((t =
                        a.indexOf(i) >= 0
                            ? a.indexOf(i)
                            : Math.floor(h / r.slidesPerGroup)) >= a.length &&
                        (t = a.length - 1),
                    h !== n)
                ) {
                    var c = parseInt(
                        this.slides.eq(h).attr("data-swiper-slide-index") || h,
                        10
                    );
                    l.extend(this, {
                        snapIndex: t,
                        realIndex: c,
                        previousIndex: n,
                        activeIndex: h,
                    }),
                        this.emit("activeIndexChange"),
                        this.emit("snapIndexChange"),
                        o !== c && this.emit("realIndexChange"),
                        this.emit("slideChange");
                } else
                    t !== d &&
                        ((this.snapIndex = t), this.emit("snapIndexChange"));
            },
            updateClickedSlide: function (e) {
                var i = this.params,
                    s = t(e.target).closest("." + i.slideClass)[0],
                    a = !1;
                if (s)
                    for (var r = 0; r < this.slides.length; r += 1)
                        this.slides[r] === s && (a = !0);
                if (!s || !a)
                    return (
                        (this.clickedSlide = void 0),
                        void (this.clickedIndex = void 0)
                    );
                (this.clickedSlide = s),
                    this.virtual && this.params.virtual.enabled
                        ? (this.clickedIndex = parseInt(
                              t(s).attr("data-swiper-slide-index"),
                              10
                          ))
                        : (this.clickedIndex = t(s).index()),
                    i.slideToClickedSlide &&
                        void 0 !== this.clickedIndex &&
                        this.clickedIndex !== this.activeIndex &&
                        this.slideToClickedSlide();
            },
        },
        f = {
            getTranslate: function (e) {
                void 0 === e && (e = this.isHorizontal() ? "x" : "y");
                var t = this.params,
                    i = this.rtl,
                    s = this.translate,
                    a = this.$wrapperEl;
                if (t.virtualTranslate) return i ? -s : s;
                var r = l.getTranslate(a[0], e);
                return i && (r = -r), r || 0;
            },
            setTranslate: function (e, t) {
                var i = this.rtl,
                    s = this.params,
                    a = this.$wrapperEl,
                    r = this.progress,
                    n = 0,
                    o = 0;
                this.isHorizontal() ? (n = i ? -e : e) : (o = e),
                    s.roundLengths &&
                        ((n = Math.floor(n)), (o = Math.floor(o))),
                    s.virtualTranslate ||
                        (h.transforms3d
                            ? a.transform(
                                  "translate3d(" + n + "px, " + o + "px, 0px)"
                              )
                            : a.transform(
                                  "translate(" + n + "px, " + o + "px)"
                              )),
                    (this.translate = this.isHorizontal() ? n : o);
                var l = this.maxTranslate() - this.minTranslate();
                (0 === l ? 0 : (e - this.minTranslate()) / l) !== r &&
                    this.updateProgress(e),
                    this.emit("setTranslate", this.translate, t);
            },
            minTranslate: function () {
                return -this.snapGrid[0];
            },
            maxTranslate: function () {
                return -this.snapGrid[this.snapGrid.length - 1];
            },
        },
        v = {
            setTransition: function (e, t) {
                this.$wrapperEl.transition(e), this.emit("setTransition", e, t);
            },
            transitionStart: function (e) {
                void 0 === e && (e = !0);
                var t = this.activeIndex,
                    i = this.params,
                    s = this.previousIndex;
                i.autoHeight && this.updateAutoHeight(),
                    this.emit("transitionStart"),
                    e &&
                        t !== s &&
                        (this.emit("slideChangeTransitionStart"),
                        t > s
                            ? this.emit("slideNextTransitionStart")
                            : this.emit("slidePrevTransitionStart"));
            },
            transitionEnd: function (e) {
                void 0 === e && (e = !0);
                var t = this.activeIndex,
                    i = this.previousIndex;
                (this.animating = !1),
                    this.setTransition(0),
                    this.emit("transitionEnd"),
                    e &&
                        t !== i &&
                        (this.emit("slideChangeTransitionEnd"),
                        t > i
                            ? this.emit("slideNextTransitionEnd")
                            : this.emit("slidePrevTransitionEnd"));
            },
        },
        m = {
            slideTo: function (e, t, i, s) {
                void 0 === e && (e = 0),
                    void 0 === t && (t = this.params.speed),
                    void 0 === i && (i = !0);
                var a = this,
                    r = e;
                r < 0 && (r = 0);
                var n = a.params,
                    o = a.snapGrid,
                    l = a.slidesGrid,
                    d = a.previousIndex,
                    p = a.activeIndex,
                    c = a.rtl,
                    u = a.$wrapperEl,
                    f = Math.floor(r / n.slidesPerGroup);
                f >= o.length && (f = o.length - 1),
                    (p || n.initialSlide || 0) === (d || 0) &&
                        i &&
                        a.emit("beforeSlideChangeStart");
                var v = -o[f];
                if ((a.updateProgress(v), n.normalizeSlideIndex))
                    for (var m = 0; m < l.length; m += 1)
                        -Math.floor(100 * v) >= Math.floor(100 * l[m]) &&
                            (r = m);
                if (a.initialized) {
                    if (
                        !a.allowSlideNext &&
                        v < a.translate &&
                        v < a.minTranslate()
                    )
                        return !1;
                    if (
                        !a.allowSlidePrev &&
                        v > a.translate &&
                        v > a.maxTranslate() &&
                        (p || 0) !== r
                    )
                        return !1;
                }
                return (c && -v === a.translate) || (!c && v === a.translate)
                    ? (a.updateActiveIndex(r),
                      n.autoHeight && a.updateAutoHeight(),
                      a.updateSlidesClasses(),
                      "slide" !== n.effect && a.setTranslate(v),
                      !1)
                    : (0 !== t && h.transition
                          ? (a.setTransition(t),
                            a.setTranslate(v),
                            a.updateActiveIndex(r),
                            a.updateSlidesClasses(),
                            a.emit("beforeTransitionStart", t, s),
                            a.transitionStart(i),
                            a.animating ||
                                ((a.animating = !0),
                                u.transitionEnd(function () {
                                    a && !a.destroyed && a.transitionEnd(i);
                                })))
                          : (a.setTransition(0),
                            a.setTranslate(v),
                            a.updateActiveIndex(r),
                            a.updateSlidesClasses(),
                            a.emit("beforeTransitionStart", t, s),
                            a.transitionStart(i),
                            a.transitionEnd(i)),
                      !0);
            },
            slideNext: function (e, t, i) {
                void 0 === e && (e = this.params.speed),
                    void 0 === t && (t = !0);
                var s = this.params,
                    a = this.animating;
                return s.loop
                    ? !a &&
                          (this.loopFix(),
                          (this._clientLeft = this.$wrapperEl[0].clientLeft),
                          this.slideTo(
                              this.activeIndex + s.slidesPerGroup,
                              e,
                              t,
                              i
                          ))
                    : this.slideTo(
                          this.activeIndex + s.slidesPerGroup,
                          e,
                          t,
                          i
                      );
            },
            slidePrev: function (e, t, i) {
                void 0 === e && (e = this.params.speed),
                    void 0 === t && (t = !0);
                var s = this.params,
                    a = this.animating;
                return s.loop
                    ? !a &&
                          (this.loopFix(),
                          (this._clientLeft = this.$wrapperEl[0].clientLeft),
                          this.slideTo(this.activeIndex - 1, e, t, i))
                    : this.slideTo(this.activeIndex - 1, e, t, i);
            },
            slideReset: function (e, t, i) {
                void 0 === e && (e = this.params.speed),
                    void 0 === t && (t = !0);
                return this.slideTo(this.activeIndex, e, t, i);
            },
            slideToClickedSlide: function () {
                var e,
                    i = this,
                    s = i.params,
                    a = i.$wrapperEl,
                    r =
                        "auto" === s.slidesPerView
                            ? i.slidesPerViewDynamic()
                            : s.slidesPerView,
                    n = i.clickedIndex;
                if (s.loop) {
                    if (i.animating) return;
                    (e = parseInt(
                        t(i.clickedSlide).attr("data-swiper-slide-index"),
                        10
                    )),
                        s.centeredSlides
                            ? n < i.loopedSlides - r / 2 ||
                              n > i.slides.length - i.loopedSlides + r / 2
                                ? (i.loopFix(),
                                  (n = a
                                      .children(
                                          "." +
                                              s.slideClass +
                                              '[data-swiper-slide-index="' +
                                              e +
                                              '"]:not(.' +
                                              s.slideDuplicateClass +
                                              ")"
                                      )
                                      .eq(0)
                                      .index()),
                                  l.nextTick(function () {
                                      i.slideTo(n);
                                  }))
                                : i.slideTo(n)
                            : n > i.slides.length - r
                            ? (i.loopFix(),
                              (n = a
                                  .children(
                                      "." +
                                          s.slideClass +
                                          '[data-swiper-slide-index="' +
                                          e +
                                          '"]:not(.' +
                                          s.slideDuplicateClass +
                                          ")"
                                  )
                                  .eq(0)
                                  .index()),
                              l.nextTick(function () {
                                  i.slideTo(n);
                              }))
                            : i.slideTo(n);
                } else i.slideTo(n);
            },
        },
        g = {
            loopCreate: function () {
                var e = this,
                    i = e.params,
                    s = e.$wrapperEl;
                s.children(
                    "." + i.slideClass + "." + i.slideDuplicateClass
                ).remove();
                var a = s.children("." + i.slideClass);
                if (i.loopFillGroupWithBlank) {
                    var r = i.slidesPerGroup - (a.length % i.slidesPerGroup);
                    if (r !== i.slidesPerGroup) {
                        for (var n = 0; n < r; n += 1) {
                            var o = t(d.createElement("div")).addClass(
                                i.slideClass + " " + i.slideBlankClass
                            );
                            s.append(o);
                        }
                        a = s.children("." + i.slideClass);
                    }
                }
                "auto" !== i.slidesPerView ||
                    i.loopedSlides ||
                    (i.loopedSlides = a.length),
                    (e.loopedSlides = parseInt(
                        i.loopedSlides || i.slidesPerView,
                        10
                    )),
                    (e.loopedSlides += i.loopAdditionalSlides),
                    e.loopedSlides > a.length && (e.loopedSlides = a.length);
                var l = [],
                    h = [];
                a.each(function (i, s) {
                    var r = t(s);
                    i < e.loopedSlides && h.push(s),
                        i < a.length &&
                            i >= a.length - e.loopedSlides &&
                            l.push(s),
                        r.attr("data-swiper-slide-index", i);
                });
                for (var p = 0; p < h.length; p += 1)
                    s.append(
                        t(h[p].cloneNode(!0)).addClass(i.slideDuplicateClass)
                    );
                for (var c = l.length - 1; c >= 0; c -= 1)
                    s.prepend(
                        t(l[c].cloneNode(!0)).addClass(i.slideDuplicateClass)
                    );
            },
            loopFix: function () {
                var e,
                    t = this.params,
                    i = this.activeIndex,
                    s = this.slides,
                    a = this.loopedSlides,
                    r = this.allowSlidePrev,
                    n = this.allowSlideNext;
                (this.allowSlidePrev = !0),
                    (this.allowSlideNext = !0),
                    i < a
                        ? ((e = s.length - 3 * a + i),
                          (e += a),
                          this.slideTo(e, 0, !1, !0))
                        : (("auto" === t.slidesPerView && i >= 2 * a) ||
                              i > s.length - 2 * t.slidesPerView) &&
                          ((e = -s.length + i + a),
                          (e += a),
                          this.slideTo(e, 0, !1, !0)),
                    (this.allowSlidePrev = r),
                    (this.allowSlideNext = n);
            },
            loopDestroy: function () {
                var e = this.$wrapperEl,
                    t = this.params,
                    i = this.slides;
                e
                    .children("." + t.slideClass + "." + t.slideDuplicateClass)
                    .remove(),
                    i.removeAttr("data-swiper-slide-index");
            },
        },
        b = {
            setGrabCursor: function (e) {
                if (!h.touch && this.params.simulateTouch) {
                    var t = this.el;
                    (t.style.cursor = "move"),
                        (t.style.cursor = e
                            ? "-webkit-grabbing"
                            : "-webkit-grab"),
                        (t.style.cursor = e ? "-moz-grabbin" : "-moz-grab"),
                        (t.style.cursor = e ? "grabbing" : "grab");
                }
            },
            unsetGrabCursor: function () {
                h.touch || (this.el.style.cursor = "");
            },
        },
        w = {
            appendSlide: function (e) {
                var t = this.$wrapperEl,
                    i = this.params;
                if (
                    (i.loop && this.loopDestroy(),
                    "object" == typeof e && "length" in e)
                )
                    for (var s = 0; s < e.length; s += 1)
                        e[s] && t.append(e[s]);
                else t.append(e);
                i.loop && this.loopCreate(),
                    (i.observer && h.observer) || this.update();
            },
            prependSlide: function (e) {
                var t = this.params,
                    i = this.$wrapperEl,
                    s = this.activeIndex;
                t.loop && this.loopDestroy();
                var a = s + 1;
                if ("object" == typeof e && "length" in e) {
                    for (var r = 0; r < e.length; r += 1)
                        e[r] && i.prepend(e[r]);
                    a = s + e.length;
                } else i.prepend(e);
                t.loop && this.loopCreate(),
                    (t.observer && h.observer) || this.update(),
                    this.slideTo(a, 0, !1);
            },
            removeSlide: function (e) {
                var t = this.params,
                    i = this.$wrapperEl,
                    s = this.activeIndex;
                t.loop &&
                    (this.loopDestroy(),
                    (this.slides = i.children("." + t.slideClass)));
                var a,
                    r = s;
                if ("object" == typeof e && "length" in e) {
                    for (var n = 0; n < e.length; n += 1)
                        (a = e[n]),
                            this.slides[a] && this.slides.eq(a).remove(),
                            a < r && (r -= 1);
                    r = Math.max(r, 0);
                } else
                    (a = e),
                        this.slides[a] && this.slides.eq(a).remove(),
                        a < r && (r -= 1),
                        (r = Math.max(r, 0));
                t.loop && this.loopCreate(),
                    (t.observer && h.observer) || this.update(),
                    t.loop
                        ? this.slideTo(r + this.loopedSlides, 0, !1)
                        : this.slideTo(r, 0, !1);
            },
            removeAllSlides: function () {
                for (var e = [], t = 0; t < this.slides.length; t += 1)
                    e.push(t);
                this.removeSlide(e);
            },
        },
        y = (function () {
            var e = o.navigator.userAgent,
                t = {
                    ios: !1,
                    android: !1,
                    androidChrome: !1,
                    desktop: !1,
                    windows: !1,
                    iphone: !1,
                    ipod: !1,
                    ipad: !1,
                    cordova: o.cordova || o.phonegap,
                    phonegap: o.cordova || o.phonegap,
                },
                i = e.match(/(Windows Phone);?[\s\/]+([\d.]+)?/),
                s = e.match(/(Android);?[\s\/]+([\d.]+)?/),
                a = e.match(/(iPad).*OS\s([\d_]+)/),
                r = e.match(/(iPod)(.*OS\s([\d_]+))?/),
                n = !a && e.match(/(iPhone\sOS|iOS)\s([\d_]+)/);
            if (
                (i &&
                    ((t.os = "windows"),
                    (t.osVersion = i[2]),
                    (t.windows = !0)),
                s &&
                    !i &&
                    ((t.os = "android"),
                    (t.osVersion = s[2]),
                    (t.android = !0),
                    (t.androidChrome = e.toLowerCase().indexOf("chrome") >= 0)),
                (a || n || r) && ((t.os = "ios"), (t.ios = !0)),
                n &&
                    !r &&
                    ((t.osVersion = n[2].replace(/_/g, ".")), (t.iphone = !0)),
                a && ((t.osVersion = a[2].replace(/_/g, ".")), (t.ipad = !0)),
                r &&
                    ((t.osVersion = r[3] ? r[3].replace(/_/g, ".") : null),
                    (t.iphone = !0)),
                t.ios &&
                    t.osVersion &&
                    e.indexOf("Version/") >= 0 &&
                    "10" === t.osVersion.split(".")[0] &&
                    (t.osVersion = e
                        .toLowerCase()
                        .split("version/")[1]
                        .split(" ")[0]),
                (t.desktop = !(t.os || t.android || t.webView)),
                (t.webView =
                    (n || a || r) && e.match(/.*AppleWebKit(?!.*Safari)/i)),
                t.os && "ios" === t.os)
            ) {
                var l = t.osVersion.split("."),
                    h = d.querySelector('meta[name="viewport"]');
                t.minimalUi =
                    !t.webView &&
                    (r || n) &&
                    (1 * l[0] == 7 ? 1 * l[1] >= 1 : 1 * l[0] > 7) &&
                    h &&
                    h.getAttribute("content").indexOf("minimal-ui") >= 0;
            }
            return (t.pixelRatio = o.devicePixelRatio || 1), t;
        })(),
        x = function (e) {
            var i = this.touchEventsData,
                s = this.params,
                a = this.touches,
                r = e;
            if (
                (r.originalEvent && (r = r.originalEvent),
                (i.isTouchEvent = "touchstart" === r.type),
                (i.isTouchEvent || !("which" in r) || 3 !== r.which) &&
                    (!i.isTouched || !i.isMoved))
            )
                if (
                    s.noSwiping &&
                    t(r.target).closest("." + s.noSwipingClass)[0]
                )
                    this.allowClick = !0;
                else if (!s.swipeHandler || t(r).closest(s.swipeHandler)[0]) {
                    (a.currentX =
                        "touchstart" === r.type
                            ? r.targetTouches[0].pageX
                            : r.pageX),
                        (a.currentY =
                            "touchstart" === r.type
                                ? r.targetTouches[0].pageY
                                : r.pageY);
                    var n = a.currentX,
                        o = a.currentY;
                    if (
                        !(
                            y.ios &&
                            !y.cordova &&
                            s.iOSEdgeSwipeDetection &&
                            n <= s.iOSEdgeSwipeThreshold &&
                            n >= window.screen.width - s.iOSEdgeSwipeThreshold
                        )
                    ) {
                        if (
                            (l.extend(i, {
                                isTouched: !0,
                                isMoved: !1,
                                allowTouchCallbacks: !0,
                                isScrolling: void 0,
                                startMoving: void 0,
                            }),
                            (a.startX = n),
                            (a.startY = o),
                            (i.touchStartTime = l.now()),
                            (this.allowClick = !0),
                            this.updateSize(),
                            (this.swipeDirection = void 0),
                            s.threshold > 0 && (i.allowThresholdMove = !1),
                            "touchstart" !== r.type)
                        ) {
                            var h = !0;
                            t(r.target).is(i.formElements) && (h = !1),
                                d.activeElement &&
                                    t(d.activeElement).is(i.formElements) &&
                                    d.activeElement.blur(),
                                h && this.allowTouchMove && r.preventDefault();
                        }
                        this.emit("touchStart", r);
                    }
                }
        },
        T = function (e) {
            var i = this.touchEventsData,
                s = this.params,
                a = this.touches,
                r = this.rtl,
                n = e;
            if (
                (n.originalEvent && (n = n.originalEvent),
                !i.isTouchEvent || "mousemove" !== n.type)
            ) {
                var o =
                        "touchmove" === n.type
                            ? n.targetTouches[0].pageX
                            : n.pageX,
                    h =
                        "touchmove" === n.type
                            ? n.targetTouches[0].pageY
                            : n.pageY;
                if (n.preventedByNestedSwiper)
                    return (a.startX = o), void (a.startY = h);
                if (!this.allowTouchMove)
                    return (
                        (this.allowClick = !1),
                        void (
                            i.isTouched &&
                            (l.extend(a, {
                                startX: o,
                                startY: h,
                                currentX: o,
                                currentY: h,
                            }),
                            (i.touchStartTime = l.now()))
                        )
                    );
                if (i.isTouchEvent && s.touchReleaseOnEdges && !s.loop)
                    if (this.isVertical()) {
                        if (
                            (h < a.startY &&
                                this.translate <= this.maxTranslate()) ||
                            (h > a.startY &&
                                this.translate >= this.minTranslate())
                        )
                            return (i.isTouched = !1), void (i.isMoved = !1);
                    } else if (
                        (o < a.startX &&
                            this.translate <= this.maxTranslate()) ||
                        (o > a.startX && this.translate >= this.minTranslate())
                    )
                        return;
                if (
                    i.isTouchEvent &&
                    d.activeElement &&
                    n.target === d.activeElement &&
                    t(n.target).is(i.formElements)
                )
                    return (i.isMoved = !0), void (this.allowClick = !1);
                if (
                    (i.allowTouchCallbacks && this.emit("touchMove", n),
                    !(n.targetTouches && n.targetTouches.length > 1))
                ) {
                    (a.currentX = o), (a.currentY = h);
                    var p,
                        c = a.currentX - a.startX,
                        u = a.currentY - a.startY;
                    if (void 0 === i.isScrolling)
                        (this.isHorizontal() && a.currentY === a.startY) ||
                        (this.isVertical() && a.currentX === a.startX)
                            ? (i.isScrolling = !1)
                            : c * c + u * u >= 25 &&
                              ((p =
                                  (180 * Math.atan2(Math.abs(u), Math.abs(c))) /
                                  Math.PI),
                              (i.isScrolling = this.isHorizontal()
                                  ? p > s.touchAngle
                                  : 90 - p > s.touchAngle));
                    if (
                        (i.isScrolling && this.emit("touchMoveOpposite", n),
                        "undefined" == typeof startMoving &&
                            ((a.currentX === a.startX &&
                                a.currentY === a.startY) ||
                                (i.startMoving = !0)),
                        i.isTouched)
                    )
                        if (i.isScrolling) i.isTouched = !1;
                        else if (i.startMoving) {
                            (this.allowClick = !1),
                                n.preventDefault(),
                                s.touchMoveStopPropagation &&
                                    !s.nested &&
                                    n.stopPropagation(),
                                i.isMoved ||
                                    (s.loop && this.loopFix(),
                                    (i.startTranslate = this.getTranslate()),
                                    this.setTransition(0),
                                    this.animating &&
                                        this.$wrapperEl.trigger(
                                            "webkitTransitionEnd transitionend"
                                        ),
                                    (i.allowMomentumBounce = !1),
                                    !s.grabCursor ||
                                        (!0 !== this.allowSlideNext &&
                                            !0 !== this.allowSlidePrev) ||
                                        this.setGrabCursor(!0),
                                    this.emit("sliderFirstMove", n)),
                                this.emit("sliderMove", n),
                                (i.isMoved = !0);
                            var f = this.isHorizontal() ? c : u;
                            (a.diff = f),
                                (f *= s.touchRatio),
                                r && (f = -f),
                                (this.swipeDirection = f > 0 ? "prev" : "next"),
                                (i.currentTranslate = f + i.startTranslate);
                            var v = !0,
                                m = s.resistanceRatio;
                            if (
                                (s.touchReleaseOnEdges && (m = 0),
                                f > 0 &&
                                i.currentTranslate > this.minTranslate()
                                    ? ((v = !1),
                                      s.resistance &&
                                          (i.currentTranslate =
                                              this.minTranslate() -
                                              1 +
                                              Math.pow(
                                                  -this.minTranslate() +
                                                      i.startTranslate +
                                                      f,
                                                  m
                                              )))
                                    : f < 0 &&
                                      i.currentTranslate <
                                          this.maxTranslate() &&
                                      ((v = !1),
                                      s.resistance &&
                                          (i.currentTranslate =
                                              this.maxTranslate() +
                                              1 -
                                              Math.pow(
                                                  this.maxTranslate() -
                                                      i.startTranslate -
                                                      f,
                                                  m
                                              ))),
                                v && (n.preventedByNestedSwiper = !0),
                                !this.allowSlideNext &&
                                    "next" === this.swipeDirection &&
                                    i.currentTranslate < i.startTranslate &&
                                    (i.currentTranslate = i.startTranslate),
                                !this.allowSlidePrev &&
                                    "prev" === this.swipeDirection &&
                                    i.currentTranslate > i.startTranslate &&
                                    (i.currentTranslate = i.startTranslate),
                                s.threshold > 0)
                            ) {
                                if (
                                    !(
                                        Math.abs(f) > s.threshold ||
                                        i.allowThresholdMove
                                    )
                                )
                                    return void (i.currentTranslate =
                                        i.startTranslate);
                                if (!i.allowThresholdMove)
                                    return (
                                        (i.allowThresholdMove = !0),
                                        (a.startX = a.currentX),
                                        (a.startY = a.currentY),
                                        (i.currentTranslate = i.startTranslate),
                                        void (a.diff = this.isHorizontal()
                                            ? a.currentX - a.startX
                                            : a.currentY - a.startY)
                                    );
                            }
                            s.followFinger &&
                                ((s.freeMode ||
                                    s.watchSlidesProgress ||
                                    s.watchSlidesVisibility) &&
                                    (this.updateActiveIndex(),
                                    this.updateSlidesClasses()),
                                s.freeMode &&
                                    (0 === i.velocities.length &&
                                        i.velocities.push({
                                            position:
                                                a[
                                                    this.isHorizontal()
                                                        ? "startX"
                                                        : "startY"
                                                ],
                                            time: i.touchStartTime,
                                        }),
                                    i.velocities.push({
                                        position:
                                            a[
                                                this.isHorizontal()
                                                    ? "currentX"
                                                    : "currentY"
                                            ],
                                        time: l.now(),
                                    })),
                                this.updateProgress(i.currentTranslate),
                                this.setTranslate(i.currentTranslate));
                        }
                }
            }
        },
        E = function (e) {
            var t = this,
                i = t.touchEventsData,
                s = t.params,
                a = t.touches,
                r = t.rtl,
                n = t.$wrapperEl,
                o = t.slidesGrid,
                d = t.snapGrid,
                h = e;
            if (
                (h.originalEvent && (h = h.originalEvent),
                i.allowTouchCallbacks && t.emit("touchEnd", h),
                (i.allowTouchCallbacks = !1),
                i.isTouched)
            ) {
                s.grabCursor &&
                    i.isMoved &&
                    i.isTouched &&
                    (!0 === t.allowSlideNext || !0 === t.allowSlidePrev) &&
                    t.setGrabCursor(!1);
                var p,
                    c = l.now(),
                    u = c - i.touchStartTime;
                if (
                    (t.allowClick &&
                        (t.updateClickedSlide(h),
                        t.emit("tap", h),
                        u < 300 &&
                            c - i.lastClickTime > 300 &&
                            (i.clickTimeout && clearTimeout(i.clickTimeout),
                            (i.clickTimeout = l.nextTick(function () {
                                t && !t.destroyed && t.emit("click", h);
                            }, 300))),
                        u < 300 &&
                            c - i.lastClickTime < 300 &&
                            (i.clickTimeout && clearTimeout(i.clickTimeout),
                            t.emit("doubleTap", h))),
                    (i.lastClickTime = l.now()),
                    l.nextTick(function () {
                        t.destroyed || (t.allowClick = !0);
                    }),
                    !i.isTouched ||
                        !i.isMoved ||
                        !t.swipeDirection ||
                        0 === a.diff ||
                        i.currentTranslate === i.startTranslate)
                )
                    return (i.isTouched = !1), void (i.isMoved = !1);
                if (
                    ((i.isTouched = !1),
                    (i.isMoved = !1),
                    (p = s.followFinger
                        ? r
                            ? t.translate
                            : -t.translate
                        : -i.currentTranslate),
                    s.freeMode)
                ) {
                    if (p < -t.minTranslate())
                        return void t.slideTo(t.activeIndex);
                    if (p > -t.maxTranslate())
                        return void (t.slides.length < d.length
                            ? t.slideTo(d.length - 1)
                            : t.slideTo(t.slides.length - 1));
                    if (s.freeModeMomentum) {
                        if (i.velocities.length > 1) {
                            var f = i.velocities.pop(),
                                v = i.velocities.pop(),
                                m = f.position - v.position,
                                g = f.time - v.time;
                            (t.velocity = m / g),
                                (t.velocity /= 2),
                                Math.abs(t.velocity) <
                                    s.freeModeMinimumVelocity &&
                                    (t.velocity = 0),
                                (g > 150 || l.now() - f.time > 300) &&
                                    (t.velocity = 0);
                        } else t.velocity = 0;
                        (t.velocity *= s.freeModeMomentumVelocityRatio),
                            (i.velocities.length = 0);
                        var b = 1e3 * s.freeModeMomentumRatio,
                            w = t.velocity * b,
                            y = t.translate + w;
                        r && (y = -y);
                        var x,
                            T = !1,
                            E =
                                20 *
                                Math.abs(t.velocity) *
                                s.freeModeMomentumBounceRatio;
                        if (y < t.maxTranslate())
                            s.freeModeMomentumBounce
                                ? (y + t.maxTranslate() < -E &&
                                      (y = t.maxTranslate() - E),
                                  (x = t.maxTranslate()),
                                  (T = !0),
                                  (i.allowMomentumBounce = !0))
                                : (y = t.maxTranslate());
                        else if (y > t.minTranslate())
                            s.freeModeMomentumBounce
                                ? (y - t.minTranslate() > E &&
                                      (y = t.minTranslate() + E),
                                  (x = t.minTranslate()),
                                  (T = !0),
                                  (i.allowMomentumBounce = !0))
                                : (y = t.minTranslate());
                        else if (s.freeModeSticky) {
                            for (var S, C = 0; C < d.length; C += 1)
                                if (d[C] > -y) {
                                    S = C;
                                    break;
                                }
                            y = -(y =
                                Math.abs(d[S] - y) < Math.abs(d[S - 1] - y) ||
                                "next" === t.swipeDirection
                                    ? d[S]
                                    : d[S - 1]);
                        }
                        if (0 !== t.velocity)
                            b = r
                                ? Math.abs((-y - t.translate) / t.velocity)
                                : Math.abs((y - t.translate) / t.velocity);
                        else if (s.freeModeSticky) return void t.slideReset();
                        s.freeModeMomentumBounce && T
                            ? (t.updateProgress(x),
                              t.setTransition(b),
                              t.setTranslate(y),
                              t.transitionStart(),
                              (t.animating = !0),
                              n.transitionEnd(function () {
                                  t &&
                                      !t.destroyed &&
                                      i.allowMomentumBounce &&
                                      (t.emit("momentumBounce"),
                                      t.setTransition(s.speed),
                                      t.setTranslate(x),
                                      n.transitionEnd(function () {
                                          t &&
                                              !t.destroyed &&
                                              t.transitionEnd();
                                      }));
                              }))
                            : t.velocity
                            ? (t.updateProgress(y),
                              t.setTransition(b),
                              t.setTranslate(y),
                              t.transitionStart(),
                              t.animating ||
                                  ((t.animating = !0),
                                  n.transitionEnd(function () {
                                      t && !t.destroyed && t.transitionEnd();
                                  })))
                            : t.updateProgress(y),
                            t.updateActiveIndex(),
                            t.updateSlidesClasses();
                    }
                    (!s.freeModeMomentum || u >= s.longSwipesMs) &&
                        (t.updateProgress(),
                        t.updateActiveIndex(),
                        t.updateSlidesClasses());
                } else {
                    for (
                        var M = 0, z = t.slidesSizesGrid[0], P = 0;
                        P < o.length;
                        P += s.slidesPerGroup
                    )
                        void 0 !== o[P + s.slidesPerGroup]
                            ? p >= o[P] &&
                              p < o[P + s.slidesPerGroup] &&
                              ((M = P), (z = o[P + s.slidesPerGroup] - o[P]))
                            : p >= o[P] &&
                              ((M = P),
                              (z = o[o.length - 1] - o[o.length - 2]));
                    var k = (p - o[M]) / z;
                    if (u > s.longSwipesMs) {
                        if (!s.longSwipes) return void t.slideTo(t.activeIndex);
                        "next" === t.swipeDirection &&
                            (k >= s.longSwipesRatio
                                ? t.slideTo(M + s.slidesPerGroup)
                                : t.slideTo(M)),
                            "prev" === t.swipeDirection &&
                                (k > 1 - s.longSwipesRatio
                                    ? t.slideTo(M + s.slidesPerGroup)
                                    : t.slideTo(M));
                    } else {
                        if (!s.shortSwipes)
                            return void t.slideTo(t.activeIndex);
                        "next" === t.swipeDirection &&
                            t.slideTo(M + s.slidesPerGroup),
                            "prev" === t.swipeDirection && t.slideTo(M);
                    }
                }
            }
        },
        S = function () {
            var e = this.params,
                t = this.el;
            if (!t || 0 !== t.offsetWidth) {
                e.breakpoints && this.setBreakpoint();
                var i = this.allowSlideNext,
                    s = this.allowSlidePrev;
                if (
                    ((this.allowSlideNext = !0),
                    (this.allowSlidePrev = !0),
                    this.updateSize(),
                    this.updateSlides(),
                    e.freeMode)
                ) {
                    var a = Math.min(
                        Math.max(this.translate, this.maxTranslate()),
                        this.minTranslate()
                    );
                    this.setTranslate(a),
                        this.updateActiveIndex(),
                        this.updateSlidesClasses(),
                        e.autoHeight && this.updateAutoHeight();
                } else
                    this.updateSlidesClasses(),
                        ("auto" === e.slidesPerView || e.slidesPerView > 1) &&
                        this.isEnd &&
                        !this.params.centeredSlides
                            ? this.slideTo(this.slides.length - 1, 0, !1, !0)
                            : this.slideTo(this.activeIndex, 0, !1, !0);
                (this.allowSlidePrev = s), (this.allowSlideNext = i);
            }
        },
        C = function (e) {
            this.allowClick ||
                (this.params.preventClicks && e.preventDefault(),
                this.params.preventClicksPropagation &&
                    this.animating &&
                    (e.stopPropagation(), e.stopImmediatePropagation()));
        };
    var M = {
            init: !0,
            direction: "horizontal",
            touchEventsTarget: "container",
            initialSlide: 0,
            speed: 300,
            iOSEdgeSwipeDetection: !1,
            iOSEdgeSwipeThreshold: 20,
            freeMode: !1,
            freeModeMomentum: !0,
            freeModeMomentumRatio: 1,
            freeModeMomentumBounce: !0,
            freeModeMomentumBounceRatio: 1,
            freeModeMomentumVelocityRatio: 1,
            freeModeSticky: !1,
            freeModeMinimumVelocity: 0.02,
            autoHeight: !1,
            setWrapperSize: !1,
            virtualTranslate: !1,
            effect: "slide",
            breakpoints: void 0,
            spaceBetween: 0,
            slidesPerView: 1,
            slidesPerColumn: 1,
            slidesPerColumnFill: "column",
            slidesPerGroup: 1,
            centeredSlides: !1,
            slidesOffsetBefore: 0,
            slidesOffsetAfter: 0,
            normalizeSlideIndex: !0,
            watchOverflow: !1,
            roundLengths: !1,
            touchRatio: 1,
            touchAngle: 45,
            simulateTouch: !0,
            shortSwipes: !0,
            longSwipes: !0,
            longSwipesRatio: 0.5,
            longSwipesMs: 300,
            followFinger: !0,
            allowTouchMove: !0,
            threshold: 0,
            touchMoveStopPropagation: !0,
            touchReleaseOnEdges: !1,
            uniqueNavElements: !0,
            resistance: !0,
            resistanceRatio: 0.85,
            watchSlidesProgress: !1,
            watchSlidesVisibility: !1,
            grabCursor: !1,
            preventClicks: !0,
            preventClicksPropagation: !0,
            slideToClickedSlide: !1,
            preloadImages: !0,
            updateOnImagesReady: !0,
            loop: !1,
            loopAdditionalSlides: 0,
            loopedSlides: null,
            loopFillGroupWithBlank: !1,
            allowSlidePrev: !0,
            allowSlideNext: !0,
            swipeHandler: null,
            noSwiping: !0,
            noSwipingClass: "swiper-no-swiping",
            passiveListeners: !0,
            containerModifierClass: "swiper-container-",
            slideClass: "swiper-slide",
            slideBlankClass: "swiper-slide-invisible-blank",
            slideActiveClass: "swiper-slide-active",
            slideDuplicateActiveClass: "swiper-slide-duplicate-active",
            slideVisibleClass: "swiper-slide-visible",
            slideDuplicateClass: "swiper-slide-duplicate",
            slideNextClass: "swiper-slide-next",
            slideDuplicateNextClass: "swiper-slide-duplicate-next",
            slidePrevClass: "swiper-slide-prev",
            slideDuplicatePrevClass: "swiper-slide-duplicate-prev",
            wrapperClass: "swiper-wrapper",
            runCallbacksOnInit: !0,
        },
        z = {
            update: u,
            translate: f,
            transition: v,
            slide: m,
            loop: g,
            grabCursor: b,
            manipulation: w,
            events: {
                attachEvents: function () {
                    var e = this.params,
                        t = this.touchEvents,
                        i = this.el,
                        s = this.wrapperEl;
                    (this.onTouchStart = x.bind(this)),
                        (this.onTouchMove = T.bind(this)),
                        (this.onTouchEnd = E.bind(this)),
                        (this.onClick = C.bind(this));
                    var a = "container" === e.touchEventsTarget ? i : s,
                        r = !!e.nested;
                    if (h.pointerEvents || h.prefixedPointerEvents)
                        a.addEventListener(t.start, this.onTouchStart, !1),
                            (h.touch ? a : d).addEventListener(
                                t.move,
                                this.onTouchMove,
                                r
                            ),
                            (h.touch ? a : d).addEventListener(
                                t.end,
                                this.onTouchEnd,
                                !1
                            );
                    else {
                        if (h.touch) {
                            var n = !(
                                "touchstart" !== t.start ||
                                !h.passiveListener ||
                                !e.passiveListeners
                            ) && { passive: !0, capture: !1 };
                            a.addEventListener(t.start, this.onTouchStart, n),
                                a.addEventListener(
                                    t.move,
                                    this.onTouchMove,
                                    h.passiveListener
                                        ? { passive: !1, capture: r }
                                        : r
                                ),
                                a.addEventListener(t.end, this.onTouchEnd, n);
                        }
                        ((e.simulateTouch && !y.ios && !y.android) ||
                            (e.simulateTouch && !h.touch && y.ios)) &&
                            (a.addEventListener(
                                "mousedown",
                                this.onTouchStart,
                                !1
                            ),
                            d.addEventListener(
                                "mousemove",
                                this.onTouchMove,
                                r
                            ),
                            d.addEventListener("mouseup", this.onTouchEnd, !1));
                    }
                    (e.preventClicks || e.preventClicksPropagation) &&
                        a.addEventListener("click", this.onClick, !0),
                        this.on("resize observerUpdate", S);
                },
                detachEvents: function () {
                    var e = this.params,
                        t = this.touchEvents,
                        i = this.el,
                        s = this.wrapperEl,
                        a = "container" === e.touchEventsTarget ? i : s,
                        r = !!e.nested;
                    if (h.pointerEvents || h.prefixedPointerEvents)
                        a.removeEventListener(t.start, this.onTouchStart, !1),
                            (h.touch ? a : d).removeEventListener(
                                t.move,
                                this.onTouchMove,
                                r
                            ),
                            (h.touch ? a : d).removeEventListener(
                                t.end,
                                this.onTouchEnd,
                                !1
                            );
                    else {
                        if (h.touch) {
                            var n = !(
                                "onTouchStart" !== t.start ||
                                !h.passiveListener ||
                                !e.passiveListeners
                            ) && { passive: !0, capture: !1 };
                            a.removeEventListener(
                                t.start,
                                this.onTouchStart,
                                n
                            ),
                                a.removeEventListener(
                                    t.move,
                                    this.onTouchMove,
                                    r
                                ),
                                a.removeEventListener(
                                    t.end,
                                    this.onTouchEnd,
                                    n
                                );
                        }
                        ((e.simulateTouch && !y.ios && !y.android) ||
                            (e.simulateTouch && !h.touch && y.ios)) &&
                            (a.removeEventListener(
                                "mousedown",
                                this.onTouchStart,
                                !1
                            ),
                            d.removeEventListener(
                                "mousemove",
                                this.onTouchMove,
                                r
                            ),
                            d.removeEventListener(
                                "mouseup",
                                this.onTouchEnd,
                                !1
                            ));
                    }
                    (e.preventClicks || e.preventClicksPropagation) &&
                        a.removeEventListener("click", this.onClick, !0),
                        this.off("resize observerUpdate", S);
                },
            },
            breakpoints: {
                setBreakpoint: function () {
                    var e = this.activeIndex,
                        t = this.loopedSlides;
                    void 0 === t && (t = 0);
                    var i = this.params,
                        s = i.breakpoints;
                    if (s && (!s || 0 !== Object.keys(s).length)) {
                        var a = this.getBreakpoint(s);
                        if (a && this.currentBreakpoint !== a) {
                            var r = a in s ? s[a] : this.originalParams,
                                n =
                                    i.loop &&
                                    r.slidesPerView !== i.slidesPerView;
                            l.extend(this.params, r),
                                l.extend(this, {
                                    allowTouchMove: this.params.allowTouchMove,
                                    allowSlideNext: this.params.allowSlideNext,
                                    allowSlidePrev: this.params.allowSlidePrev,
                                }),
                                (this.currentBreakpoint = a),
                                n &&
                                    (this.loopDestroy(),
                                    this.loopCreate(),
                                    this.updateSlides(),
                                    this.slideTo(
                                        e - t + this.loopedSlides,
                                        0,
                                        !1
                                    )),
                                this.emit("breakpoint", r);
                        }
                    }
                },
                getBreakpoint: function (e) {
                    if (e) {
                        var t = !1,
                            i = [];
                        Object.keys(e).forEach(function (e) {
                            i.push(e);
                        }),
                            i.sort(function (e, t) {
                                return parseInt(e, 10) - parseInt(t, 10);
                            });
                        for (var s = 0; s < i.length; s += 1) {
                            var a = i[s];
                            a >= o.innerWidth && !t && (t = a);
                        }
                        return t || "max";
                    }
                },
            },
            checkOverflow: {
                checkOverflow: function () {
                    var e = this.isLocked;
                    (this.isLocked = 1 === this.snapGrid.length),
                        (this.allowTouchMove = !this.isLocked),
                        e &&
                            e !== this.isLocked &&
                            ((this.isEnd = !1), this.navigation.update());
                },
            },
            classes: {
                addClasses: function () {
                    var e = this.classNames,
                        t = this.params,
                        i = this.rtl,
                        s = this.$el,
                        a = [];
                    a.push(t.direction),
                        t.freeMode && a.push("free-mode"),
                        h.flexbox || a.push("no-flexbox"),
                        t.autoHeight && a.push("autoheight"),
                        i && a.push("rtl"),
                        t.slidesPerColumn > 1 && a.push("multirow"),
                        y.android && a.push("android"),
                        y.ios && a.push("ios"),
                        (h.pointerEvents || h.prefixedPointerEvents) &&
                            a.push("wp8-" + t.direction),
                        a.forEach(function (i) {
                            e.push(t.containerModifierClass + i);
                        }),
                        s.addClass(e.join(" "));
                },
                removeClasses: function () {
                    var e = this.$el,
                        t = this.classNames;
                    e.removeClass(t.join(" "));
                },
            },
            images: {
                loadImage: function (e, t, i, s, a, r) {
                    var n;
                    function l() {
                        r && r();
                    }
                    e.complete && a
                        ? l()
                        : t
                        ? (((n = new o.Image()).onload = l),
                          (n.onerror = l),
                          s && (n.sizes = s),
                          i && (n.srcset = i),
                          t && (n.src = t))
                        : l();
                },
                preloadImages: function () {
                    var e = this;
                    function t() {
                        void 0 !== e &&
                            null !== e &&
                            e &&
                            !e.destroyed &&
                            (void 0 !== e.imagesLoaded && (e.imagesLoaded += 1),
                            e.imagesLoaded === e.imagesToLoad.length &&
                                (e.params.updateOnImagesReady && e.update(),
                                e.emit("imagesReady")));
                    }
                    e.imagesToLoad = e.$el.find("img");
                    for (var i = 0; i < e.imagesToLoad.length; i += 1) {
                        var s = e.imagesToLoad[i];
                        e.loadImage(
                            s,
                            s.currentSrc || s.getAttribute("src"),
                            s.srcset || s.getAttribute("srcset"),
                            s.sizes || s.getAttribute("sizes"),
                            !0,
                            t
                        );
                    }
                },
            },
        },
        P = {},
        k = (function (e) {
            function i() {
                for (var s, a, r, n = [], o = arguments.length; o--; )
                    n[o] = arguments[o];
                1 === n.length &&
                n[0].constructor &&
                n[0].constructor === Object
                    ? (a = n[0])
                    : ((s = (r = n)[0]), (a = r[1]));
                a || (a = {}),
                    (a = l.extend({}, a)),
                    s && !a.el && (a.el = s),
                    e.call(this, a),
                    Object.keys(z).forEach(function (e) {
                        Object.keys(z[e]).forEach(function (t) {
                            i.prototype[t] || (i.prototype[t] = z[e][t]);
                        });
                    });
                var d = this;
                void 0 === d.modules && (d.modules = {}),
                    Object.keys(d.modules).forEach(function (e) {
                        var t = d.modules[e];
                        if (t.params) {
                            var i = Object.keys(t.params)[0],
                                s = t.params[i];
                            if ("object" != typeof s) return;
                            if (!(i in a && "enabled" in s)) return;
                            !0 === a[i] && (a[i] = { enabled: !0 }),
                                "object" != typeof a[i] ||
                                    "enabled" in a[i] ||
                                    (a[i].enabled = !0),
                                a[i] || (a[i] = { enabled: !1 });
                        }
                    });
                var p = l.extend({}, M);
                d.useModulesParams(p),
                    (d.params = l.extend({}, p, P, a)),
                    (d.originalParams = l.extend({}, d.params)),
                    (d.passedParams = l.extend({}, a));
                var c = t(d.params.el);
                if ((s = c[0])) {
                    if (c.length > 1) {
                        var u = [];
                        return (
                            c.each(function (e, t) {
                                var s = l.extend({}, a, { el: t });
                                u.push(new i(s));
                            }),
                            u
                        );
                    }
                    (s.swiper = d), c.data("swiper", d);
                    var f,
                        v,
                        m = c.children("." + d.params.wrapperClass);
                    return (
                        l.extend(d, {
                            $el: c,
                            el: s,
                            $wrapperEl: m,
                            wrapperEl: m[0],
                            classNames: [],
                            slides: t(),
                            slidesGrid: [],
                            snapGrid: [],
                            slidesSizesGrid: [],
                            isHorizontal: function () {
                                return "horizontal" === d.params.direction;
                            },
                            isVertical: function () {
                                return "vertical" === d.params.direction;
                            },
                            rtl:
                                "horizontal" === d.params.direction &&
                                ("rtl" === s.dir.toLowerCase() ||
                                    "rtl" === c.css("direction")),
                            wrongRTL: "-webkit-box" === m.css("display"),
                            activeIndex: 0,
                            realIndex: 0,
                            isBeginning: !0,
                            isEnd: !1,
                            translate: 0,
                            progress: 0,
                            velocity: 0,
                            animating: !1,
                            allowSlideNext: d.params.allowSlideNext,
                            allowSlidePrev: d.params.allowSlidePrev,
                            touchEvents:
                                ((f = ["touchstart", "touchmove", "touchend"]),
                                (v = ["mousedown", "mousemove", "mouseup"]),
                                h.pointerEvents
                                    ? (v = [
                                          "pointerdown",
                                          "pointermove",
                                          "pointerup",
                                      ])
                                    : h.prefixedPointerEvents &&
                                      (v = [
                                          "MSPointerDown",
                                          "MSPointerMove",
                                          "MSPointerUp",
                                      ]),
                                {
                                    start:
                                        h.touch || !d.params.simulateTouch
                                            ? f[0]
                                            : v[0],
                                    move:
                                        h.touch || !d.params.simulateTouch
                                            ? f[1]
                                            : v[1],
                                    end:
                                        h.touch || !d.params.simulateTouch
                                            ? f[2]
                                            : v[2],
                                }),
                            touchEventsData: {
                                isTouched: void 0,
                                isMoved: void 0,
                                allowTouchCallbacks: void 0,
                                touchStartTime: void 0,
                                isScrolling: void 0,
                                currentTranslate: void 0,
                                startTranslate: void 0,
                                allowThresholdMove: void 0,
                                formElements:
                                    "input, select, option, textarea, button, video",
                                lastClickTime: l.now(),
                                clickTimeout: void 0,
                                velocities: [],
                                allowMomentumBounce: void 0,
                                isTouchEvent: void 0,
                                startMoving: void 0,
                            },
                            allowClick: !0,
                            allowTouchMove: d.params.allowTouchMove,
                            touches: {
                                startX: 0,
                                startY: 0,
                                currentX: 0,
                                currentY: 0,
                                diff: 0,
                            },
                            imagesToLoad: [],
                            imagesLoaded: 0,
                        }),
                        d.useModules(),
                        d.params.init && d.init(),
                        d
                    );
                }
            }
            e && (i.__proto__ = e),
                (i.prototype = Object.create(e && e.prototype)),
                (i.prototype.constructor = i);
            var s = {
                extendedDefaults: { configurable: !0 },
                defaults: { configurable: !0 },
                Class: { configurable: !0 },
                $: { configurable: !0 },
            };
            return (
                (i.prototype.slidesPerViewDynamic = function () {
                    var e = this.params,
                        t = this.slides,
                        i = this.slidesGrid,
                        s = this.size,
                        a = this.activeIndex,
                        r = 1;
                    if (e.centeredSlides) {
                        for (
                            var n, o = t[a].swiperSlideSize, l = a + 1;
                            l < t.length;
                            l += 1
                        )
                            t[l] &&
                                !n &&
                                ((r += 1),
                                (o += t[l].swiperSlideSize) > s && (n = !0));
                        for (var d = a - 1; d >= 0; d -= 1)
                            t[d] &&
                                !n &&
                                ((r += 1),
                                (o += t[d].swiperSlideSize) > s && (n = !0));
                    } else
                        for (var h = a + 1; h < t.length; h += 1)
                            i[h] - i[a] < s && (r += 1);
                    return r;
                }),
                (i.prototype.update = function () {
                    var e = this;
                    e &&
                        !e.destroyed &&
                        (e.updateSize(),
                        e.updateSlides(),
                        e.updateProgress(),
                        e.updateSlidesClasses(),
                        e.params.freeMode
                            ? (t(), e.params.autoHeight && e.updateAutoHeight())
                            : (("auto" === e.params.slidesPerView ||
                                  e.params.slidesPerView > 1) &&
                              e.isEnd &&
                              !e.params.centeredSlides
                                  ? e.slideTo(e.slides.length - 1, 0, !1, !0)
                                  : e.slideTo(e.activeIndex, 0, !1, !0)) || t(),
                        e.emit("update"));
                    function t() {
                        var t = e.rtl ? -1 * e.translate : e.translate,
                            i = Math.min(
                                Math.max(t, e.maxTranslate()),
                                e.minTranslate()
                            );
                        e.setTranslate(i),
                            e.updateActiveIndex(),
                            e.updateSlidesClasses();
                    }
                }),
                (i.prototype.init = function () {
                    this.initialized ||
                        (this.emit("beforeInit"),
                        this.params.breakpoints && this.setBreakpoint(),
                        this.addClasses(),
                        this.params.loop && this.loopCreate(),
                        this.updateSize(),
                        this.updateSlides(),
                        this.params.watchOverflow && this.checkOverflow(),
                        this.params.grabCursor && this.setGrabCursor(),
                        this.params.preloadImages && this.preloadImages(),
                        this.params.loop
                            ? this.slideTo(
                                  this.params.initialSlide + this.loopedSlides,
                                  0,
                                  this.params.runCallbacksOnInit
                              )
                            : this.slideTo(
                                  this.params.initialSlide,
                                  0,
                                  this.params.runCallbacksOnInit
                              ),
                        this.attachEvents(),
                        (this.initialized = !0),
                        this.emit("init"));
                }),
                (i.prototype.destroy = function (e, t) {
                    void 0 === e && (e = !0), void 0 === t && (t = !0);
                    var i = this,
                        s = i.params,
                        a = i.$el,
                        r = i.$wrapperEl,
                        n = i.slides;
                    i.emit("beforeDestroy"),
                        (i.initialized = !1),
                        i.detachEvents(),
                        s.loop && i.loopDestroy(),
                        t &&
                            (i.removeClasses(),
                            a.removeAttr("style"),
                            r.removeAttr("style"),
                            n &&
                                n.length &&
                                n
                                    .removeClass(
                                        [
                                            s.slideVisibleClass,
                                            s.slideActiveClass,
                                            s.slideNextClass,
                                            s.slidePrevClass,
                                        ].join(" ")
                                    )
                                    .removeAttr("style")
                                    .removeAttr("data-swiper-slide-index")
                                    .removeAttr("data-swiper-column")
                                    .removeAttr("data-swiper-row")),
                        i.emit("destroy"),
                        Object.keys(i.eventsListeners).forEach(function (e) {
                            i.off(e);
                        }),
                        !1 !== e &&
                            ((i.$el[0].swiper = null),
                            i.$el.data("swiper", null),
                            l.deleteProps(i)),
                        (i.destroyed = !0);
                }),
                (i.extendDefaults = function (e) {
                    l.extend(P, e);
                }),
                (s.extendedDefaults.get = function () {
                    return P;
                }),
                (s.defaults.get = function () {
                    return M;
                }),
                (s.Class.get = function () {
                    return e;
                }),
                (s.$.get = function () {
                    return t;
                }),
                Object.defineProperties(i, s),
                i
            );
        })(p),
        $ = { name: "device", proto: { device: y }, static: { device: y } },
        L = { name: "support", proto: { support: h }, static: { support: h } },
        I = (function () {
            return {
                isSafari:
                    ((e = o.navigator.userAgent.toLowerCase()),
                    e.indexOf("safari") >= 0 &&
                        e.indexOf("chrome") < 0 &&
                        e.indexOf("android") < 0),
                isUiWebView:
                    /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(
                        o.navigator.userAgent
                    ),
            };
            var e;
        })(),
        D = { name: "browser", proto: { browser: I }, static: { browser: I } },
        O = {
            name: "resize",
            create: function () {
                var e = this;
                l.extend(e, {
                    resize: {
                        resizeHandler: function () {
                            e &&
                                !e.destroyed &&
                                e.initialized &&
                                (e.emit("beforeResize"), e.emit("resize"));
                        },
                        orientationChangeHandler: function () {
                            e &&
                                !e.destroyed &&
                                e.initialized &&
                                e.emit("orientationchange");
                        },
                    },
                });
            },
            on: {
                init: function () {
                    o.addEventListener("resize", this.resize.resizeHandler),
                        o.addEventListener(
                            "orientationchange",
                            this.resize.orientationChangeHandler
                        );
                },
                destroy: function () {
                    o.removeEventListener("resize", this.resize.resizeHandler),
                        o.removeEventListener(
                            "orientationchange",
                            this.resize.orientationChangeHandler
                        );
                },
            },
        },
        A = {
            func: o.MutationObserver || o.WebkitMutationObserver,
            attach: function (e, t) {
                void 0 === t && (t = {});
                var i = this,
                    s = new (0, A.func)(function (e) {
                        e.forEach(function (e) {
                            i.emit("observerUpdate", e);
                        });
                    });
                s.observe(e, {
                    attributes: void 0 === t.attributes || t.attributes,
                    childList: void 0 === t.childList || t.childList,
                    characterData:
                        void 0 === t.characterData || t.characterData,
                }),
                    i.observer.observers.push(s);
            },
            init: function () {
                if (h.observer && this.params.observer) {
                    if (this.params.observeParents)
                        for (
                            var e = this.$el.parents(), t = 0;
                            t < e.length;
                            t += 1
                        )
                            this.observer.attach(e[t]);
                    this.observer.attach(this.$el[0], { childList: !1 }),
                        this.observer.attach(this.$wrapperEl[0], {
                            attributes: !1,
                        });
                }
            },
            destroy: function () {
                this.observer.observers.forEach(function (e) {
                    e.disconnect();
                }),
                    (this.observer.observers = []);
            },
        },
        H = {
            name: "observer",
            params: { observer: !1, observeParents: !1 },
            create: function () {
                l.extend(this, {
                    observer: {
                        init: A.init.bind(this),
                        attach: A.attach.bind(this),
                        destroy: A.destroy.bind(this),
                        observers: [],
                    },
                });
            },
            on: {
                init: function () {
                    this.observer.init();
                },
                destroy: function () {
                    this.observer.destroy();
                },
            },
        },
        N = {
            update: function (e) {
                var t = this,
                    i = t.params,
                    s = i.slidesPerView,
                    a = i.slidesPerGroup,
                    r = i.centeredSlides,
                    n = t.virtual,
                    o = n.from,
                    d = n.to,
                    h = n.slides,
                    p = n.slidesGrid,
                    c = n.renderSlide,
                    u = n.offset;
                t.updateActiveIndex();
                var f,
                    v,
                    m,
                    g = t.activeIndex || 0;
                (f =
                    t.rtl && t.isHorizontal()
                        ? "right"
                        : t.isHorizontal()
                        ? "left"
                        : "top"),
                    r
                        ? ((v = Math.floor(s / 2) + a),
                          (m = Math.floor(s / 2) + a))
                        : ((v = s + (a - 1)), (m = a));
                var b = Math.max((g || 0) - m, 0),
                    w = Math.min((g || 0) + v, h.length - 1),
                    y = (t.slidesGrid[b] || 0) - (t.slidesGrid[0] || 0);
                function x() {
                    t.updateSlides(),
                        t.updateProgress(),
                        t.updateSlidesClasses(),
                        t.lazy && t.params.lazy.enabled && t.lazy.load();
                }
                if (
                    (l.extend(t.virtual, {
                        from: b,
                        to: w,
                        offset: y,
                        slidesGrid: t.slidesGrid,
                    }),
                    o === b && d === w && !e)
                )
                    return (
                        t.slidesGrid !== p &&
                            y !== u &&
                            t.slides.css(f, y + "px"),
                        void t.updateProgress()
                    );
                if (t.params.virtual.renderExternal)
                    return (
                        t.params.virtual.renderExternal.call(t, {
                            offset: y,
                            from: b,
                            to: w,
                            slides: (function () {
                                for (var e = [], t = b; t <= w; t += 1)
                                    e.push(h[t]);
                                return e;
                            })(),
                        }),
                        void x()
                    );
                var T = [],
                    E = [];
                if (e) t.$wrapperEl.find("." + t.params.slideClass).remove();
                else
                    for (var S = o; S <= d; S += 1)
                        (S < b || S > w) &&
                            t.$wrapperEl
                                .find(
                                    "." +
                                        t.params.slideClass +
                                        '[data-swiper-slide-index="' +
                                        S +
                                        '"]'
                                )
                                .remove();
                for (var C = 0; C < h.length; C += 1)
                    C >= b &&
                        C <= w &&
                        (void 0 === d || e
                            ? E.push(C)
                            : (C > d && E.push(C), C < o && T.push(C)));
                E.forEach(function (e) {
                    t.$wrapperEl.append(c(h[e], e));
                }),
                    T.sort(function (e, t) {
                        return e < t;
                    }).forEach(function (e) {
                        t.$wrapperEl.prepend(c(h[e], e));
                    }),
                    t.$wrapperEl.children(".swiper-slide").css(f, y + "px"),
                    x();
            },
            renderSlide: function (e, i) {
                var s = this.params.virtual;
                if (s.cache && this.virtual.cache[i])
                    return this.virtual.cache[i];
                var a = s.renderSlide
                    ? t(s.renderSlide.call(this, e, i))
                    : t(
                          '<div class="' +
                              this.params.slideClass +
                              '" data-swiper-slide-index="' +
                              i +
                              '">' +
                              e +
                              "</div>"
                      );
                return (
                    a.attr("data-swiper-slide-index") ||
                        a.attr("data-swiper-slide-index", i),
                    s.cache && (this.virtual.cache[i] = a),
                    a
                );
            },
            appendSlide: function (e) {
                this.virtual.slides.push(e), this.virtual.update(!0);
            },
            prependSlide: function (e) {
                if (
                    (this.virtual.slides.unshift(e), this.params.virtual.cache)
                ) {
                    var t = this.virtual.cache,
                        i = {};
                    Object.keys(t).forEach(function (e) {
                        i[e + 1] = t[e];
                    }),
                        (this.virtual.cache = i);
                }
                this.virtual.update(!0), this.slideNext(0);
            },
        },
        X = {
            name: "virtual",
            params: {
                virtual: {
                    enabled: !1,
                    slides: [],
                    cache: !0,
                    renderSlide: null,
                    renderExternal: null,
                },
            },
            create: function () {
                l.extend(this, {
                    virtual: {
                        update: N.update.bind(this),
                        appendSlide: N.appendSlide.bind(this),
                        prependSlide: N.prependSlide.bind(this),
                        renderSlide: N.renderSlide.bind(this),
                        slides: this.params.virtual.slides,
                        cache: {},
                    },
                });
            },
            on: {
                beforeInit: function () {
                    if (this.params.virtual.enabled) {
                        this.classNames.push(
                            this.params.containerModifierClass + "virtual"
                        );
                        var e = { watchSlidesProgress: !0 };
                        l.extend(this.params, e),
                            l.extend(this.originalParams, e),
                            this.virtual.update();
                    }
                },
                setTranslate: function () {
                    this.params.virtual.enabled && this.virtual.update();
                },
            },
        },
        Y = {
            handle: function (e) {
                var t = e;
                t.originalEvent && (t = t.originalEvent);
                var i = t.keyCode || t.charCode;
                if (
                    !this.allowSlideNext &&
                    ((this.isHorizontal() && 39 === i) ||
                        (this.isVertical() && 40 === i))
                )
                    return !1;
                if (
                    !this.allowSlidePrev &&
                    ((this.isHorizontal() && 37 === i) ||
                        (this.isVertical() && 38 === i))
                )
                    return !1;
                if (
                    !(
                        t.shiftKey ||
                        t.altKey ||
                        t.ctrlKey ||
                        t.metaKey ||
                        (d.activeElement &&
                            d.activeElement.nodeName &&
                            ("input" ===
                                d.activeElement.nodeName.toLowerCase() ||
                                "textarea" ===
                                    d.activeElement.nodeName.toLowerCase()))
                    )
                ) {
                    if (
                        this.params.keyboard.onlyInViewport &&
                        (37 === i || 39 === i || 38 === i || 40 === i)
                    ) {
                        var s = !1;
                        if (
                            this.$el.parents("." + this.params.slideClass)
                                .length > 0 &&
                            0 ===
                                this.$el.parents(
                                    "." + this.params.slideActiveClass
                                ).length
                        )
                            return;
                        var a = o.pageXOffset,
                            r = o.pageYOffset,
                            n = o.innerWidth,
                            l = o.innerHeight,
                            h = this.$el.offset();
                        this.rtl && (h.left -= this.$el[0].scrollLeft);
                        for (
                            var p = [
                                    [h.left, h.top],
                                    [h.left + this.width, h.top],
                                    [h.left, h.top + this.height],
                                    [h.left + this.width, h.top + this.height],
                                ],
                                c = 0;
                            c < p.length;
                            c += 1
                        ) {
                            var u = p[c];
                            u[0] >= a &&
                                u[0] <= a + n &&
                                u[1] >= r &&
                                u[1] <= r + l &&
                                (s = !0);
                        }
                        if (!s) return;
                    }
                    this.isHorizontal()
                        ? ((37 !== i && 39 !== i) ||
                              (t.preventDefault
                                  ? t.preventDefault()
                                  : (t.returnValue = !1)),
                          ((39 === i && !this.rtl) || (37 === i && this.rtl)) &&
                              this.slideNext(),
                          ((37 === i && !this.rtl) || (39 === i && this.rtl)) &&
                              this.slidePrev())
                        : ((38 !== i && 40 !== i) ||
                              (t.preventDefault
                                  ? t.preventDefault()
                                  : (t.returnValue = !1)),
                          40 === i && this.slideNext(),
                          38 === i && this.slidePrev()),
                        this.emit("keyPress", i);
                }
            },
            enable: function () {
                this.keyboard.enabled ||
                    (t(d).on("keydown", this.keyboard.handle),
                    (this.keyboard.enabled = !0));
            },
            disable: function () {
                this.keyboard.enabled &&
                    (t(d).off("keydown", this.keyboard.handle),
                    (this.keyboard.enabled = !1));
            },
        },
        G = {
            name: "keyboard",
            params: { keyboard: { enabled: !1, onlyInViewport: !0 } },
            create: function () {
                l.extend(this, {
                    keyboard: {
                        enabled: !1,
                        enable: Y.enable.bind(this),
                        disable: Y.disable.bind(this),
                        handle: Y.handle.bind(this),
                    },
                });
            },
            on: {
                init: function () {
                    this.params.keyboard.enabled && this.keyboard.enable();
                },
                destroy: function () {
                    this.keyboard.enabled && this.keyboard.disable();
                },
            },
        };
    var B = {
            lastScrollTime: l.now(),
            event:
                o.navigator.userAgent.indexOf("firefox") > -1
                    ? "DOMMouseScroll"
                    : (function () {
                          var e = "onwheel" in d;
                          if (!e) {
                              var t = d.createElement("div");
                              t.setAttribute("onwheel", "return;"),
                                  (e = "function" == typeof t.onwheel);
                          }
                          return (
                              !e &&
                                  d.implementation &&
                                  d.implementation.hasFeature &&
                                  !0 !== d.implementation.hasFeature("", "") &&
                                  (e = d.implementation.hasFeature(
                                      "Events.wheel",
                                      "3.0"
                                  )),
                              e
                          );
                      })()
                    ? "wheel"
                    : "mousewheel",
            normalize: function (e) {
                var t = 0,
                    i = 0,
                    s = 0,
                    a = 0;
                return (
                    "detail" in e && (i = e.detail),
                    "wheelDelta" in e && (i = -e.wheelDelta / 120),
                    "wheelDeltaY" in e && (i = -e.wheelDeltaY / 120),
                    "wheelDeltaX" in e && (t = -e.wheelDeltaX / 120),
                    "axis" in e &&
                        e.axis === e.HORIZONTAL_AXIS &&
                        ((t = i), (i = 0)),
                    (s = 10 * t),
                    (a = 10 * i),
                    "deltaY" in e && (a = e.deltaY),
                    "deltaX" in e && (s = e.deltaX),
                    (s || a) &&
                        e.deltaMode &&
                        (1 === e.deltaMode
                            ? ((s *= 40), (a *= 40))
                            : ((s *= 800), (a *= 800))),
                    s && !t && (t = s < 1 ? -1 : 1),
                    a && !i && (i = a < 1 ? -1 : 1),
                    { spinX: t, spinY: i, pixelX: s, pixelY: a }
                );
            },
            handle: function (e) {
                var t = e,
                    i = this,
                    s = i.params.mousewheel;
                t.originalEvent && (t = t.originalEvent);
                var a = 0,
                    r = i.rtl ? -1 : 1,
                    n = B.normalize(t);
                if (s.forceToAxis)
                    if (i.isHorizontal()) {
                        if (!(Math.abs(n.pixelX) > Math.abs(n.pixelY)))
                            return !0;
                        a = n.pixelX * r;
                    } else {
                        if (!(Math.abs(n.pixelY) > Math.abs(n.pixelX)))
                            return !0;
                        a = n.pixelY;
                    }
                else
                    a =
                        Math.abs(n.pixelX) > Math.abs(n.pixelY)
                            ? -n.pixelX * r
                            : -n.pixelY;
                if (0 === a) return !0;
                if ((s.invert && (a = -a), i.params.freeMode)) {
                    var d = i.getTranslate() + a * s.sensitivity,
                        h = i.isBeginning,
                        p = i.isEnd;
                    if (
                        (d >= i.minTranslate() && (d = i.minTranslate()),
                        d <= i.maxTranslate() && (d = i.maxTranslate()),
                        i.setTransition(0),
                        i.setTranslate(d),
                        i.updateProgress(),
                        i.updateActiveIndex(),
                        i.updateSlidesClasses(),
                        ((!h && i.isBeginning) || (!p && i.isEnd)) &&
                            i.updateSlidesClasses(),
                        i.params.freeModeSticky &&
                            (clearTimeout(i.mousewheel.timeout),
                            (i.mousewheel.timeout = l.nextTick(function () {
                                i.slideReset();
                            }, 300))),
                        i.emit("scroll", t),
                        i.params.autoplay &&
                            i.params.autoplayDisableOnInteraction &&
                            i.stopAutoplay(),
                        0 === d || d === i.maxTranslate())
                    )
                        return !0;
                } else {
                    if (l.now() - i.mousewheel.lastScrollTime > 60)
                        if (a < 0)
                            if ((i.isEnd && !i.params.loop) || i.animating) {
                                if (s.releaseOnEdges) return !0;
                            } else i.slideNext(), i.emit("scroll", t);
                        else if (
                            (i.isBeginning && !i.params.loop) ||
                            i.animating
                        ) {
                            if (s.releaseOnEdges) return !0;
                        } else i.slidePrev(), i.emit("scroll", t);
                    i.mousewheel.lastScrollTime = new o.Date().getTime();
                }
                return (
                    t.preventDefault
                        ? t.preventDefault()
                        : (t.returnValue = !1),
                    !1
                );
            },
            enable: function () {
                if (!B.event) return !1;
                if (this.mousewheel.enabled) return !1;
                var e = this.$el;
                return (
                    "container" !== this.params.mousewheel.eventsTarged &&
                        (e = t(this.params.mousewheel.eventsTarged)),
                    e.on(B.event, this.mousewheel.handle),
                    (this.mousewheel.enabled = !0),
                    !0
                );
            },
            disable: function () {
                if (!B.event) return !1;
                if (!this.mousewheel.enabled) return !1;
                var e = this.$el;
                return (
                    "container" !== this.params.mousewheel.eventsTarged &&
                        (e = t(this.params.mousewheel.eventsTarged)),
                    e.off(B.event, this.mousewheel.handle),
                    (this.mousewheel.enabled = !1),
                    !0
                );
            },
        },
        V = {
            update: function () {
                var e = this.params.navigation;
                if (!this.params.loop) {
                    var t = this.navigation,
                        i = t.$nextEl,
                        s = t.$prevEl;
                    s &&
                        s.length > 0 &&
                        (this.isBeginning
                            ? s.addClass(e.disabledClass)
                            : s.removeClass(e.disabledClass),
                        s[
                            this.params.watchOverflow && this.isLocked
                                ? "addClass"
                                : "removeClass"
                        ](e.lockClass)),
                        i &&
                            i.length > 0 &&
                            (this.isEnd
                                ? i.addClass(e.disabledClass)
                                : i.removeClass(e.disabledClass),
                            i[
                                this.params.watchOverflow && this.isLocked
                                    ? "addClass"
                                    : "removeClass"
                            ](e.lockClass));
                }
            },
            init: function () {
                var e,
                    i,
                    s = this,
                    a = s.params.navigation;
                (a.nextEl || a.prevEl) &&
                    (a.nextEl &&
                        ((e = t(a.nextEl)),
                        s.params.uniqueNavElements &&
                            "string" == typeof a.nextEl &&
                            e.length > 1 &&
                            1 === s.$el.find(a.nextEl).length &&
                            (e = s.$el.find(a.nextEl))),
                    a.prevEl &&
                        ((i = t(a.prevEl)),
                        s.params.uniqueNavElements &&
                            "string" == typeof a.prevEl &&
                            i.length > 1 &&
                            1 === s.$el.find(a.prevEl).length &&
                            (i = s.$el.find(a.prevEl))),
                    e &&
                        e.length > 0 &&
                        e.on("click", function (e) {
                            e.preventDefault(),
                                (s.isEnd && !s.params.loop) || s.slideNext();
                        }),
                    i &&
                        i.length > 0 &&
                        i.on("click", function (e) {
                            e.preventDefault(),
                                (s.isBeginning && !s.params.loop) ||
                                    s.slidePrev();
                        }),
                    l.extend(s.navigation, {
                        $nextEl: e,
                        nextEl: e && e[0],
                        $prevEl: i,
                        prevEl: i && i[0],
                    }));
            },
            destroy: function () {
                var e = this.navigation,
                    t = e.$nextEl,
                    i = e.$prevEl;
                t &&
                    t.length &&
                    (t.off("click"),
                    t.removeClass(this.params.navigation.disabledClass)),
                    i &&
                        i.length &&
                        (i.off("click"),
                        i.removeClass(this.params.navigation.disabledClass));
            },
        },
        R = {
            update: function () {
                var e = this.rtl,
                    i = this.params.pagination;
                if (
                    i.el &&
                    this.pagination.el &&
                    this.pagination.$el &&
                    0 !== this.pagination.$el.length
                ) {
                    var s,
                        a =
                            this.virtual && this.params.virtual.enabled
                                ? this.virtual.slides.length
                                : this.slides.length,
                        r = this.pagination.$el,
                        n = this.params.loop
                            ? Math.ceil(
                                  (a - 2 * this.loopedSlides) /
                                      this.params.slidesPerGroup
                              )
                            : this.snapGrid.length;
                    if (
                        (this.params.loop
                            ? ((s = Math.ceil(
                                  (this.activeIndex - this.loopedSlides) /
                                      this.params.slidesPerGroup
                              )) >
                                  a - 1 - 2 * this.loopedSlides &&
                                  (s -= a - 2 * this.loopedSlides),
                              s > n - 1 && (s -= n),
                              s < 0 &&
                                  "bullets" !== this.params.paginationType &&
                                  (s = n + s))
                            : (s =
                                  void 0 !== this.snapIndex
                                      ? this.snapIndex
                                      : this.activeIndex || 0),
                        "bullets" === i.type &&
                            this.pagination.bullets &&
                            this.pagination.bullets.length > 0)
                    ) {
                        var o = this.pagination.bullets;
                        if (
                            (i.dynamicBullets &&
                                ((this.pagination.bulletSize = o
                                    .eq(0)
                                    [
                                        this.isHorizontal()
                                            ? "outerWidth"
                                            : "outerHeight"
                                    ](!0)),
                                r.css(
                                    this.isHorizontal() ? "width" : "height",
                                    5 * this.pagination.bulletSize + "px"
                                )),
                            o.removeClass(
                                i.bulletActiveClass +
                                    " " +
                                    i.bulletActiveClass +
                                    "-next " +
                                    i.bulletActiveClass +
                                    "-next-next " +
                                    i.bulletActiveClass +
                                    "-prev " +
                                    i.bulletActiveClass +
                                    "-prev-prev"
                            ),
                            r.length > 1)
                        )
                            o.each(function (e, a) {
                                var r = t(a);
                                r.index() === s &&
                                    (r.addClass(i.bulletActiveClass),
                                    i.dynamicBullets &&
                                        (r
                                            .prev()
                                            .addClass(
                                                i.bulletActiveClass + "-prev"
                                            )
                                            .prev()
                                            .addClass(
                                                i.bulletActiveClass +
                                                    "-prev-prev"
                                            ),
                                        r
                                            .next()
                                            .addClass(
                                                i.bulletActiveClass + "-next"
                                            )
                                            .next()
                                            .addClass(
                                                i.bulletActiveClass +
                                                    "-next-next"
                                            )));
                            });
                        else {
                            var l = o.eq(s);
                            l.addClass(i.bulletActiveClass),
                                i.dynamicBullets &&
                                    (l
                                        .prev()
                                        .addClass(i.bulletActiveClass + "-prev")
                                        .prev()
                                        .addClass(
                                            i.bulletActiveClass + "-prev-prev"
                                        ),
                                    l
                                        .next()
                                        .addClass(i.bulletActiveClass + "-next")
                                        .next()
                                        .addClass(
                                            i.bulletActiveClass + "-next-next"
                                        ));
                        }
                        if (i.dynamicBullets) {
                            var d = Math.min(o.length, 5),
                                h =
                                    (this.pagination.bulletSize * d -
                                        this.pagination.bulletSize) /
                                        2 -
                                    s * this.pagination.bulletSize,
                                p = e ? "right" : "left";
                            o.css(this.isHorizontal() ? p : "top", h + "px");
                        }
                    }
                    if (
                        ("fraction" === i.type &&
                            (r.find("." + i.currentClass).text(s + 1),
                            r.find("." + i.totalClass).text(n)),
                        "progressbar" === i.type)
                    ) {
                        var c = (s + 1) / n,
                            u = c,
                            f = 1;
                        this.isHorizontal() || ((f = c), (u = 1)),
                            r
                                .find("." + i.progressbarFillClass)
                                .transform(
                                    "translate3d(0,0,0) scaleX(" +
                                        u +
                                        ") scaleY(" +
                                        f +
                                        ")"
                                )
                                .transition(this.params.speed);
                    }
                    "custom" === i.type && i.renderCustom
                        ? (r.html(i.renderCustom(this, s + 1, n)),
                          this.emit("paginationRender", this, r[0]))
                        : this.emit("paginationUpdate", this, r[0]),
                        r[
                            this.params.watchOverflow && this.isLocked
                                ? "addClass"
                                : "removeClass"
                        ](i.lockClass);
                }
            },
            render: function () {
                var e = this.params.pagination;
                if (
                    e.el &&
                    this.pagination.el &&
                    this.pagination.$el &&
                    0 !== this.pagination.$el.length
                ) {
                    var t =
                            this.virtual && this.params.virtual.enabled
                                ? this.virtual.slides.length
                                : this.slides.length,
                        i = this.pagination.$el,
                        s = "";
                    if ("bullets" === e.type) {
                        for (
                            var a = this.params.loop
                                    ? Math.ceil(
                                          (t - 2 * this.loopedSlides) /
                                              this.params.slidesPerGroup
                                      )
                                    : this.snapGrid.length,
                                r = 0;
                            r < a;
                            r += 1
                        )
                            e.renderBullet
                                ? (s += e.renderBullet.call(
                                      this,
                                      r,
                                      e.bulletClass
                                  ))
                                : (s +=
                                      "<" +
                                      e.bulletElement +
                                      ' class="' +
                                      e.bulletClass +
                                      '"></' +
                                      e.bulletElement +
                                      ">");
                        i.html(s),
                            (this.pagination.bullets = i.find(
                                "." + e.bulletClass
                            ));
                    }
                    "fraction" === e.type &&
                        ((s = e.renderFraction
                            ? e.renderFraction.call(
                                  this,
                                  e.currentClass,
                                  e.totalClass
                              )
                            : '<span class="' +
                              e.currentClass +
                              '"></span> / <span class="' +
                              e.totalClass +
                              '"></span>'),
                        i.html(s)),
                        "progressbar" === e.type &&
                            ((s = e.renderProgressbar
                                ? e.renderProgressbar.call(
                                      this,
                                      e.progressbarFillClass
                                  )
                                : '<span class="' +
                                  e.progressbarFillClass +
                                  '"></span>'),
                            i.html(s)),
                        "custom" !== e.type &&
                            this.emit(
                                "paginationRender",
                                this.pagination.$el[0]
                            );
                }
            },
            init: function () {
                var e = this,
                    i = e.params.pagination;
                if (i.el) {
                    var s = t(i.el);
                    0 !== s.length &&
                        (e.params.uniqueNavElements &&
                            "string" == typeof i.el &&
                            s.length > 1 &&
                            1 === e.$el.find(i.el).length &&
                            (s = e.$el.find(i.el)),
                        "bullets" === i.type &&
                            i.clickable &&
                            s.addClass(i.clickableClass),
                        s.addClass(i.modifierClass + i.type),
                        "bullets" === i.type &&
                            i.dynamicBullets &&
                            s.addClass(
                                "" + i.modifierClass + i.type + "-dynamic"
                            ),
                        i.clickable &&
                            s.on("click", "." + i.bulletClass, function (i) {
                                i.preventDefault();
                                var s =
                                    t(this).index() * e.params.slidesPerGroup;
                                e.params.loop && (s += e.loopedSlides),
                                    e.slideTo(s);
                            }),
                        l.extend(e.pagination, { $el: s, el: s[0] }));
                }
            },
            destroy: function () {
                var e = this.params.pagination;
                if (
                    e.el &&
                    this.pagination.el &&
                    this.pagination.$el &&
                    0 !== this.pagination.$el.length
                ) {
                    var t = this.pagination.$el;
                    t.removeClass(e.hiddenClass),
                        t.removeClass(e.modifierClass + e.type),
                        this.pagination.bullets &&
                            this.pagination.bullets.removeClass(
                                e.bulletActiveClass
                            ),
                        e.clickable && t.off("click", "." + e.bulletClass);
                }
            },
        },
        F = {
            setTranslate: function () {
                if (this.params.scrollbar.el && this.scrollbar.el) {
                    var e = this.scrollbar,
                        t = this.rtl,
                        i = this.progress,
                        s = e.dragSize,
                        a = e.trackSize,
                        r = e.$dragEl,
                        n = e.$el,
                        o = this.params.scrollbar,
                        l = s,
                        d = (a - s) * i;
                    t && this.isHorizontal()
                        ? (d = -d) > 0
                            ? ((l = s - d), (d = 0))
                            : -d + s > a && (l = a + d)
                        : d < 0
                        ? ((l = s + d), (d = 0))
                        : d + s > a && (l = a - d),
                        this.isHorizontal()
                            ? (h.transforms3d
                                  ? r.transform(
                                        "translate3d(" + d + "px, 0, 0)"
                                    )
                                  : r.transform("translateX(" + d + "px)"),
                              (r[0].style.width = l + "px"))
                            : (h.transforms3d
                                  ? r.transform(
                                        "translate3d(0px, " + d + "px, 0)"
                                    )
                                  : r.transform("translateY(" + d + "px)"),
                              (r[0].style.height = l + "px")),
                        o.hide &&
                            (clearTimeout(this.scrollbar.timeout),
                            (n[0].style.opacity = 1),
                            (this.scrollbar.timeout = setTimeout(function () {
                                (n[0].style.opacity = 0), n.transition(400);
                            }, 1e3)));
                }
            },
            setTransition: function (e) {
                this.params.scrollbar.el &&
                    this.scrollbar.el &&
                    this.scrollbar.$dragEl.transition(e);
            },
            updateSize: function () {
                if (this.params.scrollbar.el && this.scrollbar.el) {
                    var e = this.scrollbar,
                        t = e.$dragEl,
                        i = e.$el;
                    (t[0].style.width = ""), (t[0].style.height = "");
                    var s,
                        a = this.isHorizontal()
                            ? i[0].offsetWidth
                            : i[0].offsetHeight,
                        r = this.size / this.virtualSize,
                        n = r * (a / this.size);
                    (s =
                        "auto" === this.params.scrollbar.dragSize
                            ? a * r
                            : parseInt(this.params.scrollbar.dragSize, 10)),
                        this.isHorizontal()
                            ? (t[0].style.width = s + "px")
                            : (t[0].style.height = s + "px"),
                        (i[0].style.display = r >= 1 ? "none" : ""),
                        this.params.scrollbarHide && (i[0].style.opacity = 0),
                        l.extend(e, {
                            trackSize: a,
                            divider: r,
                            moveDivider: n,
                            dragSize: s,
                        }),
                        e.$el[
                            this.params.watchOverflow && this.isLocked
                                ? "addClass"
                                : "removeClass"
                        ](this.params.scrollbar.lockClass);
                }
            },
            setDragPosition: function (e) {
                var t,
                    i = this.scrollbar,
                    s = i.$el,
                    a = i.dragSize,
                    r = i.trackSize;
                (t =
                    ((this.isHorizontal()
                        ? "touchstart" === e.type || "touchmove" === e.type
                            ? e.targetTouches[0].pageX
                            : e.pageX || e.clientX
                        : "touchstart" === e.type || "touchmove" === e.type
                        ? e.targetTouches[0].pageY
                        : e.pageY || e.clientY) -
                        s.offset()[this.isHorizontal() ? "left" : "top"] -
                        a / 2) /
                    (r - a)),
                    (t = Math.max(Math.min(t, 1), 0)),
                    this.rtl && (t = 1 - t);
                var n =
                    this.minTranslate() +
                    (this.maxTranslate() - this.minTranslate()) * t;
                this.updateProgress(n),
                    this.setTranslate(n),
                    this.updateActiveIndex(),
                    this.updateSlidesClasses();
            },
            onDragStart: function (e) {
                var t = this.params.scrollbar,
                    i = this.scrollbar,
                    s = this.$wrapperEl,
                    a = i.$el,
                    r = i.$dragEl;
                (this.scrollbar.isTouched = !0),
                    e.preventDefault(),
                    e.stopPropagation(),
                    s.transition(100),
                    r.transition(100),
                    i.setDragPosition(e),
                    clearTimeout(this.scrollbar.dragTimeout),
                    a.transition(0),
                    t.hide && a.css("opacity", 1),
                    this.emit("scrollbarDragStart", e);
            },
            onDragMove: function (e) {
                var t = this.scrollbar,
                    i = this.$wrapperEl,
                    s = t.$el,
                    a = t.$dragEl;
                this.scrollbar.isTouched &&
                    (e.preventDefault
                        ? e.preventDefault()
                        : (e.returnValue = !1),
                    t.setDragPosition(e),
                    i.transition(0),
                    s.transition(0),
                    a.transition(0),
                    this.emit("scrollbarDragMove", e));
            },
            onDragEnd: function (e) {
                var t = this.params.scrollbar,
                    i = this.scrollbar.$el;
                this.scrollbar.isTouched &&
                    ((this.scrollbar.isTouched = !1),
                    t.hide &&
                        (clearTimeout(this.scrollbar.dragTimeout),
                        (this.scrollbar.dragTimeout = l.nextTick(function () {
                            i.css("opacity", 0), i.transition(400);
                        }, 1e3))),
                    this.emit("scrollbarDragEnd", e),
                    t.snapOnRelease && this.slideReset());
            },
            enableDraggable: function () {
                if (this.params.scrollbar.el) {
                    var e = this.scrollbar.$el,
                        i = h.touch ? e[0] : document;
                    e.on(
                        this.scrollbar.dragEvents.start,
                        this.scrollbar.onDragStart
                    ),
                        t(i).on(
                            this.scrollbar.dragEvents.move,
                            this.scrollbar.onDragMove
                        ),
                        t(i).on(
                            this.scrollbar.dragEvents.end,
                            this.scrollbar.onDragEnd
                        );
                }
            },
            disableDraggable: function () {
                if (this.params.scrollbar.el) {
                    var e = this.scrollbar.$el,
                        i = h.touch ? e[0] : document;
                    e.off(this.scrollbar.dragEvents.start),
                        t(i).off(this.scrollbar.dragEvents.move),
                        t(i).off(this.scrollbar.dragEvents.end);
                }
            },
            init: function () {
                var e = this;
                if (e.params.scrollbar.el) {
                    var i = e.scrollbar,
                        s = e.$el,
                        a = e.touchEvents,
                        r = e.params.scrollbar,
                        n = t(r.el);
                    e.params.uniqueNavElements &&
                        "string" == typeof r.el &&
                        n.length > 1 &&
                        1 === s.find(r.el).length &&
                        (n = s.find(r.el));
                    var o = n.find(".swiper-scrollbar-drag");
                    0 === o.length &&
                        ((o = t('<div class="swiper-scrollbar-drag"></div>')),
                        n.append(o)),
                        (e.scrollbar.dragEvents =
                            !1 !== e.params.simulateTouch || h.touch
                                ? a
                                : {
                                      start: "mousedown",
                                      move: "mousemove",
                                      end: "mouseup",
                                  }),
                        l.extend(i, {
                            $el: n,
                            el: n[0],
                            $dragEl: o,
                            dragEl: o[0],
                        }),
                        r.draggable && i.enableDraggable();
                }
            },
            destroy: function () {
                this.scrollbar.disableDraggable();
            },
        },
        W = {
            setTransform: function (e, i) {
                var s = this.rtl,
                    a = t(e),
                    r = s ? -1 : 1,
                    n = a.attr("data-swiper-parallax") || "0",
                    o = a.attr("data-swiper-parallax-x"),
                    l = a.attr("data-swiper-parallax-y"),
                    d = a.attr("data-swiper-parallax-scale"),
                    h = a.attr("data-swiper-parallax-opacity");
                if (
                    (o || l
                        ? ((o = o || "0"), (l = l || "0"))
                        : this.isHorizontal()
                        ? ((o = n), (l = "0"))
                        : ((l = n), (o = "0")),
                    (o =
                        o.indexOf("%") >= 0
                            ? parseInt(o, 10) * i * r + "%"
                            : o * i * r + "px"),
                    (l =
                        l.indexOf("%") >= 0
                            ? parseInt(l, 10) * i + "%"
                            : l * i + "px"),
                    void 0 !== h && null !== h)
                ) {
                    var p = h - (h - 1) * (1 - Math.abs(i));
                    a[0].style.opacity = p;
                }
                if (void 0 === d || null === d)
                    a.transform("translate3d(" + o + ", " + l + ", 0px)");
                else {
                    var c = d - (d - 1) * (1 - Math.abs(i));
                    a.transform(
                        "translate3d(" +
                            o +
                            ", " +
                            l +
                            ", 0px) scale(" +
                            c +
                            ")"
                    );
                }
            },
            setTranslate: function () {
                var e = this,
                    i = e.$el,
                    s = e.slides,
                    a = e.progress,
                    r = e.snapGrid;
                i
                    .children(
                        "[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]"
                    )
                    .each(function (t, i) {
                        e.parallax.setTransform(i, a);
                    }),
                    s.each(function (i, s) {
                        var n = s.progress;
                        e.params.slidesPerGroup > 1 &&
                            "auto" !== e.params.slidesPerView &&
                            (n += Math.ceil(i / 2) - a * (r.length - 1)),
                            (n = Math.min(Math.max(n, -1), 1)),
                            t(s)
                                .find(
                                    "[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]"
                                )
                                .each(function (t, i) {
                                    e.parallax.setTransform(i, n);
                                });
                    });
            },
            setTransition: function (e) {
                void 0 === e && (e = this.params.speed);
                this.$el
                    .find(
                        "[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]"
                    )
                    .each(function (i, s) {
                        var a = t(s),
                            r =
                                parseInt(
                                    a.attr("data-swiper-parallax-duration"),
                                    10
                                ) || e;
                        0 === e && (r = 0), a.transition(r);
                    });
            },
        },
        j = {
            getDistanceBetweenTouches: function (e) {
                if (e.targetTouches.length < 2) return 1;
                var t = e.targetTouches[0].pageX,
                    i = e.targetTouches[0].pageY,
                    s = e.targetTouches[1].pageX,
                    a = e.targetTouches[1].pageY;
                return Math.sqrt(Math.pow(s - t, 2) + Math.pow(a - i, 2));
            },
            onGestureStart: function (e) {
                var i = this.params.zoom,
                    s = this.zoom,
                    a = s.gesture;
                if (
                    ((s.fakeGestureTouched = !1),
                    (s.fakeGestureMoved = !1),
                    !h.gestures)
                ) {
                    if (
                        "touchstart" !== e.type ||
                        ("touchstart" === e.type && e.targetTouches.length < 2)
                    )
                        return;
                    (s.fakeGestureTouched = !0),
                        (a.scaleStart = j.getDistanceBetweenTouches(e));
                }
                (a.$slideEl && a.$slideEl.length) ||
                ((a.$slideEl = t(this)),
                0 === a.$slideEl.length &&
                    (a.$slideEl = this.slides.eq(this.activeIndex)),
                (a.$imageEl = a.$slideEl.find("img, svg, canvas")),
                (a.$imageWrapEl = a.$imageEl.parent("." + i.containerClass)),
                (a.maxRatio =
                    a.$imageWrapEl.attr("data-swiper-zoom") || i.maxRatio),
                0 !== a.$imageWrapEl.length)
                    ? (a.$imageEl.transition(0), (this.zoom.isScaling = !0))
                    : (a.$imageEl = void 0);
            },
            onGestureChange: function (e) {
                var t = this.params.zoom,
                    i = this.zoom,
                    s = i.gesture;
                if (!h.gestures) {
                    if (
                        "touchmove" !== e.type ||
                        ("touchmove" === e.type && e.targetTouches.length < 2)
                    )
                        return;
                    (i.fakeGestureMoved = !0),
                        (s.scaleMove = j.getDistanceBetweenTouches(e));
                }
                s.$imageEl &&
                    0 !== s.$imageEl.length &&
                    (h.gestures
                        ? (this.zoom.scale = e.scale * i.currentScale)
                        : (i.scale =
                              (s.scaleMove / s.scaleStart) * i.currentScale),
                    i.scale > s.maxRatio &&
                        (i.scale =
                            s.maxRatio -
                            1 +
                            Math.pow(i.scale - s.maxRatio + 1, 0.5)),
                    i.scale < t.minRatio &&
                        (i.scale =
                            t.minRatio +
                            1 -
                            Math.pow(t.minRatio - i.scale + 1, 0.5)),
                    s.$imageEl.transform(
                        "translate3d(0,0,0) scale(" + i.scale + ")"
                    ));
            },
            onGestureEnd: function (e) {
                var t = this.params.zoom,
                    i = this.zoom,
                    s = i.gesture;
                if (!h.gestures) {
                    if (!i.fakeGestureTouched || !i.fakeGestureMoved) return;
                    if (
                        "touchend" !== e.type ||
                        ("touchend" === e.type &&
                            e.changedTouches.length < 2 &&
                            !y.android)
                    )
                        return;
                    (i.fakeGestureTouched = !1), (i.fakeGestureMoved = !1);
                }
                s.$imageEl &&
                    0 !== s.$imageEl.length &&
                    ((i.scale = Math.max(
                        Math.min(i.scale, s.maxRatio),
                        t.minRatio
                    )),
                    s.$imageEl
                        .transition(this.params.speed)
                        .transform("translate3d(0,0,0) scale(" + i.scale + ")"),
                    (i.currentScale = i.scale),
                    (i.isScaling = !1),
                    1 === i.scale && (s.$slideEl = void 0));
            },
            onTouchStart: function (e) {
                var t = this.zoom,
                    i = t.gesture,
                    s = t.image;
                i.$imageEl &&
                    0 !== i.$imageEl.length &&
                    (s.isTouched ||
                        (y.android && e.preventDefault(),
                        (s.isTouched = !0),
                        (s.touchesStart.x =
                            "touchstart" === e.type
                                ? e.targetTouches[0].pageX
                                : e.pageX),
                        (s.touchesStart.y =
                            "touchstart" === e.type
                                ? e.targetTouches[0].pageY
                                : e.pageY)));
            },
            onTouchMove: function (e) {
                var t = this.zoom,
                    i = t.gesture,
                    s = t.image,
                    a = t.velocity;
                if (
                    i.$imageEl &&
                    0 !== i.$imageEl.length &&
                    ((this.allowClick = !1), s.isTouched && i.$slideEl)
                ) {
                    s.isMoved ||
                        ((s.width = i.$imageEl[0].offsetWidth),
                        (s.height = i.$imageEl[0].offsetHeight),
                        (s.startX =
                            l.getTranslate(i.$imageWrapEl[0], "x") || 0),
                        (s.startY =
                            l.getTranslate(i.$imageWrapEl[0], "y") || 0),
                        (i.slideWidth = i.$slideEl[0].offsetWidth),
                        (i.slideHeight = i.$slideEl[0].offsetHeight),
                        i.$imageWrapEl.transition(0),
                        this.rtl && (s.startX = -s.startX),
                        this.rtl && (s.startY = -s.startY));
                    var r = s.width * t.scale,
                        n = s.height * t.scale;
                    if (!(r < i.slideWidth && n < i.slideHeight)) {
                        if (
                            ((s.minX = Math.min(i.slideWidth / 2 - r / 2, 0)),
                            (s.maxX = -s.minX),
                            (s.minY = Math.min(i.slideHeight / 2 - n / 2, 0)),
                            (s.maxY = -s.minY),
                            (s.touchesCurrent.x =
                                "touchmove" === e.type
                                    ? e.targetTouches[0].pageX
                                    : e.pageX),
                            (s.touchesCurrent.y =
                                "touchmove" === e.type
                                    ? e.targetTouches[0].pageY
                                    : e.pageY),
                            !s.isMoved && !t.isScaling)
                        ) {
                            if (
                                this.isHorizontal() &&
                                ((Math.floor(s.minX) === Math.floor(s.startX) &&
                                    s.touchesCurrent.x < s.touchesStart.x) ||
                                    (Math.floor(s.maxX) ===
                                        Math.floor(s.startX) &&
                                        s.touchesCurrent.x > s.touchesStart.x))
                            )
                                return void (s.isTouched = !1);
                            if (
                                !this.isHorizontal() &&
                                ((Math.floor(s.minY) === Math.floor(s.startY) &&
                                    s.touchesCurrent.y < s.touchesStart.y) ||
                                    (Math.floor(s.maxY) ===
                                        Math.floor(s.startY) &&
                                        s.touchesCurrent.y > s.touchesStart.y))
                            )
                                return void (s.isTouched = !1);
                        }
                        e.preventDefault(),
                            e.stopPropagation(),
                            (s.isMoved = !0),
                            (s.currentX =
                                s.touchesCurrent.x -
                                s.touchesStart.x +
                                s.startX),
                            (s.currentY =
                                s.touchesCurrent.y -
                                s.touchesStart.y +
                                s.startY),
                            s.currentX < s.minX &&
                                (s.currentX =
                                    s.minX +
                                    1 -
                                    Math.pow(s.minX - s.currentX + 1, 0.8)),
                            s.currentX > s.maxX &&
                                (s.currentX =
                                    s.maxX -
                                    1 +
                                    Math.pow(s.currentX - s.maxX + 1, 0.8)),
                            s.currentY < s.minY &&
                                (s.currentY =
                                    s.minY +
                                    1 -
                                    Math.pow(s.minY - s.currentY + 1, 0.8)),
                            s.currentY > s.maxY &&
                                (s.currentY =
                                    s.maxY -
                                    1 +
                                    Math.pow(s.currentY - s.maxY + 1, 0.8)),
                            a.prevPositionX ||
                                (a.prevPositionX = s.touchesCurrent.x),
                            a.prevPositionY ||
                                (a.prevPositionY = s.touchesCurrent.y),
                            a.prevTime || (a.prevTime = Date.now()),
                            (a.x =
                                (s.touchesCurrent.x - a.prevPositionX) /
                                (Date.now() - a.prevTime) /
                                2),
                            (a.y =
                                (s.touchesCurrent.y - a.prevPositionY) /
                                (Date.now() - a.prevTime) /
                                2),
                            Math.abs(s.touchesCurrent.x - a.prevPositionX) <
                                2 && (a.x = 0),
                            Math.abs(s.touchesCurrent.y - a.prevPositionY) <
                                2 && (a.y = 0),
                            (a.prevPositionX = s.touchesCurrent.x),
                            (a.prevPositionY = s.touchesCurrent.y),
                            (a.prevTime = Date.now()),
                            i.$imageWrapEl.transform(
                                "translate3d(" +
                                    s.currentX +
                                    "px, " +
                                    s.currentY +
                                    "px,0)"
                            );
                    }
                }
            },
            onTouchEnd: function () {
                var e = this.zoom,
                    t = e.gesture,
                    i = e.image,
                    s = e.velocity;
                if (t.$imageEl && 0 !== t.$imageEl.length) {
                    if (!i.isTouched || !i.isMoved)
                        return (i.isTouched = !1), void (i.isMoved = !1);
                    (i.isTouched = !1), (i.isMoved = !1);
                    var a = 300,
                        r = 300,
                        n = s.x * a,
                        o = i.currentX + n,
                        l = s.y * r,
                        d = i.currentY + l;
                    0 !== s.x && (a = Math.abs((o - i.currentX) / s.x)),
                        0 !== s.y && (r = Math.abs((d - i.currentY) / s.y));
                    var h = Math.max(a, r);
                    (i.currentX = o), (i.currentY = d);
                    var p = i.width * e.scale,
                        c = i.height * e.scale;
                    (i.minX = Math.min(t.slideWidth / 2 - p / 2, 0)),
                        (i.maxX = -i.minX),
                        (i.minY = Math.min(t.slideHeight / 2 - c / 2, 0)),
                        (i.maxY = -i.minY),
                        (i.currentX = Math.max(
                            Math.min(i.currentX, i.maxX),
                            i.minX
                        )),
                        (i.currentY = Math.max(
                            Math.min(i.currentY, i.maxY),
                            i.minY
                        )),
                        t.$imageWrapEl
                            .transition(h)
                            .transform(
                                "translate3d(" +
                                    i.currentX +
                                    "px, " +
                                    i.currentY +
                                    "px,0)"
                            );
                }
            },
            onTransitionEnd: function () {
                var e = this.zoom,
                    t = e.gesture;
                t.$slideEl &&
                    this.previousIndex !== this.activeIndex &&
                    (t.$imageEl.transform("translate3d(0,0,0) scale(1)"),
                    t.$imageWrapEl.transform("translate3d(0,0,0)"),
                    (t.$slideEl = void 0),
                    (t.$imageEl = void 0),
                    (t.$imageWrapEl = void 0),
                    (e.scale = 1),
                    (e.currentScale = 1));
            },
            toggle: function (e) {
                var t = this.zoom;
                t.scale && 1 !== t.scale ? t.out() : t.in(e);
            },
            in: function (e) {
                var i,
                    s,
                    a,
                    r,
                    n,
                    o,
                    l,
                    d,
                    h,
                    p,
                    c,
                    u,
                    f,
                    v,
                    m,
                    g,
                    b = this.zoom,
                    w = this.params.zoom,
                    y = b.gesture,
                    x = b.image;
                (y.$slideEl ||
                    ((y.$slideEl = this.clickedSlide
                        ? t(this.clickedSlide)
                        : this.slides.eq(this.activeIndex)),
                    (y.$imageEl = y.$slideEl.find("img, svg, canvas")),
                    (y.$imageWrapEl = y.$imageEl.parent(
                        "." + w.containerClass
                    ))),
                y.$imageEl && 0 !== y.$imageEl.length) &&
                    (y.$slideEl.addClass("" + w.zoomedSlideClass),
                    void 0 === x.touchesStart.x && e
                        ? ((i =
                              "touchend" === e.type
                                  ? e.changedTouches[0].pageX
                                  : e.pageX),
                          (s =
                              "touchend" === e.type
                                  ? e.changedTouches[0].pageY
                                  : e.pageY))
                        : ((i = x.touchesStart.x), (s = x.touchesStart.y)),
                    (b.scale =
                        y.$imageWrapEl.attr("data-swiper-zoom") || w.maxRatio),
                    (b.currentScale =
                        y.$imageWrapEl.attr("data-swiper-zoom") || w.maxRatio),
                    e
                        ? ((m = y.$slideEl[0].offsetWidth),
                          (g = y.$slideEl[0].offsetHeight),
                          (a = y.$slideEl.offset().left + m / 2 - i),
                          (r = y.$slideEl.offset().top + g / 2 - s),
                          (l = y.$imageEl[0].offsetWidth),
                          (d = y.$imageEl[0].offsetHeight),
                          (h = l * b.scale),
                          (p = d * b.scale),
                          (f = -(c = Math.min(m / 2 - h / 2, 0))),
                          (v = -(u = Math.min(g / 2 - p / 2, 0))),
                          (n = a * b.scale),
                          (o = r * b.scale),
                          n < c && (n = c),
                          n > f && (n = f),
                          o < u && (o = u),
                          o > v && (o = v))
                        : ((n = 0), (o = 0)),
                    y.$imageWrapEl
                        .transition(300)
                        .transform("translate3d(" + n + "px, " + o + "px,0)"),
                    y.$imageEl
                        .transition(300)
                        .transform(
                            "translate3d(0,0,0) scale(" + b.scale + ")"
                        ));
            },
            out: function () {
                var e = this.zoom,
                    i = this.params.zoom,
                    s = e.gesture;
                s.$slideEl ||
                    ((s.$slideEl = this.clickedSlide
                        ? t(this.clickedSlide)
                        : this.slides.eq(this.activeIndex)),
                    (s.$imageEl = s.$slideEl.find("img, svg, canvas")),
                    (s.$imageWrapEl = s.$imageEl.parent(
                        "." + i.containerClass
                    ))),
                    s.$imageEl &&
                        0 !== s.$imageEl.length &&
                        ((e.scale = 1),
                        (e.currentScale = 1),
                        s.$imageWrapEl
                            .transition(300)
                            .transform("translate3d(0,0,0)"),
                        s.$imageEl
                            .transition(300)
                            .transform("translate3d(0,0,0) scale(1)"),
                        s.$slideEl.removeClass("" + i.zoomedSlideClass),
                        (s.$slideEl = void 0));
            },
            enable: function () {
                var e = this,
                    i = e.zoom;
                if (!i.enabled) {
                    i.enabled = !0;
                    var s = e.slides,
                        a = !(
                            "touchstart" !== e.touchEvents.start ||
                            !h.passiveListener ||
                            !e.params.passiveListeners
                        ) && { passive: !0, capture: !1 };
                    h.gestures
                        ? (s.on("gesturestart", i.onGestureStart, a),
                          s.on("gesturechange", i.onGestureChange, a),
                          s.on("gestureend", i.onGestureEnd, a))
                        : "touchstart" === e.touchEvents.start &&
                          (s.on(e.touchEvents.start, i.onGestureStart, a),
                          s.on(e.touchEvents.move, i.onGestureChange, a),
                          s.on(e.touchEvents.end, i.onGestureEnd, a)),
                        e.slides.each(function (s, a) {
                            var r = t(a);
                            r.find("." + e.params.zoom.containerClass).length >
                                0 && r.on(e.touchEvents.move, i.onTouchMove);
                        });
                }
            },
            disable: function () {
                var e = this,
                    i = e.zoom;
                if (i.enabled) {
                    e.zoom.enabled = !1;
                    var s = e.slides,
                        a = !(
                            "touchstart" !== e.touchEvents.start ||
                            !h.passiveListener ||
                            !e.params.passiveListeners
                        ) && { passive: !0, capture: !1 };
                    h.gestures
                        ? (s.off("gesturestart", i.onGestureStart, a),
                          s.off("gesturechange", i.onGestureChange, a),
                          s.off("gestureend", i.onGestureEnd, a))
                        : "touchstart" === e.touchEvents.start &&
                          (s.off(e.touchEvents.start, i.onGestureStart, a),
                          s.off(e.touchEvents.move, i.onGestureChange, a),
                          s.off(e.touchEvents.end, i.onGestureEnd, a)),
                        e.slides.each(function (s, a) {
                            var r = t(a);
                            r.find("." + e.params.zoom.containerClass).length >
                                0 && r.off(e.touchEvents.move, i.onTouchMove);
                        });
                }
            },
        },
        q = {
            loadInSlide: function (e, i) {
                void 0 === i && (i = !0);
                var s = this,
                    a = s.params.lazy;
                if (void 0 !== e && 0 !== s.slides.length) {
                    var r =
                            s.virtual && s.params.virtual.enabled
                                ? s.$wrapperEl.children(
                                      "." +
                                          s.params.slideClass +
                                          '[data-swiper-slide-index="' +
                                          e +
                                          '"]'
                                  )
                                : s.slides.eq(e),
                        n = r.find(
                            "." +
                                a.elementClass +
                                ":not(." +
                                a.loadedClass +
                                "):not(." +
                                a.loadingClass +
                                ")"
                        );
                    !r.hasClass(a.elementClass) ||
                        r.hasClass(a.loadedClass) ||
                        r.hasClass(a.loadingClass) ||
                        (n = n.add(r[0])),
                        0 !== n.length &&
                            n.each(function (e, n) {
                                var o = t(n);
                                o.addClass(a.loadingClass);
                                var l = o.attr("data-background"),
                                    d = o.attr("data-src"),
                                    h = o.attr("data-srcset"),
                                    p = o.attr("data-sizes");
                                s.loadImage(
                                    o[0],
                                    d || l,
                                    h,
                                    p,
                                    !1,
                                    function () {
                                        if (
                                            void 0 !== s &&
                                            null !== s &&
                                            s &&
                                            (!s || s.params) &&
                                            !s.destroyed
                                        ) {
                                            if (
                                                (l
                                                    ? (o.css(
                                                          "background-image",
                                                          'url("' + l + '")'
                                                      ),
                                                      o.removeAttr(
                                                          "data-background"
                                                      ))
                                                    : (h &&
                                                          (o.attr("srcset", h),
                                                          o.removeAttr(
                                                              "data-srcset"
                                                          )),
                                                      p &&
                                                          (o.attr("sizes", p),
                                                          o.removeAttr(
                                                              "data-sizes"
                                                          )),
                                                      d &&
                                                          (o.attr("src", d),
                                                          o.removeAttr(
                                                              "data-src"
                                                          ))),
                                                o
                                                    .addClass(a.loadedClass)
                                                    .removeClass(
                                                        a.loadingClass
                                                    ),
                                                r
                                                    .find(
                                                        "." + a.preloaderClass
                                                    )
                                                    .remove(),
                                                s.params.loop && i)
                                            ) {
                                                var e = r.attr(
                                                    "data-swiper-slide-index"
                                                );
                                                if (
                                                    r.hasClass(
                                                        s.params
                                                            .slideDuplicateClass
                                                    )
                                                ) {
                                                    var t =
                                                        s.$wrapperEl.children(
                                                            '[data-swiper-slide-index="' +
                                                                e +
                                                                '"]:not(.' +
                                                                s.params
                                                                    .slideDuplicateClass +
                                                                ")"
                                                        );
                                                    s.lazy.loadInSlide(
                                                        t.index(),
                                                        !1
                                                    );
                                                } else {
                                                    var n =
                                                        s.$wrapperEl.children(
                                                            "." +
                                                                s.params
                                                                    .slideDuplicateClass +
                                                                '[data-swiper-slide-index="' +
                                                                e +
                                                                '"]'
                                                        );
                                                    s.lazy.loadInSlide(
                                                        n.index(),
                                                        !1
                                                    );
                                                }
                                            }
                                            s.emit(
                                                "lazyImageReady",
                                                r[0],
                                                o[0]
                                            );
                                        }
                                    }
                                ),
                                    s.emit("lazyImageLoad", r[0], o[0]);
                            });
                }
            },
            load: function () {
                var e = this,
                    i = e.$wrapperEl,
                    s = e.params,
                    a = e.slides,
                    r = e.activeIndex,
                    n = e.virtual && s.virtual.enabled,
                    o = s.lazy,
                    l = s.slidesPerView;
                function d(e) {
                    if (n) {
                        if (
                            i.children(
                                "." +
                                    s.slideClass +
                                    '[data-swiper-slide-index="' +
                                    e +
                                    '"]'
                            ).length
                        )
                            return !0;
                    } else if (a[e]) return !0;
                    return !1;
                }
                function h(e) {
                    return n
                        ? t(e).attr("data-swiper-slide-index")
                        : t(e).index();
                }
                if (
                    ("auto" === l && (l = 0),
                    e.lazy.initialImageLoaded ||
                        (e.lazy.initialImageLoaded = !0),
                    e.params.watchSlidesVisibility)
                )
                    i.children("." + s.slideVisibleClass).each(function (i, s) {
                        var a = n
                            ? t(s).attr("data-swiper-slide-index")
                            : t(s).index();
                        e.lazy.loadInSlide(a);
                    });
                else if (l > 1)
                    for (var p = r; p < r + l; p += 1)
                        d(p) && e.lazy.loadInSlide(p);
                else e.lazy.loadInSlide(r);
                if (o.loadPrevNext)
                    if (
                        l > 1 ||
                        (o.loadPrevNextAmount && o.loadPrevNextAmount > 1)
                    ) {
                        for (
                            var c = o.loadPrevNextAmount,
                                u = l,
                                f = Math.min(r + u + Math.max(c, u), a.length),
                                v = Math.max(r - Math.max(u, c), 0),
                                m = r + l;
                            m < f;
                            m += 1
                        )
                            d(m) && e.lazy.loadInSlide(m);
                        for (var g = v; g < r; g += 1)
                            d(g) && e.lazy.loadInSlide(g);
                    } else {
                        var b = i.children("." + s.slideNextClass);
                        b.length > 0 && e.lazy.loadInSlide(h(b));
                        var w = i.children("." + s.slidePrevClass);
                        w.length > 0 && e.lazy.loadInSlide(h(w));
                    }
            },
        },
        K = {
            LinearSpline: function (e, t) {
                var i,
                    s,
                    a,
                    r,
                    n,
                    o = function (e, t) {
                        for (s = -1, i = e.length; i - s > 1; )
                            e[(a = (i + s) >> 1)] <= t ? (s = a) : (i = a);
                        return i;
                    };
                return (
                    (this.x = e),
                    (this.y = t),
                    (this.lastIndex = e.length - 1),
                    (this.interpolate = function (e) {
                        return e
                            ? ((n = o(this.x, e)),
                              (r = n - 1),
                              ((e - this.x[r]) * (this.y[n] - this.y[r])) /
                                  (this.x[n] - this.x[r]) +
                                  this.y[r])
                            : 0;
                    }),
                    this
                );
            },
            getInterpolateFunction: function (e) {
                this.controller.spline ||
                    (this.controller.spline = this.params.loop
                        ? new K.LinearSpline(this.slidesGrid, e.slidesGrid)
                        : new K.LinearSpline(this.snapGrid, e.snapGrid));
            },
            setTranslate: function (e, t) {
                var i,
                    s,
                    a = this,
                    r = a.controller.control;
                function n(e) {
                    var t =
                        e.rtl && "horizontal" === e.params.direction
                            ? -a.translate
                            : a.translate;
                    "slide" === a.params.controller.by &&
                        (a.controller.getInterpolateFunction(e),
                        (s = -a.controller.spline.interpolate(-t))),
                        (s && "container" !== a.params.controller.by) ||
                            ((i =
                                (e.maxTranslate() - e.minTranslate()) /
                                (a.maxTranslate() - a.minTranslate())),
                            (s =
                                (t - a.minTranslate()) * i + e.minTranslate())),
                        a.params.controller.inverse &&
                            (s = e.maxTranslate() - s),
                        e.updateProgress(s),
                        e.setTranslate(s, a),
                        e.updateActiveIndex(),
                        e.updateSlidesClasses();
                }
                if (Array.isArray(r))
                    for (var o = 0; o < r.length; o += 1)
                        r[o] !== t && r[o] instanceof k && n(r[o]);
                else r instanceof k && t !== r && n(r);
            },
            setTransition: function (e, t) {
                var i,
                    s = this,
                    a = s.controller.control;
                function r(t) {
                    t.setTransition(e, s),
                        0 !== e &&
                            (t.transitionStart(),
                            t.$wrapperEl.transitionEnd(function () {
                                a &&
                                    (t.params.loop &&
                                        "slide" === s.params.controller.by &&
                                        t.loopFix(),
                                    t.transitionEnd());
                            }));
                }
                if (Array.isArray(a))
                    for (i = 0; i < a.length; i += 1)
                        a[i] !== t && a[i] instanceof k && r(a[i]);
                else a instanceof k && t !== a && r(a);
            },
        },
        U = {
            makeElFocusable: function (e) {
                return e.attr("tabIndex", "0"), e;
            },
            addElRole: function (e, t) {
                return e.attr("role", t), e;
            },
            addElLabel: function (e, t) {
                return e.attr("aria-label", t), e;
            },
            disableEl: function (e) {
                return e.attr("aria-disabled", !0), e;
            },
            enableEl: function (e) {
                return e.attr("aria-disabled", !1), e;
            },
            onEnterKey: function (e) {
                var i = this.params.a11y;
                if (13 === e.keyCode) {
                    var s = t(e.target);
                    this.navigation &&
                        this.navigation.$nextEl &&
                        s.is(this.navigation.$nextEl) &&
                        ((this.isEnd && !this.params.loop) || this.slideNext(),
                        this.isEnd
                            ? this.a11y.notify(i.lastSlideMessage)
                            : this.a11y.notify(i.nextSlideMessage)),
                        this.navigation &&
                            this.navigation.$prevEl &&
                            s.is(this.navigation.$prevEl) &&
                            ((this.isBeginning && !this.params.loop) ||
                                this.slidePrev(),
                            this.isBeginning
                                ? this.a11y.notify(i.firstSlideMessage)
                                : this.a11y.notify(i.prevSlideMessage)),
                        this.pagination &&
                            s.is("." + this.params.pagination.bulletClass) &&
                            s[0].click();
                }
            },
            notify: function (e) {
                var t = this.a11y.liveRegion;
                0 !== t.length && (t.html(""), t.html(e));
            },
            updateNavigation: function () {
                if (!this.params.loop) {
                    var e = this.navigation,
                        t = e.$nextEl,
                        i = e.$prevEl;
                    i &&
                        i.length > 0 &&
                        (this.isBeginning
                            ? this.a11y.disableEl(i)
                            : this.a11y.enableEl(i)),
                        t &&
                            t.length > 0 &&
                            (this.isEnd
                                ? this.a11y.disableEl(t)
                                : this.a11y.enableEl(t));
                }
            },
            updatePagination: function () {
                var e = this,
                    i = e.params.a11y;
                e.pagination &&
                    e.params.pagination.clickable &&
                    e.pagination.bullets &&
                    e.pagination.bullets.length &&
                    e.pagination.bullets.each(function (s, a) {
                        var r = t(a);
                        e.a11y.makeElFocusable(r),
                            e.a11y.addElRole(r, "button"),
                            e.a11y.addElLabel(
                                r,
                                i.paginationBulletMessage.replace(
                                    /{{index}}/,
                                    r.index() + 1
                                )
                            );
                    });
            },
            init: function () {
                this.$el.append(this.a11y.liveRegion);
                var e,
                    t,
                    i = this.params.a11y;
                this.navigation &&
                    this.navigation.$nextEl &&
                    (e = this.navigation.$nextEl),
                    this.navigation &&
                        this.navigation.$prevEl &&
                        (t = this.navigation.$prevEl),
                    e &&
                        (this.a11y.makeElFocusable(e),
                        this.a11y.addElRole(e, "button"),
                        this.a11y.addElLabel(e, i.nextSlideMessage),
                        e.on("keydown", this.a11y.onEnterKey)),
                    t &&
                        (this.a11y.makeElFocusable(t),
                        this.a11y.addElRole(t, "button"),
                        this.a11y.addElLabel(t, i.prevSlideMessage),
                        t.on("keydown", this.a11y.onEnterKey)),
                    this.pagination &&
                        this.params.pagination.clickable &&
                        this.pagination.bullets &&
                        this.pagination.bullets.length &&
                        this.pagination.$el.on(
                            "keydown",
                            "." + this.params.pagination.bulletClass,
                            this.a11y.onEnterKey
                        );
            },
            destroy: function () {
                var e, t;
                this.a11y.liveRegion &&
                    this.a11y.liveRegion.length > 0 &&
                    this.a11y.liveRegion.remove(),
                    this.navigation &&
                        this.navigation.$nextEl &&
                        (e = this.navigation.$nextEl),
                    this.navigation &&
                        this.navigation.$prevEl &&
                        (t = this.navigation.$prevEl),
                    e && e.off("keydown", this.a11y.onEnterKey),
                    t && t.off("keydown", this.a11y.onEnterKey),
                    this.pagination &&
                        this.params.pagination.clickable &&
                        this.pagination.bullets &&
                        this.pagination.bullets.length &&
                        this.pagination.$el.off(
                            "keydown",
                            "." + this.params.pagination.bulletClass,
                            this.a11y.onEnterKey
                        );
            },
        },
        _ = {
            init: function () {
                if (this.params.history) {
                    if (!o.history || !o.history.pushState)
                        return (
                            (this.params.history.enabled = !1),
                            void (this.params.hashNavigation.enabled = !0)
                        );
                    var e = this.history;
                    (e.initialized = !0),
                        (e.paths = _.getPathValues()),
                        (e.paths.key || e.paths.value) &&
                            (e.scrollToSlide(
                                0,
                                e.paths.value,
                                this.params.runCallbacksOnInit
                            ),
                            this.params.history.replaceState ||
                                o.addEventListener(
                                    "popstate",
                                    this.history.setHistoryPopState
                                ));
                }
            },
            destroy: function () {
                this.params.history.replaceState ||
                    o.removeEventListener(
                        "popstate",
                        this.history.setHistoryPopState
                    );
            },
            setHistoryPopState: function () {
                (this.history.paths = _.getPathValues()),
                    this.history.scrollToSlide(
                        this.params.speed,
                        this.history.paths.value,
                        !1
                    );
            },
            getPathValues: function () {
                var e = o.location.pathname
                        .slice(1)
                        .split("/")
                        .filter(function (e) {
                            return "" !== e;
                        }),
                    t = e.length;
                return { key: e[t - 2], value: e[t - 1] };
            },
            setHistory: function (e, t) {
                if (this.history.initialized && this.params.history.enabled) {
                    var i = this.slides.eq(t),
                        s = _.slugify(i.attr("data-history"));
                    o.location.pathname.includes(e) || (s = e + "/" + s);
                    var a = o.history.state;
                    (a && a.value === s) ||
                        (this.params.history.replaceState
                            ? o.history.replaceState({ value: s }, null, s)
                            : o.history.pushState({ value: s }, null, s));
                }
            },
            slugify: function (e) {
                return e
                    .toString()
                    .toLowerCase()
                    .replace(/\s+/g, "-")
                    .replace(/[^\w-]+/g, "")
                    .replace(/--+/g, "-")
                    .replace(/^-+/, "")
                    .replace(/-+$/, "");
            },
            scrollToSlide: function (e, t, i) {
                if (t)
                    for (var s = 0, a = this.slides.length; s < a; s += 1) {
                        var r = this.slides.eq(s);
                        if (
                            _.slugify(r.attr("data-history")) === t &&
                            !r.hasClass(this.params.slideDuplicateClass)
                        ) {
                            var n = r.index();
                            this.slideTo(n, e, i);
                        }
                    }
                else this.slideTo(0, e, i);
            },
        },
        Z = {
            onHashCange: function () {
                var e = d.location.hash.replace("#", "");
                e !== this.slides.eq(this.activeIndex).attr("data-hash") &&
                    this.slideTo(
                        this.$wrapperEl
                            .children(
                                "." +
                                    this.params.slideClass +
                                    '[data-hash="' +
                                    e +
                                    '"]'
                            )
                            .index()
                    );
            },
            setHash: function () {
                if (
                    this.hashNavigation.initialized &&
                    this.params.hashNavigation.enabled
                )
                    if (
                        this.params.hashNavigation.replaceState &&
                        o.history &&
                        o.history.replaceState
                    )
                        o.history.replaceState(
                            null,
                            null,
                            "#" +
                                this.slides
                                    .eq(this.activeIndex)
                                    .attr("data-hash") || ""
                        );
                    else {
                        var e = this.slides.eq(this.activeIndex),
                            t = e.attr("data-hash") || e.attr("data-history");
                        d.location.hash = t || "";
                    }
            },
            init: function () {
                if (
                    !(
                        !this.params.hashNavigation.enabled ||
                        (this.params.history && this.params.history.enabled)
                    )
                ) {
                    this.hashNavigation.initialized = !0;
                    var e = d.location.hash.replace("#", "");
                    if (e)
                        for (var i = 0, s = this.slides.length; i < s; i += 1) {
                            var a = this.slides.eq(i);
                            if (
                                (a.attr("data-hash") ||
                                    a.attr("data-history")) === e &&
                                !a.hasClass(this.params.slideDuplicateClass)
                            ) {
                                var r = a.index();
                                this.slideTo(
                                    r,
                                    0,
                                    this.params.runCallbacksOnInit,
                                    !0
                                );
                            }
                        }
                    this.params.hashNavigation.watchState &&
                        t(o).on("hashchange", this.hashNavigation.onHashCange);
                }
            },
            destroy: function () {
                this.params.hashNavigation.watchState &&
                    t(o).off("hashchange", this.hashNavigation.onHashCange);
            },
        },
        Q = {
            run: function () {
                var e = this,
                    t = e.slides.eq(e.activeIndex),
                    i = e.params.autoplay.delay;
                t.attr("data-swiper-autoplay") &&
                    (i =
                        t.attr("data-swiper-autoplay") ||
                        e.params.autoplay.delay),
                    (e.autoplay.timeout = l.nextTick(function () {
                        e.params.autoplay.reverseDirection
                            ? e.params.loop
                                ? (e.loopFix(),
                                  e.slidePrev(e.params.speed, !0, !0),
                                  e.emit("autoplay"))
                                : e.isBeginning
                                ? e.params.autoplay.stopOnLastSlide
                                    ? e.autoplay.stop()
                                    : (e.slideTo(
                                          e.slides.length - 1,
                                          e.params.speed,
                                          !0,
                                          !0
                                      ),
                                      e.emit("autoplay"))
                                : (e.slidePrev(e.params.speed, !0, !0),
                                  e.emit("autoplay"))
                            : e.params.loop
                            ? (e.loopFix(),
                              e.slideNext(e.params.speed, !0, !0),
                              e.emit("autoplay"))
                            : e.isEnd
                            ? e.params.autoplay.stopOnLastSlide
                                ? e.autoplay.stop()
                                : (e.slideTo(0, e.params.speed, !0, !0),
                                  e.emit("autoplay"))
                            : (e.slideNext(e.params.speed, !0, !0),
                              e.emit("autoplay"));
                    }, i));
            },
            start: function () {
                return (
                    void 0 === this.autoplay.timeout &&
                    !this.autoplay.running &&
                    ((this.autoplay.running = !0),
                    this.emit("autoplayStart"),
                    this.autoplay.run(),
                    !0)
                );
            },
            stop: function () {
                return (
                    !!this.autoplay.running &&
                    void 0 !== this.autoplay.timeout &&
                    (this.autoplay.timeout &&
                        (clearTimeout(this.autoplay.timeout),
                        (this.autoplay.timeout = void 0)),
                    (this.autoplay.running = !1),
                    this.emit("autoplayStop"),
                    !0)
                );
            },
            pause: function (e) {
                var t = this;
                t.autoplay.running &&
                    (t.autoplay.paused ||
                        (t.autoplay.timeout && clearTimeout(t.autoplay.timeout),
                        (t.autoplay.paused = !0),
                        0 !== e && t.params.autoplay.waitForTransition
                            ? t.$wrapperEl.transitionEnd(function () {
                                  t &&
                                      !t.destroyed &&
                                      ((t.autoplay.paused = !1),
                                      t.autoplay.running
                                          ? t.autoplay.run()
                                          : t.autoplay.stop());
                              })
                            : ((t.autoplay.paused = !1), t.autoplay.run())));
            },
        },
        J = {
            setTranslate: function () {
                for (var e = this.slides, t = 0; t < e.length; t += 1) {
                    var i = this.slides.eq(t),
                        s = -i[0].swiperSlideOffset;
                    this.params.virtualTranslate || (s -= this.translate);
                    var a = 0;
                    this.isHorizontal() || ((a = s), (s = 0));
                    var r = this.params.fadeEffect.crossFade
                        ? Math.max(1 - Math.abs(i[0].progress), 0)
                        : 1 + Math.min(Math.max(i[0].progress, -1), 0);
                    i.css({ opacity: r }).transform(
                        "translate3d(" + s + "px, " + a + "px, 0px)"
                    );
                }
            },
            setTransition: function (e) {
                var t = this,
                    i = t.slides,
                    s = t.$wrapperEl;
                if ((i.transition(e), t.params.virtualTranslate && 0 !== e)) {
                    var a = !1;
                    i.transitionEnd(function () {
                        if (!a && t && !t.destroyed) {
                            (a = !0), (t.animating = !1);
                            for (
                                var e = [
                                        "webkitTransitionEnd",
                                        "transitionend",
                                    ],
                                    i = 0;
                                i < e.length;
                                i += 1
                            )
                                s.trigger(e[i]);
                        }
                    });
                }
            },
        },
        ee = {
            setTranslate: function () {
                var e,
                    i = this.$el,
                    s = this.$wrapperEl,
                    a = this.slides,
                    r = this.width,
                    n = this.height,
                    o = this.rtl,
                    l = this.size,
                    d = this.params.cubeEffect,
                    h = this.isHorizontal(),
                    p = this.virtual && this.params.virtual.enabled,
                    c = 0;
                d.shadow &&
                    (h
                        ? (0 === (e = s.find(".swiper-cube-shadow")).length &&
                              ((e = t(
                                  '<div class="swiper-cube-shadow"></div>'
                              )),
                              s.append(e)),
                          e.css({ height: r + "px" }))
                        : 0 === (e = i.find(".swiper-cube-shadow")).length &&
                          ((e = t('<div class="swiper-cube-shadow"></div>')),
                          i.append(e)));
                for (var u = 0; u < a.length; u += 1) {
                    var f = a.eq(u),
                        v = u;
                    p && (v = parseInt(f.attr("data-swiper-slide-index"), 10));
                    var m = 90 * v,
                        g = Math.floor(m / 360);
                    o && ((m = -m), (g = Math.floor(-m / 360)));
                    var b = Math.max(Math.min(f[0].progress, 1), -1),
                        w = 0,
                        y = 0,
                        x = 0;
                    v % 4 == 0
                        ? ((w = 4 * -g * l), (x = 0))
                        : (v - 1) % 4 == 0
                        ? ((w = 0), (x = 4 * -g * l))
                        : (v - 2) % 4 == 0
                        ? ((w = l + 4 * g * l), (x = l))
                        : (v - 3) % 4 == 0 &&
                          ((w = -l), (x = 3 * l + 4 * l * g)),
                        o && (w = -w),
                        h || ((y = w), (w = 0));
                    var T =
                        "rotateX(" +
                        (h ? 0 : -m) +
                        "deg) rotateY(" +
                        (h ? m : 0) +
                        "deg) translate3d(" +
                        w +
                        "px, " +
                        y +
                        "px, " +
                        x +
                        "px)";
                    if (
                        (b <= 1 &&
                            b > -1 &&
                            ((c = 90 * v + 90 * b),
                            o && (c = 90 * -v - 90 * b)),
                        f.transform(T),
                        d.slideShadows)
                    ) {
                        var E = h
                                ? f.find(".swiper-slide-shadow-left")
                                : f.find(".swiper-slide-shadow-top"),
                            S = h
                                ? f.find(".swiper-slide-shadow-right")
                                : f.find(".swiper-slide-shadow-bottom");
                        0 === E.length &&
                            ((E = t(
                                '<div class="swiper-slide-shadow-' +
                                    (h ? "left" : "top") +
                                    '"></div>'
                            )),
                            f.append(E)),
                            0 === S.length &&
                                ((S = t(
                                    '<div class="swiper-slide-shadow-' +
                                        (h ? "right" : "bottom") +
                                        '"></div>'
                                )),
                                f.append(S)),
                            E.length && (E[0].style.opacity = Math.max(-b, 0)),
                            S.length && (S[0].style.opacity = Math.max(b, 0));
                    }
                }
                if (
                    (s.css({
                        "-webkit-transform-origin": "50% 50% -" + l / 2 + "px",
                        "-moz-transform-origin": "50% 50% -" + l / 2 + "px",
                        "-ms-transform-origin": "50% 50% -" + l / 2 + "px",
                        "transform-origin": "50% 50% -" + l / 2 + "px",
                    }),
                    d.shadow)
                )
                    if (h)
                        e.transform(
                            "translate3d(0px, " +
                                (r / 2 + d.shadowOffset) +
                                "px, " +
                                -r / 2 +
                                "px) rotateX(90deg) rotateZ(0deg) scale(" +
                                d.shadowScale +
                                ")"
                        );
                    else {
                        var C = Math.abs(c) - 90 * Math.floor(Math.abs(c) / 90),
                            M =
                                1.5 -
                                (Math.sin((2 * C * Math.PI) / 360) / 2 +
                                    Math.cos((2 * C * Math.PI) / 360) / 2),
                            z = d.shadowScale,
                            P = d.shadowScale / M,
                            k = d.shadowOffset;
                        e.transform(
                            "scale3d(" +
                                z +
                                ", 1, " +
                                P +
                                ") translate3d(0px, " +
                                (n / 2 + k) +
                                "px, " +
                                -n / 2 / P +
                                "px) rotateX(-90deg)"
                        );
                    }
                var $ = I.isSafari || I.isUiWebView ? -l / 2 : 0;
                s.transform(
                    "translate3d(0px,0," +
                        $ +
                        "px) rotateX(" +
                        (this.isHorizontal() ? 0 : c) +
                        "deg) rotateY(" +
                        (this.isHorizontal() ? -c : 0) +
                        "deg)"
                );
            },
            setTransition: function (e) {
                var t = this.$el;
                this.slides
                    .transition(e)
                    .find(
                        ".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left"
                    )
                    .transition(e),
                    this.params.cubeEffect.shadow &&
                        !this.isHorizontal() &&
                        t.find(".swiper-cube-shadow").transition(e);
            },
        },
        te = {
            setTranslate: function () {
                for (var e = this.slides, i = 0; i < e.length; i += 1) {
                    var s = e.eq(i),
                        a = s[0].progress;
                    this.params.flipEffect.limitRotation &&
                        (a = Math.max(Math.min(s[0].progress, 1), -1));
                    var r = -180 * a,
                        n = 0,
                        o = -s[0].swiperSlideOffset,
                        l = 0;
                    if (
                        (this.isHorizontal()
                            ? this.rtl && (r = -r)
                            : ((l = o), (o = 0), (n = -r), (r = 0)),
                        (s[0].style.zIndex =
                            -Math.abs(Math.round(a)) + e.length),
                        this.params.flipEffect.slideShadows)
                    ) {
                        var d = this.isHorizontal()
                                ? s.find(".swiper-slide-shadow-left")
                                : s.find(".swiper-slide-shadow-top"),
                            h = this.isHorizontal()
                                ? s.find(".swiper-slide-shadow-right")
                                : s.find(".swiper-slide-shadow-bottom");
                        0 === d.length &&
                            ((d = t(
                                '<div class="swiper-slide-shadow-' +
                                    (this.isHorizontal() ? "left" : "top") +
                                    '"></div>'
                            )),
                            s.append(d)),
                            0 === h.length &&
                                ((h = t(
                                    '<div class="swiper-slide-shadow-' +
                                        (this.isHorizontal()
                                            ? "right"
                                            : "bottom") +
                                        '"></div>'
                                )),
                                s.append(h)),
                            d.length && (d[0].style.opacity = Math.max(-a, 0)),
                            h.length && (h[0].style.opacity = Math.max(a, 0));
                    }
                    s.transform(
                        "translate3d(" +
                            o +
                            "px, " +
                            l +
                            "px, 0px) rotateX(" +
                            n +
                            "deg) rotateY(" +
                            r +
                            "deg)"
                    );
                }
            },
            setTransition: function (e) {
                var t = this,
                    i = t.slides,
                    s = t.activeIndex,
                    a = t.$wrapperEl;
                if (
                    (i
                        .transition(e)
                        .find(
                            ".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left"
                        )
                        .transition(e),
                    t.params.virtualTranslate && 0 !== e)
                ) {
                    var r = !1;
                    i.eq(s).transitionEnd(function () {
                        if (!r && t && !t.destroyed) {
                            (r = !0), (t.animating = !1);
                            for (
                                var e = [
                                        "webkitTransitionEnd",
                                        "transitionend",
                                    ],
                                    i = 0;
                                i < e.length;
                                i += 1
                            )
                                a.trigger(e[i]);
                        }
                    });
                }
            },
        },
        ie = {
            setTranslate: function () {
                for (
                    var e = this.width,
                        i = this.height,
                        s = this.slides,
                        a = this.$wrapperEl,
                        r = this.slidesSizesGrid,
                        n = this.params.coverflowEffect,
                        o = this.isHorizontal(),
                        l = this.translate,
                        d = o ? e / 2 - l : i / 2 - l,
                        p = o ? n.rotate : -n.rotate,
                        c = n.depth,
                        u = 0,
                        f = s.length;
                    u < f;
                    u += 1
                ) {
                    var v = s.eq(u),
                        m = r[u],
                        g =
                            ((d - v[0].swiperSlideOffset - m / 2) / m) *
                            n.modifier,
                        b = o ? p * g : 0,
                        w = o ? 0 : p * g,
                        y = -c * Math.abs(g),
                        x = o ? 0 : n.stretch * g,
                        T = o ? n.stretch * g : 0;
                    Math.abs(T) < 0.001 && (T = 0),
                        Math.abs(x) < 0.001 && (x = 0),
                        Math.abs(y) < 0.001 && (y = 0),
                        Math.abs(b) < 0.001 && (b = 0),
                        Math.abs(w) < 0.001 && (w = 0);
                    var E =
                        "translate3d(" +
                        T +
                        "px," +
                        x +
                        "px," +
                        y +
                        "px)  rotateX(" +
                        w +
                        "deg) rotateY(" +
                        b +
                        "deg)";
                    if (
                        (v.transform(E),
                        (v[0].style.zIndex = 1 - Math.abs(Math.round(g))),
                        n.slideShadows)
                    ) {
                        var S = o
                                ? v.find(".swiper-slide-shadow-left")
                                : v.find(".swiper-slide-shadow-top"),
                            C = o
                                ? v.find(".swiper-slide-shadow-right")
                                : v.find(".swiper-slide-shadow-bottom");
                        0 === S.length &&
                            ((S = t(
                                '<div class="swiper-slide-shadow-' +
                                    (o ? "left" : "top") +
                                    '"></div>'
                            )),
                            v.append(S)),
                            0 === C.length &&
                                ((C = t(
                                    '<div class="swiper-slide-shadow-' +
                                        (o ? "right" : "bottom") +
                                        '"></div>'
                                )),
                                v.append(C)),
                            S.length && (S[0].style.opacity = g > 0 ? g : 0),
                            C.length && (C[0].style.opacity = -g > 0 ? -g : 0);
                    }
                }
                (h.pointerEvents || h.prefixedPointerEvents) &&
                    (a[0].style.perspectiveOrigin = d + "px 50%");
            },
            setTransition: function (e) {
                this.slides
                    .transition(e)
                    .find(
                        ".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left"
                    )
                    .transition(e);
            },
        },
        se = [
            $,
            L,
            D,
            O,
            H,
            X,
            G,
            {
                name: "mousewheel",
                params: {
                    mousewheel: {
                        enabled: !1,
                        releaseOnEdges: !1,
                        invert: !1,
                        forceToAxis: !1,
                        sensitivity: 1,
                        eventsTarged: "container",
                    },
                },
                create: function () {
                    l.extend(this, {
                        mousewheel: {
                            enabled: !1,
                            enable: B.enable.bind(this),
                            disable: B.disable.bind(this),
                            handle: B.handle.bind(this),
                            lastScrollTime: l.now(),
                        },
                    });
                },
                on: {
                    init: function () {
                        this.params.mousewheel.enabled &&
                            this.mousewheel.enable();
                    },
                    destroy: function () {
                        this.mousewheel.enabled && this.mousewheel.disable();
                    },
                },
            },
            {
                name: "navigation",
                params: {
                    navigation: {
                        nextEl: null,
                        prevEl: null,
                        hideOnClick: !1,
                        disabledClass: "swiper-button-disabled",
                        hiddenClass: "swiper-button-hidden",
                        lockClass: "swiper-button-lock",
                    },
                },
                create: function () {
                    l.extend(this, {
                        navigation: {
                            init: V.init.bind(this),
                            update: V.update.bind(this),
                            destroy: V.destroy.bind(this),
                        },
                    });
                },
                on: {
                    init: function () {
                        this.navigation.init(), this.navigation.update();
                    },
                    toEdge: function () {
                        this.navigation.update();
                    },
                    fromEdge: function () {
                        this.navigation.update();
                    },
                    destroy: function () {
                        this.navigation.destroy();
                    },
                    click: function (e) {
                        var i = this.navigation,
                            s = i.$nextEl,
                            a = i.$prevEl;
                        !this.params.navigation.hideOnClick ||
                            t(e.target).is(a) ||
                            t(e.target).is(s) ||
                            (s &&
                                s.toggleClass(
                                    this.params.navigation.hiddenClass
                                ),
                            a &&
                                a.toggleClass(
                                    this.params.navigation.hiddenClass
                                ));
                    },
                },
            },
            {
                name: "pagination",
                params: {
                    pagination: {
                        el: null,
                        bulletElement: "span",
                        clickable: !1,
                        hideOnClick: !1,
                        renderBullet: null,
                        renderProgressbar: null,
                        renderFraction: null,
                        renderCustom: null,
                        type: "bullets",
                        dynamicBullets: !1,
                        bulletClass: "swiper-pagination-bullet",
                        bulletActiveClass: "swiper-pagination-bullet-active",
                        modifierClass: "swiper-pagination-",
                        currentClass: "swiper-pagination-current",
                        totalClass: "swiper-pagination-total",
                        hiddenClass: "swiper-pagination-hidden",
                        progressbarFillClass:
                            "swiper-pagination-progressbar-fill",
                        clickableClass: "swiper-pagination-clickable",
                        lockClass: "swiper-pagination-lock",
                    },
                },
                create: function () {
                    l.extend(this, {
                        pagination: {
                            init: R.init.bind(this),
                            render: R.render.bind(this),
                            update: R.update.bind(this),
                            destroy: R.destroy.bind(this),
                        },
                    });
                },
                on: {
                    init: function () {
                        this.pagination.init(),
                            this.pagination.render(),
                            this.pagination.update();
                    },
                    activeIndexChange: function () {
                        this.params.loop
                            ? this.pagination.update()
                            : void 0 === this.snapIndex &&
                              this.pagination.update();
                    },
                    snapIndexChange: function () {
                        this.params.loop || this.pagination.update();
                    },
                    slidesLengthChange: function () {
                        this.params.loop &&
                            (this.pagination.render(),
                            this.pagination.update());
                    },
                    snapGridLengthChange: function () {
                        this.params.loop ||
                            (this.pagination.render(),
                            this.pagination.update());
                    },
                    destroy: function () {
                        this.pagination.destroy();
                    },
                    click: function (e) {
                        this.params.pagination.el &&
                            this.params.pagination.hideOnClick &&
                            this.pagination.$el.length > 0 &&
                            !t(e.target).hasClass(
                                this.params.pagination.bulletClass
                            ) &&
                            this.pagination.$el.toggleClass(
                                this.params.pagination.hiddenClass
                            );
                    },
                },
            },
            {
                name: "scrollbar",
                params: {
                    scrollbar: {
                        el: null,
                        dragSize: "auto",
                        hide: !1,
                        draggable: !1,
                        snapOnRelease: !0,
                        lockClass: "swiper-scrollbar-lock",
                    },
                },
                create: function () {
                    l.extend(this, {
                        scrollbar: {
                            init: F.init.bind(this),
                            destroy: F.destroy.bind(this),
                            updateSize: F.updateSize.bind(this),
                            setTranslate: F.setTranslate.bind(this),
                            setTransition: F.setTransition.bind(this),
                            enableDraggable: F.enableDraggable.bind(this),
                            disableDraggable: F.disableDraggable.bind(this),
                            setDragPosition: F.setDragPosition.bind(this),
                            onDragStart: F.onDragStart.bind(this),
                            onDragMove: F.onDragMove.bind(this),
                            onDragEnd: F.onDragEnd.bind(this),
                            isTouched: !1,
                            timeout: null,
                            dragTimeout: null,
                        },
                    });
                },
                on: {
                    init: function () {
                        this.scrollbar.init(),
                            this.scrollbar.updateSize(),
                            this.scrollbar.setTranslate();
                    },
                    update: function () {
                        this.scrollbar.updateSize();
                    },
                    resize: function () {
                        this.scrollbar.updateSize();
                    },
                    observerUpdate: function () {
                        this.scrollbar.updateSize();
                    },
                    setTranslate: function () {
                        this.scrollbar.setTranslate();
                    },
                    setTransition: function (e) {
                        this.scrollbar.setTransition(e);
                    },
                    destroy: function () {
                        this.scrollbar.destroy();
                    },
                },
            },
            {
                name: "parallax",
                params: { parallax: { enabled: !1 } },
                create: function () {
                    l.extend(this, {
                        parallax: {
                            setTransform: W.setTransform.bind(this),
                            setTranslate: W.setTranslate.bind(this),
                            setTransition: W.setTransition.bind(this),
                        },
                    });
                },
                on: {
                    beforeInit: function () {
                        this.params.watchSlidesProgress = !0;
                    },
                    init: function () {
                        this.params.parallax && this.parallax.setTranslate();
                    },
                    setTranslate: function () {
                        this.params.parallax && this.parallax.setTranslate();
                    },
                    setTransition: function (e) {
                        this.params.parallax && this.parallax.setTransition(e);
                    },
                },
            },
            {
                name: "zoom",
                params: {
                    zoom: {
                        enabled: !1,
                        maxRatio: 3,
                        minRatio: 1,
                        toggle: !0,
                        containerClass: "swiper-zoom-container",
                        zoomedSlideClass: "swiper-slide-zoomed",
                    },
                },
                create: function () {
                    var e = this,
                        t = {
                            enabled: !1,
                            scale: 1,
                            currentScale: 1,
                            isScaling: !1,
                            gesture: {
                                $slideEl: void 0,
                                slideWidth: void 0,
                                slideHeight: void 0,
                                $imageEl: void 0,
                                $imageWrapEl: void 0,
                                maxRatio: 3,
                            },
                            image: {
                                isTouched: void 0,
                                isMoved: void 0,
                                currentX: void 0,
                                currentY: void 0,
                                minX: void 0,
                                minY: void 0,
                                maxX: void 0,
                                maxY: void 0,
                                width: void 0,
                                height: void 0,
                                startX: void 0,
                                startY: void 0,
                                touchesStart: {},
                                touchesCurrent: {},
                            },
                            velocity: {
                                x: void 0,
                                y: void 0,
                                prevPositionX: void 0,
                                prevPositionY: void 0,
                                prevTime: void 0,
                            },
                        };
                    "onGestureStart onGestureChange onGestureEnd onTouchStart onTouchMove onTouchEnd onTransitionEnd toggle enable disable in out"
                        .split(" ")
                        .forEach(function (i) {
                            t[i] = j[i].bind(e);
                        }),
                        l.extend(e, { zoom: t });
                },
                on: {
                    init: function () {
                        this.params.zoom.enabled && this.zoom.enable();
                    },
                    destroy: function () {
                        this.zoom.disable();
                    },
                    touchStart: function (e) {
                        this.zoom.enabled && this.zoom.onTouchStart(e);
                    },
                    touchEnd: function (e) {
                        this.zoom.enabled && this.zoom.onTouchEnd(e);
                    },
                    doubleTap: function (e) {
                        this.params.zoom.enabled &&
                            this.zoom.enabled &&
                            this.params.zoom.toggle &&
                            this.zoom.toggle(e);
                    },
                    transitionEnd: function () {
                        this.zoom.enabled &&
                            this.params.zoom.enabled &&
                            this.zoom.onTransitionEnd();
                    },
                },
            },
            {
                name: "lazy",
                params: {
                    lazy: {
                        enabled: !1,
                        loadPrevNext: !1,
                        loadPrevNextAmount: 1,
                        loadOnTransitionStart: !1,
                        elementClass: "swiper-lazy",
                        loadingClass: "swiper-lazy-loading",
                        loadedClass: "swiper-lazy-loaded",
                        preloaderClass: "swiper-lazy-preloader",
                    },
                },
                create: function () {
                    l.extend(this, {
                        lazy: {
                            initialImageLoaded: !1,
                            load: q.load.bind(this),
                            loadInSlide: q.loadInSlide.bind(this),
                        },
                    });
                },
                on: {
                    beforeInit: function () {
                        this.params.lazy.enabled &&
                            this.params.preloadImages &&
                            (this.params.preloadImages = !1);
                    },
                    init: function () {
                        this.params.lazy.enabled &&
                            !this.params.loop &&
                            0 === this.params.initialSlide &&
                            this.lazy.load();
                    },
                    scroll: function () {
                        this.params.freeMode &&
                            !this.params.freeModeSticky &&
                            this.lazy.load();
                    },
                    resize: function () {
                        this.params.lazy.enabled && this.lazy.load();
                    },
                    scrollbarDragMove: function () {
                        this.params.lazy.enabled && this.lazy.load();
                    },
                    transitionStart: function () {
                        this.params.lazy.enabled &&
                            (this.params.lazy.loadOnTransitionStart ||
                                (!this.params.lazy.loadOnTransitionStart &&
                                    !this.lazy.initialImageLoaded)) &&
                            this.lazy.load();
                    },
                    transitionEnd: function () {
                        this.params.lazy.enabled &&
                            !this.params.lazy.loadOnTransitionStart &&
                            this.lazy.load();
                    },
                },
            },
            {
                name: "controller",
                params: {
                    controller: { control: void 0, inverse: !1, by: "slide" },
                },
                create: function () {
                    l.extend(this, {
                        controller: {
                            control: this.params.controller.control,
                            getInterpolateFunction:
                                K.getInterpolateFunction.bind(this),
                            setTranslate: K.setTranslate.bind(this),
                            setTransition: K.setTransition.bind(this),
                        },
                    });
                },
                on: {
                    update: function () {
                        this.controller.control &&
                            this.controller.spline &&
                            ((this.controller.spline = void 0),
                            delete this.controller.spline);
                    },
                    resize: function () {
                        this.controller.control &&
                            this.controller.spline &&
                            ((this.controller.spline = void 0),
                            delete this.controller.spline);
                    },
                    observerUpdate: function () {
                        this.controller.control &&
                            this.controller.spline &&
                            ((this.controller.spline = void 0),
                            delete this.controller.spline);
                    },
                    setTranslate: function (e, t) {
                        this.controller.control &&
                            this.controller.setTranslate(e, t);
                    },
                    setTransition: function (e, t) {
                        this.controller.control &&
                            this.controller.setTransition(e, t);
                    },
                },
            },
            {
                name: "a11y",
                params: {
                    a11y: {
                        enabled: !1,
                        notificationClass: "swiper-notification",
                        prevSlideMessage: "Previous slide",
                        nextSlideMessage: "Next slide",
                        firstSlideMessage: "This is the first slide",
                        lastSlideMessage: "This is the last slide",
                        paginationBulletMessage: "Go to slide {{index}}",
                    },
                },
                create: function () {
                    var e = this;
                    l.extend(e, {
                        a11y: {
                            liveRegion: t(
                                '<span class="' +
                                    e.params.a11y.notificationClass +
                                    '" aria-live="assertive" aria-atomic="true"></span>'
                            ),
                        },
                    }),
                        Object.keys(U).forEach(function (t) {
                            e.a11y[t] = U[t].bind(e);
                        });
                },
                on: {
                    init: function () {
                        this.params.a11y.enabled &&
                            (this.a11y.init(), this.a11y.updateNavigation());
                    },
                    toEdge: function () {
                        this.params.a11y.enabled &&
                            this.a11y.updateNavigation();
                    },
                    fromEdge: function () {
                        this.params.a11y.enabled &&
                            this.a11y.updateNavigation();
                    },
                    paginationUpdate: function () {
                        this.params.a11y.enabled &&
                            this.a11y.updatePagination();
                    },
                    destroy: function () {
                        this.params.a11y.enabled && this.a11y.destroy();
                    },
                },
            },
            {
                name: "history",
                params: {
                    history: { enabled: !1, replaceState: !1, key: "slides" },
                },
                create: function () {
                    l.extend(this, {
                        history: {
                            init: _.init.bind(this),
                            setHistory: _.setHistory.bind(this),
                            setHistoryPopState: _.setHistoryPopState.bind(this),
                            scrollToSlide: _.scrollToSlide.bind(this),
                            destroy: _.destroy.bind(this),
                        },
                    });
                },
                on: {
                    init: function () {
                        this.params.history.enabled && this.history.init();
                    },
                    destroy: function () {
                        this.params.history.enabled && this.history.destroy();
                    },
                    transitionEnd: function () {
                        this.history.initialized &&
                            this.history.setHistory(
                                this.params.history.key,
                                this.activeIndex
                            );
                    },
                },
            },
            {
                name: "hash-navigation",
                params: {
                    hashNavigation: {
                        enabled: !1,
                        replaceState: !1,
                        watchState: !1,
                    },
                },
                create: function () {
                    l.extend(this, {
                        hashNavigation: {
                            initialized: !1,
                            init: Z.init.bind(this),
                            destroy: Z.destroy.bind(this),
                            setHash: Z.setHash.bind(this),
                            onHashCange: Z.onHashCange.bind(this),
                        },
                    });
                },
                on: {
                    init: function () {
                        this.params.hashNavigation.enabled &&
                            this.hashNavigation.init();
                    },
                    destroy: function () {
                        this.params.hashNavigation.enabled &&
                            this.hashNavigation.destroy();
                    },
                    transitionEnd: function () {
                        this.hashNavigation.initialized &&
                            this.hashNavigation.setHash();
                    },
                },
            },
            {
                name: "autoplay",
                params: {
                    autoplay: {
                        enabled: !1,
                        delay: 3e3,
                        waitForTransition: !0,
                        disableOnInteraction: !0,
                        stopOnLastSlide: !1,
                        reverseDirection: !1,
                    },
                },
                create: function () {
                    l.extend(this, {
                        autoplay: {
                            running: !1,
                            paused: !1,
                            run: Q.run.bind(this),
                            start: Q.start.bind(this),
                            stop: Q.stop.bind(this),
                            pause: Q.pause.bind(this),
                        },
                    });
                },
                on: {
                    init: function () {
                        this.params.autoplay.enabled && this.autoplay.start();
                    },
                    beforeTransitionStart: function (e, t) {
                        this.autoplay.running &&
                            (t || !this.params.autoplay.disableOnInteraction
                                ? this.autoplay.pause(e)
                                : this.autoplay.stop());
                    },
                    sliderFirstMove: function () {
                        this.autoplay.running &&
                            (this.params.autoplay.disableOnInteraction
                                ? this.autoplay.stop()
                                : this.autoplay.pause());
                    },
                    destroy: function () {
                        this.autoplay.running && this.autoplay.stop();
                    },
                },
            },
            {
                name: "effect-fade",
                params: { fadeEffect: { crossFade: !1 } },
                create: function () {
                    l.extend(this, {
                        fadeEffect: {
                            setTranslate: J.setTranslate.bind(this),
                            setTransition: J.setTransition.bind(this),
                        },
                    });
                },
                on: {
                    beforeInit: function () {
                        if ("fade" === this.params.effect) {
                            this.classNames.push(
                                this.params.containerModifierClass + "fade"
                            );
                            var e = {
                                slidesPerView: 1,
                                slidesPerColumn: 1,
                                slidesPerGroup: 1,
                                watchSlidesProgress: !0,
                                spaceBetween: 0,
                                virtualTranslate: !0,
                            };
                            l.extend(this.params, e),
                                l.extend(this.originalParams, e);
                        }
                    },
                    setTranslate: function () {
                        "fade" === this.params.effect &&
                            this.fadeEffect.setTranslate();
                    },
                    setTransition: function (e) {
                        "fade" === this.params.effect &&
                            this.fadeEffect.setTransition(e);
                    },
                },
            },
            {
                name: "effect-cube",
                params: {
                    cubeEffect: {
                        slideShadows: !0,
                        shadow: !0,
                        shadowOffset: 20,
                        shadowScale: 0.94,
                    },
                },
                create: function () {
                    l.extend(this, {
                        cubeEffect: {
                            setTranslate: ee.setTranslate.bind(this),
                            setTransition: ee.setTransition.bind(this),
                        },
                    });
                },
                on: {
                    beforeInit: function () {
                        if ("cube" === this.params.effect) {
                            this.classNames.push(
                                this.params.containerModifierClass + "cube"
                            ),
                                this.classNames.push(
                                    this.params.containerModifierClass + "3d"
                                );
                            var e = {
                                slidesPerView: 1,
                                slidesPerColumn: 1,
                                slidesPerGroup: 1,
                                watchSlidesProgress: !0,
                                resistanceRatio: 0,
                                spaceBetween: 0,
                                centeredSlides: !1,
                                virtualTranslate: !0,
                            };
                            l.extend(this.params, e),
                                l.extend(this.originalParams, e);
                        }
                    },
                    setTranslate: function () {
                        "cube" === this.params.effect &&
                            this.cubeEffect.setTranslate();
                    },
                    setTransition: function (e) {
                        "cube" === this.params.effect &&
                            this.cubeEffect.setTransition(e);
                    },
                },
            },
            {
                name: "effect-flip",
                params: { flipEffect: { slideShadows: !0, limitRotation: !0 } },
                create: function () {
                    l.extend(this, {
                        flipEffect: {
                            setTranslate: te.setTranslate.bind(this),
                            setTransition: te.setTransition.bind(this),
                        },
                    });
                },
                on: {
                    beforeInit: function () {
                        if ("flip" === this.params.effect) {
                            this.classNames.push(
                                this.params.containerModifierClass + "flip"
                            ),
                                this.classNames.push(
                                    this.params.containerModifierClass + "3d"
                                );
                            var e = {
                                slidesPerView: 1,
                                slidesPerColumn: 1,
                                slidesPerGroup: 1,
                                watchSlidesProgress: !0,
                                spaceBetween: 0,
                                virtualTranslate: !0,
                            };
                            l.extend(this.params, e),
                                l.extend(this.originalParams, e);
                        }
                    },
                    setTranslate: function () {
                        "flip" === this.params.effect &&
                            this.flipEffect.setTranslate();
                    },
                    setTransition: function (e) {
                        "flip" === this.params.effect &&
                            this.flipEffect.setTransition(e);
                    },
                },
            },
            {
                name: "effect-coverflow",
                params: {
                    coverflowEffect: {
                        rotate: 50,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        slideShadows: !0,
                    },
                },
                create: function () {
                    l.extend(this, {
                        coverflowEffect: {
                            setTranslate: ie.setTranslate.bind(this),
                            setTransition: ie.setTransition.bind(this),
                        },
                    });
                },
                on: {
                    beforeInit: function () {
                        "coverflow" === this.params.effect &&
                            (this.classNames.push(
                                this.params.containerModifierClass + "coverflow"
                            ),
                            this.classNames.push(
                                this.params.containerModifierClass + "3d"
                            ),
                            (this.params.watchSlidesProgress = !0),
                            (this.originalParams.watchSlidesProgress = !0));
                    },
                    setTranslate: function () {
                        "coverflow" === this.params.effect &&
                            this.coverflowEffect.setTranslate();
                    },
                    setTransition: function (e) {
                        "coverflow" === this.params.effect &&
                            this.coverflowEffect.setTransition(e);
                    },
                },
            },
        ];
    return (
        void 0 === k.use &&
            ((k.use = k.Class.use), (k.installModule = k.Class.installModule)),
        k.use(se),
        k
    );
});
