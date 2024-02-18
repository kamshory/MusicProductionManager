String.prototype.explode = function (separator, limit) {
  let array = this.split(separator);
  if (limit !== undefined && array.length > limit && limit > 1) {
    let array3 = [];
    let i;
    let array2 = this.split(separator, limit - 1);
    if (array.length > limit) {
      for (i in array) {
        if (i >= limit - 1) {
          array3.push(array[i]);
        }
      }
    }
    array2.push(array3.join(separator));
    return array2;
  }
  return array;
};

Object.size = function (obj) {
  let size = 0,
    key;
  for (key in obj) {
    if (obj.hasOwnProperty(key)) size++;
  }
  return size;
};

let lyricData = {};
let lyricTimeScale = 1;
let lyricTimeOffset = 0;
let lyricIntervalWrite = 4000;
let lyricDuration = 10000;
let lyricIntervalHiligth = 50;
let lyricLastTimeWrite = 0;
let lyricLastTimeHiligth = 0;
let tempo = 0;
let withLyric = false;
let timebase = 1;
let timeInfo = [];
let playerModal;

function updateLyricForm(value) {
  value = value.split("\n").join("\r\n");
  value = value.split("\r\r\n").join("\r\n");
  value = value.split("\r").join("\r\n");
  value = value.split("\r\n\n").join("\r\n");
  let values = value.split('\r\n').join(' ').split(' ');
  console.log(values);
  let idx = 0;
  $('.timetable tbody').find('tr').each(function (e) {
    let tr = $(this);
    let sub = values[idx] || null;
    if (sub != null && sub.length <= 10) {
      sub = sub.split('_').join(' ');
      sub = sub.split('\\').join('\r\n');
      tr.find('textarea.ta-lyric-editor').val('"' + sub + '"');
    }
    idx++;
  });
  renderLyricEditor();
}

