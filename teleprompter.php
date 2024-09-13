<?php

use MagicObject\Request\InputGet;
use MusicProductionManager\Data\Entity\Song;


require_once "inc/auth-with-login-form.php";

$song = new Song(null, $database);

$inputGet = new InputGet();
$delayStr = $inputGet->getDelay();
if($delayStr == null || empty($delayStr))
{
    $delay = 0;
}
else
{
    $delay = intval($delayStr);
}
$lyric = array('lyric' => '', 'start'=>0, 'duration'=>0, 'song_id'=>'');
if($inputGet->getSongId() != null)
{
    $song->findOneBySongId($inputGet->getSongId());
    $lyric['lyric'] = $song->getLyric();
    $lyric['duration'] = $song->getDuration() * 1000;
    $lyric['start'] = (time() * 1000) + $delay;
    $lyric['song_id'] = $song->getSongId();
}


require_once "inc/header.php";
?>
    <script>
        let url = 'ws://localhost:8889/';
        if (typeof wsReconnectInterval == "undefined") {
            var wsReconnectInterval = 10000;
        }

        function connect() {
            let ws = new WebSocket(url);
            ws.onopen = function () {
                // subscribe to some channels
                console.log('Connected');
            };

            ws.onmessage = function (e) {
                processIncommingData(e.data);
            };

            ws.onclose = function (e) {
                console.log("Socket is closed. Reconnect will be attempted in " + wsReconnectInterval + " millisecond.", e.reason);
                setTimeout(function () {
                    // create new connection onclose
                    connect();
                }, wsReconnectInterval);
            };

            ws.onerror = function (err) {
                console.error("Socket encountered error: ", err.message, "Closing socket");
                ws.close();
            };
        }
        if (typeof wsEndpoint != "undefined") {
            connect();
        }

        function processIncommingData(message) {
            console.log(message);
        }

        function getUrl(originalUrl, tag)
        {
            // construct URL here
            return originalUrl;
        }

        window.onload = function()
        {
            console.log('connecting');
            connect();
        }
    let data = <?php echo json_encode($lyric);?>;

    </script>
    <script src="karaoke-js.js"></script>



<style> 
    .teleprompter
    {
        position: relative;
        width: calc(100% + 40px);
        height: calc(100vh - 160px);
        background-color: white;
        overflow: hidden;
        margin: 0px -20px;
        white-space: nowrap;
        text-transform: uppercase;
    }
    @media screen and (min-width: 1200px) {
        .main .teleprompter{
            margin: 0;
            width: 100%;
        }
        
    }
    
    .teleprompter-container{
        position: relative;
        width: 100%;
    }
    .teleprompter-container > div{
        position: absolute;
        text-align: center;
        width: 100%;
        border-top: 1px solid #fafafa;
        padding-top: 5px;
        box-sizing: border-box;
    }
    .marked{
        background-color: #cdff43c4;
        color: #222222;
    }
</style>

<div class="teleprompter">
    <div class="teleprompter-container"></div>
</div>
<script>
    let karaoke = null;
    if(typeof data.lyric != 'undefined' && data.lyric != '')
    {
        karaoke = new Karaoke(data, '.teleprompter-container');      
        animate();
    }
    function animate()
    {
        karaoke.animate();
        requestAnimationFrame(animate);
    }
    </script>

<?php
require_once "inc/footer.php";
?>