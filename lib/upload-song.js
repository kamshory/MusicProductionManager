let selectName = "";
let allDownloaded = false;

let uploadModalSelector;
let uploadModal;

let albumModalSelector;
let albumModal;

let genreModalSelector;
let genreModal;

let artistModalSelector;
let artistModal;

function initModal() {
  uploadModalSelector = document.querySelector("#uploadFile");
  uploadModal = new bootstrap.Modal(uploadModalSelector, {
    keyboard: false,
  });
  initModal2();
}
function initModal2() {
  albumModalSelector = document.querySelector("#addAlbumGeneralDialog");
  albumModal = new bootstrap.Modal(albumModalSelector, {
    keyboard: false,
  });

  genreModalSelector = document.querySelector("#addGenreGeneralDialog");
  genreModal = new bootstrap.Modal(genreModalSelector, {
    keyboard: false,
  });

  artistModalSelector = document.querySelector("#addArtistGeneralDialog");
  artistModal = new bootstrap.Modal(artistModalSelector, {
    keyboard: false,
  });
}

function createProgressBar(container) {
  let pg = $('<div />');
  pg.addClass('progress-upload');
  pg.append('<div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>')
  $(container).append(pg);
  return pg;
}

$(document).ready(function (e) {
  $(document).on(
    "change",
    '.upload-drop-zone-add input[type="file"]',
    function (e) {
      document.querySelector('[name="title"]').value = e.target.files[0].name;
      let files = e.target.files;

      let randomSongId = $('[name="random_song_id"]').length > 0 ? $('[name="random_song_id"]').val() : '';

      for (let i = 0; i < files.length; i++) {
        let file = files[i];
        let formData = new FormData();
        formData.append("file", file);
        formData.append(
          "random_song_id",
          randomSongId
        );

        let progressBar = createProgressBar('.progress-bar-container-add');

        $.ajax({
          xhr: function () {
            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener(
              "progress",
              function (evt) {
                if (evt.lengthComputable) {
                  let val = parseInt((evt.loaded / evt.total) * 100);
                  let pb = progressBar.find('.progress-bar');
                  pb.css("width", val + "%");
                  pb.attr("aria-valuenow", val);
                  pb.html(val + "%");
                }
              },
              false
            );
            return xhr;
          },
          type: "POST",
          url: "lib.ajax/song-upload.php",
          data: formData,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
            let pb = progressBar.find('.progress-bar');
            let val = 0;
            pb.css("width", val + "%");
            pb.attr("aria-valuenow", val);
            pb.html(val + "%");
            $(".loader-icon").show();
          },
          error: function (e1, e2) {
            $(".loader-icon").html(
              '<p style="color:#EA4335;">File upload failed, please try again.</p>'
            );
          },
          success: function (response) {
            if (response) {
              $(".loader-icon").html(
                '<p style="color:#28A74B;">File has uploaded successfully!</p>'
              );
            } else if (resp == "err") {
              $(".loader-icon").html(
                '<p style="color:#EA4335;">Please select a valid file to upload.</p>'
              );
            }
          },
        });
        $(".file-uploader").attr("data-status", "2");
      }
    }
  );

  $(document).on(
    "change",
    '.upload-drop-zone-update input[type="file"]',
    function (e) {
      let files = e.target.files;

       let songId = $('#updateSongDialog [name="song_id"]').length > 0 ? $('#updateSongDialog [name="song_id"]').val() : '';

      for (let i = 0; i < files.length; i++) {
        let file = files[i];
        let formData = new FormData();
        formData.append("file", file);
        formData.append(
          "song_id",
          songId
        );

        let progressBar = createProgressBar('.progress-bar-container-update');

        $.ajax({
          xhr: function () {
            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener(
              "progress",
              function (evt) {
                if (evt.lengthComputable) {
                  let val = parseInt((evt.loaded / evt.total) * 100);
                  let pb = progressBar.find('.progress-bar');
                  pb.css("width", val + "%");
                  pb.attr("aria-valuenow", val);
                  pb.html(val + "%");
                }
              },
              false
            );
            return xhr;
          },
          type: "POST",
          url: "lib.ajax/song-upload.php",
          data: formData,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
            let pb = progressBar.find('.progress-bar');
            let val = 0;
            pb.css("width", val + "%");
            pb.attr("aria-valuenow", val);
            pb.html(val + "%");
            $(".loader-icon").show();
          },
          error: function (e1, e2) {
            $(".loader-icon").html(
              '<p style="color:#EA4335;">File upload failed, please try again.</p>'
            );
          },
          success: function (response) {
            if (response) {
              $(".loader-icon").html(
                '<p style="color:#28A74B;">File has uploaded successfully!</p>'
              );
            } else if (resp == "err") {
              $(".loader-icon").html(
                '<p style="color:#EA4335;">Please select a valid file to upload.</p>'
              );
            }
          },
        });
      }
    }
  );

  // Show upload file modal
  $(document).on("click", ".button-upload-file", function (e1) {
    e1.preventDefault();
    e1.stopPropagation();

    downloadForm(".lazy-dom-container", function () {
      if (!allDownloaded) {
        initModal();
        allDownloaded = true;
      }
      loadForm();
      document.querySelector('[name="random_song_id"]').value =
        generateRandom(20);
      document.querySelector(".file-uploader").setAttribute("data-status", "1");
      uploadModal.show();
    });
  });

  $(document).on("click", ".save-add-general-song", function (e1) {
    e1.preventDefault();
    e1.stopPropagation();
    let dataRequest = $(uploadModalSelector).find("form").serializeArray();
    $.ajax({
      url: "lib.ajax/song-add.php",
      type: "POST",
      dataType: "json",
      data: dataRequest,
      success: function (data) {
        uploadModal.hide();
      },
    });
  });

  // Show adbum modal
  $(document).on("click", ".button-add-general-album", function (e1) {
    e1.preventDefault();
    e1.stopPropagation();
    selectName = e1.target.parentNode
      .querySelector("select")
      .getAttribute("name");
    albumModal.show();
    setTimeout(function () {
      albumModalSelector.querySelector('[name="name"]').select();
    }, 1000);
  });

  // Save album
  $(document).on("click", ".save-add-general-album", function (e1) {
    e1.preventDefault();
    e1.stopPropagation();
    let dataRequest = $("#addAlbumGeneralDialog")
      .closest("form")
      .serializeArray();
    $.ajax({
      url: "lib.ajax/album-add.php",
      type: "POST",
      dataType: "json",
      data: dataRequest,
      success: function (data) {
        if (data?.album_id && data.name) {
          appendOption("album_id", data.album_id, data.name);
        }
      },
    });
    albumModal.hide();
  });

  $(document).on("click", ".button-add-general-genre", function (e1) {
    e1.preventDefault();
    e1.stopPropagation();
    selectName = e1.target.parentNode
      .querySelector("select")
      .getAttribute("name");
    genreModalSelector.querySelector('[name="name"]').value = "";
    genreModal.show();
    setTimeout(function () {
      genreModalSelector.querySelector('[name="name"]').select();
    }, 1000);
  });

  $(document).on("click", ".save-add-general-genre", function (e1) {
    let textElement =
      e2.target.parentNode.parentNode.parentNode.querySelector(
        'input[type="text"]'
      );
    e1.preventDefault();
    e1.stopPropagation();
    let dataRequest = $("#addGenreGeneralDialog")
      .closest("form")
      .serializeArray();
    $.ajax({
      url: "lib.ajax/genre-add.php",
      type: "POST",
      dataType: "json",
      data: dataRequest,
      success: function (data) {
        if (data?.genre_id && data.name) {
          appendOption("genre_id", data.genre_id, data.name);
        }
      },
    });
    genreModal.hide();
  });

  $(document).on("click", ".button-add-general-artist", function (e1) {
    e1.preventDefault();
    e1.stopPropagation();
    selectName = e1.target.parentNode
      .querySelector("select")
      .getAttribute("name");
    artistModalSelector.querySelector('[name="name"]').value = "";
    artistModal.show();
    setTimeout(function () {
      artistModalSelector.querySelector('[name="name"]').select();
    }, 1000);
  });

  $(document).on("click", ".save-add-general-artist", function (e1) {
    let textElement =
      e2.target.parentNode.parentNode.parentNode.querySelector(
        'input[type="text"]'
      );
    e1.preventDefault();
    e1.stopPropagation();
    let dataRequest = $("#addArtistGeneralDialog")
      .closest("form")
      .serializeArray();
    $.ajax({
      url: "lib.ajax/artist-add.php",
      type: "POST",
      dataType: "json",
      data: dataRequest,
      success: function (data) {
        if (data?.artist_id && data.name) {
          appendOption("artist_id", data.artist_id, data.name);
        }
      },
    });
    artistModal.hide();
  });

  $(document).on(
    "change",
    'input#song_file_uploader[type="file"]',
    function(e) {
      let files = e.target.files;
      let songId = e.target.getAttribute('data-song-id');

      for (let i = 0; i < files.length; i++) {
        let file = files[i];
        let formData = new FormData();
        formData.append("file", file);
        formData.append(
          "song_id",
          songId
        );
        let toastIndex = getToastIndex();
        let toastBody = $('<div />');
        toastBody.addClass('upload-area');
        let fileName = $('<div />');
        fileName.addClass('file-name');
        fileName.text(file.name);
        toastBody.append(fileName);
        let toast = createToast(toastIndex, '', 'upload', 'Upload File', toastBody);
        showToast(toast);
        setToastIndex(toastIndex + 1);
        let progressBar = createProgressBar(toast.find('.upload-area'));

        $.ajax({
          xhr: function() {
            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener(
              "progress",
              function(evt) {
                if (evt.lengthComputable) {
                  let val = parseInt((evt.loaded / evt.total) * 100);
                  let pb = progressBar.find('.progress-bar');
                  pb.css("width", val + "%");
                  pb.attr("aria-valuenow", val);
                  pb.html(val + "%");
                  if (val == 100) {
                    setTimeout(function() {
                      pb.closest('.toast').fadeOut('fast', function() {
                        $(this).remove();
                      });
                    }, 2000);
                  }
                }
              },
              false
            );
            return xhr;
          },
          type: "POST",
          url: "lib.ajax/song-upload.php",
          data: formData,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
            let pb = progressBar.find('.progress-bar');
            let val = 0;
            pb.css("width", val + "%");
            pb.attr("aria-valuenow", val);
            pb.html(val + "%");
            $(".loader-icon").show();
          },
          error: function(e1, e2) {
            $(".loader-icon").html(
              '<p style="color:#EA4335;">File upload failed, please try again.</p>'
            );
          },
          success: function(response) {
            if (response) {
              $(".loader-icon").html(
                '<p style="color:#28A74B;">File has uploaded successfully!</p>'
              );
            } else if (resp == "err") {
              $(".loader-icon").html(
                '<p style="color:#EA4335;">Please select a valid file to upload.</p>'
              );
            }
          },
        });
        $(".file-uploader").attr("data-status", "2");
      }
    }
  );
});