$(document).ready(function () {
  $(document).on("change", ".table-check-controller", function (e) {
    let checked = $(this)[0].checked;
    $(".table-check-target").each(function (e2) {
      $(this)[0].checked = checked;
    });
  });
  $(document).on("change", ".filter-bar select", function (e) {
    $(this).closest("form").submit();
  });

  $(document).on("click", ".mp-play", function () {
    if ($(".planet-midi-player").attr("data-is-stoped") == "true") {
      let url = $(".planet-midi-player").attr("data-midi-url");
      console.log(url);
      MIDIjs.play(url);
      $(".planet-midi-player").attr("data-is-stoped", "false");
      $(".lyric-preview").find(".lyric-item").removeClass("hilight");
    } else if ($(".planet-midi-player").attr("data-is-playing") == "true") {
      MIDIjs.pause();
      $(".planet-midi-player").attr("data-is-playing", "false");
    } else {
      MIDIjs.resume();
      $(".planet-midi-player").attr("data-is-playing", "true");
    }
  });
  $(document).on("click", ".mp-stop", function () {
    MIDIjs.stop();
    $(".mp-progress-bar-inner").css({ width: "0%" });
    $(".mp-elapsed").text("0:0");
    $(".planet-midi-player").attr("data-is-playing", "false");
    $(".planet-midi-player").attr("data-is-stoped", "true");
  });
  $(document).on("click", ".mp-prev", function () {
    playPrev();
  });
  $(document).on("click", ".mp-next", function () {
    playNext();
  });

  MIDIjs.message_callback = function (message) {
    $(".mp-status-bar").text(message);
  };

  MIDIjs.player_callback = function (message) {
    let percent = (100 * message.time) / message.duration;
    $(".mp-progress-bar-inner").css({ width: percent + "%" });
    $(".planet-midi-player").attr(
      "data-is-playing",
      message.isPlaying ? "true" : "false"
    );
    if (message.duration > 0) {
      $(".mp-duration").text(toDMS(message.duration));
      $(".mp-elapsed").text(toDMS(message.time));
      updateLyric(message.time);
      updateIndicator(message.time);
    }
  };
  MIDIjs.on_ended = function () {
    setTimeout(function () {
      $(".planet-midi-player").attr("data-is-playing", "false");
      $(".planet-midi-player").attr("data-is-stoped", "true");
    }, 200);
  };
  MIDIjs.on_song_loaded = function (p1, p2, p3, p4, p5) {
    $(".mp-song-duration").text(toDMS(p5));
  };
  MIDIjs.visualization = function (originalBuffer) {
    visualization(originalBuffer);
  };

  $("#midi-player").on("hide.bs.modal", function () {
    let sys = $("#midi-player").attr("data-system") || "false";
    if (sys != "true") {
      MIDIjs.stop();
    }
  });


  $(document).on('click', '#save-raw', function (e) {
    let rawData = $('#rawdata').val();
    let songId = $('#song_id').val();
    $.ajax({
      url: 'midi-lyric.php?action=save-raw',
      type: 'POST',
      data: { 'raw': rawData, 'song_id': songId },
      success: function (data) {
      },
      error: function (e1, e2) {
        console.error(e1);
        console.error(e2);
      }
    });
  });
  $(document).on('click', '#update-lyric', function (e) {
    getFormData();
  });


  lyricData.lyric = getLyric(midiData);
  lyricData.time = getTempoData(midiData);
  lyricData.timebase = midiData.timebase;
  lyricData.note = getNote(midiData, lyricData.time, lyricData.timebase);

  let playerModalSelector = document.querySelector("#generate-dialog");
  playerModal = new bootstrap.Modal(playerModalSelector, {
    keyboard: false,
  });

  $(document).on('change, keyup', '.rawdata', function (e) {
    let value = $(this).val();
    updateLyricForm(value);
  });

  $(document).on("click", "#generate", function (e) {
    playerModal.show();
  });

  $(document).on("click", "#replace-lyric", function (e) { });

  $(document).on("keyup", "textarea", function (e) {
    renderLyricEditor();
  });
  $(document).on("mouseover", ".lyric-editor tbody tr", function (e2) {
    let tm = $(this).attr("data-rtime");
    $(".lyric-preview .lyric-item").removeClass("hilight-green");
    $('.lyric-preview .lyric-item[data-rtime="' + tm + '"]').addClass(
      "hilight-green"
    );
  });
  $(document).on("click", "#save", function (e1) {
    lyricData.lyric.tracks = [];
    $(".lyric-editor table tbody")
      .find("tr")
      .each(function (e2) {
        let rtime = $(this).attr("data-rtime");
        let track = $(this).attr("data-track");
        if ($(this).find("textarea").length > 0) {
          let txt = $(this).find("textarea").val().trim();
          txt = txt.substring(1, txt.length - 1);
          txt = txt.split('\\"').join('"');
          txt = txt.split("\n").join("\r\n");
          txt = txt.split("\r\r\n").join("\r\n");
          txt = txt.split("\r").join("\r\n");
          txt = txt.split("\r\n\n").join("\r\n");
          txt = txt.split('"').join('\\"');
          txt = '"' + txt + '"';
          if (typeof lyricData.lyric.tracks[track] == "undefined") {
            lyricData.lyric.tracks[track] = [];
          }
          lyricData.lyric.tracks[track].push(rtime + " Meta Lyric " + txt);
        }
      });
    let url = $(".planet-midi-player").attr("data-midi-url");
    $.ajax({
      url: "ajax-save-lyric.php",
      type: "post",
      dataType: "html",
      data: { url: url, lyric: JSON.stringify(lyricData.lyric) },
      success: function (data) {
        console.log(data);
      },
    });
  });
  generateLyric();
  renderLyricEditor();
});

