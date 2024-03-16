<?php

require_once "inc/auth-with-login-form.php";

require_once "inc/header.php";
?>
<style>
    .recording-result{
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .recording-result li{
        margin: 0;
        padding: 5px 0;
    }
</style>
<input type="hidden" id="encodingTypeSelect" name="encodingTypeSelect" value="mp3">
<div id="controls">
    <button class="btn btn-success" id="recordButton">Record</button>
    <button class="btn btn-danger" id="stopButton" disabled>Stop</button>
</div>
<div id="formats"></div>

<pre>Recordings</pre>
<ol class="recording-result" id="recordingsList"></ol>
<pre>Log</pre>
<pre id="log"></pre>

<script src="assets/sound-recorder/WebAudioRecorder.min.js"></script>
<script src="assets/sound-recorder/app.js"></script>

<?php
require_once "inc/footer.php";
?>