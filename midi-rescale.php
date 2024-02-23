<?php

use MagicObject\Request\PicoRequest;
use Midi\MidiDuration;

require_once "inc/auth-with-login-form.php";

$inputGet = new PicoRequest(INPUT_GET);

if(isset($_POST['update']))
{
    require_once __DIR__ . "/lib.ajax/midi-rescale.php";
}

if (isset($song)) {
    $midi = new MidiDuration();
    if (file_exists($song->getFilePathMidi())) {
        $midi->importMid($song->getFilePathMidi());

        $tempo = $midi->getBpm();
        $timeBase = $midi->getTimebase();
        $duration = $midi->getDuration();
        $ts = $midi->getTimeSignature();
        $timeSignature = 'n/a';
        if(isset($ts) && is_array($ts) && !empty($ts))
        {
            $arr0 = $ts[0];
            if(!empty($arr0))
            {
                $ts2 = explode(' ', $arr0[0]['time_signature']);
                $timeSignature = $ts2[0];
            }
        }

?>

        <div class="main-content">
            <form action="" method="post" onsubmit="return confirm('Are you sure to rescale MIDI?');">
                <table class="table table-responsive">
                    <tbody>
                        <tr>
                            <td>Title</td>
                            <td><?php echo $song->getTitle();?></td>
                        </tr>
                        <tr>
                            <td>Time Sinature</td>
                            <td><?php echo $timeSignature;?></td>
                        </tr>
                        <tr>
                            <td>Time Base</td>
                            <td><?php echo $timeBase;?></td>
                        </tr>
                        <tr>
                            <td>Tempo</td>
                            <td><?php echo $tempo;?></td>
                        </tr>
                        <tr>
                            <td>Duration</td>
                            <td><?php echo $duration;?></td>
                        </tr>
                        <tr>
                            <td>Rescale</td>
                            <td><select name="scale" id="scale" class="form-control">
                                <option value="1/1">1</option>
                                <option value="1/2">1/2</option>
                                <option value="1/4">1/4</option>
                                <option value="1/8">1/8</option>
                                <option value="2/1">2</option>
                                <option value="4/4">4</option>
                                <option value="8/1">8</option>
                            </select></td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="song_id" value="<?php echo $song->getSongId();?>">
                <button type="submit" class="btn btn-success" name="update">Update</button>
            </form>
        <?php
    } else {
        ?>
            <div class="alert alert-warning">MIDI file not found</div>
            <div class="button-area">
                <button class="btn btn-primary" onclick="window.history.back()">Back</button>
            </div>
    <?php
    }
}
    ?>