function getFormData() {
  lyricData.lyric.tracks = [];
  $('.lyric-editor table tbody').find('tr').each(function (e2) {
    var rtime = $(this).attr('data-rtime');
    var track = $(this).attr('data-track');
    if ($(this).find('textarea').length > 0) {
      var txt = $(this).find('textarea').val().trim();
      txt = txt.substring(1, txt.length - 1);
      txt = txt.split('\\"').join('"');
      txt = txt.split('\n').join('\r\n');
      txt = txt.split('\r\r\n').join('\r\n');
      txt = txt.split('\r').join('\r\n');
      txt = txt.split('\r\n\n').join('\r\n');
      txt = txt.split('"').join('\\"');
      txt = '"' + txt + '"';
      if (typeof lyricData.lyric.tracks[track] == 'undefined') {
        lyricData.lyric.tracks[track] = [];
      }
      lyricData.lyric.tracks[track].push(rtime + ' Meta Lyric ' + txt);
    }
  });
  let songId = $('#song_id').val();
  var url = $('.planet-midi-player').attr('data-midi-url');
  $.ajax({
    url: 'lib.ajax/lyric-midi-update.php',
    type: 'post',
    dataType: 'html',
    data: {
      song_id: songId,
      lyric: JSON.stringify(lyricData.lyric)
    },
    success: function (data) {
      console.log(data);
    }
  });
}

function isValidEvent(x, note) {
  return note.event == "On";
  //return x == null || note.event == "On" || x.event == "Off";
}

function isValidLyricData(lyricDt, i, channel) {
  return (
    lyricDt.note.tracks[i].length > 0 &&
    typeof lyricDt.note.tracks[i][channel] != "undefined" &&
    lyricDt.note.tracks[i][channel].length > 0
  );
}

function getLastEvent(lastNote, tone) {
  return lastNote[tone] || null;
}

function generateLyricFromVocal() {
  let channel = parseInt($('[name="channel"]').val());

  let note = {};
  let tone = 0;
  let rtime = 0;
  let atime = 0;
  let symbol = "";
  let symbol2 = "";
  $(".lyric-editor tbody").empty();
  let lastRtime = 0;
  for (let i in lyricData.note.tracks) {
    if (isValidLyricData(lyricData, i, channel)) {
      let lastNote = {};
      for (let j in lyricData.note.tracks[i][channel]) {
        note = lyricData.note.tracks[i][channel][j];

        rtime = note.rtime;
        if (rtime >= lastRtime) {
          atime = note.atime;
          tone = note.note;

          symbol = getNoteFromCode(tone);
          symbol2 = '"' + symbol.split('"').join('\\"') + ' "';

          let x = getLastEvent(lastNote, tone);
          if (isValidEvent(x, note)) {
            let ta =
              '<textarea class="ta-lyric-editor">' + symbol2 + "</textarea>";
            let html =
              '<tr data-track="' +
              i +
              '" data-rtime="' +
              rtime +
              '" data-atime="' +
              atime +
              '"><td>' +
              i + '/' + channel +
              "</td><td>" +
              rtime +
              "</td><td>" +
              atime +
              "</td><td>" +
              ta +
              "</td></tr>";
            $(".lyric-editor tbody").append(html);
          }

          lastNote[tone] = note;
        }
        lastRtime = rtime;
      }
    }
  }
  renderLyricEditor();
  playerModal.hide();
}

function generateLyric() {
  let i;
  let j;
  let track;
  let line;
  let arr;
  for (i in lyricData.lyric.tracks) {
    track = lyricData.lyric.tracks[i];
    for (j in track) {
      line = track[j];
      arr = line.explode(" ", 4);
      if (arr[2] == "Lyric") {
        let atime = getTime(lyricData, parseInt(arr[0]));
        let ta = '<textarea class="ta-lyric-editor">' + arr[3] + "</textarea>";
        let html =
          '<tr data-track="' +
          i +
          '" data-rtime="' +
          arr[0] +
          '" data-atime="' +
          atime +
          '"><td>' +
          i +
          "</td><td>" +
          arr[0] +
          "</td><td>" +
          atime +
          "</td><td>" +
          ta +
          "</td></tr>";
        $(".lyric-editor tbody").append(html);
      }
    }
  }
}
function renderLyricEditor() {
  let data = [];
  $(".lyric-editor table tbody")
    .find("tr")
    .each(function (e2) {
      if ($(this).find("textarea").length > 0) {
        let span = $("<span>");
        span.addClass("lyric-item");
        span.attr("data-rtime", $(this).attr("data-rtime"));
        span.attr("data-atime", $(this).attr("data-atime"));
        let txt = $(this).find("textarea").val().trim();
        txt = txt.substring(1, txt.length - 1);
        txt = txt.split("\n").join("\r\n");
        txt = txt.split("\r\r\n").join("\r\n");
        txt = txt.split("\r").join("\r\n");
        txt = txt.split("\r\n\n").join("\r\n");
        txt = txt.split("\r\n").join("<br />");
        span.html(txt);
        data.push(span[0].outerHTML);
      }
    });
  $(".lyric-preview").html(data.join(""));
}

