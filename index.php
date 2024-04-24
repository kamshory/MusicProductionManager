<?php

use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSortable;
use MagicObject\Database\PicoSpecification;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\InputGet;
use MagicObject\Util\Dms;
use MusicProductionManager\Data\Dto\SongFile;
use MusicProductionManager\Data\Entity\EntityAlbum;
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

<?php
$query = new PicoDatabaseQueryBuilder($database->getDatabaseType());
?>
<!--  Row 1 -->
<div class="row">
  <div class="col-lg-8 d-flex align-items-strech">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
          <div class="mb-3 mb-sm-0">
            <h5 class="card-title fw-semibold">Music Production</h5>
          </div>
          <div>
            <select class="form-select">
              <option value="1">2024</option>
              <option value="2">2022</option>
              <option value="3">2022</option>
            </select>
          </div>
        </div>
        <div id="production-chart"></div>
      </div>
    </div>
  </div>

  <?php
  // current year
  $data1 = array();
  $data2 = array();
  
  $sql = $query->newQuery()
    ->select("song.song_id, song.time_create, song.time_edit")
    ->from("song")
    ->where(
      "song.time_create like ? or song.time_create like ? ",
      (date("Y") - 1) . "%",
      date("Y") . "%"
    )
    ->orderBy("song.time_create asc");
  try {
    $rows = $database->fetchAll($sql, PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
      $month1 = date('y/m', strtotime($row['time_create']));
      if (!isset($data1[$month1])) {
        $data1[$month1] = 0;
      }
      if (!isset($data2[$month1])) {
        $data2[$month1] = 0;
      }
      $data1[$month1]++;
    }



  } catch (Exception $e) {
    // do nothing
  }

  $sql = $query->newQuery()
    ->select("song_draft.song_draft_id, song_draft.time_create, song_draft.time_edit")
    ->from("song_draft")
    ->where(
      "song_draft.time_create like ? or song_draft.time_create like ? ",
      (date("Y") - 1) . "%",
      date("Y") . "%"
    )
    ->orderBy("song_draft.time_create asc");
  try {
    $rows = $database->fetchAll($sql, PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
      $month2 = date('y/m', strtotime($row['time_create']));
      if (!isset($data2[$month2])) {
        $data2[$month2] = 0;
      }
      if (!isset($data1[$month2])) {
        $data1[$month2] = 0;
      }

      $data2[$month2]++;
    }

  } catch (Exception $e) {
    // do nothing
  }

  
  $keysMerge = array_merge(array_keys($data1), array_keys($data2));
  sort($keysMerge);
  foreach($keysMerge as $key)
  {
    if(!isset($data1[$key]))
    {
      $data1[$key] = 0;
    }
    if(!isset($data2[$key]))
    {
      $data2[$key] = 0;
    }
  }
  
  ksort($data1);
  ksort($data2);
    
  ?>

  <script>
    let colors = ["#17B890", "#5E807F", "#082D0F", "#BBBE64", "#EAF0CE", "#C0C5C1", "#7D8491", "#443850", "#EE6055", "#60D394", "#AAF683", "#FFD97D", "#FF9B85", "#764248", "#DDA3B2", "#FFADC6", "#E3C5BB", "#DFE2CF", "#DEE5E5", "#9DC5BB"];
    $(document).ready(function() {
      var chart = {

        chart: {
          type: "bar",
          height: 345,
          offsetX: -15,
          toolbar: {
            show: true
          },
          foreColor: "#adb0bb",
          fontFamily: 'inherit',
          sparkline: {
            enabled: false
          },
        },

        colors: colors,

        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "35%",
            borderRadius: [6],
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'all'
          },
        },
        markers: {
          size: 0
        },

        dataLabels: {
          enabled: false,
        },

        legend: {
          show: false,
        },

        grid: {
          borderColor: "rgba(0,0,0,0.1)",
          strokeDashArray: 3,
          xaxis: {
            lines: {
              show: false,
            },
          },
        },

        series: [{
          name: "Draft:",
          data: <?php echo json_encode(array_values($data2)); ?>
        },
        {
          name: "Complete:",
          data: <?php echo json_encode(array_values($data1)); ?>
        }],

        xaxis: {
          type: "category",
          categories: <?php echo json_encode(array_keys($data1)); ?>,
          labels: {
            style: {
              cssClass: "grey--text lighten-2--text fill-color"
            },
          },
        },

        yaxis: {
          show: true,
          min: 0,
          tickAmount: 4,
          labels: {
            style: {
              cssClass: "grey--text lighten-2--text fill-color",
            },
          },
        },
        stroke: {
          show: true,
          width: 3,
          lineCap: "butt",
          colors: ["transparent"],
        },

        tooltip: {
          theme: "light"
        },

        responsive: [{
          breakpoint: 600,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 3,
              }
            },
          }
        }]
      };

      var chart = new ApexCharts(document.querySelector("#production-chart"), chart);
      chart.render();
    });
  </script>

  <?php
  $data4 = array();
  $totalSong = 0;
  $sql = $query->newQuery()
    ->select("song.song_id, song.time_create, song.time_edit")
    ->from("song")
    ->where("song.active = true ")
    ->orderBy("time_create asc");
  try {
    $rows = $database->fetchAll($sql, PDO::FETCH_ASSOC);
    $totalSong = count($rows);
    foreach ($rows as $row) {
      $year = date('Y', strtotime($row['time_create']));
      if (!isset($data4[$year])) {
        $data4[$year] = 0;
      }

      $data4[$year]++;
    }
  } catch (Exception $e) {
    // do nothing
  }
  $y1 = date('Y') - 1;
  $y2 = date('Y') - 2;

  if (isset($data4[$y2]) && isset($data4[$y1])) {
    $incr = ($data4[$y1] - $data4[$y2]) / $data4[$y2];
  } else if (isset($data4[$y1])) {
    $incr = 100;
  } else {
    $incr = 0;
  }

  if ($incr > 0) {
    $increment = "+" . sprintf("%.2f", $incr) . "%";
  } else {
    $increment = sprintf("%.2f", $incr) . "%";
  }
  ?>
  <div class="col-lg-4">
    <div class="row">
      <div class="col-lg-12">
        <!-- Yearly Breakup -->

        <div class="card overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title mb-9 fw-semibold">Yearly Breakup</h5>
            <div class="row align-items-center">
              <div class="col-8">
                <h4 class="fw-semibold mb-3"><?php $totalSong; ?></h4>
                <div class="d-flex align-items-center mb-3">
                  <span class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                    <i class="ti ti-arrow-up-left text-success"></i>
                  </span>
                  <p class="text-dark me-1 fs-3 mb-0"><?php echo $increment; ?></p>
                  <p class="fs-3 mb-0">last year</p>
                </div>
                <div class="d-flex align-items-center">
                  <div class="me-4">
                    <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                    <span class="fs-2"><?php echo $y2; ?></span>
                  </div>
                  <div>
                    <span class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                    <span class="fs-2"><?php echo $y1; ?></span>
                  </div>
                </div>
              </div>
              <div class="col-4">
                <div class="d-flex justify-content-center">
                  <div id="yearly-breakup"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php
      $monthly_activity = array();
      $sql = $query->newQuery()
      ->select("song_update_history.song_update_history_id, song_update_history.time_update")
      ->from("song_update_history")
      ->where(
        "song_update_history.time_update like ? or song_update_history.time_update like ? ",
        (date("Y") - 1) . "%",
        date("Y") . "%"
      )
      ->orderBy("song_update_history.time_update asc");
      try {
      $rows = $database->fetchAll($sql, PDO::FETCH_ASSOC);

      foreach ($rows as $row) {
        $month2 = date('y/m', strtotime($row['time_update']));
        if (!isset($monthly_activity[$month2])) {
          $monthly_activity[$month2] = 0;
        }
        if (!isset($data1[$month2])) {
          $data1[$month2] = 0;
        }

        $monthly_activity[$month2]++;
      }

      } catch (Exception $e) {
      // do nothing
      }
      ?>

      <script>
        $(document).ready(function() {
          // =====================================
          // Breakup
          // =====================================
          var breakup = {
            color: "#adb5bd",
            series: <?php echo json_encode(array_values($data4));?>,
            labels: <?php echo json_encode(array_keys($data4));?>,
            chart: {
              width: 180,
              type: "donut",
              fontFamily: "Plus Jakarta Sans', sans-serif",
              foreColor: "#adb0bb",
            },
            plotOptions: {
              pie: {
                startAngle: 0,
                endAngle: 360,
                donut: {
                  size: '75%',
                },
              },
            },
            stroke: {
              show: false,
            },

            dataLabels: {
              enabled: false,
            },

            legend: {
              show: false,
            },
            colors: colors,

            responsive: [{
              breakpoint: 991,
              options: {
                chart: {
                  width: 150,
                },
              },
            }, ],
            tooltip: {
              theme: "dark",
              fillSeriesColor: false,
            },
          };

          var chart = new ApexCharts(document.querySelector("#yearly-breakup"), breakup);
          chart.render();

        });
      </script>

      <div class="col-lg-12">
        <!-- Monthly Activity -->
        <div class="card">
          <div class="card-body">
            <div class="row alig n-items-start">
              <div class="col-12">
                <h5 class="card-title mb-9 fw-semibold"> Monthly Activity </h5>
              </div>
            </div>
          </div>
          <div id="mothly-activity"></div>
        </div>
      </div>



      <script>
        function TimeOpt() {
          this.ts = new Date();
          this.dateFormatter = function(ts) {
            this.ts = ts;
            return this;
          }
          this.format = function(fmt) {
            let yy = this.ts.getYear();
            let mm = this.ts.getMonth() + 1;
            if (mm < 10) {
              mm = '0' + mm;
            }
            return yy + '/' + mm
          }
        }
        let opts = new TimeOpt();

        $(document).ready(function() {
          var monthlyActivity = {
            chart: {
              id: "sparkline3",
              type: "area",
              height: 60,
              sparkline: {
                enabled: true,
              },
              group: "sparklines",
              fontFamily: "Plus Jakarta Sans', sans-serif",
              foreColor: "#adb0bb",
            },
            series: [{
              name: "Updated Song",
              color: "#49BEFF",
              data: <?php echo json_encode(array_values($monthly_activity)); ?>,
            }, ],
            stroke: {
              curve: "smooth",
              width: 2,
            },
            fill: {
              colors: ["#f3feff"],
              type: "solid",
              opacity: 0.05,
            },

            markers: {
              size: 0,
            },
            tooltip: {
              theme: "dark",
              fixed: {
                enabled: true,
                position: "right",
              },
              x: {
                show: false,
              },
            },
          };
          new ApexCharts(document.querySelector("#mothly-activity"), monthlyActivity).render();
        });
      </script>


    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12 col-lg-5 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body p-4">
        <div class="mb-4">
          <h5 class="card-title fw-semibold">Recent Activity</h5>
        </div>
        <ul class="timeline-widget mb-0 pb-5 position-relative mb-n5">

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
              $dateFormat = 'M j<\s\u\p>S</\s\u\p> H:i:s';
            }

          ?>
            <li class="timeline-item d-flex position-relative overflow-hidden">
              <div class="timeline-time text-dark flex-shrink-0 text-end" title="<?php echo $uActivity->getTimeCreate();?>"><?php echo date($dateFormat, strtotime($uActivity->getTimeCreate())); ?></div>
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


  <div class="col-md-12 col-lg-7 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-4">Recent Album</h5>
        <div class="">

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

          $albumEntity = new EntityAlbum(null, $database);
          $rowData = $albumEntity->findAll($spesification, $pagable, $sortable, true);

          $result = $rowData->getResult();
          if (!empty($result)) {
          ?>
            <div class="table-contai" style="overflow-x:auto">
            <table class="table text-nowrap mb-0 align-middle">
              <thead class="text-dark fs-4">
                <tr>
                  <th class="border-bottom-0" width="32">
                    <h6 class="fw-semibold mb-0">No</h6>
                  </th>
                  <th class="border-bottom-0">
                    <h6 class="fw-semibold mb-0">Album</h6>
                  </th>
                  <th class="border-bottom-0">
                    <h6 class="fw-semibold mb-0">Producer</h6>
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
                      <h6 class="fw-normal mb-0 fs-4"><?php echo $album->hasValueProducer() ? $album->getProducer()->getName() : ""; ?></h6>
                    </td>
                    <td class="border-bottom-0">
                      <h6 class="fw-normal mb-0 fs-4"><?php echo $album->getNumberOfSong(); ?></h6>
                    </td>
                    <td class="border-bottom-0">
                      <h6 class="fw-normal mb-0 fs-4"><?php echo (new Dms())->ddToDms($album->getDuration() / 3600)->printDms(true, true); ?></h6>
                    </td>
                    <td class="border-bottom-0">
                      <p class="mb-0 fw-normal"><?php echo !$album->hasValueReleaseDate() || $album->emptyReleaseDate() || $album->equalsReleaseDate('0000-00-00') ? "-" : date('j M Y', strtotime($album->getReleaseDate())); ?></p>
                    </td>
                  </tr>

                <?php
                }
                ?>
              </tbody>
            </table>
            </div>
          <?php
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="assets/rateyo/rateyo.min.css">
<script src="assets/rateyo/rateyo.min.js"></script>
<script>
  $(document).ready(function() {
    $('.song-rating').each(function(e) {
      let rate = parseFloat($(this).attr('data-rate'));
      $(this).rateYo({
        rating: rate,
        starWidth: "14px"
      });
    });

    $('.song-rating').rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });

  });

  function setRateEvent(e, data) {
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

  function updateRate(response) {
    let selector = '.song-rating[data-song-id="' + response.song_id + '"]';
    let newRate = $('<div />');
    $(selector).replaceWith(newRate);
    newRate.addClass("song-rating");
    newRate.addClass("half-star-ratings");
    newRate.attr("data-rateyo-half-star", "true");
    newRate.attr('data-song-id', response.song_id);
    newRate.attr('data-rate', response.rating);

    $(selector).rateYo({
      rating: parseFloat(response.rating),
      starWidth: "14px"
    });
    $(selector).rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });
  }