function createToast(id, time, icon, title, body) {
  let toast = $('<div />');
  toast.addClass('toast');
  toast.addClass('hide');
  toast.attr({
    id: id,
    role: 'alert',
    'aria-live': 'assertive',
    'aria-atomic': 'true'
  });
  toast.append(`<div class="toast-header">
    <span class="ti"></span>
    <strong class="me-auto title-text"></strong>
    <small>on progress</small>
    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  <div class="toast-body">
    
  </div>`);
  toast.find('.toast-header .ti').addClass('ti-' + icon);
  toast.find('.toast-header .title-text').text(title);
  if (typeof body != 'undefined') {
    toast.find('.toast-body').append(body);
  }
  return toast;
}

function showToast(toast) {
  let container;
  if ($('.toast-container').length > 0) {
    container = $('.toast-container');
  } else {
    container = $('<div />');
    container.addClass('toast-container');
    container.addClass('position-fixed');
    container.addClass('bottom-0');
    container.addClass('end-0');
    container.addClass('p-3');
    container.css('z-index', 11);
    container.appendTo($('body'));
  }
  container.append(toast);
  toast.css('display', 'block');
}

function getToastIndex() {
  let container;
  if ($('.toast-container').length > 0) {
    container = $('.toast-container');
    let lastId = parseInt(container.attr('data-next-index'));
    return lastId;
  }
  return 0;
}

