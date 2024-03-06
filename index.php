<?php

use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSortable;
use MagicObject\Database\PicoSpecification;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Request\PicoRequest;
use MagicObject\Util\Dms;
use MusicProductionManager\Data\Dto\SongFile;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\EntityUserActivity;
use MusicProductionManager\Utility\SongFileUtil;
use MusicProductionManager\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";


$inputGet = new InputGet();
?>
<script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="assets/libs/simplebar/dist/simplebar.js"></script>
<script src="assets/js/dashboard.js"></script>

<!--  Row 1 -->
<div class="row">
  <div class="col-lg-8 d-flex align-items-strech">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
          <div class="mb-3 mb-sm-0">
            <h5 class="card-title fw-semibold">Sales Overview</h5>
          </div>
          <div>
            <select class="form-select">
              <option value="1">March 2023</option>
              <option value="2">April 2023</option>
              <option value="3">May 2023</option>
              <option value="4">June 2023</option>
            </select>
          </div>
        </div>
        <div id="chart"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="row">
      <div class="col-lg-12">
        <!-- Yearly Breakup -->

        <?php

        $query = new PicoDatabaseQueryBuilder($database->getDatabaseType());

        ?>

        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title mb-9 fw-semibold">Yearly Breakup</h5>
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3">36,358</h4>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-arrow-up-left text-success"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                  <p class="fs-3 mb-0">last year</p>
                </div>
                <div class="d-flex align-items-center">
                  <div class="me-4">
                    <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                    <span class="fs-2">2023</span>
                  </div>
                  <div>
                    <span class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                    <span class="fs-2">2023</span>
                  </div>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-center">
                  <div id="breakup"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <!-- Monthly Earnings -->
        <div class="card">
          <div class="card-body">
            <div class="row alig n-items-start">
              <div class="col-8">
                <h5 class="card-title mb-9 fw-semibold"> Monthly Earnings </h5>
                <h4 class="fw-semibold mb-3">$6,820</h4>
                <div class="d-flex align-items-center pb-1">
                  <span class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-arrow-down-right text-danger"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                  <p class="fs-3 mb-0">last year</p>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-end">
                  <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                    <i class="ti ti-currency-dollar fs-6"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="earning"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-4 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body p-4">
        <div class="mb-4">
          <h5 class="card-title fw-semibold">Recent Activity</h5>
        </div>
        <ul class="timeline-widget mb-0 position-relative mb-n5">

          <?php


          $orderMap = array(
            'userId' => 'userId',
            'timeCreate' => 'timeCreate'
          );
          $defaultOrderBy = 'timeCreate';
          $defaultOrderType = 'desc';

          $spesification = new PicoSpecification();

          $sortable = new PicoSortable('timeCreate', 'desc');

          $pagable = new PicoPagable(new PicoPage(1, 10), $sortable);

          $userActivityEntity = new EntityUserActivity(null, $database);
          $rowData = $userActivityEntity->findAll($spesification, $pagable, $sortable, true);

          $result = $rowData->getResult();

          $currentDay = date('Y-m-d');
          foreach ($result as $uActivity) {

            if (date('Y-m-d', strtotime($uActivity->getTimeCreate())) == $currentDay) {
              $dateFormat = '\T\o\d\a\y H:i:s';
            } else {
              $dateFormat = 'M j<\s\u\p>S</\s\u\p> Y H:i:s';
            }

          ?>
            <li class="timeline-item d-flex position-relative overflow-hidden">
              <div class="timeline-time text-dark flex-shrink-0 text-end"><?php echo date($dateFormat, strtotime($uActivity->getTimeCreate())); ?></div>
              <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                <span class="timeline-badge border-2 border border-info flex-shrink-0 my-8"></span>
                <span class="timeline-badge-border d-block flex-shrink-0"></span>
              </div>
              <div class="timeline-desc fs-3 text-dark mt-n1 fw-semibold">
                <div><?php echo $uActivity->getName(); ?> </div>
                <div><?php echo $uActivity->hasValueUser() ? $uActivity->getUser()->getName() : ''; ?></div>
              </div>
            </li>
          <?php

          }
          ?>

        </ul>
      </div>
    </div>
  </div>


  <div class="col-lg-8 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-4">Recent Album</h5>
        <div class="table-responsive">

          <?php

          $orderMap = array(
            'userId' => 'userId',
            'timeCreate' => 'timeCreate'
          );
          $defaultOrderBy = 'sortOrder';
          $defaultOrderType = 'desc';

          $spesification = (new PicoSpecification())
            ->add((new PicoPredicate())->equals('active', true))
            ->add((new PicoPredicate())->equals('asDraft', false));

          $sortable = new PicoSortable('timeCreate', 'desc');

          $pagable = new PicoPagable(new PicoPage(1, 10), $sortable);

          $albumEntity = new Album(null, $database);
          $rowData = $albumEntity->findAll($spesification, $pagable, $sortable, true);

          $result = $rowData->getResult();
          if (!empty($result)) {
          ?>
            <table class="table text-nowrap mb-0 align-middle">
              <thead class="text-dark fs-4">
                <tr>
                  <th class="border-bottom-0" width="32">
                    <h6 class="fw-semibold mb-0">No</h6>
                  </th>
                  <th class="border-bottom-0">
                    <h6 class="fw-semibold mb-0">Album</h6>
                  </th>
                  <th class="border-bottom-0" width="100">
                    <h6 class="fw-semibold mb-0">Song</h6>
                  </th>
                  <th class="border-bottom-0" width="120">
                    <h6 class="fw-semibold mb-0">Duration</h6>
                  </th>
                  <th class="border-bottom-0" width="180">
                    <h6 class="fw-semibold mb-0">Release Date</h6>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 0;
                foreach ($result as $album) {
                  $no++;
                ?>

                  <tr>
                    <td class="border-bottom-0">
                      <h6 class="fw-semibold mb-0"><?php echo $no; ?></h6>
                    </td>
                    <td class="border-bottom-0">
                      <h6 class="fw-semibold mb-1"><?php echo $album->getName(); ?></h6>
                      <span class="fw-normal"><?php echo $album->getDescription(); ?></span>
                    </td>
                    <td class="border-bottom-0">
                      <h6 class="fw-normal mb-0 fs-4"><?php echo $album->getNumberOfSong(); ?></h6>
                    </td>
                    <td class="border-bottom-0">
                      <h6 class="fw-normal mb-0 fs-4"><?php echo $album->getDuration(); ?></h6>
                    </td>
                    <td class="border-bottom-0">
                      <p class="mb-0 fw-normal"><?php echo date('j F Y', strtotime($album->getReleaseDate())); ?></p>
                    </td>
                  </tr>

                <?php
                }
                ?>
              </tbody>
            </table>
          <?php
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="assets/rateyo/rateyo.css">
<script src="assets/rateyo/rateyo.js"></script>
<script>
  $(document).ready(function() {
    $('.song-rating').each(function(e) {
      let rate = parseFloat($(this).attr('data-rate'));
      $(this).rateYo({
        rating: rate,
        starWidth: "16px"
      });
    });

    $('.song-rating').rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });

  });
  
  function setRateEvent(e, data)
  {
    let songId = $(e.currentTarget).attr('data-song-id');
      $.ajax({
        type: 'POST',
        url: 'lib.ajax/song-set-rating.php',
        dataType: 'json',
        data: {
          song_id: songId,
          rating: data.rating
        },
        success: function(response) {
          updateRate(response);
        }
      });
  }
  function updateRate(response)
  {
    let selector = '.song-rating[data-song-id="'+response.song_id+'"]';
    let newRate = $('<div />');
    $(selector).replaceWith(newRate);
    newRate.addClass("song-rating");
    newRate.addClass("half-star-ratings");
    newRate.attr("data-rateyo-half-star", "true");
    newRate.attr('data-song-id', response.song_id);
    newRate.attr('data-rate', response.rating);
  
    $(selector).rateYo({
      rating: parseFloat(response.rating),
      starWidth: "16px"
    });
    $(selector).rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });
  }
