<?php
use Pico\Pagination\PicoPagination;
use Pico\Data\Entity\Album;
use Pico\Data\Entity\Artist;
use Pico\Data\Entity\EntityMidi;
use Pico\Data\Entity\Genre;
use Pico\Data\Entity\Midi;
use Pico\Data\Tools\SelectOption;
use Pico\Database\PicoPagable;
use Pico\Database\PicoPage;
use Pico\Database\PicoSortable;
use Pico\Exceptions\NoRecordFoundException;
use Pico\Request\PicoFilterConstant;
use Pico\Request\PicoRequest;
use Pico\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new PicoRequest(INPUT_GET);
if($inputGet->equalsAction('compose') && $inputGet->getMidiId() != null)
{
  $midi = new Midi(null, $database);
  try
  {
  $midi->findOneByMidiId($inputGet->getMidiId());
  
  ?>
<link rel="stylesheet" href="lib/midi-editor.css">
<script src="lib/midi-editor.js"></script>
<script src="lib/ajax.js"></script>
<link rel="stylesheet" href="lib/icon.css">

  <div class="srt-editor editor1">
      <div class="row">
          <div class="col col-7" style="display: none;">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Timeline</button>
                  </li>
                  <li class="nav-item" role="presentation">
                      <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Raw</button>
                  </li>
              </ul>
              <div class="tab-content" id="srt-tab-content">
                  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                      <!-- list begin -->
                      <div class="srt-list-wrapper">
                          <div class="srt-list-container">

                          </div>
                      </div>
                      <!-- list end -->

                  </div>
                  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                      <div class="srt-raw">
                          <textarea class="srt-text-raw" spellcheck="false"></textarea>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col col-5">
              <div class="player">
                  <div class="text-display-container" style="display: none;">
                      <div class="text-display">
                          <div class="text-display-inner d-flex align-items-center justify-content-center"></div>
                      </div>
                  </div>
                  <div class="srt-zoom-control-wrapper">
                  <input type="range" class="srt-zoom-control" min="0" max="8" step="1" list="input-markers">
                  <datalist id="input-markers" style="--list-length: 9;">
                      <option value="0">0.125x</option><option value="1">0.25x</option><option value="2">0.5x</option><option value="3">0.75x</option><option value="4">1x</option><option value="5">1.25x</option><option value="6">1.5x</option><option value="7">1.75x</option><option value="8">2x</option>
                  </datalist>
                  </div>
                  <div class="player-controller">
                      <button class="btn btn-dark button-play-master">Play</button>
                      <button class="btn btn-dark button-pause-master">Pause</button>
                      <button class="btn btn-dark button-scroll-master">Scroll</button>
                      <button class="btn btn-dark button-reset-master">Reset</button>
                      <button class="btn btn-dark button-save-master">Save</button>
                  </div>
              </div>
          </div>
      </div>

      <!-- controller drag begin -->
      <div class="srt-map">
          <div class="srt-map-first-layer">

              <div class="srt-time-position">
                  <div class="srt-time-position-inner">
                      <div class="srt-time-position-pointer" data-toggle="tooltip" data-placement="top" title="00:00:00"></div>
                  </div>
              </div>

              <div class="srt-timestamp">
                  <canvas class="srt-timeline-canvas"></canvas>
              </div>

              <div class="srt-edit-area">
                  <div class="srt-waveform">
                      <canvas class="srt-timeline-canvas-edit" height="64" width="100%"></canvas>
                  </div>
                  <div class="srt-map-srt-container">
                  </div>
              </div>
          </div>
      </div>
      <!-- controller drag end -->
  </div>

  <!-- Modal -->
  <div class="modal fade" id="deleteItem" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteItemLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="deleteItemLabel">Delete Text</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  Are you sure you want to delete this one?
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger delete">OK</button>
                  <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
              </div>
          </div>
      </div>
  </div>

<?php
    $lyric = $midi->getLyric();
    if(strlen(trim($lyric)) == 0)
    {
        $lyric = "{type here}";
    }
    if(stripos($lyric, "-->") === false)
    {
        $lyric = "00:00:00,000 --> 00:00:01,000\r\n".$lyric;
    }
?>
<script>
    let midi_id = '<?php echo $midi->getMidiId(); ?>';
    let path = '<?php echo $cfg->getMidiBaseUrl();?>/<?php echo $midi->getFileName(); ?>';
    let jsonData = <?php echo json_encode(array('lyric'=>$lyric)); ?>;
    let rawData = jsonData.lyric;
</script>
<script>
    let midi;
    $(document).ready(function(evt)
    {      
        midi = new MidiEditor('.editor1', rawData, path);
        midi.onDeleteData = function(index, countData) {
            if (countData > 1) {
                idToDelete = index;
                let myModal = new bootstrap.Modal(document.querySelector('#deleteItem'), {
                    keyboard: false
                });
                myModal.show();
                document.querySelector('#deleteItem .delete').addEventListener('click', function(e) {
                    midi.deleteData(idToDelete);
                    idToDelete = -1;
                    myModal.hide();
                });
            }
        };

        document.querySelector('.button-play-master').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            midi.play();
        });

        document.querySelector('.button-scroll-master').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            midi.toggleScroll();
        });

        document.querySelector('.button-pause-master').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            midi.pause(true);
        });

        document.querySelector('.button-save-master').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            saveLyric();
        });
        document.querySelector('.button-reset-master').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            resetLyric();
        });

        document.onkeydown = function(e) {
            if (e.ctrlKey && e.keyCode === 83) {
                e.preventDefault();
                e.stopPropagation();
                saveLyric();
            }
        };
    });
    function resetLyric()
    {
         $.ajax({
            type:'GET',
            url:'lib.ajax/lyric-load.php',
            data:{midi_id:midi_id},
            dataType:'json',
            success:function(data)
            {
                rawData:data.lyric;
                midi.initData(rawData, path)
            }
        });
    }
    function saveLyric()
    {
        if(midi.zoomLevelIndex < midi.zoomLevelIndexOriginal)
        {
            midi.resetZoom();
        }
        midi.updateData();
        let duration = midi.duration;
        rawData = midi.getFinalResult();
        ajax.post('lib.ajax/lyric-save.php', {
            midi_id: midi_id,
            lyric: rawData,
            duration: duration
        }, function(response, status) {
        });
    }
