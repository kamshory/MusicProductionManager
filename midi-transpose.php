<?php
use MagicObject\Request\InputGet;
use Midi\Midi;

require_once "inc/auth-with-login-form.php";

$inputGet = new InputGet();

if (isset($_POST['transpose'])) {
    require_once __DIR__ . "/lib.ajax/midi-transpose.php";
}

if (isset($song)) {
    $midi = new Midi();
    if (file_exists($song->getFilePathMidi())) {
        $midi->importMid($song->getFilePathMidi());

        $channelList = $midi->getChannelList();
        $trList = array();
        $chList = array();
        foreach($channelList as $trackNumber=>$track)
        {
            if(!empty($track))
            {
                $trList[] = $trackNumber;
                foreach($track as $ch)
                {
                    $chList[] = $ch;
                }
            }
        }
        $chList = array_unique($chList);

?>

<h3 style="font-size: 18px; padding-bottom:2px;"><?php echo $song->getName();?></h3>
<?php
require_once __DIR__ . "/inc/menu-song.php";
?>

        <div class="main-content">
            <form action="" method="post" onsubmit="return confirm('Are you sure to transpose MIDI?');">
                <table class="table table-responsive">
                    <tbody>
                        <tr>
                            <td>Title</td>
                            <td><?php echo $song->getName(); ?></td>
                        </tr>
                        <tr>
                            <td>Track</td>
                            <td><select name="track_number" id="track_number" class="form-control">
                                <option value="all">&mdash; All &mdash;</option>
                                <?php 
                                foreach($trList as $i=>$tr)
                                {
                                    ?>
                                    <option value="<?php echo $tr;?>">Track <?php echo $tr;?></option>
                                    <?php
                                }
                                ?>
                            </select></td>
                        </tr>
                        <tr>
                            <td>Channel</td>
                            <td><select name="channel_number" id="channel_number" class="form-control">
                                <option value="all">&mdash; All &mdash;</option>
                                <?php 
                                foreach($chList as $i=>$ch)
                                {
                                    ?>
                                    <option value="<?php echo $ch;?>"<?php echo $ch == $song->getMidiVocalChannel() ? ' selected' : '';?><?php echo $ch == 10 ? ' disabled' : '';?>>Channel <?php echo $ch;?></option>
                                    <?php
                                }
                                ?>
                            </select></td>
                        </tr>
                        <tr>
                            <td>Semitone</td>
                            <td><input type="number" step="1" name="semitone" id="semitone" class="form-control" value="0"></td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="song_id" value="<?php echo $song->getSongId(); ?>">
                <button type="submit" class="btn btn-success" name="transpose">Transpose</button>
                <input type="button" class="btn btn-primary" value="Download MIDI" onclick="window.open('midi.php?action=download&song_id=<?php echo $song->getSongId();?>');">
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