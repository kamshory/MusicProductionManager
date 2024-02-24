<?php
$songIdStr = $song->getSongId();
$linkMenu1 = 'subtitle.php?action=edit&song_id='.$songIdStr;
$linkMenu2 = 'midi.php?action=edit-lyric&song_id='.$songIdStr;
$linkMenu3 = 'midi.php?action=edit-instrument&song_id='.$songIdStr;
$linkMenu4 = 'midi.php?action=rescale&song_id='.$songIdStr;
$active1 = '';
$active2 = '';
$active3 = '';
$active4 = '';
if(basename($_SERVER['PHP_SELF']) == 'subtitle.php')
{
    $active1 = ' active';
}
if(basename($_SERVER['PHP_SELF']) == 'midi.php' && @$_GET['action'] == 'edit-lyric')
{
    $active2 = ' active';
}
if(basename($_SERVER['PHP_SELF']) == 'midi.php' && @$_GET['action'] == 'edit-instrument')
{
    $active3 = ' active';
}
if(basename($_SERVER['PHP_SELF']) == 'midi.php' && @$_GET['action'] == 'rescale')
{
    $active4 = ' active';
}
?>
<style>
    .nav-menu-song{
        padding-bottom: 5px;
    }
    .nav-link.active a{
        color: white;
    }
</style>
<ul class="nav nav-tabs nav-menu-song">
    <li class="nav-link<?php echo $active1;?>"><a href="<?php echo $linkMenu1; ?>">Subtitle</a></li>
    <li class="nav-link<?php echo $active2;?>"><a href="<?php echo $linkMenu2; ?>">MIDI Lyric</a></li>
    <li class="nav-link<?php echo $active3;?>"><a href="<?php echo $linkMenu3; ?>">MIDI Instrument</a></li>
    <li class="nav-link<?php echo $active4;?>"><a href="<?php echo $linkMenu4; ?>">MIDI Rescale</a></li>
</ul>