function visualization(originalBuffer) {
  let arrayBuff = [];
  let length = 128;
  let i;
  let j;
  let k = -1;
  let sum = 0;
  let l = -1;
  let len = Object.size(originalBuffer);
  let mul = Math.floor(len / length);
  if (mul == 0) {
    mul = 1;
  }
  for (i = 0; i < len; i++, l++) {
    sum += originalBuffer[i];
    if (l == mul - 1) {
      k++;
      l = -1;
      arrayBuff[k] = Math.floor((sum * length) / mul);
      sum = 0;
    }
  }
  let canvas = document.querySelector("#canvas");
  let WIDTH = canvas.width;
  let HEIGHT = canvas.height;
  let MID = Math.round(HEIGHT / 2);

  canvas.width = WIDTH;
  canvas.height = HEIGHT;

  let canvasCtx = canvas.getContext("2d");

  canvasCtx.lineWidth = 1;
  canvasCtx.clearRect(0, 0, canvas.width, canvas.height);
  canvasCtx.strokeStyle = "#FF0000";

  let bufferLength = arrayBuff.length;
  let sliceWidth = (WIDTH * 1.0) / bufferLength;
  let x = 0;

  canvasCtx.beginPath();
  let v, y;
  v = (length - arrayBuff[i]) / length;
  y = v * HEIGHT;
  canvasCtx.lineTo(x, y - MID);
  for (i = 0; i < bufferLength; i++) {
    v = (length - arrayBuff[i]) / length;
    y = v * HEIGHT;
    x = i * sliceWidth;

    x = Math.round(x);
    canvasCtx.lineTo(x, y - MID);
  }
  canvasCtx.stroke();
}
function toDMS(input) {
  let tm = parseInt(input);
  let sec = tm % 60;
  let min = parseInt(Math.floor((tm - sec) / 60));
  return min + ":" + sec;
}
function edit(id) {
  $.ajax({
    type: "get",
    dataType: "html",
    url: "ajax-midi.php",
    data: { action: "select", id: id },
    success: function (data) {
      $(".ajax-content-loader-edit-midi").empty().append(data);
      $("#edit").modal("show");
    },
  });
}
function updateMidi() {
  let id = $('.ajax-content-loader-edit-midi [name="id"]').val();
  let genre_id = $('.ajax-content-loader-edit-midi [name="genre_id"]').val();
  let artist_id = $('.ajax-content-loader-edit-midi [name="artist_id"]').val();
  let name = $('.ajax-content-loader-edit-midi [name="name"]').val();
  let active = $('.ajax-content-loader-edit-midi [name="active"]')[0].checked
    ? 1
    : 0;
  $.ajax({
    type: "post",
    dataType: "json",
    url: "ajax-midi.php",
    data: {
      action: "update-midi",
      id: id,
      genre_id: genre_id,
      artist_id: artist_id,
      name: name,
      active: active,
    },
    success: function (data) {
      $('tr[data-id="' + id + '"]')
        .find(".table-cell")
        .each(function (e) {
          let key = $(this).attr("data-key");
          $(this).text(data[key]);
        });
      $("#edit").modal("hide");
    },
  });
}
function dialogChangeGenre() {
  $.ajax({
    type: "get",
    dataType: "html",
    url: "ajax-midi.php",
    data: { action: "select-genre-option" },
    success: function (data) {
      $(".ajax-content-loader-change-genre").empty().append(data);
      $("#change-genre").modal("show");
    },
  });
}
function updateGenre() {
  let genre_id = $('.ajax-content-loader-change-genre [name="genre_id"]').val();

  let ids = [];
  $(".table-check-target").each(function (e) {
    if ($(this)[0].checked) {
      ids.push($(this).val());
    }
  });
  $.ajax({
    type: "post",
    url: "ajax-midi.php",
    data: { action: "update-genre", ids: ids.join(","), genre_id: genre_id },
    success: function (data) {
      window.location.reload();
    },
  });
}
function dialogChangeArtist() {
  $.ajax({
    type: "get",
    dataType: "html",
    url: "ajax-midi.php",
    data: { action: "select-artist-option" },
    success: function (data) {
      $(".ajax-content-loader-change-artist").empty().append(data);
      $("#change-artist").modal("show");
    },
  });
}
function updateArtist() {
  let artist_id = $(
    '.ajax-content-loader-change-artist [name="artist_id"]'
  ).val();

  let ids = [];
  $(".table-check-target").each(function (e) {
    if ($(this)[0].checked) {
      ids.push($(this).val());
    }
  });

  $.ajax({
    type: "post",
    url: "ajax-midi.php",
    data: { action: "update-artist", ids: ids.join(","), artist_id: artist_id },
    success: function (data) {
      window.location.reload();
    },
  });
}
function dialogTrimLeft() {
  $("#trim-left").modal("show");
}
function trimLeft() {
  let text = $('.ajax-content-loader-trim-left [name="text"]').val();

  let ids = [];
  $(".table-check-target").each(function (e) {
    if ($(this)[0].checked) {
      ids.push($(this).val());
    }
  });

  $.ajax({
    type: "post",
    url: "ajax-midi.php",
    data: { action: "trim-left", ids: ids.join(","), text: text },
    success: function (data) {
      window.location.reload();
    },
  });
}
function dialogTrimRight() {
  $("#trim-right").modal("show");
}
function trimRight() {
  let text = $('.ajax-content-loader-trim-right [name="text"]').val();

  let ids = [];
  $(".table-check-target").each(function (e) {
    if ($(this)[0].checked) {
      ids.push($(this).val());
    }
  });

  $.ajax({
    type: "post",
    url: "ajax-midi.php",
    data: { action: "trim-right", ids: ids.join(","), text: text },
    success: function (data) {
      window.location.reload();
    },
  });
}