</script>

<div class="row">

  <?php

$orderMap = array(
  'name'=>'name', 
  'title'=>'title', 
  'rating'=>'rating',
  'albumId'=>'albumId', 
  'album'=>'album.sortOrder', 
  'trackNumber'=>'trackNumber',
  'genreId'=>'genreId', 
  'genre'=>'album.sortOrder',
  'producer'=>'producer.name',
  'artistVocalId'=>'artistVocalId',
  'artistVocalist'=>'vocalist.name',
  'artistComposer'=>'composer.name',
  'artistArranger'=>'arranger.name',
  'duration'=>'duration',
  'subtitleComplete'=>'subtitleComplete',
  'vocal'=>'vocal',
  'active'=>'active'
);
  $defaultOrderBy = 'songId';
  $defaultOrderType = PicoSortable::ORDER_TYPE_DESC;
  $pagination = new PicoPagination($cfg->getResultPerPage());

  $spesification = SpecificationUtil::createSongSpecification($inputGet);


  $sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));

  $pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), 10), $sortable);

  $songEntity = new EntitySong(null, $database);
  $rowData = $songEntity->findAll($spesification, $pagable, $sortable, true);

  $result = $rowData->getResult();

  foreach ($result as $song) {
    $songFile = new SongFile($song);
    $buttonMp3 = SongFileUtil::createDownloadButton($songFile, 'mp3', 'MP3', 'read-file.php', '_blank');
    $buttonMidi = SongFileUtil::createDownloadButton($songFile, 'midi', 'MID', 'read-file.php', '_blank');
    $buttonXml = SongFileUtil::createDownloadButton($songFile, 'xml', 'XML', 'read-file.php', '_blank');
    $buttonPdf = SongFileUtil::createDownloadButton($songFile, 'pdf', 'PDF', 'read-file.php', '_blank');
  ?>

<div class="custom-card-container col-md-12 col-lg-6">
    <div class="card overflow-hidden rounded-2">
      <div class="card-body pt-3 p-4">

        <div class="d-flex align-items-center justify-content-between">
          <h6 class="fw-semibold fs-4 col-4"><?php echo $song->getName(); ?></h6>
          <div class="song-tools list-unstyled d-flex align-items-center col-8 mb-0 me-1 justify-content-end text-end">
            <div class="d-inline-block">
              <a href="karaoke.php?song_id=<?php echo $song->getSongId(); ?>&action=open"><span class="ti ti-music"></span></a> &nbsp;
              <a href="karaoke.php?song_id=<?php echo $song->getSongId(); ?>&action=open"><span class="ti ti-microphone"></span></a> &nbsp;
              <a href="comment.php?song_id=<?php echo $song->getSongId(); ?>&action=edit"><span class="ti ti-message"></span></a> &nbsp;
              <div class="d-inline-block">
                <div class="song-rating half-star-ratings" data-rateyo-half-star="true" data-rate="<?php echo $song->getRating() * 1; ?>" data-song-id="<?php echo $song->getSongId(); ?>"></div>
              </div>
          </div>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Title</div>
          <div class="col-8"><?php echo $song->getTitle(); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Album</div>
          <div class="col-8"><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Producer</div>
          <div class="col-8"><?php echo $song->hasValueProducer() ? $song->getProducer()->getName() : ''; ?></div>
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
          <div class="col-8"><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Track</div>
          <div class="col-8"><?php echo $song->getTrackNumber(); ?><?php echo $song->hasValueAlbum() ? "/" . $song->getAlbum()->getNumberOfSong() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Duration</div>
          <div class="col-8"><?php echo (new Dms())->ddToDms($song->getDuration() / 3600)->printDms(true, true); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">BPM</div>
          <div class="col-8"><?php echo $song->getBpm(); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Time Signature</div>
          <div class="col-8"><?php echo $song->getTimeSignature(); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Last Update</div>
          <div class="col-8"><?php echo date('M j<\s\u\p>S</\s\u\p> Y H:i:s', strtotime($song->getTimeEdit())); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-end text-end pt-4">
          <div class="list-unstyled align-items-center mb-0 me-1 d-inline">
            <a href="subtitle.php?action=edit&song_id=<?php echo $song->getSongId(); ?>" class="btn btn-sm btn-tn btn-success"><span class="ti ti-edit"></span> EDIT</a>
            <a href="javascript;" onclick="uploadFile('<?php echo $song->getSongId(); ?>'); return false" class="btn btn-sm btn-tn btn-success"><span class="ti ti-upload"></span> UPLOAD</a>
            <?php echo $buttonMp3; ?>
            <?php echo $buttonMidi; ?>
            <?php echo $buttonXml; ?>
            <?php echo $buttonPdf; ?>

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