var markerClusterer = (function (t) {
    "use strict";
    function e(t, e) {
        var s = {};
        for (var r in t)
            Object.prototype.hasOwnProperty.call(t, r) &&
                e.indexOf(r) < 0 &&
                (s[r] = t[r]);
        if (null != t && "function" == typeof Object.getOwnPropertySymbols) {
            var o = 0;
            for (r = Object.getOwnPropertySymbols(t); o < r.length; o++)
                e.indexOf(r[o]) < 0 &&
                    Object.prototype.propertyIsEnumerable.call(t, r[o]) &&
                    (s[r[o]] = t[r[o]]);
        }
        return s;
    }
    class s {
        static isAdvancedMarkerAvailable(t) {
            return (
                google.maps.marker &&
                !0 === t.getMapCapabilities().isAdvancedMarkersAvailable
            );
        }
        static isAdvancedMarker(t) {
            return (
                google.maps.marker &&
                t instanceof google.maps.marker.AdvancedMarkerElement
            );
        }
        static setMap(t, e) {
            this.isAdvancedMarker(t) ? (t.map = e) : t.setMap(e);
        }
        static getPosition(t) {
            if (this.isAdvancedMarker(t)) {
                if (t.position) {
                    if (t.position instanceof google.maps.LatLng)
                        return t.position;
                    if (t.position.lat && t.position.lng)
                        return new google.maps.LatLng(
                            t.position.lat,
                            t.position.lng
                        );
                }
                return new google.maps.LatLng(null);
            }
            return t.getPosition();
        }
        static getVisible(t) {
            return !!this.isAdvancedMarker(t) || t.getVisible();
        }
    }
    class r {
        constructor(t) {
            let { markers: e, position: s } = t;
            (this.markers = e),
                s &&
                    (s instanceof google.maps.LatLng
                        ? (this._position = s)
                        : (this._position = new google.maps.LatLng(s)));
        }
        get bounds() {
            if (0 === this.markers.length && !this._position) return;
            const t = new google.maps.LatLngBounds(
                this._position,
                this._position
            );
            for (const e of this.markers) t.extend(s.getPosition(e));
            return t;
        }
        get position() {
            return this._position || this.bounds.getCenter();
        }
        get count() {
            return this.markers.filter((t) => s.getVisible(t)).length;
        }
        push(t) {
            this.markers.push(t);
        }
        delete() {
            this.marker &&
                (s.setMap(this.marker, null), (this.marker = void 0)),
                (this.markers.length = 0);
        }
    }
    const o = (t, e, r, o) => {
            const n = i(t.getBounds(), e, o);
            return r.filter((t) => n.contains(s.getPosition(t)));
        },
        i = (t, e, s) => {
            const { northEast: r, southWest: o } = h(t, e),
                i = l({ northEast: r, southWest: o }, s);
            return c(i, e);
        },
        n = (t, e, s) => {
            const r = i(t, e, s),
                o = r.getNorthEast(),
                n = r.getSouthWest();
            return [n.lng(), n.lat(), o.lng(), o.lat()];
        },
        a = (t, e) => {
            const s = ((e.lat - t.lat) * Math.PI) / 180,
                r = ((e.lng - t.lng) * Math.PI) / 180,
                o = Math.sin(s / 2),
                i = Math.sin(r / 2),
                n =
                    o * o +
                    Math.cos((t.lat * Math.PI) / 180) *
                        Math.cos((e.lat * Math.PI) / 180) *
                        i *
                        i;
            return 6371 * (2 * Math.atan2(Math.sqrt(n), Math.sqrt(1 - n)));
        },
        h = (t, e) => ({
            northEast: e.fromLatLngToDivPixel(t.getNorthEast()),
            southWest: e.fromLatLngToDivPixel(t.getSouthWest()),
        }),
        l = (t, e) => {
            let { northEast: s, southWest: r } = t;
            return (
                (s.x += e),
                (s.y -= e),
                (r.x -= e),
                (r.y += e),
                { northEast: s, southWest: r }
            );
        },
        c = (t, e) => {
            let { northEast: s, southWest: r } = t;
            const o = e.fromDivPixelToLatLng(r),
                i = e.fromDivPixelToLatLng(s);
            return new google.maps.LatLngBounds(o, i);
        };
    class u {
        constructor(t) {
            let { maxZoom: e = 16 } = t;
            this.maxZoom = e;
        }
        noop(t) {
            let { markers: e } = t;
            return m(e);
        }
    }
    class p extends u {
        constructor(t) {
            var { viewportPadding: s = 60 } = t;
            super(e(t, ["viewportPadding"])),
                (this.viewportPadding = 60),
                (this.viewportPadding = s);
        }
        calculate(t) {
            let { markers: e, map: s, mapCanvasProjection: r } = t;
            return s.getZoom() >= this.maxZoom
                ? { clusters: this.noop({ markers: e }), changed: !1 }
                : {
                      clusters: this.cluster({
                          markers: o(s, r, e, this.viewportPadding),
                          map: s,
                          mapCanvasProjection: r,
                      }),
                  };
        }
    }
    const m = (t) =>
        t.map((t) => new r({ position: s.getPosition(t), markers: [t] }));
    function d(t) {
        return t &&
            t.__esModule &&
            Object.prototype.hasOwnProperty.call(t, "default")
            ? t.default
            : t;
    }
    var g = function t(e, s) {
            if (e === s) return !0;
            if (e && s && "object" == typeof e && "object" == typeof s) {
                if (e.constructor !== s.constructor) return !1;
                var r, o, i;
                if (Array.isArray(e)) {
                    if ((r = e.length) != s.length) return !1;
                    for (o = r; 0 != o--; ) if (!t(e[o], s[o])) return !1;
                    return !0;
                }
                if (e.constructor === RegExp)
                    return e.source === s.source && e.flags === s.flags;
                if (e.valueOf !== Object.prototype.valueOf)
                    return e.valueOf() === s.valueOf();
                if (e.toString !== Object.prototype.toString)
                    return e.toString() === s.toString();
                if ((r = (i = Object.keys(e)).length) !== Object.keys(s).length)
                    return !1;
                for (o = r; 0 != o--; )
                    if (!Object.prototype.hasOwnProperty.call(s, i[o]))
                        return !1;
                for (o = r; 0 != o--; ) {
                    var n = i[o];
                    if (!t(e[n], s[n])) return !1;
                }
                return !0;
            }
            return e != e && s != s;
        },
        f = d(g);
    const k = [
        Int8Array,
        Uint8Array,
        Uint8ClampedArray,
        Int16Array,
        Uint16Array,
        Int32Array,
        Uint32Array,
        Float32Array,
        Float64Array,
    ];
    class w {
        static from(t) {
            if (!(t instanceof ArrayBuffer))
                throw new Error("Data must be an instance of ArrayBuffer.");
            const [e, s] = new Uint8Array(t, 0, 2);
            if (219 !== e)
                throw new Error(
                    "Data does not appear to be in a KDBush format."
                );
            const r = s >> 4;
            if (1 !== r) throw new Error(`Got v${r} data when expected v1.`);
            const o = k[15 & s];
            if (!o) throw new Error("Unrecognized array type.");
            const [i] = new Uint16Array(t, 2, 1),
                [n] = new Uint32Array(t, 4, 1);
            return new w(n, i, o, t);
        }
        constructor(t, e = 64, s = Float64Array, r) {
            if (isNaN(t) || t < 0)
                throw new Error(`Unpexpected numItems value: ${t}.`);
            (this.numItems = +t),
                (this.nodeSize = Math.min(Math.max(+e, 2), 65535)),
                (this.ArrayType = s),
                (this.IndexArrayType = t < 65536 ? Uint16Array : Uint32Array);
            const o = k.indexOf(this.ArrayType),
                i = 2 * t * this.ArrayType.BYTES_PER_ELEMENT,
                n = t * this.IndexArrayType.BYTES_PER_ELEMENT,
                a = (8 - (n % 8)) % 8;
            if (o < 0) throw new Error(`Unexpected typed array class: ${s}.`);
            r && r instanceof ArrayBuffer
                ? ((this.data = r),
                  (this.ids = new this.IndexArrayType(this.data, 8, t)),
                  (this.coords = new this.ArrayType(
                      this.data,
                      8 + n + a,
                      2 * t
                  )),
                  (this._pos = 2 * t),
                  (this._finished = !0))
                : ((this.data = new ArrayBuffer(8 + i + n + a)),
                  (this.ids = new this.IndexArrayType(this.data, 8, t)),
                  (this.coords = new this.ArrayType(
                      this.data,
                      8 + n + a,
                      2 * t
                  )),
                  (this._pos = 0),
                  (this._finished = !1),
                  new Uint8Array(this.data, 0, 2).set([219, 16 + o]),
                  (new Uint16Array(this.data, 2, 1)[0] = e),
                  (new Uint32Array(this.data, 4, 1)[0] = t));
        }
        add(t, e) {
            const s = this._pos >> 1;
            return (
                (this.ids[s] = s),
                (this.coords[this._pos++] = t),
                (this.coords[this._pos++] = e),
                s
            );
        }
        finish() {
            const t = this._pos >> 1;
            if (t !== this.numItems)
                throw new Error(
                    `Added ${t} items when expected ${this.numItems}.`
                );
            return (
                y(
                    this.ids,
                    this.coords,
                    this.nodeSize,
                    0,
                    this.numItems - 1,
                    0
                ),
                (this._finished = !0),
                this
            );
        }
        range(t, e, s, r) {
            if (!this._finished)
                throw new Error("Data not yet indexed - call index.finish().");
            const { ids: o, coords: i, nodeSize: n } = this,
                a = [0, o.length - 1, 0],
                h = [];
            for (; a.length; ) {
                const l = a.pop() || 0,
                    c = a.pop() || 0,
                    u = a.pop() || 0;
                if (c - u <= n) {
                    for (let n = u; n <= c; n++) {
                        const a = i[2 * n],
                            l = i[2 * n + 1];
                        a >= t && a <= s && l >= e && l <= r && h.push(o[n]);
                    }
                    continue;
                }
                const p = (u + c) >> 1,
                    m = i[2 * p],
                    d = i[2 * p + 1];
                m >= t && m <= s && d >= e && d <= r && h.push(o[p]),
                    (0 === l ? t <= m : e <= d) &&
                        (a.push(u), a.push(p - 1), a.push(1 - l)),
                    (0 === l ? s >= m : r >= d) &&
                        (a.push(p + 1), a.push(c), a.push(1 - l));
            }
            return h;
        }
        within(t, e, s) {
            if (!this._finished)
                throw new Error("Data not yet indexed - call index.finish().");
            const { ids: r, coords: o, nodeSize: i } = this,
                n = [0, r.length - 1, 0],
                a = [],
                h = s * s;
            for (; n.length; ) {
                const l = n.pop() || 0,
                    c = n.pop() || 0,
                    u = n.pop() || 0;
                if (c - u <= i) {
                    for (let s = u; s <= c; s++)
                        C(o[2 * s], o[2 * s + 1], t, e) <= h && a.push(r[s]);
                    continue;
                }
                const p = (u + c) >> 1,
                    m = o[2 * p],
                    d = o[2 * p + 1];
                C(m, d, t, e) <= h && a.push(r[p]),
                    (0 === l ? t - s <= m : e - s <= d) &&
                        (n.push(u), n.push(p - 1), n.push(1 - l)),
                    (0 === l ? t + s >= m : e + s >= d) &&
                        (n.push(p + 1), n.push(c), n.push(1 - l));
            }
            return a;
        }
    }
    function y(t, e, s, r, o, i) {
        if (o - r <= s) return;
        const n = (r + o) >> 1;
        M(t, e, n, r, o, i),
            y(t, e, s, r, n - 1, 1 - i),
            y(t, e, s, n + 1, o, 1 - i);
    }
    function M(t, e, s, r, o, i) {
        for (; o > r; ) {
            if (o - r > 600) {
                const n = o - r + 1,
                    a = s - r + 1,
                    h = Math.log(n),
                    l = 0.5 * Math.exp((2 * h) / 3),
                    c =
                        0.5 *
                        Math.sqrt((h * l * (n - l)) / n) *
                        (a - n / 2 < 0 ? -1 : 1);
                M(
                    t,
                    e,
                    s,
                    Math.max(r, Math.floor(s - (a * l) / n + c)),
                    Math.min(o, Math.floor(s + ((n - a) * l) / n + c)),
                    i
                );
            }
            const n = e[2 * s + i];
            let a = r,
                h = o;
            for (v(t, e, r, s), e[2 * o + i] > n && v(t, e, r, o); a < h; ) {
                for (v(t, e, a, h), a++, h--; e[2 * a + i] < n; ) a++;
                for (; e[2 * h + i] > n; ) h--;
            }
            e[2 * r + i] === n ? v(t, e, r, h) : (h++, v(t, e, h, o)),
                h <= s && (r = h + 1),
                s <= h && (o = h - 1);
        }
    }
    function v(t, e, s, r) {
        x(t, s, r), x(e, 2 * s, 2 * r), x(e, 2 * s + 1, 2 * r + 1);
    }
    function x(t, e, s) {
        const r = t[e];
        (t[e] = t[s]), (t[s] = r);
    }
    function C(t, e, s, r) {
        const o = t - s,
            i = e - r;
        return o * o + i * i;
    }
    const P = {
            minZoom: 0,
            maxZoom: 16,
            minPoints: 2,
            radius: 40,
            extent: 512,
            nodeSize: 64,
            log: !1,
            generateId: !1,
            reduce: null,
            map: (t) => t,
        },
        _ =
            Math.fround ||
            ((E = new Float32Array(1)), (t) => ((E[0] = +t), E[0]));
    var E;
    const A = 3,
        b = 5,
        L = 6;
    class O {
        constructor(t) {
            (this.options = Object.assign(Object.create(P), t)),
                (this.trees = new Array(this.options.maxZoom + 1)),
                (this.stride = this.options.reduce ? 7 : 6),
                (this.clusterProps = []);
        }
        load(t) {
            const { log: e, minZoom: s, maxZoom: r } = this.options;
            e && console.time("total time");
            const o = `prepare ${t.length} points`;
            e && console.time(o), (this.points = t);
            const i = [];
            for (let e = 0; e < t.length; e++) {
                const s = t[e];
                if (!s.geometry) continue;
                const [r, o] = s.geometry.coordinates,
                    n = _(T(r)),
                    a = _(j(o));
                i.push(n, a, 1 / 0, e, -1, 1), this.options.reduce && i.push(0);
            }
            let n = (this.trees[r + 1] = this._createTree(i));
            e && console.timeEnd(o);
            for (let t = r; t >= s; t--) {
                const s = +Date.now();
                (n = this.trees[t] = this._createTree(this._cluster(n, t))),
                    e &&
                        console.log(
                            "z%d: %d clusters in %dms",
                            t,
                            n.numItems,
                            +Date.now() - s
                        );
            }
            return e && console.timeEnd("total time"), this;
        }
        getClusters(t, e) {
            let s = ((((t[0] + 180) % 360) + 360) % 360) - 180;
            const r = Math.max(-90, Math.min(90, t[1]));
            let o =
                180 === t[2] ? 180 : ((((t[2] + 180) % 360) + 360) % 360) - 180;
            const i = Math.max(-90, Math.min(90, t[3]));
            if (t[2] - t[0] >= 360) (s = -180), (o = 180);
            else if (s > o) {
                const t = this.getClusters([s, r, 180, i], e),
                    n = this.getClusters([-180, r, o, i], e);
                return t.concat(n);
            }
            const n = this.trees[this._limitZoom(e)],
                a = n.range(T(s), j(i), T(o), j(r)),
                h = n.data,
                l = [];
            for (const t of a) {
                const e = this.stride * t;
                l.push(
                    h[e + b] > 1
                        ? Z(h, e, this.clusterProps)
                        : this.points[h[e + A]]
                );
            }
            return l;
        }
        getChildren(t) {
            const e = this._getOriginId(t),
                s = this._getOriginZoom(t),
                r = "No cluster with the specified id.",
                o = this.trees[s];
            if (!o) throw new Error(r);
            const i = o.data;
            if (e * this.stride >= i.length) throw new Error(r);
            const n =
                    this.options.radius /
                    (this.options.extent * Math.pow(2, s - 1)),
                a = i[e * this.stride],
                h = i[e * this.stride + 1],
                l = o.within(a, h, n),
                c = [];
            for (const e of l) {
                const s = e * this.stride;
                i[s + 4] === t &&
                    c.push(
                        i[s + b] > 1
                            ? Z(i, s, this.clusterProps)
                            : this.points[i[s + A]]
                    );
            }
            if (0 === c.length) throw new Error(r);
            return c;
        }
        getLeaves(t, e, s) {
            (e = e || 10), (s = s || 0);
            const r = [];
            return this._appendLeaves(r, t, e, s, 0), r;
        }
        getTile(t, e, s) {
            const r = this.trees[this._limitZoom(t)],
                o = Math.pow(2, t),
                { extent: i, radius: n } = this.options,
                a = n / i,
                h = (s - a) / o,
                l = (s + 1 + a) / o,
                c = { features: [] };
            return (
                this._addTileFeatures(
                    r.range((e - a) / o, h, (e + 1 + a) / o, l),
                    r.data,
                    e,
                    s,
                    o,
                    c
                ),
                0 === e &&
                    this._addTileFeatures(
                        r.range(1 - a / o, h, 1, l),
                        r.data,
                        o,
                        s,
                        o,
                        c
                    ),
                e === o - 1 &&
                    this._addTileFeatures(
                        r.range(0, h, a / o, l),
                        r.data,
                        -1,
                        s,
                        o,
                        c
                    ),
                c.features.length ? c : null
            );
        }
        getClusterExpansionZoom(t) {
            let e = this._getOriginZoom(t) - 1;
            for (; e <= this.options.maxZoom; ) {
                const s = this.getChildren(t);
                if ((e++, 1 !== s.length)) break;
                t = s[0].properties.cluster_id;
            }
            return e;
        }
        _appendLeaves(t, e, s, r, o) {
            const i = this.getChildren(e);
            for (const e of i) {
                const i = e.properties;
                if (
                    (i && i.cluster
                        ? o + i.point_count <= r
                            ? (o += i.point_count)
                            : (o = this._appendLeaves(t, i.cluster_id, s, r, o))
                        : o < r
                        ? o++
                        : t.push(e),
                    t.length === s)
                )
                    break;
            }
            return o;
        }
        _createTree(t) {
            const e = new w(
                (t.length / this.stride) | 0,
                this.options.nodeSize,
                Float32Array
            );
            for (let s = 0; s < t.length; s += this.stride)
                e.add(t[s], t[s + 1]);
            return e.finish(), (e.data = t), e;
        }
        _addTileFeatures(t, e, s, r, o, i) {
            for (const n of t) {
                const t = n * this.stride,
                    a = e[t + b] > 1;
                let h, l, c;
                if (a)
                    (h = I(e, t, this.clusterProps)),
                        (l = e[t]),
                        (c = e[t + 1]);
                else {
                    const s = this.points[e[t + A]];
                    h = s.properties;
                    const [r, o] = s.geometry.coordinates;
                    (l = T(r)), (c = j(o));
                }
                const u = {
                    type: 1,
                    geometry: [
                        [
                            Math.round(this.options.extent * (l * o - s)),
                            Math.round(this.options.extent * (c * o - r)),
                        ],
                    ],
                    tags: h,
                };
                let p;
                (p =
                    a || this.options.generateId
                        ? e[t + A]
                        : this.points[e[t + A]].id),
                    void 0 !== p && (u.id = p),
                    i.features.push(u);
            }
        }
        _limitZoom(t) {
            return Math.max(
                this.options.minZoom,
                Math.min(Math.floor(+t), this.options.maxZoom + 1)
            );
        }
        _cluster(t, e) {
            const {
                    radius: s,
                    extent: r,
                    reduce: o,
                    minPoints: i,
                } = this.options,
                n = s / (r * Math.pow(2, e)),
                a = t.data,
                h = [],
                l = this.stride;
            for (let s = 0; s < a.length; s += l) {
                if (a[s + 2] <= e) continue;
                a[s + 2] = e;
                const r = a[s],
                    c = a[s + 1],
                    u = t.within(a[s], a[s + 1], n),
                    p = a[s + b];
                let m = p;
                for (const t of u) {
                    const s = t * l;
                    a[s + 2] > e && (m += a[s + b]);
                }
                if (m > p && m >= i) {
                    let t,
                        i = r * p,
                        n = c * p,
                        d = -1;
                    const g =
                        (((s / l) | 0) << 5) + (e + 1) + this.points.length;
                    for (const r of u) {
                        const h = r * l;
                        if (a[h + 2] <= e) continue;
                        a[h + 2] = e;
                        const c = a[h + b];
                        (i += a[h] * c),
                            (n += a[h + 1] * c),
                            (a[h + 4] = g),
                            o &&
                                (t ||
                                    ((t = this._map(a, s, !0)),
                                    (d = this.clusterProps.length),
                                    this.clusterProps.push(t)),
                                o(t, this._map(a, h)));
                    }
                    (a[s + 4] = g),
                        h.push(i / m, n / m, 1 / 0, g, -1, m),
                        o && h.push(d);
                } else {
                    for (let t = 0; t < l; t++) h.push(a[s + t]);
                    if (m > 1)
                        for (const t of u) {
                            const s = t * l;
                            if (!(a[s + 2] <= e)) {
                                a[s + 2] = e;
                                for (let t = 0; t < l; t++) h.push(a[s + t]);
                            }
                        }
                }
            }
            return h;
        }
        _getOriginId(t) {
            return (t - this.points.length) >> 5;
        }
        _getOriginZoom(t) {
            return (t - this.points.length) % 32;
        }
        _map(t, e, s) {
            if (t[e + b] > 1) {
                const r = this.clusterProps[t[e + L]];
                return s ? Object.assign({}, r) : r;
            }
            const r = this.points[t[e + A]].properties,
                o = this.options.map(r);
            return s && o === r ? Object.assign({}, o) : o;
        }
    }
    function Z(t, e, s) {
        return {
            type: "Feature",
            id: t[e + A],
            properties: I(t, e, s),
            geometry: {
                type: "Point",
                coordinates: [((r = t[e]), 360 * (r - 0.5)), S(t[e + 1])],
            },
        };
        var r;
    }
    function I(t, e, s) {
        const r = t[e + b],
            o =
                r >= 1e4
                    ? `${Math.round(r / 1e3)}k`
                    : r >= 1e3
                    ? Math.round(r / 100) / 10 + "k"
                    : r,
            i = t[e + L],
            n = -1 === i ? {} : Object.assign({}, s[i]);
        return Object.assign(n, {
            cluster: !0,
            cluster_id: t[e + A],
            point_count: r,
            point_count_abbreviated: o,
        });
    }
    function T(t) {
        return t / 360 + 0.5;
    }
    function j(t) {
        const e = Math.sin((t * Math.PI) / 180),
            s = 0.5 - (0.25 * Math.log((1 + e) / (1 - e))) / Math.PI;
        return s < 0 ? 0 : s > 1 ? 1 : s;
    }
    function S(t) {
        const e = ((180 - 360 * t) * Math.PI) / 180;
        return (360 * Math.atan(Math.exp(e))) / Math.PI - 90;
    }
    class z extends u {
        constructor(t) {
            var { maxZoom: s, radius: r = 60 } = t,
                o = e(t, ["maxZoom", "radius"]);
            super({ maxZoom: s }),
                (this.state = { zoom: -1 }),
                (this.superCluster = new O(
                    Object.assign({ maxZoom: this.maxZoom, radius: r }, o)
                ));
        }
        calculate(t) {
            let e = !1;
            const r = { zoom: t.map.getZoom() };
            if (!f(t.markers, this.markers)) {
                (e = !0), (this.markers = [...t.markers]);
                const r = this.markers.map((t) => {
                    const e = s.getPosition(t);
                    return {
                        type: "Feature",
                        geometry: {
                            type: "Point",
                            coordinates: [e.lng(), e.lat()],
                        },
                        properties: { marker: t },
                    };
                });
                this.superCluster.load(r);
            }
            return (
                e ||
                    ((this.state.zoom <= this.maxZoom ||
                        r.zoom <= this.maxZoom) &&
                        (e = !f(this.state, r))),
                (this.state = r),
                e && (this.clusters = this.cluster(t)),
                { clusters: this.clusters, changed: e }
            );
        }
        cluster(t) {
            let { map: e } = t;
            return this.superCluster
                .getClusters([-180, -90, 180, 90], Math.round(e.getZoom()))
                .map((t) => this.transformCluster(t));
        }
        transformCluster(t) {
            let {
                geometry: {
                    coordinates: [e, o],
                },
                properties: i,
            } = t;
            if (i.cluster)
                return new r({
                    markers: this.superCluster
                        .getLeaves(i.cluster_id, 1 / 0)
                        .map((t) => t.properties.marker),
                    position: { lat: o, lng: e },
                });
            const n = i.marker;
            return new r({ markers: [n], position: s.getPosition(n) });
        }
    }
    class U {
        constructor(t, e) {
            this.markers = { sum: t.length };
            const s = e.map((t) => t.count),
                r = s.reduce((t, e) => t + e, 0);
            this.clusters = {
                count: e.length,
                markers: {
                    mean: r / e.length,
                    sum: r,
                    min: Math.min(...s),
                    max: Math.max(...s),
                },
            };
        }
    }
    class B {
        render(t, e, r) {
            let { count: o, position: i } = t;
            const n = `<svg fill="${
                    o > Math.max(10, e.clusters.markers.mean)
                        ? "#ff0000"
                        : "#0000ff"
                }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240" width="50" height="50">\n<circle cx="120" cy="120" opacity=".6" r="70" />\n<circle cx="120" cy="120" opacity=".3" r="90" />\n<circle cx="120" cy="120" opacity=".2" r="110" />\n<text x="50%" y="50%" style="fill:#fff" text-anchor="middle" font-size="50" dominant-baseline="middle" font-family="roboto,arial,sans-serif">${o}</text>\n</svg>`,
                a = `Cluster of ${o} markers`,
                h = Number(google.maps.Marker.MAX_ZINDEX) + o;
            if (s.isAdvancedMarkerAvailable(r)) {
                const t = new DOMParser().parseFromString(
                    n,
                    "image/svg+xml"
                ).documentElement;
                t.setAttribute("transform", "translate(0 25)");
                const e = {
                    map: r,
                    position: i,
                    zIndex: h,
                    title: a,
                    content: t,
                };
                return new google.maps.marker.AdvancedMarkerElement(e);
            }
            const l = {
                position: i,
                zIndex: h,
                title: a,
                icon: {
                    url: `data:image/svg+xml;base64,${btoa(n)}`,
                    anchor: new google.maps.Point(25, 25),
                },
            };
            return new google.maps.Marker(l);
        }
    }
    class D {
        constructor() {
            !(function (t, e) {
                for (let s in e.prototype) t.prototype[s] = e.prototype[s];
            })(D, google.maps.OverlayView);
        }
    }
    var N;
    (t.MarkerClustererEvents = void 0),
        ((N =
            t.MarkerClustererEvents ||
            (t.MarkerClustererEvents = {})).CLUSTERING_BEGIN =
            "clusteringbegin"),
        (N.CLUSTERING_END = "clusteringend"),
        (N.CLUSTER_CLICK = "click");
    const F = (t, e, s) => {
        s.fitBounds(e.bounds);
    };
    return (
        (t.AbstractAlgorithm = u),
        (t.AbstractViewportAlgorithm = p),
        (t.Cluster = r),
        (t.ClusterStats = U),
        (t.DefaultRenderer = B),
        (t.GridAlgorithm = class extends p {
            constructor(t) {
                var { maxDistance: s = 4e4, gridSize: r = 40 } = t;
                super(e(t, ["maxDistance", "gridSize"])),
                    (this.clusters = []),
                    (this.state = { zoom: -1 }),
                    (this.maxDistance = s),
                    (this.gridSize = r);
            }
            calculate(t) {
                let { markers: e, map: s, mapCanvasProjection: r } = t;
                const i = { zoom: s.getZoom() };
                let n = !1;
                return (
                    (this.state.zoom >= this.maxZoom &&
                        i.zoom >= this.maxZoom) ||
                        (n = !f(this.state, i)),
                    (this.state = i),
                    s.getZoom() >= this.maxZoom
                        ? { clusters: this.noop({ markers: e }), changed: n }
                        : {
                              clusters: this.cluster({
                                  markers: o(s, r, e, this.viewportPadding),
                                  map: s,
                                  mapCanvasProjection: r,
                              }),
                          }
                );
            }
            cluster(t) {
                let { markers: e, map: s, mapCanvasProjection: r } = t;
                return (
                    (this.clusters = []),
                    e.forEach((t) => {
                        this.addToClosestCluster(t, s, r);
                    }),
                    this.clusters
                );
            }
            addToClosestCluster(t, e, o) {
                let n = this.maxDistance,
                    h = null;
                for (let e = 0; e < this.clusters.length; e++) {
                    const r = this.clusters[e],
                        o = a(
                            r.bounds.getCenter().toJSON(),
                            s.getPosition(t).toJSON()
                        );
                    o < n && ((n = o), (h = r));
                }
                if (
                    h &&
                    i(h.bounds, o, this.gridSize).contains(s.getPosition(t))
                )
                    h.push(t);
                else {
                    const e = new r({ markers: [t] });
                    this.clusters.push(e);
                }
            }
        }),
        (t.MarkerClusterer = class extends D {
            constructor(t) {
                let {
                    map: e,
                    markers: s = [],
                    algorithmOptions: r = {},
                    algorithm: o = new z(r),
                    renderer: i = new B(),
                    onClusterClick: n = F,
                } = t;
                super(),
                    (this.markers = [...s]),
                    (this.clusters = []),
                    (this.algorithm = o),
                    (this.renderer = i),
                    (this.onClusterClick = n),
                    e && this.setMap(e);
            }
            addMarker(t, e) {
                this.markers.includes(t) ||
                    (this.markers.push(t), e || this.render());
            }
            addMarkers(t, e) {
                t.forEach((t) => {
                    this.addMarker(t, !0);
                }),
                    e || this.render();
            }
            removeMarker(t, e) {
                const r = this.markers.indexOf(t);
                return (
                    -1 !== r &&
                    (s.setMap(t, null),
                    this.markers.splice(r, 1),
                    e || this.render(),
                    !0)
                );
            }
            removeMarkers(t, e) {
                let s = !1;
                return (
                    t.forEach((t) => {
                        s = this.removeMarker(t, !0) || s;
                    }),
                    s && !e && this.render(),
                    s
                );
            }
            clearMarkers(t) {
                (this.markers.length = 0), t || this.render();
            }
            render() {
                const e = this.getMap();
                if (e instanceof google.maps.Map && e.getProjection()) {
                    google.maps.event.trigger(
                        this,
                        t.MarkerClustererEvents.CLUSTERING_BEGIN,
                        this
                    );
                    const { clusters: r, changed: o } =
                        this.algorithm.calculate({
                            markers: this.markers,
                            map: e,
                            mapCanvasProjection: this.getProjection(),
                        });
                    if (o || null == o) {
                        const t = new Set();
                        for (const e of r)
                            1 == e.markers.length && t.add(e.markers[0]);
                        const e = [];
                        for (const r of this.clusters)
                            null != r.marker &&
                                (1 == r.markers.length
                                    ? t.has(r.marker) ||
                                      s.setMap(r.marker, null)
                                    : e.push(r.marker));
                        (this.clusters = r),
                            this.renderClusters(),
                            requestAnimationFrame(() =>
                                e.forEach((t) => s.setMap(t, null))
                            );
                    }
                    google.maps.event.trigger(
                        this,
                        t.MarkerClustererEvents.CLUSTERING_END,
                        this
                    );
                }
            }
            onAdd() {
                (this.idleListener = this.getMap().addListener(
                    "idle",
                    this.render.bind(this)
                )),
                    this.render();
            }
            onRemove() {
                google.maps.event.removeListener(this.idleListener),
                    this.reset();
            }
            reset() {
                this.markers.forEach((t) => s.setMap(t, null)),
                    this.clusters.forEach((t) => t.delete()),
                    (this.clusters = []);
            }
            renderClusters() {
                const e = new U(this.markers, this.clusters),
                    r = this.getMap();
                this.clusters.forEach((o) => {
                    1 === o.markers.length
                        ? (o.marker = o.markers[0])
                        : ((o.marker = this.renderer.render(o, e, r)),
                          o.markers.forEach((t) => s.setMap(t, null)),
                          this.onClusterClick &&
                              o.marker.addListener("click", (e) => {
                                  google.maps.event.trigger(
                                      this,
                                      t.MarkerClustererEvents.CLUSTER_CLICK,
                                      o
                                  ),
                                      this.onClusterClick(e, o, r);
                              })),
                        s.setMap(o.marker, r);
                });
            }
        }),
        (t.MarkerUtils = s),
        (t.NoopAlgorithm = class extends u {
            constructor(t) {
                super(e(t, []));
            }
            calculate(t) {
                let { markers: e, map: s, mapCanvasProjection: r } = t;
                return {
                    clusters: this.cluster({
                        markers: e,
                        map: s,
                        mapCanvasProjection: r,
                    }),
                    changed: !1,
                };
            }
            cluster(t) {
                return this.noop(t);
            }
        }),
        (t.SuperClusterAlgorithm = z),
        (t.SuperClusterViewportAlgorithm = class extends p {
            constructor(t) {
                var { maxZoom: s, radius: r = 60, viewportPadding: o = 60 } = t,
                    i = e(t, ["maxZoom", "radius", "viewportPadding"]);
                super({ maxZoom: s, viewportPadding: o }),
                    (this.superCluster = new O(
                        Object.assign({ maxZoom: this.maxZoom, radius: r }, i)
                    )),
                    (this.state = { zoom: -1, view: [0, 0, 0, 0] });
            }
            calculate(t) {
                const e = {
                    zoom: Math.round(t.map.getZoom()),
                    view: n(
                        t.map.getBounds(),
                        t.mapCanvasProjection,
                        this.viewportPadding
                    ),
                };
                let r = !f(this.state, e);
                if (!f(t.markers, this.markers)) {
                    (r = !0), (this.markers = [...t.markers]);
                    const e = this.markers.map((t) => {
                        const e = s.getPosition(t);
                        return {
                            type: "Feature",
                            geometry: {
                                type: "Point",
                                coordinates: [e.lng(), e.lat()],
                            },
                            properties: { marker: t },
                        };
                    });
                    this.superCluster.load(e);
                }
                return (
                    r && ((this.clusters = this.cluster(t)), (this.state = e)),
                    { clusters: this.clusters, changed: r }
                );
            }
            cluster(t) {
                let { map: e, mapCanvasProjection: s } = t;
                const r = {
                    zoom: Math.round(e.getZoom()),
                    view: n(e.getBounds(), s, this.viewportPadding),
                };
                return this.superCluster
                    .getClusters(r.view, r.zoom)
                    .map((t) => this.transformCluster(t));
            }
            transformCluster(t) {
                let {
                    geometry: {
                        coordinates: [e, o],
                    },
                    properties: i,
                } = t;
                if (i.cluster)
                    return new r({
                        markers: this.superCluster
                            .getLeaves(i.cluster_id, 1 / 0)
                            .map((t) => t.properties.marker),
                        position: { lat: o, lng: e },
                    });
                const n = i.marker;
                return new r({ markers: [n], position: s.getPosition(n) });
            }
        }),
        (t.defaultOnClusterClickHandler = F),
        (t.distanceBetweenPoints = a),
        (t.extendBoundsToPaddedViewport = i),
        (t.extendPixelBounds = l),
        (t.filterMarkersToPaddedViewport = o),
        (t.getPaddedViewport = n),
        (t.noop = m),
        (t.pixelBoundsToLatLngBounds = c),
        Object.defineProperty(t, "__esModule", { value: !0 }),
        t
    );
})({});
//# sourceMappingURL=index.min.js.map