function dialogReplaceAll() {
  $("#replace-all").modal("show");
}
function replaceAll() {
  let search_for = $(
    '.ajax-content-loader-replace-all [name="search_for"]'
  ).val();
  let replace_with = $(
    '.ajax-content-loader-replace-all [name="replace_with"]'
  ).val();
  let case_insensitive = $(
    '.ajax-content-loader-replace-all [name="case_insensitive"]'
  ).val();
  let ids = [];
  $(".table-check-target").each(function (e) {
    if ($(this)[0].checked) {
      ids.push($(this).val());
    }
  });

  $.ajax({
    type: "post",
    url: "ajax-midi.php",
    data: {
      action: "replace-all",
      ids: ids.join(","),
      search_for: search_for,
      replace_with: replace_with,
      case_insensitive: case_insensitive,
    },
    success: function (data) {
      window.location.reload();
    },
  });
}
function dialogChangeCase() {
  $("#case-option").modal("show");
}
function changeCase() {
  let case_option = $('.ajax-content-loader-case-option [name="case"]').val();
  let ids = [];
  $(".table-check-target").each(function (e) {
    if ($(this)[0].checked) {
      ids.push($(this).val());
    }
  });

  $.ajax({
    type: "post",
    url: "ajax-midi.php",
    data: {
      action: "change-case",
      ids: ids.join(","),
      case_option: case_option,
    },
    success: function (data) {
      window.location.reload();
    },
  });
}
let current_index = 0;
function playMidi(url, title, artist, genre, duration, tempo) {
  current_index = getIndex(url, song_list);
  $(".planet-midi-player").attr("data-midi-url", url);
  $("#midi-player").modal("show");
  $(".mp-song-title").text(title);
  $(".mp-song-artist").text(artist);
  $(".mp-song-genre").text(genre);
  $(".mp-song-duration").text(toDMS(duration));
  $(".mp-song-tempo").text(tempo.toFixed(0));
  MIDIjs.play(url);
  loadLyric(url);
}

