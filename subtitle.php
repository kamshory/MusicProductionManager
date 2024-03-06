<?php

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSort;
use MagicObject\Database\PicoSortable;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Response\Generated\PicoSelectOption;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Genre;
use MusicProductionManager\Data\Entity\Producer;
use MusicProductionManager\Data\Entity\Song;

use MusicProductionManager\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";
?>
<link rel="stylesheet" href="lib/lyric-editor.css">
<script src="lib/subtitle-editor.js"></script>
<script src="lib/ajax.js"></script>
<link rel="stylesheet" href="lib/icon.css">
<?php
require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new InputGet();
if($inputGet->equalsAction(ParamConstant::ACTION_EDIT) && $inputGet->getSongId() != null)
{
$songId = $inputGet->getSongId();
try
{
$song = new Song(null, $database);
$song->findOneBySongId($songId);
?>
<div class="song-tite">
    <h3 style="font-size: 18px; padding-bottom:2px;"><?php echo $song->getName();?></h3>
</div>

<?php
require_once __DIR__ . "/inc/menu-song.php";
?>

<style>
    .player-controller .button-offset{
        width: 40px;
    }
</style>
<div class="srt-editor editor1">
    <div class="row">
        <div class="col col-7">
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
                <div class="text-display-container">
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
                    <button class="btn btn-dark button-complete-master">OK</button>
                    <button class="btn btn-dark button-offset button-sub">&lsaquo;</button>
                    <button class="btn btn-dark button-offset button-add">&rsaquo;</button>
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

if($song != null)
{
    $subtitle = $song->getSubtitle();
    if(strlen(trim($subtitle)) == 0)
    {
        $subtitle = "{type here}";
    }
    if(stripos($subtitle, "-->") === false)
    {
        $subtitle = "00:00:00,000 --> 00:00:01,000\r\n".$subtitle;
    }
?>
<script>
    let song_id = '<?php echo $song->getSongId(); ?>';
    let path = '<?php echo $cfg->getSongBaseUrl();?>/<?php echo $song->getFileName(); ?>?hash=<?php echo str_replace(array(" ", "-", ":"), "", $song->getLastUploadTime());?>';
    let jsonData = <?php echo json_encode(array('subtitle'=>$subtitle)); ?>;
    let rawData = jsonData.subtitle;
</script>
<script>
    let srt;
    let increment = 10;
    $(document).ready(function(evt)
    {      
        srt = new SrtGenerator('.editor1', rawData, path);
        srt.onDeleteData = function(index, countData) {
            if (countData > 1) {
                idToDelete = index;
                let myModal = new bootstrap.Modal(document.querySelector('#deleteItem'), {
                    keyboard: false
                });
                myModal.show();
                document.querySelector('#deleteItem .delete').addEventListener('click', function(e) {
                    srt.deleteData(idToDelete);
                    idToDelete = -1;
                    myModal.hide();
                });
            }
        };

        document.querySelector('.button-play-master').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            srt.play();
        });

        document.querySelector('.button-scroll-master').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            srt.toggleScroll();
        });

        document.querySelector('.button-pause-master').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            srt.pause(true);
        });

        document.querySelector('.button-save-master').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            saveLyric();
        });
        document.querySelector('.button-complete-master').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            saveLyric(true);
        });
        document.querySelector('.button-reset-master').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            resetSubtitle();
        });

        document.querySelector('.button-sub').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            srt.moveLeft(increment);
        });

        document.querySelector('.button-add').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            srt.moveRight(increment);
        });


        document.onkeydown = function(e) {
            if (e.ctrlKey && e.keyCode === 83) {
                e.preventDefault();
                e.stopPropagation();
                saveLyric();
            }
        };
    });
    function resetSubtitle()
    {
        $.ajax({
            type:'GET',
            url:'lib.ajax/subtitle-load.php',
            data:{song_id:song_id},
            dataType:'json',
            success:function(data)
            {
                rawData:data.subtitle;
                srt.initData(rawData, path)
            }
        });
    }
    function saveLyric(complete)
    {
        if(srt.zoomLevelIndex < srt.zoomLevelIndexOriginal)
        {
            srt.resetZoom();
        }
        srt.updateData();
        let duration = srt.duration;
        rawData = srt.getFinalResult();
        ajax.post('lib.ajax/subtitle-save.php', {
            song_id: song_id,
            subtitle: rawData,
            lsubtitleComplete:complete?1:0,
            duration: duration
        }, function(response, status) {
        });
    }
</script>