function setToastIndex(id) {
  let container;
  if ($('.toast-container').length > 0) {
    container = $('.toast-container');
    container.attr('data-next-index', id);
  }
}

function uploadFile(songId) {
  let uploader = $('input#song_file_uploader[type="file"]');
  uploader.attr('data-song-id', songId);
  uploader.click();
}


function appendOption(selectName, value, label) {
  let opt = $("<option>");
  opt.attr("value", value);
  opt.text(label);
  $('select[name="' + selectName + '"]').each(function (e) {
    $(this)[0].add(new Option(label, value));
    $(this).val(value);
  });
}

function loadForm() {
  $('select[data-ajax="true"]').each(function (index) {
    let element = $(this);
    let current_value = $(this).attr("data-value") || "";
    if (current_value == "") {
      current_value = $(this).find("option:selected").val() || "";
    }
    let path = $(this).attr("data-source");
    $.ajax({
      type: "GET",
      dataType: "json",
      url: path,
      data: { current_value: current_value },
      success: function (data) {
        if (data?.length) {
          element.empty();
          let opt = $("<option>- select -</option>");
          opt.attr("value", "");
          element.append(opt);
          for (
            let i in data
          ) {
            let opt = $("<option />");
            opt.text(data[i].value);
            opt.attr("value", data[i].id);
            if (typeof data[i].selected != "undefined" && data[i].selected) {
              opt.attr("selected", "selected");
            }
            element.append(opt);
          }
        }
      },
      error: function (err) { },
    });
  });
}

function generateRandom(length) {
  let result = "";
  const characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  const charactersLength = characters.length;
  let counter = 0;
  while (counter < length) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
    counter += 1;
  }
  return result;
}

let len = 0;
let count = 0;
function downloadForm(selector, callback) {
  count = 0;
  len = $(selector).find(".lazy-dom").length;

  $(selector)
    .find(".lazy-dom")
    .each(function (index) {
      let _this = $(this);
      let _url = $(this).attr("data-url");
      let _dialogOpen = false;
      if ($(this).attr("data-loaded") == "true") {
        count++;
      } else {
        $(_this).load(_url, function (responseTxt, statusTxt, xhr) {
          _this.attr("data-loaded", "true");
          count++;
          if (!_dialogOpen && count >= len) {
            _dialogOpen = true;
            callback();
          }
        });
      }
      if (!_dialogOpen && count >= len) {
        _dialogOpen = true;
        callback();
      }
    });
}