function loadLyric(url) {
  lyricLastTimeWrite = 0;
  lyricLastTimeHiligth = 0;
  withLyric = false;
  $(".lyric").css("display", "none");
  $.ajax({
    url: "ajax-get-lyric.php",
    type: "get",
    dataType: "json",
    data: { url: url },
    success: function (data) {
      let tracks = getLyricTrack(data);
      if (tracks != null && tracks.length > 0) {
        lyricData = data;
        timebase = data.timebase;
        lyricTimeScale = (data.timebase * 1000) / data.tempo;
        timeInfo = data.timeinfo;
        withLyric = true;
        $(".lyric").css("display", "block");
      }
    },
  });
}
let trimTime = 8;
function updateLyric(second) {
  if ($(".lyric-preview").find(".lyric-item").length > 0) {
    $(".lyric-preview")
      .find(".lyric-item")
      .each(function (e) {
        let tm = parseInt($(this).attr("data-atime")) - trimTime;
        if (tm < second * 1000) {
          $(this).addClass("hilight");
        }
      });
  }
}
let noteMap = {
  21: "A0",
  22: "A#0",
  23: "B#0",

  24: "C1",
  25: "C#1",
  26: "D1",
  27: "D#1",
  28: "E1",
  29: "F1",
  30: "F#1",
  31: "G1",
  32: "G#1",
  33: "A1",
  34: "A#1",
  35: "B1",

  36: "C2",
  37: "C#2",
  38: "D2",
  39: "D#2",
  40: "E2",
  41: "F2",
  42: "F#2",
  43: "G2",
  44: "G#2",
  45: "A2",
  46: "A#2",
  47: "B2",

  48: "C3",
  49: "C#3",
  50: "D3",
  51: "D#3",
  52: "E3",
  53: "F3",
  54: "F#3",
  55: "G3",
  56: "G#3",
  57: "A3",
  58: "A#3",
  59: "B3",

  60: "C4",
  61: "C#4",
  62: "D4",
  63: "D#4",
  64: "E4",
  65: "F4",
  66: "F#4",
  67: "G4",
  68: "G#4",
  69: "A4",
  70: "A#4",
  71: "B4",

  72: "C5",
  73: "C#5",
  74: "D5",
  75: "D#5",
  76: "E5",
  77: "F5",
  78: "F#5",
  79: "G5",
  80: "G#5",
  81: "A5",
  82: "A#5",
  83: "B5",

  84: "C6",
  85: "C#6",
  86: "D6",
  87: "D#6",
  88: "E6",
  89: "F6",
  90: "F#6",
  91: "G6",
  92: "G#6",
  93: "A6",
  94: "A#6",
  95: "B6",

  96: "C7",
  97: "C#7",
  98: "D7",
  99: "D#7",
  100: "E7",
  101: "F7",
  102: "F#7",
  103: "G7",
  104: "G#7",
  105: "A7",
  106: "A#7",
  107: "B7",

  108: "C8",
  109: "C#8",
  110: "D8",
  111: "D#8",
  112: "E8",
  113: "F8",
  114: "F#8",
  115: "G8",
  116: "G#8",
  117: "A8",
  118: "A#8",
  119: "B8",

  120: "C9",
  121: "C#9",
  122: "D9",
  123: "D#9",
  124: "E9",
  125: "F9",
  126: "F#9",
  127: "G9",
  128: "G#9",
};

function getNoteFromCode(code) {
  return noteMap[code] || "";
}