</script>

<?php
}
catch(Exception $e)
{
    // do nothing
}
}
else if($inputGet->equalsAction(PicoRequest::ACTION_DETAIL) && $inputGet->getMidiId() != null)
{
  $midi = new Midi(null, $database);
  try
  {
  $midi->findOneByMidiId($inputGet->getMidiId());
  ?>
  <table class="table table-responsive">
    <tbody>
      <tr>
        <td>MIDI ID</td>
        <td><?php echo $midi->getMidiId();?></td>
      </tr>
      <tr>
        <td>Title</td>
        <td><?php echo $midi->getTitle();?></td>
      </tr>
    </tbody>
  </table>
  
  <?php
  }
  catch(NoRecordFoundException $e)
  {
    ?>
    <div class="alert alert-warning"><?php echo $e->getMessage();?></div>
    <?php
  }
  catch(Exception $e)
  {
    // do nothing
  }
}
else
{
?>

<div class="filter-container">
    <form action="" method="get">
    <div class="filter-group">
        <span>Genre</span>
        <select class="form-control" name="genre_id" id="genre_id">
            <option value="">- All -</option>
            <?php echo new SelectOption(new Genre(null, $database), array('value'=>'genreId', 'label'=>'name'), $inputGet->getGenreId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Album</span>
        <select class="form-control" name="album_id" id="album_id">
            <option value="">- All -</option>
            <?php echo new SelectOption(new Album(null, $database), array('value'=>'albumId', 'label'=>'name'), $inputGet->getAlbumId(), null, new PicoSortable('sortOrder', PicoSortable::ORDER_TYPE_DESC)); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Artist Vocal</span>
        <select class="form-control" name="artist_vocal_id" id="artist_vocal_id">
            <option value="">- All -</option>
            <?php echo new SelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getArtistVocalId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Title</span>
        <input class="form-control" type="text" name="title" id="title" autocomplete="off" value="<?php echo $inputGet->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    
    <input class="btn btn-success" type="submit" value="Show">
    
    </form>
</div>

<?php
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createMidiSpecification($inputGet);

$sortable = new PicoSortable('title', PicoSortable::ORDER_TYPE_DESC);
$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);
$midi = new EntityMidi(null, $database);
$rowData = $midi->findAll($spesification, $pagable, true);

$result = $rowData->getResult();

if(!empty($result))
{
?>

<div class="pagination">

</div>

<div class="pagination">
    <div class="pagination-number">
    <?php
    foreach($rowData->getPagination() as $pg)
    {
        ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="#"><?php echo $pg['page'];?></a></span><?php
    }
    ?>
    </div>
</div>
<table class="table">
  <thead>
    <tr>
    <th scope="col" width="20"><i class="ti ti-edit"></i></th>
    <th scope="col" width="20"><i class="ti ti-trash"></i></th>
    <th scope="col" width="20"><i class="ti ti-music"></i></th>
      <th scope="col" width="20">#</th>
      <th scope="col">Title</th>
      <th scope="col">Genre</th>
      <th scope="col">Artist</th>
      <th scope="col">Album</th>
      <th scope="col">Duration</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = $pagination->getOffset();
    foreach($result as $midi)
    {
      $no++;
      $midiId = $midi->getMidiId();
      $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&midi_id=".$midiId;
      $linkMusic = basename($_SERVER['PHP_SELF'])."?action=compose&midi_id=".$midiId;
      $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&midi_id=".$midiId;
    ?>
    <tr data-id="<?php echo $midiId;?>">
        <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
        <th scope="row"><a href="<?php echo $linkEdit;?>" class="delete-data"><i class="ti ti-trash"></i></a></th>
        <th scope="row"><a href="<?php echo $linkMusic;?>" class="edit-data"><i class="ti ti-music"></i></a></th>
        <th scope="row"><?php echo $no;?></th>
        <td><a href="<?php echo $linkDetail;?>"><?php echo $midi->getTitle();?></a></td>
        <td><?php echo $midi->hasValueGenre() ? $midi->getGenre()->getName() : "";?></td>
        <td><?php echo $midi->hasValueArtistVocal() ? $midi->getArtistVocal()->getName() : "";?></td>
        <td><?php echo $midi->hasValueAlbum() ? $midi->getAlbum()->getName() : "";?></td>
        <td><?php echo $midi->getDuration();?></td>
    </tr>
    <?php
    }
    ?>
    
  </tbody>
</table>
<div class="pagination">
    <div class="pagination-number">
    <?php
    foreach($rowData->getPagination() as $pg)
    {
        ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="#"><?php echo $pg['page'];?></a></span><?php
    }
    ?>
    </div>
</div>
<?php
}
}
require_once "inc/footer.php";
?>