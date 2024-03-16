<?php

require_once "inc/auth-with-login-form.php";

require_once "inc/header.php";
?>

		<select id="encodingTypeSelect" class="form-control">
		  <option value="mp3">MP3 (MPEG-1 Audio Layer III) (.mp3)</option>
		  <option value="ogg">Ogg Vorbis (.ogg)</option>
		</select>
		<div id="controls">
			<button class="btn btn-success" id="recordButton">Record</button>
			<button class="btn btn-danger" id="stopButton" disabled>Stop</button>
		</div>
		<div id="formats"></div>

		<pre>Recordings</pre>
		<ol id="recordingsList"></ol>
		<pre>Log</pre>
		<pre id="log"></pre>

	<script src="assets/sound-recorder/WebAudioRecorder.min.js"></script>
	<script src="assets/sound-recorder/app.js"></script>

<?php
require_once "inc/footer.php";
?>