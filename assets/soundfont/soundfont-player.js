(function () {
  function r(e, n, t) {
    function o(i, f) {
      if (!n[i]) {
        if (!e[i]) {
          let c = "function" == typeof require && require;
          if (!f && c) 
          {
            return c(i, !0);
          }
          if (u) 
          {
            return u(i, !0);
          }
          let a = new Error("Cannot find module '" + i + "'");
          throw ((a.code = "MODULE_NOT_FOUND"), a);
        }
        let p = (n[i] = { exports: {} });
        e[i][0].call(
          p.exports,
          function (r) {
            let n = e[i][1][r];
            return o(n || r);
          },
          p,
          p.exports,
          r,
          e,
          n,
          t
        );
      }
      return n[i].exports;
    }
    for (
      let u = "function" == typeof require && require, i = 0;
      i < t.length;
      i++
    )
      o(t[i]);
    return o;
  }
  return r;
})()(
  {
    1: [
      function (require, module, exports) {
        "use strict";
        let load = require("audio-loader");
        let player = require("sample-player");
        function instrument(ac, name, options) {
          if (arguments.length === 1)
            return function (n, o) {
              return instrument(ac, n, o);
            };
          let opts = options || {};
          let isUrl = opts.isSoundfontURL || isSoundfontURL;
          let toUrl = opts.nameToUrl || nameToUrl;
          let url = isUrl(name)
            ? name
            : toUrl(name, opts.soundfont, opts.format);
          return load(ac, url, { only: opts.only || opts.notes }).then(
            function (buffers) {
              let p = player(ac, buffers, opts).connect(
                opts.destination ? opts.destination : ac.destination
              );
              p.url = url;
              p.name = name;
              return p;
            }
          );
        }
        function isSoundfontURL(name) {
          return /\.js(\?.*)?$/i.test(name);
        }
        function nameToUrl(name, sf, format) {
          format = format === "ogg" ? format : "mp3";
          sf = sf === "FluidR3_GM" ? sf : "MusyngKite";
          return (
            baseSoundFontUrl +
            sf +
            "/" +
            name +
            "-" +
            format +
            ".js"
          );
        }
        let Soundfont = require("./legacy");
        Soundfont.instrument = instrument;
        Soundfont.nameToUrl = nameToUrl;
        if (typeof module === "object" && module.exports)
          module.exports = Soundfont;
        if (typeof window !== "undefined") window.Soundfont = Soundfont;
      },
      { "./legacy": 2, "audio-loader": 6, "sample-player": 10 },
    ],
    2: [
      function (require, module, exports) {
        "use strict";
        let parser = require("note-parser");
        function Soundfont(ctx, nameToUrl) {
          console.warn("new Soundfont() is deprected");
          console.log(
            "Please use Soundfont.instrument() instead of new Soundfont().instrument()"
          );
          if (!(this instanceof Soundfont)) return new Soundfont(ctx);
          this.nameToUrl = nameToUrl || Soundfont.nameToUrl;
          this.ctx = ctx;
          this.instruments = {};
          this.promises = [];
        }
        Soundfont.prototype.onready = function (callback) {
          console.warn("deprecated API");
          console.log(
            "Please use Promise.all(Soundfont.instrument(), Soundfont.instrument()).then() instead of new Soundfont().onready()"
          );
          Promise.all(this.promises).then(callback);
        };
        Soundfont.prototype.instrument = function (name, options) {
          console.warn("new Soundfont().instrument() is deprecated.");
          console.log("Please use Soundfont.instrument() instead.");
          let ctx = this.ctx;
          name = name || "default";
          if (name in this.instruments) return this.instruments[name];
          let inst = { name: name, play: oscillatorPlayer(ctx, options) };
          this.instruments[name] = inst;
          if (name !== "default") {
            let promise = Soundfont.instrument(ctx, name, options).then(
              function (instrument) {
                inst.play = instrument.play;
                return inst;
              }
            );
            this.promises.push(promise);
            inst.onready = function (cb) {
              console.warn(
                "onready is deprecated. Use Soundfont.instrument().then()"
              );
              promise.then(cb);
            };
          } else {
            inst.onready = function (cb) {
              console.warn(
                "onready is deprecated. Use Soundfont.instrument().then()"
              );
              cb();
            };
          }
          return inst;
        };
        function loadBuffers(ac, name, options) {
          console.warn("Soundfont.loadBuffers is deprecate.");
          console.log(
            "Use Soundfont.instrument(..) and get buffers properties from the result."
          );
          return Soundfont.instrument(ac, name, options).then(function (inst) {
            return inst.buffers;
          });
        }
        Soundfont.loadBuffers = loadBuffers;
        function oscillatorPlayer(ctx, defaultOptions) {
          defaultOptions = defaultOptions || {};
          return function (note, time, duration, options) {
            console.warn("The oscillator player is deprecated.");
            console.log(
              "Starting with version 0.9.0 you will have to wait until the soundfont is loaded to play sounds."
            );
            let midi = note > 0 && note < 129 ? +note : parser.midi(note);
            let freq = midi ? parser.midiToFreq(midi, 440) : null;
            if (!freq) return;
            duration = duration || 0.2;
            options = options || {};
            let destination =
              options.destination ||
              defaultOptions.destination ||
              ctx.destination;
            let vcoType = options.vcoType || defaultOptions.vcoType || "sine";
            let gain = options.gain || defaultOptions.gain || 0.4;
            let vco = ctx.createOscillator();
            vco.type = vcoType;
            vco.frequency.value = freq;
            let vca = ctx.createGain();
            vca.gain.value = gain;
            vco.connect(vca);
            vca.connect(destination);
            vco.start(time);
            if (duration > 0) vco.stop(time + duration);
            return vco;
          };
        }
        Soundfont.noteToMidi = parser.midi;
        module.exports = Soundfont;
      },
      { "note-parser": 8 },
    ],
    3: [
      function (require, module, exports) {
        module.exports = ADSR;
        function ADSR(audioContext) {
          let node = audioContext.createGain();
          let voltage = (node._voltage = getVoltage(audioContext));
          let value = scale(voltage);
          let startValue = scale(voltage);
          let endValue = scale(voltage);
          node._startAmount = scale(startValue);
          node._endAmount = scale(endValue);
          node._multiplier = scale(value);
          node._multiplier.connect(node);
          node._startAmount.connect(node);
          node._endAmount.connect(node);
          node.value = value.gain;
          node.startValue = startValue.gain;
          node.endValue = endValue.gain;
          node.startValue.value = 0;
          node.endValue.value = 0;
          Object.defineProperties(node, props);
          return node;
        }
        let props = {
          attack: { value: 0, writable: true },
          decay: { value: 0, writable: true },
          sustain: { value: 1, writable: true },
          release: { value: 0, writable: true },
          getReleaseDuration: {
            value: function () {
              return this.release;
            },
          },
          start: {
            value: function (at) {
              let target = this._multiplier.gain;
              let startAmount = this._startAmount.gain;
              let endAmount = this._endAmount.gain;
              this._voltage.start(at);
              this._decayFrom = this._decayFrom = at + this.attack;
              this._startedAt = at;
              let sustain = this.sustain;
              target.cancelScheduledValues(at);
              startAmount.cancelScheduledValues(at);
              endAmount.cancelScheduledValues(at);
              endAmount.setValueAtTime(0, at);
              if (this.attack) {
                target.setValueAtTime(0, at);
                target.linearRampToValueAtTime(1, at + this.attack);
                startAmount.setValueAtTime(1, at);
                startAmount.linearRampToValueAtTime(0, at + this.attack);
              } else {
                target.setValueAtTime(1, at);
                startAmount.setValueAtTime(0, at);
              }
              if (this.decay) {
                target.setTargetAtTime(
                  sustain,
                  this._decayFrom,
                  getTimeConstant(this.decay)
                );
              }
            },
          },
          stop: {
            value: function (at, isTarget) {
              if (isTarget) {
                at = at - this.release;
              }
              let endTime = at + this.release;
              if (this.release) {
                let target = this._multiplier.gain;
                let startAmount = this._startAmount.gain;
                let endAmount = this._endAmount.gain;
                target.cancelScheduledValues(at);
                startAmount.cancelScheduledValues(at);
                endAmount.cancelScheduledValues(at);
                let expFalloff = getTimeConstant(this.release);
                if (this.attack && at < this._decayFrom) {
                  let valueAtTime = getValue(
                    0,
                    1,
                    this._startedAt,
                    this._decayFrom,
                    at
                  );
                  target.linearRampToValueAtTime(valueAtTime, at);
                  startAmount.linearRampToValueAtTime(1 - valueAtTime, at);
                  startAmount.setTargetAtTime(0, at, expFalloff);
                }
                endAmount.setTargetAtTime(1, at, expFalloff);
                target.setTargetAtTime(0, at, expFalloff);
              }
              this._voltage.stop(endTime);
              return endTime;
            },
          },
          onended: {
            get: function () {
              return this._voltage.onended;
            },
            set: function (value) {
              this._voltage.onended = value;
            },
          },
        };
        let flat = new Float32Array([1, 1]);
        function getVoltage(context) {
          let voltage = context.createBufferSource();
          let buffer = context.createBuffer(1, 2, context.sampleRate);
          buffer.getChannelData(0).set(flat);
          voltage.buffer = buffer;
          voltage.loop = true;
          return voltage;
        }
        function scale(node) {
          let gain = node.context.createGain();
          node.connect(gain);
          return gain;
        }
        function getTimeConstant(time) {
          return Math.log(time + 1) / Math.log(100);
        }
        function getValue(start, end, fromTime, toTime, at) {
          let difference = end - start;
          let time = toTime - fromTime;
          let truncateTime = at - fromTime;
          let phase = truncateTime / time;
          let value = start + phase * difference;
          if (value <= start) {
            value = start;
          }
          if (value >= end) {
            value = end;
          }
          return value;
        }
      },
      {},
    ],
    4: [
      function (require, module, exports) {
        "use strict";
        function b64ToUint6(nChr) {
          return nChr > 64 && nChr < 91
            ? nChr - 65
            : nChr > 96 && nChr < 123
            ? nChr - 71
            : nChr > 47 && nChr < 58
            ? nChr + 4
            : nChr === 43
            ? 62
            : nChr === 47
            ? 63
            : 0;
        }
        function decode(sBase64, nBlocksSize) {
          let sB64Enc = sBase64.replace(/[^A-Za-z0-9\+\/]/g, "");
          let nInLen = sB64Enc.length;
          let nOutLen = nBlocksSize
            ? Math.ceil(((nInLen * 3 + 1) >> 2) / nBlocksSize) * nBlocksSize
            : (nInLen * 3 + 1) >> 2;
          let taBytes = new Uint8Array(nOutLen);
          for (
            let nMod3, nMod4, nUint24 = 0, nOutIdx = 0, nInIdx = 0;
            nInIdx < nInLen;
            nInIdx++
          ) {
            nMod4 = nInIdx & 3;
            nUint24 |=
              b64ToUint6(sB64Enc.charCodeAt(nInIdx)) << (18 - 6 * nMod4);
            if (nMod4 === 3 || nInLen - nInIdx === 1) {
              for (
                nMod3 = 0;
                nMod3 < 3 && nOutIdx < nOutLen;
                nMod3++, nOutIdx++
              ) {
                taBytes[nOutIdx] = (nUint24 >>> ((16 >>> nMod3) & 24)) & 255;
              }
              nUint24 = 0;
            }
          }
          return taBytes;
        }
        module.exports = { decode: decode };
      },
      {},
    ],
    5: [
      function (require, module, exports) {
        "use strict";
        module.exports = function (url, type) {
          return new Promise(function (done, reject) {
            let req = new XMLHttpRequest();
            if (type) req.responseType = type;
            req.open("GET", url);
            req.onload = function () {
              req.status === 200
                ? done(req.response)
                : reject(Error(req.statusText));
            };
            req.onerror = function () {
              reject(Error("Network Error"));
            };
            req.send();
          });
        };
      },
      {},
    ],
    6: [
      function (require, module, exports) {
        "use strict";
        let base64 = require("./base64");
        let fetch = require("./fetch");
        function fromRegex(r) {
          return function (o) {
            return typeof o === "string" && r.test(o);
          };
        }
        function prefix(pre, name) {
          return typeof pre === "string"
            ? pre + name
            : typeof pre === "function"
            ? pre(name)
            : name;
        }
        function load(ac, source, options, defVal) {
          let loader = isArrayBuffer(source)
            ? loadArrayBuffer
            : isAudioFileName(source)
            ? loadAudioFile
            : isPromise(source)
            ? loadPromise
            : isArray(source)
            ? loadArrayData
            : isObject(source)
            ? loadObjectData
            : isJsonFileName(source)
            ? loadJsonFile
            : isBase64Audio(source)
            ? loadBase64Audio
            : isJsFileName(source)
            ? loadMidiJSFile
            : null;
          let opts = options || {};
          return loader
            ? loader(ac, source, opts)
            : defVal
            ? Promise.resolve(defVal)
            : Promise.reject("Source not valid (" + source + ")");
        }
        load.fetch = fetch;
        function isArrayBuffer(o) {
          return o instanceof ArrayBuffer;
        }
        function loadArrayBuffer(ac, array, options) {
          return new Promise(function (done, reject) {
            ac.decodeAudioData(
              array,
              function (buffer) {
                done(buffer);
              },
              function () {
                reject(
                  "Can't decode audio data (" + array.slice(0, 30) + "...)"
                );
              }
            );
          });
        }
        let isAudioFileName = fromRegex(/\.(mp3|wav|ogg)(\?.*)?$/i);
        function loadAudioFile(ac, name, options) {
          let url = prefix(options.from, name);
          return load(ac, load.fetch(url, "arraybuffer"), options);
        }
        function isPromise(o) {
          return o && typeof o.then === "function";
        }
        function loadPromise(ac, promise, options) {
          return promise.then(function (value) {
            return load(ac, value, options);
          });
        }
        let isArray = Array.isArray;
        function loadArrayData(ac, array, options) {
          return Promise.all(
            array.map(function (data) {
              return load(ac, data, options, data);
            })
          );
        }
        function isObject(o) {
          return o && typeof o === "object";
        }
        function loadObjectData(ac, obj, options) {
          let dest = {};
          let promises = Object.keys(obj).map(function (key) {
            if (options.only && options.only.indexOf(key) === -1) return null;
            let value = obj[key];
            return load(ac, value, options, value).then(function (audio) {
              dest[key] = audio;
            });
          });
          return Promise.all(promises).then(function () {
            return dest;
          });
        }
        let isJsonFileName = fromRegex(/\.json(\?.*)?$/i);
        function loadJsonFile(ac, name, options) {
          let url = prefix(options.from, name);
          return load(ac, load.fetch(url, "text").then(JSON.parse), options);
        }
        let isBase64Audio = fromRegex(/^data:audio/);
        function loadBase64Audio(ac, source, options) {
          let i = source.indexOf(",");
          return load(ac, base64.decode(source.slice(i + 1)).buffer, options);
        }
        let isJsFileName = fromRegex(/\.js(\?.*)?$/i);
        function loadMidiJSFile(ac, name, options) {
          let url = prefix(options.from, name);
          return load(ac, load.fetch(url, "text").then(midiJsToJson), options);
        }
        function midiJsToJson(data) {
          let begin = data.indexOf("MIDI.Soundfont.");
          if (begin < 0) throw Error("Invalid MIDI.js Soundfont format");
          begin = data.indexOf("=", begin) + 2;
          let end = data.lastIndexOf(",");
          return JSON.parse(data.slice(begin, end) + "}");
        }
        if (typeof module === "object" && module.exports) module.exports = load;
        if (typeof window !== "undefined") window.loadAudio = load;
      },
      { "./base64": 4, "./fetch": 5 },
    ],
    7: [
      function (require, module, exports) {
        (function (global) {
          (function (e) {
            if (typeof exports === "object" && typeof module !== "undefined") {
              module.exports = e();
            } else if (typeof define === "function" && define.amd) {
              define([], e);
            } else {
              let t;
              if (typeof window !== "undefined") {
                t = window;
              } else if (typeof global !== "undefined") {
                t = global;
              } else if (typeof self !== "undefined") {
                t = self;
              } else {
                t = this;
              }
              t.midimessage = e();
            }
          })(function () {
            let e, t, s;
            return (function o(e, t, s) {
              function a(n, i) {
                if (!t[n]) {
                  if (!e[n]) {
                    let l = typeof require == "function" && require;
                    if (!i && l) return l(n, !0);
                    if (r) return r(n, !0);
                    let h = new Error("Cannot find module '" + n + "'");
                    throw ((h.code = "MODULE_NOT_FOUND"), h);
                  }
                  let c = (t[n] = { exports: {} });
                  e[n][0].call(
                    c.exports,
                    function (t) {
                      let s = e[n][1][t];
                      return a(s ? s : t);
                    },
                    c,
                    c.exports,
                    o,
                    e,
                    t,
                    s
                  );
                }
                return t[n].exports;
              }
              let r = typeof require == "function" && require;
              for (let n = 0; n < s.length; n++) a(s[n]);
              return a;
            })(
              {
                1: [
                  function (e, t, s) {
                    "use strict";
                    Object.defineProperty(s, "__esModule", { value: true });
                    s["default"] = function (e) {
                      function t(e) {
                        this._event = e;
                        this._data = e.data;
                        this.receivedTime = e.receivedTime;
                        if (this._data && this._data.length < 2) {
                          console.warn(
                            "Illegal MIDI message of length",
                            this._data.length
                          );
                          return;
                        }
                        this._messageCode = e.data[0] & 240;
                        this.channel = e.data[0] & 15;
                        switch (this._messageCode) {
                          case 128:
                            this.messageType = "noteoff";
                            this.key = e.data[1] & 127;
                            this.velocity = e.data[2] & 127;
                            break;
                          case 144:
                            this.messageType = "noteon";
                            this.key = e.data[1] & 127;
                            this.velocity = e.data[2] & 127;
                            break;
                          case 160:
                            this.messageType = "keypressure";
                            this.key = e.data[1] & 127;
                            this.pressure = e.data[2] & 127;
                            break;
                          case 176:
                            this.messageType = "controlchange";
                            this.controllerNumber = e.data[1] & 127;
                            this.controllerValue = e.data[2] & 127;
                            if (
                              this.controllerNumber === 120 &&
                              this.controllerValue === 0
                            ) {
                              this.channelModeMessage = "allsoundoff";
                            } else if (this.controllerNumber === 121) {
                              this.channelModeMessage = "resetallcontrollers";
                            } else if (this.controllerNumber === 122) {
                              if (this.controllerValue === 0) {
                                this.channelModeMessage = "localcontroloff";
                              } else {
                                this.channelModeMessage = "localcontrolon";
                              }
                            } else if (
                              this.controllerNumber === 123 &&
                              this.controllerValue === 0
                            ) {
                              this.channelModeMessage = "allnotesoff";
                            } else if (
                              this.controllerNumber === 124 &&
                              this.controllerValue === 0
                            ) {
                              this.channelModeMessage = "omnimodeoff";
                            } else if (
                              this.controllerNumber === 125 &&
                              this.controllerValue === 0
                            ) {
                              this.channelModeMessage = "omnimodeon";
                            } else if (this.controllerNumber === 126) {
                              this.channelModeMessage = "monomodeon";
                            } else if (this.controllerNumber === 127) {
                              this.channelModeMessage = "polymodeon";
                            }
                            break;
                          case 192:
                            this.messageType = "programchange";
                            this.program = e.data[1];
                            break;
                          case 208:
                            this.messageType = "channelpressure";
                            this.pressure = e.data[1] & 127;
                            break;
                          case 224:
                            this.messageType = "pitchbendchange";
                            let t = e.data[2] & 127;
                            let s = e.data[1] & 127;
                            this.pitchBend = (t << 8) + s;
                            break;
                        }
                      }
                      return new t(e);
                    };
                    t.exports = s["default"];
                  },
                  {},
                ],
              },
              {},
              [1]
            )(1);
          });
        }).call(
          this,
          typeof global !== "undefined"
            ? global
            : typeof self !== "undefined"
            ? self
            : typeof window !== "undefined"
            ? window
            : {}
        );
      },
      {},
    ],
    8: [
      function (require, module, exports) {
        !(function (t, n) {
          "object" == typeof exports && "undefined" != typeof module
            ? n(exports)
            : "function" == typeof define && define.amd
            ? define(["exports"], n)
            : n((t.NoteParser = t.NoteParser || {}));
        })(this, function (t) {
          "use strict";
          function n(t, n) {
            return Array(n + 1).join(t);
          }
          function r(t) {
            return "number" == typeof t;
          }
          function e(t) {
            return "string" == typeof t;
          }
          function u(t) {
            return void 0 !== t;
          }
          function c(t, n) {
            return Math.pow(2, (t - 69) / 12) * (n || 440);
          }
          function o() {
            return b;
          }
          function i(t, n, r) {
            if ("string" != typeof t) return null;
            let e = b.exec(t);
            if (!e || (!n && e[4])) return null;
            let u = {
              letter: e[1].toUpperCase(),
              acc: e[2].replace(/x/g, "##"),
            };
            (u.pc = u.letter + u.acc),
              (u.step = (u.letter.charCodeAt(0) + 3) % 7),
              (u.alt = "b" === u.acc[0] ? -u.acc.length : u.acc.length);
            let o = A[u.step] + u.alt;
            return (
              (u.chroma = o < 0 ? 12 + o : o % 12),
              e[3] &&
                ((u.oct = +e[3]),
                (u.midi = o + 12 * (u.oct + 1)),
                (u.freq = c(u.midi, r))),
              n && (u.tonicOf = e[4]),
              u
            );
          }
          function f(t) {
            return r(t) ? (t < 0 ? n("b", -t) : n("#", t)) : "";
          }
          function a(t) {
            return r(t) ? "" + t : "";
          }
          function l(t, n, r) {
            return null === t || void 0 === t
              ? null
              : t.step
              ? l(t.step, t.alt, t.oct)
              : t < 0 || t > 6
              ? null
              : C.charAt(t) + f(n) + a(r);
          }
          function p(t) {
            if ((r(t) || e(t)) && t >= 0 && t < 128) return +t;
            let n = i(t);
            return n && u(n.midi) ? n.midi : null;
          }
          function s(t, n) {
            let r = p(t);
            return null === r ? null : c(r, n);
          }
          function d(t) {
            return (i(t) || {}).letter;
          }
          function m(t) {
            return (i(t) || {}).acc;
          }
          function h(t) {
            return (i(t) || {}).pc;
          }
          function v(t) {
            return (i(t) || {}).step;
          }
          function g(t) {
            return (i(t) || {}).alt;
          }
          function x(t) {
            return (i(t) || {}).chroma;
          }
          function y(t) {
            return (i(t) || {}).oct;
          }
          let b = /^([a-gA-G])(#{1,}|b{1,}|x{1,}|)(-?\d*)\s*(.*)\s*$/,
            A = [0, 2, 4, 5, 7, 9, 11],
            C = "CDEFGAB";
          (t.regex = o),
            (t.parse = i),
            (t.build = l),
            (t.midi = p),
            (t.freq = s),
            (t.letter = d),
            (t.acc = m),
            (t.pc = h),
            (t.step = v),
            (t.alt = g),
            (t.chroma = x),
            (t.oct = y);
        });
      },
      {},
    ],
    9: [
      function (require, module, exports) {
        module.exports = function (player) {
          player.on = function (event, cb) {
            if (arguments.length === 1 && typeof event === "function")
              return player.on("event", event);
            let prop = "on" + event;
            let old = player[prop];
            player[prop] = old ? chain(old, cb) : cb;
            return player;
          };
          return player;
        };
        function chain(fn1, fn2) {
          return function (a, b, c, d) {
            fn1(a, b, c, d);
            fn2(a, b, c, d);
          };
        }
      },
      {},
    ],
    10: [
      function (require, module, exports) {
        "use strict";
        let player = require("./player");
        let events = require("./events");
        let notes = require("./notes");
        let scheduler = require("./scheduler");
        let midi = require("./midi");
        function SamplePlayer(ac, source, options) {
          return midi(scheduler(notes(events(player(ac, source, options)))));
        }
        if (typeof module === "object" && module.exports)
          module.exports = SamplePlayer;
        if (typeof window !== "undefined") window.SamplePlayer = SamplePlayer;
      },
      {
        "./events": 9,
        "./midi": 11,
        "./notes": 12,
        "./player": 13,
        "./scheduler": 14,
      },
    ],
    11: [
      function (require, module, exports) {
        let midimessage = require("midimessage");
        module.exports = function (player) {
          player.listenToMidi = function (input, options) {
            let started = {};
            let opts = options || {};
            let gain =
              opts.gain ||
              function (vel) {
                return vel / 127;
              };
            input.onmidimessage = function (msg) {
              let mm = msg.messageType ? msg : midimessage(msg);
              if (mm.messageType === "noteon" && mm.velocity === 0) {
                mm.messageType = "noteoff";
              }
              if (opts.channel && mm.channel !== opts.channel) return;
              switch (mm.messageType) {
                case "noteon":
                  started[mm.key] = player.play(mm.key, 0, {
                    gain: gain(mm.velocity),
                  });
                  break;
                case "noteoff":
                  if (started[mm.key]) {
                    started[mm.key].stop();
                    delete started[mm.key];
                  }
                  break;
              }
            };
            return player;
          };
          return player;
        };
      },
      { midimessage: 7 },
    ],
    12: [
      function (require, module, exports) {
        "use strict";
        let note = require("note-parser");
        let isMidi = function (n) {
          return n !== null && n !== [] && n >= 0 && n < 129;
        };
        let toMidi = function (n) {
          return isMidi(n) ? +n : note.midi(n);
        };
        module.exports = function (player) {
          if (player.buffers) {
            let map = player.opts.map;
            let toKey = typeof map === "function" ? map : toMidi;
            let mapper = function (name) {
              return name ? toKey(name) || name : null;
            };
            player.buffers = mapBuffers(player.buffers, mapper);
            let start = player.start;
            player.start = function (name, when, options) {
              let key = mapper(name);
              let dec = key % 1;
              if (dec) {
                key = Math.floor(key);
                options = Object.assign(options || {}, {
                  cents: Math.floor(dec * 100),
                });
              }
              return start(key, when, options);
            };
          }
          return player;
        };
        function mapBuffers(buffers, toKey) {
          return Object.keys(buffers).reduce(function (mapped, name) {
            mapped[toKey(name)] = buffers[name];
            return mapped;
          }, {});
        }
      },
      { "note-parser": 15 },
    ],
    13: [
      function (require, module, exports) {
        "use strict";
        let ADSR = require("adsr");
        let EMPTY = {};
        let DEFAULTS = {
          gain: 1,
          attack: 0.01,
          decay: 0.1,
          sustain: 0.9,
          release: 0.3,
          loop: false,
          cents: 0,
          loopStart: 0,
          loopEnd: 0,
        };
        function SamplePlayer(ac, source, options) {
          let connected = false;
          let nextId = 0;
          let tracked = {};
          let out = ac.createGain();
          out.gain.value = 1;
          let opts = Object.assign({}, DEFAULTS, options);
          let player = { context: ac, out: out, opts: opts };
          if (source instanceof AudioBuffer) player.buffer = source;
          else player.buffers = source;
          player.start = function (name, when, options) {
            if (player.buffer && name !== null)
              return player.start(null, name, when);
            let buffer = name ? player.buffers[name] : player.buffer;
            if (!buffer) {
              console.warn("Buffer " + name + " not found.");
              return;
            } else if (!connected) {
              console.warn("SamplePlayer not connected to any node.");
              return;
            }
            let opts = options || EMPTY;
            when = Math.max(ac.currentTime, when || 0);
            player.emit("start", when, name, opts);
            let node = createNode(name, buffer, opts);
            node.id = track(name, node);
            node.env.start(when);
            node.source.start(when);
            player.emit("started", when, node.id, node);
            if (opts.duration) node.stop(when + opts.duration);
            return node;
          };
          player.play = function (name, when, options) {
            return player.start(name, when, options);
          };
          player.stop = function (when, ids) {
            let node;
            ids = ids || Object.keys(tracked);
            return ids.map(function (id) {
              node = tracked[id];
              if (!node) return null;
              node.stop(when);
              return node.id;
            });
          };
          player.connect = function (dest) {
            connected = true;
            out.connect(dest);
            return player;
          };
          player.emit = function (event, when, obj, opts) {
            if (player.onevent) player.onevent(event, when, obj, opts);
            let fn = player["on" + event];
            if (fn) fn(when, obj, opts);
          };
          return player;
          function track(name, node) {
            node.id = nextId++;
            tracked[node.id] = node;
            node.source.onended = function () {
              let now = ac.currentTime;
              node.source.disconnect();
              node.env.disconnect();
              node.disconnect();
              player.emit("ended", now, node.id, node);
            };
            return node.id;
          }
          function createNode(name, buffer, options) {
            let node = ac.createGain();
            node.gain.value = 0;
            node.connect(out);
            node.env = envelope(ac, options, opts);
            node.env.connect(node.gain);
            node.source = ac.createBufferSource();
            node.source.buffer = buffer;
            node.source.connect(node);
            node.source.loop = options.loop || opts.loop;
            node.source.playbackRate.value = centsToRate(
              options.cents || opts.cents
            );
            node.source.loopStart = options.loopStart || opts.loopStart;
            node.source.loopEnd = options.loopEnd || opts.loopEnd;
            node.stop = function (when) {
              let time = when || ac.currentTime;
              player.emit("stop", time, name);
              let stopAt = node.env.stop(time);
              node.source.stop(stopAt);
            };
            return node;
          }
        }
        function isNum(x) {
          return typeof x === "number";
        }
        let PARAMS = ["attack", "decay", "sustain", "release"];
        function envelope(ac, options, opts) {
          let env = ADSR(ac);
          let adsr = options.adsr || opts.adsr;
          PARAMS.forEach(function (name, i) {
            if (adsr) env[name] = adsr[i];
            else env[name] = options[name] || opts[name];
          });
          env.value.value = isNum(options.gain)
            ? options.gain
            : isNum(opts.gain)
            ? opts.gain
            : 1;
          return env;
        }
        function centsToRate(cents) {
          return cents ? Math.pow(2, cents / 1200) : 1;
        }
        module.exports = SamplePlayer;
      },
      { adsr: 3 },
    ],
    14: [
      function (require, module, exports) {
        "use strict";
        let isArr = Array.isArray;
        let isObj = function (o) {
          return o && typeof o === "object";
        };
        let OPTS = {};
        module.exports = function (player) {
          player.schedule = function (time, events) {
            let now = player.context.currentTime;
            let when = time < now ? now : time;
            player.emit("schedule", when, events);
            let t, o, note, opts;
            return events.map(function (event) {
              if (!event) return null;
              else if (isArr(event)) {
                t = event[0];
                o = event[1];
              } else {
                t = event.time;
                o = event;
              }
              if (isObj(o)) {
                note = o.name || o.key || o.note || o.midi || null;
                opts = o;
              } else {
                note = o;
                opts = OPTS;
              }
              return player.start(note, when + (t || 0), opts);
            });
          };
          return player;
        };
      },
      {},
    ],
    15: [
      function (require, module, exports) {
        "use strict";
        let REGEX = /^([a-gA-G])(#{1,}|b{1,}|x{1,}|)(-?\d*)\s*(.*)\s*$/;
        function regex() {
          return REGEX;
        }
        let SEMITONES = [0, 2, 4, 5, 7, 9, 11];
        function parse(str, isTonic, tuning) {
          if (typeof str !== "string") return null;
          let m = REGEX.exec(str);
          if (!m || (!isTonic && m[4])) return null;
          let p = { letter: m[1].toUpperCase(), acc: m[2].replace(/x/g, "##") };
          p.pc = p.letter + p.acc;
          p.step = (p.letter.charCodeAt(0) + 3) % 7;
          p.alt = p.acc[0] === "b" ? -p.acc.length : p.acc.length;
          p.chroma = SEMITONES[p.step] + p.alt;
          if (m[3]) {
            p.oct = +m[3];
            p.midi = p.chroma + 12 * (p.oct + 1);
            p.freq = midiToFreq(p.midi, tuning);
          }
          if (isTonic) p.tonicOf = m[4];
          return p;
        }
        function midiToFreq(midi, tuning) {
          return Math.pow(2, (midi - 69) / 12) * (tuning || 440);
        }
        let parser = { parse: parse, regex: regex, midiToFreq: midiToFreq };
        let FNS = [
          "letter",
          "acc",
          "pc",
          "step",
          "alt",
          "chroma",
          "oct",
          "midi",
          "freq",
        ];
        FNS.forEach(function (name) {
          parser[name] = function (src) {
            let p = parse(src);
            return p && typeof p[name] !== "undefined" ? p[name] : null;
          };
        });
        module.exports = parser;
      },
      {},
    ],
  },
  {},
  [1]
);
