!function(e) {
    function userAgent() {
        var e, n, t, r = navigator.userAgent,
            o = navigator.appName,
            a = "" + parseFloat(navigator.appVersion),
            i = parseInt(navigator.appVersion, 10);
        (n = r.indexOf("Opera")) != -1 ? (o = "Opera", a = r.substring(n + 6), (n = r.indexOf("Version")) != -1 && (a = r.substring(n + 8))) : (n = r.indexOf("MSIE")) != -1 ? (o = "Microsoft Internet Explorer", a = r.substring(n + 5)) : (n = r.indexOf("Trident")) != -1 ? (o = "Microsoft Internet Explorer", a = (n = r.indexOf("rv:")) != -1 ? r.substring(n + 3) : "0.0") : (n = r.indexOf("Chrome")) != -1 ? (o = "Chrome", a = r.substring(n + 7)) : (n = r.indexOf("Android")) != -1 ? (o = "Android", a = r.substring(n + 8)) : (n = r.indexOf("Safari")) != -1 ? (o = "Safari", a = r.substring(n + 7), (n = r.indexOf("Version")) != -1 && (a = r.substring(n + 8))) : (n = r.indexOf("Firefox")) != -1 ? (o = "Firefox", a = r.substring(n + 8)) : (e = r.lastIndexOf(" ") + 1) < (n = r.lastIndexOf("/")) && (o = r.substring(e, n), a = r.substring(n + 1), o.toLowerCase() == o.toUpperCase() && (o = navigator.appName)), (t = a.indexOf(";")) != -1 && (a = a.substring(0, t)), (t = a.indexOf(" ")) != -1 && (a = a.substring(0, t)), (t = a.indexOf(")")) != -1 && (a = a.substring(0, t)), i = parseInt("" + a, 10), isNaN(i) && (a = "" + parseFloat(navigator.appVersion), i = parseInt(navigator.appVersion, 10));
        var s = new Object;
        return s.browserName = o, s.fullVersion = a, s.majorVersion = i, s.appName = navigator.appName, s.userAgent = navigator.userAgent, s.platform = navigator.platform, s
    }

    function loadScript(e, n) {
        for (var t = 0; t < document.scripts.length; t++) {
            var r = document.scripts[t].src;
            if (k == r) {
                if (K) return void n();
                var o = newjs.onload;
                return newjs.onreadystatechange = function() {
                    "loaded" !== newjs.readyState && "complete" !== newjs.readyState || (newjs.onreadystatechange = null, K = !0, o(), n())
                }, void(newjs.onload = function() {
                    K = !0, o(), n()
                })
            }
        }
        var a = document.getElementsByTagName("script")[0];
        newjs = document.createElement("script"), newjs.onreadystatechange = function() {
            "loaded" !== newjs.readyState && "complete" !== newjs.readyState || (newjs.onreadystatechange = null, K = !0, n())
        }, newjs.onload = function() {
            K = !0, n()
        }, newjs.onerror = function() {
            log("Error: Cannot load  JavaScript file " + e)
        }, newjs.src = e, newjs.type = "text/javascript", a.parentNode.insertBefore(newjs, a)
    }

    function audioOnProcess(e) {
        if (H = Module.ccall("mid_song_read_wave", "number", ["number", "number", "number", "number"], [W, O, 2 * R, X]), 0 == H) return void free();
        for (var n = Math.pow(2, 15), t = 0; t < R; t++) t < H ? e.outputBuffer.getChannelData(0)[t] = Module.getValue(O + 2 * t, "i16") / n : e.outputBuffer.getChannelData(0)[t] = 0;
        0 == z && (z = S.currentTime)
    }

    function loadInstruments(e, n, t) {
        var o = new XMLHttpRequest;
        o.open("GET", n + t, !0), o.responseType = "arraybuffer", o.onerror = function() {
            log("Error: Cannot retrieve patch file " + n + t)
        }, o.onload = function() {
            if (200 != o.status) return void log("Error: Cannot retrieve patch file " + n + t + " : " + o.status);
            if (F--, FS.createDataFile("pat/", t, new Int8Array(o.response), !0, !0), MIDIjs.message_callback && F > 0 && MIDIjs.message_callback("Instruments to be loaded: " + F), log("Instruments to be loaded: " + F), 0 == F) {
                var a = Module.ccall("mid_istream_open_mem", "number", ["number", "number", "number"], [E, T.length, !1]),
                    s = 32784,
                    u = Module.ccall("mid_create_options", "number", ["number", "number", "number", "number"], [S.sampleRate, s, 1, 2 * R]);
                W = Module.ccall("mid_song_load", "number", ["number", "number"], [a, u]);
                Module.ccall("mid_istream_close", "number", ["number"], [a]);
                Module.ccall("mid_song_start", "void", ["number"], [W]), V = S.createScriptProcessor(R, 0, 1), O = Module._malloc(2 * R), V.onaudioprocess = audioOnProcess, V.connect(S.destination), P = setInterval(callbackInterval, J), MIDIjs.message_callback && MIDIjs.message_callback("Playing: " + e), log("Playing: " + e + " ...")
            }
        }, o.send()
    }

    function ajaxLoad(e) {
        var n = new XMLHttpRequest;
        n.open("GET", e, !0), n.responseType = "arraybuffer", n.onerror = function() {
            log("Error: Cannot preload file " + e)
        }, n.onload = function() {
            if (200 != n.status) return void log("Error: Cannot preload file " + e + " : " + n.status)
        }, n.send()
    }

    function callbackInterval() {
        var e = new Object;
        0 != z ? e.time = S.currentTime - z : e.time = 0, MIDIjs.player_callback && MIDIjs.player_callback(e)
    }

    function suspend() {
        S && S.suspend()
    }

    function resume() {
        if (S && S.resume) return S.resume()
    }

    function play(e) {
        stop(), X = !1, R = B, resumeAndLoad(e)
    }

    function resumeAndLoad(e) {
        S || (window.AudioContext = window.AudioContext || window.webkitAudioContext, S = new AudioContext), S.resume ? S.resume().then(load(e)) : load(e)
    }

    function load(e) {
        z = 0, callbackInterval(), log("Loading libtimidity ... "), loadScript(k, function() {
            loadMidi(e, malloc, null)
        })
    }
    
    function setCurrentTime(currentTime)
    {
        if(S)
        {
            S.currentTime = currentTime;
        }
    }

    function loadMidi(e, n, t) {
        if (-1 != e.indexOf("data:")) {
            var r = e.indexOf(",") + 1,
                o = atob(e.substring(r));
            T = new Uint8Array(new ArrayBuffer(o.length));
            for (var a = 0; a < o.length; a++) T[a] = o.charCodeAt(a);
            return n("data:audio/x-midi ...", T, t)
        }
        log("Loading MIDI file " + e + " ..."), t || MIDIjs.message_callback("Loading MIDI file " + e + " ...");
        var i = new XMLHttpRequest;
        i.open("GET", e, !0), i.responseType = "arraybuffer", i.onerror = function() {
            log("Error: Cannot retrieve MIDI file " + e)
        }, i.onload = function() {
            if (200 != i.status) return void log("Error: Cannot retrieve MIDI file " + e + " : " + i.status);
            log("MIDI file loaded: " + e), T = new Int8Array(i.response);
            var r = n(e, T, t);
            return r
        }, i.send()
    }

    function malloc(e, n, t) {
        E = Module._malloc(n.length), Module.writeArrayToMemory(n, E), rval = Module.ccall("mid_init", "number", [], []);
        var a = Module.ccall("mid_istream_open_mem", "number", ["number", "number", "number"], [E, n.length, !1]),
            s = 32784,
            u = Module.ccall("mid_create_options", "number", ["number", "number", "number", "number"], [S.sampleRate, s, 1, 2 * R]);
        if (W = Module.ccall("mid_song_load", "number", ["number", "number"], [a, u]), rval = Module.ccall("mid_istream_close", "number", ["number"], [a]), F = Module.ccall("mid_song_get_num_missing_instruments", "number", ["number"], [W]), 0 < F)
            for (var l = 0; l < F; l++) {
                var c = Module.ccall("mid_song_get_missing_instrument", "string", ["number", "number"], [W, l]);
                loadInstruments(e, q + "pat/", c)
            } else Module.ccall("mid_song_start", "void", ["number"], [W]), V = S.createScriptProcessor(R, 0, 1), O = Module._malloc(2 * R), V.onaudioprocess = audioOnProcess, V.connect(S.destination), P = setInterval(callbackInterval, J), MIDIjs.message_callback && MIDIjs.message_callback("Playing: " + e), log("Playing: " + e + " ...")
    }

    function noteOn(e, n, t) {
        X || (X = !0, R = L, resumeAndLoad(q + "../midi/initsynth.midi")), 0 != W && Module.ccall("mid_song_note_on", "void", ["number", "number", "number", "number"], [W, e, n, t])
    }

    function startSynth() {
        MIDIjs.noteOn(0, 60, 0)
    }

    function free() {
        V && (V.disconnect(), V.onaudioprocess = 0, V = 0), W && (Module._free(O), Module._free(E), Module.ccall("mid_song_free", "void", ["number"], [W]), W = 0)
    }

    function stop() {
        free(), clearInterval(P), log(G)
    }

    function createLink(e) {
        return "undefined" == typeof N && (N = document.createElement("a")), N.href = e, N.href
    }

    function createBaseURL(e) {
        if (e.indexOf("http:") != -1) return e;
        var n = createLink(e),
            t = n.replace("https:", "http:");
        return t
    }

    function timeInterval() {
        var e = new Object;
        0 == z && (z = (new Date).getTime()), e.time = ((new Date).getTime() - z) / 1e3, MIDIjs.player_callback && MIDIjs.player_callback(e)
    }

    function createBGSound(e) {
        silent(), url = createBaseURL(e);
        var n = document.getElementById("scorioMIDI");
        n ? n.lastChild.setAttribute("src", url) : (n = document.createElement("div"), n.setAttribute("id", "scorioMIDI"), n.innerHTML = '&nbsp;<bgsound src="' + url + '" volume="0"/>', document.body && document.body.appendChild(n)), P = setInterval(timeInterval, J), z = 0, V = n, log("Playing " + url + " ...")
    }

    function silent() {
        if (V) {
            var e = V;
            e.lastChild.setAttribute("src", createBaseURL(q) + "silence.mid"), clearInterval(P), V = 0
        }
        log(G)
    }

    function createMidiObject(e) {
        remove();
        var n = document.getElementById("scorioMIDI");
        n ? n.lastChild.setAttribute("data", e) : (n = document.createElement("div"), n.setAttribute("id", "scorioMIDI"), n.innerHTML = '<object data="' + e + '" autostart="true" volume="0" type="audio/mid"></object>', document.body && document.body.appendChild(n)), P = setInterval(timeInterval, J), z = 0, V = n, log("Playing " + e + " ...")
    }

    function remove() {
        if (V) {
            var e = V;
            e.parentNode.removeChild(e), clearInterval(P), V = 0
        }
        log(G)
    }

    function initAll(e, n, t) {
        var r = Module._malloc(n.length);
        Module.writeArrayToMemory(n, r);
        var o = (Module.ccall("mid_init", "number", [], []), Module.ccall("mid_istream_open_mem", "number", ["number", "number", "number"], [r, n.length, !1])),
            a = 32784,
            i = Module.ccall("mid_create_options", "number", ["number", "number", "number", "number"], [44100, a, 1, 2 * R]),
            s = Module.ccall("mid_song_load", "number", ["number", "number"], [o, i]),
            u = (Module.ccall("mid_istream_close", "number", ["number"], [o]), Module.ccall("mid_song_get_total_time", "number", ["number"], [s]) / 1e3);
        Module.ccall("mid_song_free", "void", ["number"], [s]), Module._free(r), t && t(u)
    }

    function getBaseURL() {
        for (var e = 0; e < document.scripts.length; e++) {
            var n = document.scripts[e].src;
            var t = n.lastIndexOf("/midi.min.js");			
            if (t == n.length - 8) 
            {
                return n.substr(0, t + 1)
            }
            console.log('it1');
            console.log(n);
            console.log(t);
        }
        for (var e = 0; e < document.scripts.length; e++) {
            var n = document.scripts[e].src;
            var t = n.lastIndexOf("/midi.js");			
            if (t == n.length - 4) 
            {
                return n.substr(0, t + 1)
            }
            console.log('it2');
            console.log(n);
            console.log(t);
        }
        return null
    }

    function log(e) {
        U && console.log(e)
    }
    try {
        e.MIDIjs = new Object, e.MIDIjs.initError = "initializing ...";
        var C, O, E, T, k, N, P, S = null,
            V = 0,
            L = 512,
            B = 8192,
            R = B,
            F = 0,
            H = 0,
            W = 0,
            q = "",
            z = 0,
            G = "",
            X = !1,
            U = !1,
            J = 100,
            K = !1;
        q = getBaseURL(), k = q + "libtimidity.min.js";
        var Q = userAgent();
        try {
            ("iPhone" == Q.platform || "iPod" == Q.platform || "iPad" == Q.platform) && Q.majorVersion <= 6 ? C = "none" : (window.AudioContext = window.AudioContext || window.webkitAudioContext, S = new AudioContext, C = "WebAudioAPI")
        } catch (Y) {
            C = "Microsoft Internet Explorer" == Q.browserName ? "bgsound" : "Android" == Q.browserName ? "none" : "object"
        }
        e.MIDIjs.set_logging = function(e) {
            U = e
        }, e.MIDIjs.get_loggging = function() {
            return U
        }, e.MIDIjs.player_callback = function(e) {}, e.MIDIjs.message_callback = function(e) {}, e.MIDIjs.get_audio_status = function() {
            return G
        }, e.MIDIjs.get_duration = function(e, n) {
            "Microsoft Internet Explorer" == Q.browserName && Q.fullVersion < 10 ? n && n(-1) : loadScript(k, function() {
                loadMidi(e, initAll, n)
            })
        }, e.MIDIjs.pause = function() {
            
        }, e.MIDIjs.resume = function() {
            
        }, e.MIDIjs.resumeWebAudioContext = function() {
            
        }, e.MIDIjs.setCurrentTime = function(currentTime) {
            setCurrentTime(currentTime)
        }, 
        "WebAudioAPI" == C ? (e.MIDIjs.resumeWebAudioContext = resume, e.MIDIjs.pause = suspend, e.MIDIjs.resume = resume, e.MIDIjs.play = play, e.MIDIjs.stop = stop, G = "audioMethod: WebAudioAPI, sampleRate (Hz): " + S.sampleRate + ", audioBufferSize (Byte): " + R, e.MIDIjs.noteOn = noteOn, e.MIDIjs.startSynth = startSynth) : "bgsound" == C ? (e.MIDIjs.play = createBGSound, e.MIDIjs.stop = silent, G = "audioMethod: &lt;bgsound&gt;") : "object" == C ? (e.MIDIjs.play = createMidiObject, e.MIDIjs.stop = remove, G = "audioMethod: &lt;object&gt;") : (e.MIDIjs.play = function(e) {}, e.MIDIjs.stop = function(e) {}, G = "audioMethod: No method found"), "Microsoft Internet Explorer" == Q.browserName && "https:" == location.protocol.toLowerCase() && setTimeout(function() {
            createBGSound(createBaseURL(q) + "silence.mid"), clearInterval(P)
        }, 1), -1 == location.href.indexOf("scorio.com") && -1 == location.href.indexOf("weblily.net") && -1 == location.href.indexOf("local") || "WebAudioAPI" == C && (ajaxLoad(q + "pat/arachno-127.pat"), ajaxLoad(q + "pat/MT32Drums/mt32drum-41.pat"), ajaxLoad(k)), e.MIDIjs.initError = null
    } catch (Z) {
        e.MIDIjs = new Object, e.MIDIjs.initError = Z
    }
}(this);