<?php
}
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
            <?php echo new PicoSelectOption(new Genre(null, $database), array('value'=>'genreId', 'label'=>'name'), $inputGet->getGenreId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Album</span>
        <select class="form-control" name="album_id" id="album_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Album(null, $database), array('value'=>'albumId', 'label'=>'name'), $inputGet->getAlbumId(), null, new PicoSortable('sortOrder', PicoSortable::ORDER_TYPE_DESC)); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Producer</span>
        <select class="form-control" name="producer_id" id="producer_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Producer(null, $database), array('value'=>'producerId', 'label'=>'name'), $inputGet->getProducerId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Artist Vocal</span>
        <select class="form-control" name="artist_vocal_id" id="artist_vocal_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getArtistVocalistId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Title</span>
        <input class="form-control" type="text" name="title" id="title" autocomplete="off" value="<?php echo $inputGet->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>Subtitle</span>
        <input class="form-control" type="text" name="subtitle" id="subtitle" autocomplete="off" value="<?php echo $inputGet->getSubtitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>

    <div class="filter-group">
        <span>Vocal</span>
        <select class="form-control" name="vocal" id="vocal">
            <option value="">- All -</option>
            <option value="1"<?php echo $inputGet->createSelectedVocal("1");?>>Yes</option>
            <option value="0"<?php echo $inputGet->createSelectedVocal("0");?>>No</option>
        </select>
    </div>
    
    <div class="filter-group">
        <span>Complete</span>
        <select class="form-control" name="subtitle_complete" id="subtitle_complete">
            <option value="">- All -</option>
            <option value="1"<?php echo $inputGet->createSelectedLyricComplete("1");?>>Yes</option>
            <option value="0"<?php echo $inputGet->createSelectedLyricComplete("0");?>>No</option>
        </select>
    </div>
    
    <input class="btn btn-success" type="submit" value="Show">
    
    </form>
</div>
<?php
$orderMap = array(
    'songId'=>'songId',
    'name'=>'name',
    'title'=>'title', 
    'rating'=>'rating',
    'albumId'=>'albumId', 
    'album'=>'albumId',
    'trackNumber'=>'trackNumber', 
    'genreId'=>'genreId', 
    'genre'=>'genreId',
    'producerId'=>'producerId',
    'artistVocalId'=>'artistVocalId',
    'artistVocalist'=>'artistVocalId',
    'artistComposer'=>'artistComposer',
    'artistArranger'=>'artistArranger',
    'duration'=>'duration',
    'vocal'=>'vocal',
    'active'=>'active',
    'lsubtitleComplete'=>'lsubtitleComplete'
);
$defaultOrderBy = 'songId';
$defaultOrderType = 'desc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createSongSpecification($inputGet, array('active'=>true));
if($pagination->getOrderBy() == '')
{
    $sortable = new PicoSortable();
    $sort1 = new PicoSort('albumId', PicoSortable::ORDER_TYPE_DESC);
    $sortable->addSortable($sort1);
    $sort2 = new PicoSort('trackNumber', PicoSortable::ORDER_TYPE_ASC);
    $sortable->addSortable($sort2);
}
else
{
    $sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
}
$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$songEntity = new EntitySong(null, $database);
$rowData = $songEntity->findAll($spesification, $pagable, $sortable, true);

$result = $rowData->getResult();

?>

<script>
    $(document).ready(function(e){
        let pg = new Pagination('.pagination', '.page-selector', 'data-page-number', 'page');
        pg.init();
        $(document).on('change', '.filter-container form select', function(e2){
            $(this).closest('form').submit();
        });
    });
</script>

<?php
if(!empty($result))
{
?>
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
        <th scope="col" width="20">#</th>
        <th scope="col" class="col-sort" data-name="name">Name</th>
        <th scope="col" class="col-sort" data-name="title">Title</th>
        <th scope="col" class="col-sort" data-name="rating">Rating</th>
        <th scope="col" class="col-sort" data-name="album_id">Album</th>
        <th scope="col" class="col-sort" data-name="track_number">Track</th>
        <th scope="col" class="col-sort" data-name="genre_id">Genre</th>
        <th scope="col" class="col-sort" data-name="artist_vocalist">Vocalist</th>
        <th scope="col" class="col-sort" data-name="artist_composer">Composer</th>
        <th scope="col" class="col-sort" data-name="duration">Duration</th>
        <th scope="col" class="col-sort" data-name="vocal">Vocal</th>
        <th scope="col" class="col-sort" data-name="subtitle_complete">Complete</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = $pagination->getOffset();
        foreach($result as $song)
        {
        $no++;
        $songId = $song->getSongId();
        $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&song_id=".$songId;
        $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&song_id=".$songId;
        ?>
        <tr>
        <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
        <th class="text-right" scope="row"><?php echo $no;?></th>
        <td><a href="<?php echo $linkDetail;?>"><?php echo $song->getName();?></a></td>
        <td><a href="<?php echo $linkDetail;?>"><?php echo $song->getTitle();?></a></td>
        <td><?php echo $song->hasValueRating() ? $song->getRating() : "";?></td>
        <td><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : "";?></td>
        <td><?php echo $song->hasValueTrackNumber() ? $song->getTrackNumber() : "";?></td>
        <td><?php echo $song->hasValueGenre() ? $song->getGenre()->getName() : "";?></td>
        <td><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : "";?></td>
        <td><?php echo $song->hasValueComposer() ? $song->getComposer()->getName() : "";?></td>
        <td><?php echo $song->getDuration();?></td>
        <td class="text-data text-data-vocal"><?php echo $song->isVocal() ? 'Yes' : 'No';?></td>
        <td><?php echo $song->isLyricComplete() ? 'Yes':'No';?></td>
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