function updateIndicator(second) {
  let milisecond = second * 1000;
  let from = milisecond - 100;
  let to = milisecond + 100;

  let note = {};
  let height = 0;
  $(".midi-display")
    .find(".midi-channel")
    .each(function (e) {
      let ch_control = $(this).find("div");
      let channel = parseInt($(this).attr("data-channel"));
      one: for (let i in lyricData.note.tracks) {
        if (typeof lyricData.note.tracks[i][channel] != "undefined") {
          ch_control.parent().removeAttr("data-note");
          two: for (
            let j = 0;
            j < lyricData.note.tracks[i][channel].length;
            j++
          ) {
            note = lyricData.note.tracks[i][channel][j];
            if (note.atime >= from && note.atime <= to) {
              if (note.event == "On") {
                height = (note.velocity * 100) / 255;
                ch_control
                  .parent()
                  .attr("data-note", getNoteFromCode(note.note));
              } else {
                height = 0;
              }
              ch_control.css({ height: height + "%" });
              break one;
            }
          }
        }
      }
    });
}
function getLyric(midi) {
  let lyric = { tracks: [] };
  let line = "";
  let arr = [];
  for (let i in midi.tracks) {
    if (midi.tracks[i].length > 0) {
      lyric.tracks[parseInt(i)] = [];
      for (let j in midi.tracks[i]) {
        line = midi.tracks[i][j];
        arr = line.split(" ");
        if (arr[2] == "Lyric") {
          lyric.tracks[i].push(line);
        }
      }
    }
  }
  return lyric;
}
function getTempoData(midi) {
  let tempo = { tracks: [] };
  let line = "";
  let arr = [];
  for (let i in midi.tracks) {
    if (midi.tracks[i].length > 0) {
      tempo.tracks[parseInt(i)] = [];
      for (let j in midi.tracks[i]) {
        line = midi.tracks[i][j];
        arr = line.split(" ");
        if (arr[1] == "Tempo") {
          tempo.tracks[i].push(line);
        }
      }
    }
  }
  return tempo;
}
function isNoteEvent(arr) {
  return arr[1] == "On" || arr[1] == "Off";
}
function getNote(midi, time_data, timebase) {
  let note = { tracks: [] };
  let line = "";
  let arr = [];
  let notation = {};
  let rtime = 0;
  let atime = 0;
  let channel = 0;
  let tone = 0;
  let velocity = 0;
  let mevent = "";
  for (let i in midi.tracks) {
    if (midi.tracks[i].length > 0) {
      note.tracks[parseInt(i)] = [];
      for (let j in midi.tracks[i]) {
        line = midi.tracks[i][j];
        arr = line.split(" ");
        if (isNoteEvent(arr)) {
          //1151 On ch=3 n=54 v=66
          mevent = arr[1];
          rtime = parseInt(arr[0]);
          atime = getTime(midi, rtime, time_data, timebase);
          channel = parseInt(arr[2].substring(3));
          tone = parseInt(arr[3].substring(2));
          velocity = parseInt(arr[4].substring(2));
          notation = {
            event: mevent,
            rtime: rtime,
            atime: atime,
            channel: channel,
            note: tone,
            velocity: velocity,
          };
          if (typeof note.tracks[i][channel] == "undefined") {
            note.tracks[i][channel] = [];
          }
          note.tracks[i][channel].push(notation);
        }
      }
    }
  }
  return note;
}
function getLyricTrack(lyric) {
  if (typeof lyric != "undefined") {
    for (let i in lyric.lyric.tracks) {
      if (lyric.lyric.tracks[i].length > 0) {
        return lyric.lyric.tracks[i];
      }
    }
  }
  return null;
}

function getTempo(tm) {
  let lastTempo = tempo;
  for (let i in timeInfo) {
    if (tm < timeInfo[i][0]) {
      break;
    }
    if (tm >= timeInfo[i][0]) {
      lastTempo = timeInfo[i][2];
    }
  }
  return lastTempo;
}
let offsetLyric = 0;

