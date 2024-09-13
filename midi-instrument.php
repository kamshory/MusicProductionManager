<?php
use Midi\MidiInstrument;

require_once "inc/auth-with-login-form.php";
if(isset($song))
{
$midi = new MidiInstrument();
if(file_exists($song->getFilePathMidi()))
{
$midi->importMid($song->getFilePathMidi());

$list = $midi->getMidInstrumentList();

$instrumentName = $midi->getInstrumentList();
?>

<h3 style="font-size: 18px; padding-bottom:2px;"><?php echo $song->getName();?></h3>
<?php
require_once __DIR__ . "/inc/menu-song.php";
?>

<style>
    .selectize-control{
        display: inline-block;
    }
    .selectize-input{
        max-width: calc(100% - 0px);
        width: 300px;
        box-sizing: border-box;
        vertical-align: middle;
    }
    .select-wrapper .selectize-input{
        max-width: calc(100% - 0px);
        width: 300px;
        box-sizing: border-box;
        vertical-align: middle;
    }
    .select-wrapper select{
        max-width: calc(100% - 0px);
        width: 300px;
        box-sizing: border-box;
    }
    .select-wrapper .selectize-control.form-control{
        display: inline-block;
        max-width: calc(100% - 0px);
        width: 300px;
    }
    
    .midi-channel .selectize-input{
        max-width: calc(100% - 20px);
        width: 280px;
        box-sizing: border-box;
        vertical-align: middle;
    }
    
    .midi-channel select{
        max-width: calc(100% - 20px);
        width: 280px;
        box-sizing: border-box;
    }
    .midi-channel .selectize-control.form-control{
        display: inline-block;
        width: 100%;
        min-width: 300px;
    }
    
    .selectize-control.single .selectize-input::after{
        right: 10px !important;
    }
    input[type="select-one"]{
        box-sizing: border-box;
    }
    .track-label, .channel-label{
        padding: 2px 0;
    }
    
    ul.midi-program{
        padding: 0 0;
        margin: 0;
        list-style-type: none;
        padding-left: 20px;
    }
    ul.midi-program > li{
        padding: 0 0 0 0;
        margin: 0;
    }
    ul.channel-child{
        padding: 0;
        margin: 0;
        list-style-type: none;
        padding-left: 20px;
    }
    ul.channel-child > li{
        padding: 0;
        margin: 0;
    }
    .button-area{
        padding: 5px 0;
    }
    .midi-program .form-control{
        display: inline-block;
    }
    
    
</style>
<form action="">
<ul class="midi-program">

<?php
foreach($list->program->parsed as $trackNumber=>$track)
{
    $trackName = '';
    if(isset($track[0]) && isset($track[0]['track_name']) && !empty($track[0]['track_name']))
    {
        $trackName = ' &mdash; ' . $track[0]['track_name'];
    }
    ?>
    <li class="midi-track" data-track-number="<?php echo $trackNumber;?>">    
    <div class="track-label">Track <?php echo $trackNumber;?><?php echo $trackName;?></div>
    <?php
    
    $inst = array();
    foreach($track as $index=>$instrument)
    {
        $inst[] = $instrument['program'];
        $ch[] = $instrument['channel'];
    }
    $inst = array_unique($inst);
    $ch = array_unique($ch);
    $parentInst = count($inst) == 1 ? $inst[0] : "";
    ?>
    <div class="select-wrapper" data-track-number="<?php echo $trackNumber;?>">
    
    <?php
    if(count($inst) == 1)
    {
    ?>
    <select class="form-control channel-parent" data-value="<?php echo $parentInst;?>"></select> 
    <button type="button" class="btn btn-primary apply-to-all">&#8595;</button>
    <?php
    }
    ?>
    </div>
    <ul class="channel-child">
    <?php
    foreach($track as $index=>$instrument)
    {
        ?>
        <li class="midi-channel" data-index="<?php echo $index;?>" data-channel-number="<?php echo $instrument['channel'];?>">
            <div>
            <div class="channel-label">Channel <?php echo $instrument['channel'];?></div>
            <div class="select-wrapper">
            <select class="form-control midi-instrument" data-value="<?php echo $instrument['program'];?>"></select>
            </div>
            </div>
        </li>
        <?php
    }
    ?>
    </ul>
    </li>
    <?php
}

?>
</ul>
<div class="button-area">
<input type="button" class="btn btn-success" value="Update Instrument" onclick="updateInstrument();">
<input type="button" class="btn btn-primary" value="Download MIDI" onclick="window.open('midi.php?action=download&song_id=<?php echo $song->getSongId();?>');">
<input type="hidden" name="song_id" value="<?php echo $song->getSongId();?>">
</div>
</form>
<script>
    let midiProgram = <?php echo json_encode($list->program->parsed);?>; 
    let instrumentList = <?php echo json_encode($instrumentName, JSON_FORCE_OBJECT);?>; 
</script>
<script src="assets/js/selectize.min.js"></script>
<link rel="stylesheet" href="assets/css/selectize.css" />

<script>
    function getData()
    {
        let tracks = document.querySelectorAll('li.midi-track');
        let trackData = [];
        for(let i = 0; i < tracks.length; i++)
        {
            let track = tracks[i];
            let trackNumber = track.getAttribute('data-track-number');
            let channels = track.querySelectorAll('li.midi-channel');
            let channelData = [];
            for(let j = 0; j < channels.length; j++)
            {
                let channel = channels[j];
                let channelNumber = channel.getAttribute('data-channel-number');
                let index = channel.getAttribute('data-index');
                let program = channel.querySelector('select').value;
                channelData.push({
                    'channel':channelNumber,
                    'index':index,
                    'program':program
                });
            }
            trackData[trackNumber] = channelData;
        }
        return trackData;
    }
    function updateInstrument()
    {
        let songId = $('[name="song_id"]').val();
        let newInstrument = getData();
        $.ajax({
            type:'POST',
            url:'lib.ajax/midi-update-instrument.php',
            data:{
                song_id:songId,
                new_instrument:JSON.stringify(newInstrument)
            },
            success: function(data)
            {
                console.log(data);
            }
        });
    }
    window.onload = function(){
        for(let t in midiProgram)
        {
            if(midiProgram.hasOwnProperty(t))
            {
                let track = midiProgram[t];
                for(let i in track)
                {
                    let channel = track[i].channel;
                    let value = track[i].program;
                    appendOptionParent(t, value, instrumentList);
                    appendOptionChild(t, channel, i, value, instrumentList);
                }
            }
        }
        
        let btns = document.querySelectorAll('.apply-to-all');
        for(let i = 0; i < btns.length; i++)
        {
            let btnx = btns[i];
            btnx.addEventListener('click', function(e){  
                let btn = e.target;            
                let par = $(btn).closest('div')[0];
                let value = par.querySelector('select.channel-parent').value;
                let trackNumber = par.getAttribute('data-track-number');
                let grandPar = $(par).closest('li')[0];
                let chs = grandPar.querySelectorAll('ul.channel-child li.midi-channel');
                for(let j = 0; j < chs.length; j++)
                {
                    let ch = chs[j];
                    let channelNumber = ch.getAttribute('data-channel-number');
                    let index = ch.getAttribute('data-index');
                    updateOptionChild(trackNumber, channelNumber, index, value);
                }
            });
        }
    };
    function appendOptionParent(track, value, instList)
    {
        let selector = document.querySelector('li.midi-track[data-track-number="'+track+'"] select.channel-parent');
        if(selector != null)
        {
            selector.appendChild(new Option('— Select One —', ''));
            for(let p in instList)
            {
                if(instList.hasOwnProperty(p))
                {
                    let opt = new Option(p+' — '+instList[p], p);
                    selector.appendChild(opt);
                }
            }
            value = value + ''; // convert to string
            if(value != '')
            {
                selector.value = value;
            }
            $(selector).selectize({
                //sortField: 'text'
            });
        }
    }
    function updateOptionParent(track, value)
    {
        let selector = document.querySelector('li.midi-track[data-track-number="'+track+'"] select.channel-parent');
        if(selector != null)
        {
            value = value + ''; // convert to string
            if(value != '')
            {
                selector.value = value;
            }
            $(selector).selectize()[0].selectize.destroy();
            $(selector).selectize({
                //sortField: 'text'
            });
        }
    }
    function appendOptionChild(track, channel, index, value, instList)
    {
        let selector = document.querySelector('li.midi-track[data-track-number="'+track+'"] ul.channel-child li.midi-channel[data-index="'+index+'"][data-channel-number="'+channel+'"] select');
        for(let p in instList)
        {
            if(instList.hasOwnProperty(p))
            {
                let opt = new Option(p+' — '+instList[p], p);
                selector.appendChild(opt);
            }
        }
        value = value + ''; // convert to string
        if(value != '')
        {
            selector.value = value;
        }
        $(selector).selectize({
            //sortField: 'text'
        });
    }
    function updateOptionChild(track, channel, index, value)
    {
        let selector = document.querySelector('li.midi-track[data-track-number="'+track+'"] ul.channel-child li.midi-channel[data-index="'+index+'"][data-channel-number="'+channel+'"] select');
        if(selector != null)
        {
            $(selector).selectize()[0].selectize.destroy();
            value = value + ''; // convert to string
            if(value != '')
            {
                selector.value = value;
            }           
            $(selector).selectize({
                //sortField: 'text'
            });
        }
    }
</script>
<?php
}
else
{
	?>
	<div class="alert alert-warning">MIDI file not found</div>
    <div class="button-area">
        <button class="btn btn-primary" onclick="window.history.back()">Back</button>
    </div>
	<?php
}
}
?>