</script>
<style>
  .btn-tn {
    font-size: 0.7em;
  }
</style>
<div class="row">

  <?php

  $orderMap = array(
    'title' => 'title',
    'score' => 'score',
    'albumId' => 'albumId',
    'album' => 'albumId',
    'trackNumber' => 'trackNumber',
    'genreId' => 'genreId',
    'genre' => 'genreId',
    'artistVocalId' => 'artistVocalId',
    'artistVocal' => 'artistVocalId',
    'artistComposerId' => 'artistComposerId',
    'artistComposer' => 'artistComposerId',
    'duration' => 'duration',
    'lyricComplete' => 'lyricComplete',
    'vocal' => 'vocal',
    'active' => 'active'
  );
  $defaultOrderBy = 'songId';
  $defaultOrderType = 'desc';
  $pagination = new PicoPagination($cfg->getResultPerPage());

  $spesification = SpecificationUtil::createSongSpecification($inputGet);


  $sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));

  $pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

  $songEntity = new EntitySong(null, $database);
  $rowData = $songEntity->findAll($spesification, $pagable, $sortable, true);

  $result = $rowData->getResult();

  foreach ($result as $song) {
    $songFile = new SongFile($song);
    $buttonMp3 = SongFileUtil::createDownloadButton($songFile, 'mp3', 'MP3', 'read-file.php', '_blank');
    $buttonMidi = SongFileUtil::createDownloadButton($songFile, 'midi', 'MIDI', 'read-file.php', '_blank');
    $buttonXml = SongFileUtil::createDownloadButton($songFile, 'xml', 'XML', 'read-file.php', '_blank');
    $buttonPdf = SongFileUtil::createDownloadButton($songFile, 'pdf', 'PDF', 'read-file.php', '_blank');
  ?>

    <div class="col-sm-6 col-xl-3">
      <div class="card overflow-hidden rounded-2">
        <div class="card-body pt-3 p-4">

          <div class="d-flex align-items-center justify-content-between">
            <h6 class="fw-semibold fs-4 col-5"><?php echo $song->getName(); ?></h6>
            <div class="col-7 justify-content-end text-end">
              <a href="subtitle.php?action=edit&song_id=<?php echo $song->getSongId(); ?>" class="btn btn-sm btn-tn btn-success"><span class="ti ti-edit"></span> EDIT</a>
              <a href="javascript;" onclick="uploadFile('<?php echo $song->getSongId(); ?>'); return false" class="btn btn-sm btn-tn btn-success"><span class="ti ti-upload"></span> UPLOAD</a>
              <?php echo $buttonMp3;?>
              <?php echo $buttonMidi;?>
              <?php echo $buttonXml;?>
              <?php echo $buttonPdf;?>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-4">Album</div>
            <div class="col-8"><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : ''; ?></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-4">Genre</div>
            <div class="col-8"><?php echo $song->hasValueGenre() ? $song->getGenre()->getName() : ''; ?></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-4">Composer</div>
            <div class="col-8"><?php echo $song->hasValueComposer() ? $song->getComposer()->getName() : ''; ?></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-4">Arranger</div>
            <div class="col-8"><?php echo $song->hasValueArranger() ? $song->getArranger()->getName() : ''; ?></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-4">Vocalist</div>
            <div class="col-8"><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : "";?></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-4">Track</div>
            <div class="col-8"><?php echo $song->getTrackNumber(); ?><?php echo $song->hasValueAlbum() ? "/".$song->getAlbum()->getNumberOfSong() : ''; ?></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-4">Duration</div>
            <div class="col-8"><?php echo (new Dms())->ddToDms($song->getDuration() / 3600)->printDms(true, true); ?></div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div class="col-5"><?php echo date('M j<\s\u\p>S</\s\u\p> Y H:i:s', strtotime($song->getTimeEdit())); ?></div>
            <div class="list-unstyled d-flex align-items-center mb-0 me-1">
              <a href="karaoke.php?song_id=<?php echo $song->getSongId(); ?>&action=open"><span class="ti ti-music"></span></a> &nbsp;
              <a href="karaoke.php?song_id=<?php echo $song->getSongId(); ?>&action=open"><span class="ti ti-microphone"></span></a> &nbsp;
              <a href="comment.php?song_id=<?php echo $song->getSongId(); ?>&action=edit"><span class="ti ti-message"></span></a> &nbsp;
              <div class="song-rating half-star-ratings" data-rateyo-half-star="true" data-rate="<?php echo $song->getRating(); ?>" data-song-id="<?php echo $song->getSongId(); ?>"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

  <?php
  }
  ?>


</div>


<?php
require_once "inc/footer.php";
?>