function getTime(lyric, time, time_data, timebase) {
  time_data = time_data || lyric.time;
  timebase = timebase || lyric.timebase;

  let duration = 0;
  let currentTempo = 0;
  let t = 0;

  let dt = 0;

  let f = 1 / timebase / 1000000;
  let tm = 0;
  let msg = [];
  one: for (let h in time_data.tracks) {
    let trk = time_data.tracks[h];
    let mc = trk.length;
    two: for (let i = 0; i < mc; i++) {
      msg = trk[i].split(" ");
      tm = parseInt(msg[0]);
      if (tm > time) {
        break one;
      }
      if (msg[1] == "Tempo") {
        dt = tm - t;
        duration += dt * currentTempo * f;
        t = tm;
        currentTempo = parseInt(msg[2]);
      }
    }
  }
  dt = time - t;
  duration += dt * currentTempo * f;
  return duration * 1000;
}
function renderLyricPlayer(lyric, range_start, range_stop) {
  let data = getLyricTrack(lyric);
  let lr = [];
  let time = 0;
  for (let i in data) {
    let arr = data[i].explode(" ", 4);
    time = getTime(lyric, parseInt(arr[0]));
    if (time >= range_start && time <= range_stop) {
      let txt = arr[3];
      txt = txt.substring(1, txt.length - 1);
      lr.push(
        '<span class="lyric-item" data-time="' + time + '">' + txt + "</span>"
      );
    }
    if (time > range_stop) {
      break;
    }
  }
  lr.push("&nbsp;");
  return lr.join("").split("\n").join("<br>");
}
function getIndex(url, song_list) {
  let idx = 0;
  for (let i = 0; i < song_list.length; i++) {
    if (url == song_list[i].url) {
      idx = i;
      break;
    }
  }
  return idx;
}
function playNext() {
  MIDIjs.stop();
  setTimeout(function () {
    $(".planet-midi-player").attr("data-is-playing", "false");
    $(".planet-midi-player").attr("data-is-stoped", "true");
    let sl = song_list.length;
    if (current_index >= sl - 1) {
      current_index = 0;
    } else {
      current_index++;
    }
    let song = song_list[current_index];
    playMidi(
      song.url,
      song.title,
      song.artist,
      song.genre,
      song.duration,
      song.tempo
    );
  }, 100);
}
function playPrev() {
  MIDIjs.stop();
  setTimeout(function () {
    $(".planet-midi-player").attr("data-is-playing", "false");
    $(".planet-midi-player").attr("data-is-stoped", "true");
    let sl = song_list.length;
    if (current_index < 1) {
      current_index = sl - 1;
    } else {
      current_index--;
    }
    let song = song_list[current_index];
    playMidi(
      song.url,
      song.title,
      song.artist,
      song.genre,
      song.duration,
      song.tempo
    );
  }, 100);
}
let midiUpdate = function (time) { };
let midiStop = function () {
  $("#midi-player").attr("data-system", "true");
  $("#midi-player").modal("hide");
};

function startPlaying() {
  $(".midi-player-container").midiPlayer.play(song);
}
function stopPlaying() {
  $(".midi-player-container").midiPlayer.stop();
}
function dialogDownloadMidi() {
  $("#download-midi").modal("show");
}
function downloadMidi() {
  let file_name_option = $(
    '.ajax-content-loader-file-name-option [name="file-name-option"]'
  ).val();
  let file_name_separator = $(
    '.ajax-content-loader-file-name-option [name="file-name-separator"]'
  ).val();
  let file_name_white_space = $(
    '.ajax-content-loader-file-name-option [name="file-name-white-space"]'
  ).val();
  let dos =
    $('.ajax-content-loader-file-name-option [name="dos"]').val() || "0";
  let ids = [];
  $(".table-check-target").each(function (e) {
    if ($(this)[0].checked) {
      ids.push($(this).val());
    }
  });
  window.open(
    "download.php?action=download-midi&ids=" +
    ids +
    "&file_name_option=" +
    file_name_option +
    "&file_name_separator=" +
    file_name_separator +
    "&file_name_white_space=" +
    file_name_white_space +
    "&dos=" +
    dos
  );
}
