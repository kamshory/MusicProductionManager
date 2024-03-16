//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; //stream from getUserMedia()
var recorder; //WebAudioRecorder object
var input; //MediaStreamAudioSourceNode  we'll be recording
var encodingType; //holds selected encoding for resulting audio (file)
var encodeAfterRecord = true; // when to encode

// shim for AudioContext when it's not avb.
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext; //new audio context to help us record

var encodingTypeSelect = document.getElementById("encodingTypeSelect");
var recordButton = document.getElementById("recordButton");
var stopButton = document.getElementById("stopButton");

//add events to those 2 buttons
recordButton.addEventListener("click", startRecording);
stopButton.addEventListener("click", stopRecording);

function startRecording() {
  console.log("startRecording() called");
  var constraints = { audio: true, video: false };
  navigator.mediaDevices
    .getUserMedia(constraints)
    .then(function (stream) {
      __log(
        "getUserMedia() success, stream created, initializing WebAudioRecorder..."
      );
      audioContext = new AudioContext();
      gumStream = stream;
      input = audioContext.createMediaStreamSource(stream);
      encodingType = encodingTypeSelect.value;

      recorder = new WebAudioRecorder(input, {
        workerDir: "assets/sound-recorder/", 
        encoding: encodingType,
        numChannels: 2, 
        onEncoderLoading: function (recorder, encoding) {
           __log("Loading " + encoding + " encoder...");
        },
        onEncoderLoaded: function (recorder, encoding) {
          __log(encoding + " encoder loaded");
        },
      });

      recorder.onComplete = function (recorder, blob) {
        __log("Encoding complete");
        createDownloadLink(blob, recorder.encoding);
      };

      recorder.setOptions({
        timeLimit: 120,
        encodeAfterRecord: encodeAfterRecord,
        ogg: { quality: 0.5 },
        mp3: { bitRate: 160 },
      });

      recorder.startRecording();

      __log("Recording started");
    })
    .catch(function (err) {
      recordButton.disabled = false;
      stopButton.disabled = true;
    });

  //disable the record button
  recordButton.disabled = true;
  stopButton.disabled = false;
}

function stopRecording() {
  console.log("stopRecording() called");

  gumStream.getAudioTracks()[0].stop();

  //disable the stop button
  stopButton.disabled = true;
  recordButton.disabled = false;

  recorder.finishRecording();

  __log("Recording stopped");
}

function createDownloadLink(blob, encoding) {
  var url = URL.createObjectURL(blob);
  var au = document.createElement("audio");
  var li = document.createElement("li");
  var link = document.createElement("a");

  //add controls to the <audio> element
  au.controls = true;
  au.src = url;

  //link the a element to the blob
  link.href = url;
  link.download = new Date().toISOString() + "." + encoding;
  link.innerHTML = link.download;

  //add the new audio and a elements to the li element
  let _au = document.createElement("div");
  _au.classList.add("audio-container");
  _au.appendChild(au);

  let _link = document.createElement("div");
  _link.classList.add("button-container");
  _link.appendChild(link);

  // save
  let _btn1 = document.createElement("button");
  _btn1.setAttribute("class", "btn btn-success button-save");
  _btn1.setAttribute("data-url", link);
  _btn1.textContent = "Save";

  _btn1.addEventListener('click', function(e){
	let elem = e.target;
	audioUrl = elem.getAttribute('data-url');
	uploadAudio(audioUrl, function(base64data){
		console.log(base64data);
	});
  });

  // download
  let _btn2 = document.createElement("button");
  _btn2.setAttribute("class", "btn btn-primary button-download");
  _btn2.setAttribute("data-url", link);
  _btn2.textContent = "Download";

  // delete
  let _btn3 = document.createElement("button");
  _btn3.setAttribute("class", "btn btn-danger button-delete");
  _btn3.setAttribute("data-url", link);
  _btn3.textContent = "Delete";

  li.appendChild(_au);
  li.appendChild(_btn1);
  li.appendChild(document.createTextNode(" "));
  li.appendChild(_btn2);
  li.appendChild(document.createTextNode(" "));
  li.appendChild(_btn3);

  //add the li element to the ordered list
  recordingsList.appendChild(li);
}

const fetchData = async url => {
	const response = await fetch(url, {mode: 'no-cors',})
	const blob = await response.blob()
	return blob
}

async function uploadAudio(url, clbk) {
  const audioBlob = await fetchData(url)
  const reader = new FileReader();
  reader.onload = (e) => {
    const base64data = reader.result;
	if(clbk)
	{
		clbk(base64data);
	}
  };
  reader.onerror = () => {
    console.log("error");
  };
  reader.readAsDataURL(audioBlob);
}

function __log(e, data) {
  log.innerHTML += "\n" + e + " " + (data || "");
}
