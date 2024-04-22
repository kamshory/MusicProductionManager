let ctx;
!function(event) {
    
    function userAgent() {
        let spacePos;
        let browser;
        let versionFloatSeparator;
        let userAgent = navigator.userAgent;
        let browserName = navigator.appName;
        let versionFloat = "" + parseFloat(navigator.appVersion);
        let versionInteger = parseInt(navigator.appVersion, 10);
        if(userAgent.indexOf("Opera") != -1)
        {
            browserName = "Opera";
            versionFloat = userAgent.substring(browser + 6);
            if(userAgent.indexOf("Version") != -1)
            {
                versionFloat = userAgent.substring(browser + 8);
            }
        }
        else if(userAgent.indexOf("MSIE") != -1)
        {
            browserName = "Microsoft Internet Explorer";
            versionFloat = userAgent.substring(browser + 5);
        } 
        else if(userAgent.indexOf("Trident") != -1)
        {
            browserName = "Microsoft Internet Explorer";
            if(userAgent.indexOf("rv:") != -1)
            {
                versionFloat = userAgent.substring(browser + 3);
            }
            else 
            {
                versionFloat = "0.0";
            }
        } 
        else if(userAgent.indexOf("Chrome") != -1)
        {
            browserName = "Chrome";
            versionFloat = userAgent.substring(browser + 7);
        } 
        else if(userAgent.indexOf("Android") != -1)
        {
            browserName = "Android";
            versionFloat = userAgent.substring(browser + 8);
        }
        else if(userAgent.indexOf("Safari") != -1)
        {
            browserName = "Safari";
            versionFloat = userAgent.substring(browser + 7);
            if(userAgent.indexOf("Version") != -1)
            {
                versionFloat = userAgent.substring(browser + 8);
            }
        } 
        else if(userAgent.indexOf("Firefox") != -1)
        {
            browserName = "Firefox";
            versionFloat = userAgent.substring(browser + 8);
        }
        else if((userAgent.lastIndexOf(" ") + 1) < userAgent.lastIndexOf("/"))
        {
            spacePos = userAgent.lastIndexOf(" ") + 1;
            browserName = userAgent.substring(browser, spacePos);
            versionFloat = userAgent.substring(browser + 1);
            if(browserName.toLowerCase() == browserName.toUpperCase()) 
            {
                browserName = navigator.appName;
                versionFloatSeparator = versionFloat.indexOf(";");
                if(versionFloatSeparator != -1)
                {
                    versionFloat = versionFloat.substring(0, versionFloatSeparator);
                }
                versionFloatSeparator = versionFloat.indexOf(" ");
                if(versionFloatSeparator != -1)
                {
                    versionFloat = versionFloat.substring(0, versionFloatSeparator);
                }
                versionFloatSeparator = versionFloat.indexOf(")")
                if(versionFloatSeparator != -1)
                {
                    versionFloat = versionFloat.substring(0, versionFloatSeparator);
                }
                versionInteger = parseInt("" + versionFloat, 10);
                if(isNaN(versionInteger))
                {
                    versionFloat = "" + parseFloat(navigator.appVersion);
                } 
                versionInteger = parseInt(navigator.appVersion, 10);
            }
            
        }
        let ua = new Object;
        ua.browserName = browserName;
        ua.fullVersion = versionFloat;
        ua.majorVersion = versionInteger;
        ua.appName = navigator.appName;
        ua.userAgent = navigator.userAgent;
        ua.platform = navigator.platform;
        return ua;
    }

    function loadScript(name, clbk) {
        for (let t = 0; t < document.scripts.length; t++) //NOSONAR
        {
            let jsURL = document.scripts[t].src;
            if (scriptName == jsURL) 
            {
                if (scriptLoaded)
                {
                    return clbk();
                }
                let fn = newjs.onload;
                newjs.onreadystatechange = function() {
                    if("loaded" == newjs.readyState || "complete" == newjs.readyState )
                    {
                        newjs.onreadystatechange = null;
                        scriptLoaded = !0;
                        fn();
                        clbk();
                    }
                };
                newjs.onload = function() {
                    scriptLoaded = !0;
                    fn();
                    clbk();
                }
            }
        }
        let jsNode = document.getElementsByTagName("script")[0];
        newjs = document.createElement("script");
        newjs.onreadystatechange = function() {
           if("loaded" == newjs.readyState || "complete" == newjs.readyState)
           {
               newjs.onreadystatechange = null;
               scriptLoaded = !0;
               clbk();
           }
        };
        newjs.onload = function() {
            scriptLoaded = !0;
            clbk();
        };
        newjs.onerror = function() {
            log("Error: Cannot load  JavaScript file " + name);
        };
        newjs.src = name;
        newjs.type = "text/javascript";
        jsNode.parentNode.insertBefore(newjs, jsNode);
    }

    function processAudio(audioProcessingEvent) {
        
        songNumber = Module.ccall("mid_song_read_wave", "number", ["number", "number", "number", "number"], [song, generalBuffer1, 2 * audioBufferSize, isPaused]);
        if (0 == songNumber) 
        {
            return free();
        }
        let buff = [];
        for (let n = Math.pow(2, 15), i = 0; i < audioBufferSize; i++)
        {
            if(i < songNumber)
            {
                audioProcessingEvent.outputBuffer.getChannelData(0)[i] = buff[i] = Module.getValue(generalBuffer1 + 2 * i, "i16") / n;
            }  
            else
            {
                audioProcessingEvent.outputBuffer.getChannelData(0)[i] = buff[i] = 0;
            }
        }
        bufVisual = buff;
        if(0 == startTime)
        {
            startTime = audioContext.currentTime;   
        }
    }
    function draw() {
        event.MIDIjs.visualization(bufVisual);
        if(isPlaying)
        {
            requestAnimationFrame(draw);
        }
        else
        {
            drawed = false;
        }
    };

    function loadInstruments(midiURL, instrumentURL, missingInstrument) {
        let req = new XMLHttpRequest;
        req.open("GET", instrumentURL + missingInstrument, !0);
        req.responseType = "arraybuffer";
        req.onerror = function() {
            log("Error: Cannot retrieve patch file " + instrumentURL + missingInstrument)
        };
        req.onload = function() {
            if (200 != req.status)
            {
                return log("Error: Cannot retrieve patch file " + instrumentURL + missingInstrument + " : " + req.status);
            }
            instrumentToBeLoad--;
            FS.createDataFile("pat/", missingInstrument, new Int8Array(req.response), !0, !0);
            if(MIDIjs.messageCallback && instrumentToBeLoad > 0)
            {
                MIDIjs.messageCallback("Loading Instruments: " + instrumentToBeLoad);
                log("Loading Instruments: " + instrumentToBeLoad)
            } 
            if (0 == instrumentToBeLoad) {
                let a = Module.ccall("mid_istream_open_mem", "number", ["number", "number", "number"], [generalBuffer2, midiAudioData.length, !1]);
                let s = 32784;
                let u = Module.ccall("mid_create_options", "number", ["number", "number", "number", "number"], [audioContext.sampleRate, s, 1, 2 * audioBufferSize]);
                song = Module.ccall("mid_song_load", "number", ["number", "number"], [a, u]);
                Module.ccall("mid_istream_close", "number", ["number"], [a]);
                Module.ccall("mid_song_start", "void", ["number"], [song]);
                scriptProcessor = audioContext.createScriptProcessor(audioBufferSize, 0, 1);
                generalBuffer1 = Module._malloc(2 * audioBufferSize);
                scriptProcessor.onaudioprocess = processAudio;
                scriptProcessor.connect(audioContext.destination);
                clearInterval(playingInterval);
                playingInterval = setInterval(callbackInterval, timeInterval);
                if(MIDIjs.messageCallback)
                {
                    MIDIjs.messageCallback("Playing MIDI");
                }
                log("Playing: " + midiURL + " ...")
            }
        };
        req.send()
    }

    function ajaxLoad(e) {
        let req = new XMLHttpRequest;
        req.open("GET", e, !0);
        req.responseType = "arraybuffer";
        req.onerror = function() {
            log("Error: Cannot preload file " + e)
        };
        req.onload = function() {
            if (200 != req.status) 
            {
                return log("Error: Cannot preload file " + e + " : " + req.status);
            }
        };
        req.send()
    }
    function setCurrentTime(par1, par2, par3)
    {
        // do nothing
    }
    function callbackInterval() 
    {
        let message = new Object;
        message.audioMethod = event.MIDIjs.data.audioMethod;
        message.isPlaying = isPlaying;
        message.duration = event.MIDIjs.data.duration;
        if(0 != startTime)
        {
            message.time = audioContext.currentTime - startTime;
        }
        else
        {
            message.time = 0;
        } 
        if(message.time > message.duration && message.duration > 1)
        {
            stop();
            event.MIDIjs.on_ended();
        }
        if(MIDIjs.playerCallback)
        {
            MIDIjs.playerCallback(message);
        }
    }
    function resfreshInterval() {
        let message = new Object;
        message.audioMethod = event.MIDIjs.data.audioMethod;
        message.isPlaying = isPlaying;
        message.duration = event.MIDIjs.data.duration;
        if(0 == startTime)
        {
            startTime = (new Date).getTime();
        } 
        message.time = ((new Date).getTime() - startTime) / 1000;
        if(MIDIjs.playerCallback)
        {
            MIDIjs.playerCallback(message);
        } 
    }
    function pause() {
        isPlaying = false;
        drawed = false;
        MIDIjs.messageCallback("Paused");
        if(audioContext)
        {
            audioContext.suspend();
        }
    }

    function resume() {
        isPlaying = true;
        MIDIjs.messageCallback("Playing MIDI");
        if(!drawed)
        {
            draw();
            drawed = true;
        }
        if (audioContext) 
        {
            return audioContext.resume();
        }
    }
    function play(url, playingOffset) {
        stop();
        isPaused = false;
        isPlaying = true;
        audioBufferSize = bufferSize;
        resumeAndLoad(url, playingOffset)
        if(event.MIDIjs.data.duration > 1)
        {
            clearInterval(playingInterval);
            playingInterval = setInterval(callbackInterval, timeInterval);
        }
        if(!drawed)
        {
            draw();
            drawed = true;
        }
    }

    function resumeAndLoad(url, playingOffset) 
    {
        playingOffset = playingOffset || 0;
        if(!audioContext)
        {
            window.AudioContext = window.AudioContext || window.webkitAudioContext;
            audioContext = new AudioContext;
        }

        if (audioContext.resume) 
        {
            audioContext.resume().then(load(url, playingOffset))
        } 
        else 
        {
            load(url, playingOffset)
        }
    }

    function load(midiUrl, playingOffset) 
    {
        playingOffset = playingOffset || 0;
        playingOffset = 20;
        startTime = 0;
        callbackInterval();
        log("Loading libtimidity ... ");
        loadScript(scriptName, function() 
        {
            loadMidi(midiUrl, playingOffset, malloc, null);
        })
        
        arrayBuffer = audioContext.createBuffer(
            2,
            audioContext.sampleRate * 3,
            audioContext.sampleRate,
          );
        
        // Get an AudioBufferSourceNode.
        // This is the AudioNode to use when we want to play an AudioBuffer
        source = audioContext.createBufferSource();
        // set the buffer in the AudioBufferSourceNode
        source.buffer = arrayBuffer;
        // connect the AudioBufferSourceNode to the
        // destination so we can hear the sound
        source.connect(audioContext.destination);
        // start the source playing
        return true;
    }
    function loadMidi(midiURL, playingOffset, callback1, callback2) 
    {
        playingOffset = playingOffset || 0;
        if (-1 != midiURL.indexOf("data:")) {
            let dataOffset = midiURL.indexOf(",") + 1;
            let midiFileContent = atob(midiURL.substring(dataOffset));
            midiAudioData = new Uint8Array(new ArrayBuffer(midiFileContent.length));
            for (let a = 0; a < midiFileContent.length; a++) 
            {
                midiAudioData[a] = midiFileContent.charCodeAt(a);
            }
            return callback1("data:audio/x-midi ...", playingOffset, midiAudioData, callback2)
        }
        log("Loading MIDI file " + midiURL + " ...");
        if(!callback2)
        {
            MIDIjs.messageCallback("Loading MIDI");
        } 
        let req = new XMLHttpRequest;
        req.open("GET", midiURL, !0);
        req.responseType = "arraybuffer";
        req.onerror = function() {
            log("Error: Cannot retrieve MIDI file " + midiURL)
        };
        req.onload = function() {
            if (200 != req.status) 
            {
                return log("Error: Cannot retrieve MIDI file " + midiURL + " : " + req.status);
            }
            log("MIDI file loaded: " + midiURL);
            midiAudioData = new Int8Array(req.response);
            let r = callback1(midiURL, playingOffset, midiAudioData, callback2);
            return r;
        };
        req.send()
    }

    function malloc(midiUrl, playingOffset, data, callback1) {
        playingOffset = playingOffset || 0;
        generalBuffer2 = Module._malloc(data.length);
        Module.writeArrayToMemory(data, generalBuffer2);
        rval = Module.ccall("mid_init", "number", [], []);
        let a = Module.ccall("mid_istream_open_mem", "number", ["number", "number", "number"], [generalBuffer2, data.length, !1]);
        let s = 32784;
        let u = Module.ccall("mid_create_options", "number", ["number", "number", "number", "number"], [audioContext.sampleRate, s, 1, 2 * audioBufferSize]);
        song = Module.ccall("mid_song_load", "number", ["number", "number"], [a, u]);
        rval = Module.ccall("mid_istream_close", "number", ["number"], [a]);
        instrumentToBeLoad = Module.ccall("mid_song_get_num_missing_instruments", "number", ["number"], [song]);
        if (0 < instrumentToBeLoad)
        {
            for (let l = 0; l < instrumentToBeLoad; l++) 
            {
                let missingInstrument = Module.ccall("mid_song_get_missing_instrument", "string", ["number", "number"], [song, l]);
                loadInstruments(midiUrl, baseScriptURL + "pat/", missingInstrument)
            } 
        }
        else 
        {
            Module.ccall("mid_song_start", "void", ["number"], [song]);
            scriptProcessor = audioContext.createScriptProcessor(audioBufferSize, 0, 1);
            generalBuffer1 = Module._malloc(2 * audioBufferSize);
            scriptProcessor.onaudioprocess = processAudio;
            scriptProcessor.connect(audioContext.destination);
            clearInterval(playingInterval);
            playingInterval = setInterval(callbackInterval, timeInterval);
            if(MIDIjs.messageCallback)
            {
                MIDIjs.messageCallback("Playing MIDI");
            }
            log("Playing: " + midiUrl + " ...");
        }
        processMidiData(midiUrl, playingOffset, data, callback1);
    }
    function processMidiData(midiUrl, playingOffset, data, callback1) {
        playingOffset = playingOffset || 0;
        let r = Module._malloc(data.length);
        Module.writeArrayToMemory(data, r);
        let o = (Module.ccall("mid_init", "number", [], []), Module.ccall("mid_istream_open_mem", "number", ["number", "number", "number"], [r, data.length, !1]));
        let a = 32784;
        let i = Module.ccall("mid_create_options", "number", ["number", "number", "number", "number"], [44100, a, 1, 2 * audioBufferSize]);
        let s = Module.ccall("mid_song_load", "number", ["number", "number"], [o, i]);
        let songDuration = (Module.ccall("mid_istream_close", "number", ["number"], [o]), Module.ccall("mid_song_get_total_time", "number", ["number"], [s]) / 1000);      
        event.MIDIjs.data.duration = songDuration;
        event.MIDIjs.on_song_loaded(o, a, i, s, songDuration);
        Module.ccall("mid_song_free", "void", ["number"], [s]);
        Module._free(r);
        
        
        if(callback1)
        {
            callback1(songDuration)
        }
        if(playingOffset > 0)
        {
            setTimeout(function(){
                seek(playingOffset);
            }, playingOffset);
            
        }
    }
    function noteOn(e, n, t) {
        if(!isPaused)
        {
            isPlaying = true;
            audioBufferSize = bSize;
            resumeAndLoad(baseScriptURL + "initsynth.midi");
        }
        if(0 != song)
        {
            Module.ccall("mid_song_note_on", "void", ["number", "number", "number", "number"], [song, e, n, t])
        }
    }

    function startSynth() {
        MIDIjs.noteOn(0, 60, 0);
    }

    function free() {
        if(scriptProcessor){
            scriptProcessor.disconnect();
            scriptProcessor.onaudioprocess = 0;
            scriptProcessor = 0;  
        }
        if(song){
            Module._free(generalBuffer1);
            Module._free(generalBuffer2);
            Module.ccall("mid_song_free", "void", ["number"], [song]);
            song = 0;  
        }
    }

    function stop() {
        isPlaying = false;
        MIDIjs.messageCallback("Stoped");
        free();
        clearInterval(playingInterval);
        log(audioStatus);
    }

    function createLink(inputURL) {
        if("undefined" == typeof link) 
        {
            link = document.createElement("a");
        }
        link.href = inputURL;
        return link.href
    }

    function createBaseURL(inputURL) {
        if (inputURL.indexOf("http:") != -1) 
        {
            return inputURL;
        }
        let linkURL = createLink(inputURL);
        let fixedLinkURL = linkURL.replace("https:", "http:");
        return fixedLinkURL
    }

    function createBGSound(sonudURL) {
        silent();
        let url = createBaseURL(sonudURL);
        let domElement = document.getElementById("planetMIDI");
        if(domElement)
        {
            domElement.lastChild.setAttribute("src", url)
        }
        else
        {
            domElement = document.createElement("div");
            domElement.setAttribute("id", "planetMIDI");
            domElement.innerHTML = '&nbsp;<bgsound src="' + url + '" volume="0"/>';
            if(document.body)
            {
                document.body.appendChild(domElement);
            } 
        } 
        clearInterval(playingInterval);
        playingInterval = setInterval(resfreshInterval, timeInterval);
        startTime = 0;
        scriptProcessor = domElement;
        log("Playing " + url + " ...");
    }

    function silent() {
        if (scriptProcessor) 
        {
            let e = scriptProcessor;
            e.lastChild.setAttribute("src", createBaseURL(baseScriptURL) + "silence.mid");
            clearInterval(playingInterval);
            scriptProcessor = 0
        }
        log(audioStatus)
    }

    function createMidiObject(url) {
        remove();
        let elem = document.getElementById("planetMIDI");
        if(elem)
        {
            elem.lastChild.setAttribute("data", url)
        }
        else
        { 
            elem = document.createElement("div");
            elem.setAttribute("id", "planetMIDI");
            elem.innerHTML = '<object data="' + url + '" autostart="true" volume="0" type="audio/mid"></object>';
            if(document.body)
            {
                document.body.appendChild(elem);
            } 
            clearInterval(playingInterval);
            playingInterval = setInterval(resfreshInterval, timeInterval);
            startTime = 0;
            scriptProcessor = elem;
            log("Playing " + url + " ...");
        }
    }

    function remove() {
        if (scriptProcessor) {
            let e = scriptProcessor;
            e.parentNode.removeChild(e);
            clearInterval(playingInterval);
            scriptProcessor = 0;
        }
        log(audioStatus)
    }



    function getBaseScriptURL() {
        for (let e = 0; e < document.scripts.length; e++) //NOSONAR
        {
            let n = document.scripts[e].src;
            let t = n.lastIndexOf("/midi.min.js");
            if (t > -1) 
            {
                let arr = n.split("/");
                arr.pop();
                return arr.join("/")+"/";
            }
        }
        for (let e = 0; e < document.scripts.length; e++) //NOSONAR
        {
            let n = document.scripts[e].src;
            let t = n.lastIndexOf("/midi.js");
            if (t > -1) 
            {
                let arr = n.split("/");
                arr.pop();
                return arr.join("/")+"/";
            }
        }
        return null
    }
    function seek(time){
        if(typeof Module != 'undefined')
        {
            Module.ccall("skip_to", "void", ["number", "number"], [song, time*audioContext.sampleRate]);
            startTime = audioContext.currentTime - time;
        }
        
    }
    function getSource()
    {
        return source;
    }
    function getAudioContext()
    {
        return audioContext;
    }

    function log(e) {
        logging && console.log(e)
    }
    var audioMethod, 
        generalBuffer1, 
        generalBuffer2, 
        midiAudioData, 
        scriptName, 
        link, 
        playingInterval, 
        audioContext = null,
        scriptProcessor = 0,
        bSize = 512,
        bufferSize = 8192,
        audioBufferSize = bufferSize,
        instrumentToBeLoad = 0,
        songNumber = 0,
        song = 0,
        baseScriptURL = "",
        startTime = 0,
        audioStatus = "",
        isPaused = !1,
        logging = !1,
        timeInterval = 10,
        scriptLoaded = !1,
        arrayBuffer = null,
        source = null,
        rval = null,
        isPlaying = false,
        newjs = null,
        drawed = false, 
        bufVisual = {}
    ;
    try {
        event.MIDIjs = new Object;
        event.MIDIjs.data = new Object;
        event.MIDIjs.initError = "initializing ...";
        
        baseScriptURL = getBaseScriptURL();
        scriptName = baseScriptURL + "libtimidity.min.js";
        let ua = userAgent();
        drawed = false;
        try 
        {
            if (("iPhone" == ua.platform || "iPod" == ua.platform || "iPad" == ua.platform) && ua.majorVersion <= 6) 
            {
                audioMethod = "none"
            } 
            else 
            {
                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                audioContext = new AudioContext;
                audioMethod = "WebAudioAPI";
            }
        } 
        catch (Y) 
        {
            if("Microsoft Internet Explorer" == ua.browserName)
            {
                audioMethod = "bgsound";
            }
            else if("Android" == ua.browserName)
            {
                audioMethod = "none";
            }
            else
            {
                audioMethod = "object";
            }
        }
        event.MIDIjs.data.duration = 0;
        event.MIDIjs.data.audioMethod = audioMethod;

        event.MIDIjs.setPlayingTime = function() {

        };
        event.MIDIjs.getAudioContext = function() {

        };
        event.MIDIjs.on_ended = function() {

        };
        event.MIDIjs.set_logging = function(e) {
            logging = e
        };
        event.MIDIjs.get_loggging = function() {
            return logging
        }; 
        event.MIDIjs.playerCallback = function(e) {

        }; 
        event.MIDIjs.messageCallback = function(e) {

        }; 
        event.MIDIjs.get_audio_status = function() {
            return audioStatus
        }; 
        event.MIDIjs.getSource = function() {
        }; 
        event.MIDIjs.visualization = function(buff) {
        }; 
        event.MIDIjs.get_duration = function(url, clbk) {
            if("Microsoft Internet Explorer" == ua.browserName && ua.fullVersion < 10) 
            {
                if(clbk)
                {
                    clbk(-1);
                }
            } 
            else 
            {
                loadScript(scriptName, function(){
                    loadMidi(url, processMidiData, clbk);
                })
            }
        }; 
        event.MIDIjs.pause = function() {

        };
        event.MIDIjs.resume = function() {

        };
        event.MIDIjs.resumeWebAudioContext = function() {

        };
        event.MIDIjs.on_song_loaded = function() {

        };
        if ("WebAudioAPI" == audioMethod) 
        {
            event.MIDIjs.resumeWebAudioContext = resume;
            event.MIDIjs.pause = pause;
            event.MIDIjs.resume = resume;
            event.MIDIjs.play = play;
            event.MIDIjs.stop = stop;
            event.MIDIjs.setPlayingTime = setCurrentTime;
            event.MIDIjs.getAudioContext = getAudioContext;
            audioStatus = "audioMethod: WebAudioAPI, sampleRate (Hz): " + audioContext.sampleRate + ", audioBufferSize (Byte): " + audioBufferSize;
            event.MIDIjs.noteOn = noteOn;
            event.MIDIjs.startSynth = startSynth;
            event.MIDIjs.seek = seek;
            event.MIDIjs.getSource = getSource;
        } 
        else if ("bgsound" == audioMethod) {
            event.MIDIjs.play = createBGSound;
            event.MIDIjs.stop = silent;
            audioStatus = "audioMethod: &lt;bgsound&gt;";
        } 
        else if ("object" == audioMethod) 
        {
            event.MIDIjs.play = createMidiObject;
            event.MIDIjs.stop = remove;
            audioStatus = "audioMethod: &lt;object&gt;";
        } 
        else 
        {
            event.MIDIjs.play = function(e) {};
            event.MIDIjs.stop = function(e) {};
            audioStatus = "audioMethod: No method found";
            if("Microsoft Internet Explorer" == ua.browserName && "https:" == location.protocol.toLowerCase())
            {
                setTimeout(function() {
                    createBGSound(createBaseURL(baseScriptURL) + "silence.mid");
                    clearInterval(playingInterval);
                }, 1);
            }
        }
        ctx = audioContext;
        let arrayBuff = [];
        bufVisual = {};
        if(-1 == location.href.indexOf("local") || "WebAudioAPI" == audioMethod)
        {
            ajaxLoad(baseScriptURL + "pat/arachno-127.pat");
            ajaxLoad(baseScriptURL + "pat/MT32Drums/mt32drum-41.pat");
            ajaxLoad(scriptName)
        } 
        event.MIDIjs.initError = null;
    } 
    catch (exc) 
    {
        event.MIDIjs = new Object;
        event.MIDIjs.initError = exc
    }
}(this);
