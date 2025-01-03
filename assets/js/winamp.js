

class Winamp {
  constructor(list) {
    let _this = this;
    this.trackname = [];
    this.qsall = null;
    this.qs = null;
    this.root = null;
    this.timeDisplayer = null;
    this.trackInfoDisplayer = null;
    this.volumeController = null;
    this.progressBar = null;
    //this.visualisation = null;
    this.resizable = null;
    this.navBtn = null;
    this.prevBtn = null;
    this.playBtn = null;
    this.pauseBtn = null;
    this.stopBtn = null;
    this.nextBtn = null;
    this.shuffleBtn = null;
    this.repeatBtn = null;
    this.playlist = null;
    this.audio = null;
    this.tracks = [];
    this.tracksNb = 2;
    this.albumEntry = {};
    this.audioContext = null;
    this.source = null;
    this.analyserLeft = null;
    this.analyserRight = null;
    this.splitter = null;
    this.drawInterval = setInterval(function () {}, 10000000);

    this.isPlaying = false;

    this.onPlay = function () {};

    this.onPaused = function () {};

    this.onEnded = function () {};

    

    this.loadSong = function(index)
    {
      this.audio.src = this.tracks[index].url;
      this.onLoadSong(this.songList.album, this.songList.songList[index]);
    }
    this.getAudioCurrentTime = function()
    {
      if(typeof this.audio.src != 'undefined')
      {
        return this.audio.currentTime;
      }
      else
      {
        return 0;
      }
    }
    this.onLoadSong = function(album, song)
    {
      
    }

    this.createPlaylistItem = function (index) {
      let tNumber = this.tracks[index].trackNumber;
      if (tNumber < 10) {
        tNumber = "0" + tNumber;
      }
      return (
        '<div class = "prevent-select track-info' +
        (index === 0 ? " highlighted-track" : "") +
        '" data-id = "' +
        index +
        '"><div class = "track-id">' +
        tNumber +
        ". " +
        this.tracks[index].name +
        " - " +
        this.tracks[index].title +
        '</div><div class = "track-duration">' +
        this.trackDuration(index) +
        "</div></div>"
      );
      
    };

    this.createPlaylist = function () {
      //first I fill the playlist with the tracks infos
      for (let i = 0; i < this.tracks.length; i++) {
        this.playlist.innerHTML += this.createPlaylistItem(i);
      }

      //then I create the informations interactions (on double click / on touch)
      this.trackInfo = this.qsall(".track-info");

      this.trackInfo.forEach((track) => {
        track.addEventListener("dblclick", (e) => {
          _this.trackInteraction(e);
        });
        track.addEventListener("click", (e) => {
          _this.trackInfo[_this.trackLoaded].classList.toggle(
            "highlighted-track"
          );
          _this.trackLoaded = e.target.dataset.id;
          _this.trackInfo[_this.trackLoaded].classList.toggle(
            "highlighted-track"
          );
          _this.updateTrackInfo();
        });
        track.addEventListener("touchstart", (e) => {
          _this.trackInteraction(e);
        });
      });

      this.trackInteraction = function (e) {
        this.trackInfo[this.trackLoaded].classList.toggle("highlighted-track");
        this.trackLoaded = e.target.dataset.id;
        this.trackInfo[this.trackLoaded].classList.toggle("highlighted-track");
        
        this.loadSong(this.trackLoaded);
        this.playAudio();

        this.updateTrackInfo();
        if (!this.isPlaying) {
          this.playBtn.classList.toggle("highlighted");
          this.stopBtn.classList.toggle("highlighted");
        }
        this.isaudioPaused();
        this.isPlaying = true;
        //this.visualisation.style.display = "block";
      };

      this.updateTrackInfo();
      
      this.loadSong(this.trackLoaded);
    };

    this.beforePlay = function () {
      this.loadVuMeter();
    };
    this.afterPlay = function () {
      //this.loadVuMeter();
      if(this.sourceConnected)
      {
        this.audioContext.resume();
      }
      
    };
    this.playAudio = function () {
      this.beforePlay();
      this.audio.play();
      this.onPlay();
      this.afterPlay();
    };

    this.sourceConnected = false;

    this.loadVuMeter = function () {
      if(!this.sourceConnected)
      {
        try
        {
        this.audioContext = new AudioContext();
        this.source = this.audioContext.createMediaElementSource(this.audio);
        this.source.connect(this.audioContext.destination);
        this.analyserLeft = this.audioContext.createAnalyser();
        this.analyserLeft.fftSize = 2048;
        let bufferLength = this.analyserLeft.frequencyBinCount;
        let dataArrayLeft = new Uint8Array(bufferLength);
        this.analyserLeft.getByteTimeDomainData(dataArrayLeft);
        this.analyserRight = this.audioContext.createAnalyser();
        this.analyserRight.fftSize = 2048;
        let dataArrayRight = new Uint8Array(bufferLength);
        this.analyserRight.getByteTimeDomainData(dataArrayRight);
        this.splitter = this.audioContext.createChannelSplitter(2);
        this.source.connect(this.splitter);
        this.splitter.connect(this.analyserLeft, 1);
        this.splitter.connect(this.analyserRight, 0);
        this.sourceConnected = true;
        }
        catch(ex)
        {
          console.error(ex)
        }
  
        
      }
      clearInterval(this.drawInterval);
      
    };

    this.updateTrackInfo = function () {
      let tNumber = this.tracks[this.trackLoaded].trackNumber;
      if (tNumber < 10) {
        tNumber = "0" + tNumber;
      }
      this.trackInfoDisplayer.textContent =
        tNumber +
        ". " +
        this.tracks[this.trackLoaded].name +
        " (" +
        this.trackDuration(this.trackLoaded) +
        ")";
    };

    this.trackDuration = function (place) {
      return (
        Math.floor(this.tracks[place].duration / 60) +
        ":" +
        (this.tracks[place].duration % 60 < 10 ? "0" : "") +
        Math.floor(this.tracks[place].duration % 60)
      );
    };

    this.isaudioPaused = function () {
      if (this.pause) {
        this.pause = false;
        this.pauseBtn.classList.toggle("highlighted");
      }
    };

    this.hilightCurrentTrack = function (trackNumber) {
      let i = 0;
      this.trackInfo.forEach((track) => {
        track.classList.remove("highlighted-track");
        if (i == trackNumber) {
          track.classList.add("highlighted-track");
        }
        i++;
      });
    };

    this.nextTrack = function () {
      if (this.shuffle) {
        this.trackLoaded = Math.floor(Math.random() * this.tracks.length);
      } else {
        this.trackLoaded++;
        this.loadSong(this.trackLoaded);
      }
      this.hilightCurrentTrack(this.trackLoaded);
      
      

      this.playAudio();
      this.isPlaying = true;

      this.updateTrackInfo();
    };

    this.init = function (list) {
      this.songList = list;
      this.qsall = document.querySelectorAll.bind(document); //shortcut for querySelectorAll
      this.qs = document.querySelector.bind(document); //shortcut for querySelector
      this.root = document.querySelector(":root");
      this.timeDisplayer = this.qs(".time-displayer");
      this.trackInfoDisplayer = this.qs(".track-info-displayer");
      this.volumeController = this.qs(".volume-controller");
      this.progressBar = this.qs(".progress-bar");
      //this.visualisation = this.qs(".visualisation");
      this.resizable = this.qsall(".resizable");
      this.navBtn = this.qsall(".nav-btn");
      this.prevBtn = this.qs(".prev-btn");
      this.playBtn = this.qs(".play-btn");
      this.pauseBtn = this.qs(".pause-btn");
      this.stopBtn = this.qs(".stop-btn");
      this.nextBtn = this.qs(".next-btn");
      this.shuffleBtn = this.qs(".shuffle-btn");
      this.repeatBtn = this.qs(".repeat-btn");
      this.playlist = this.qs(".playlist");
      
      this.audio = new Audio();
      this.audio.setAttribute("crossorigin", "anonymous");

      this.tracks = []; // array with tracks info : name, title, duration and url
      this.tracksNb = this.songList.songList.length; // number of tracks
      this.tracksCreated = 0;
      this.trackInfo = []; //will store the track-info div after their creation
      this.trackLoaded = 0; //track that will be played

      this.isPlaying = false;
      this.isPaused = false;
      this.isShuffle = false;
      this.isRepeat = false;

      this.lightness = "50%";

      //Now the playlist is created, let handle the buttons
      this.playBtn.addEventListener("click", () => {
        if (!this.isPlaying) {
          
          
          this.loadSong(this.trackLoaded);

          this.playAudio();

          this.isPlaying = true;

          this.playBtn.classList.toggle("highlighted");
          this.stopBtn.classList.toggle("highlighted");
          //this.visualisation.style.display = "block";
        }
      });

      this.stopBtn.addEventListener("click", () => {
        if (this.isPlaying) {
          this.audio.pause();
          this.audio.currentTime = 0;
          this.isPlaying = false;
          this.isaudioPaused();
          this.playBtn.classList.toggle("highlighted");
          this.stopBtn.classList.toggle("highlighted");
          //this.visualisation.style.display = "none";
        }
      });

      this.navBtn.forEach((navigation) => {
        navigation.addEventListener("mouseup", () => {
          navigation.classList.toggle("highlighted");
        });

        navigation.addEventListener("mousedown", () => {
          navigation.classList.toggle("highlighted");
        });

        navigation.addEventListener("click", () => {
          this.trackInfo[this.trackLoaded].classList.toggle(
            "highlighted-track"
          );
          if (navigation.dataset.nav === "prev") {
            if (this.trackLoaded === 0) {
              this.trackLoaded = this.tracks.length - 1;
            } else {
              this.trackLoaded--;
            }
          } else if (navigation.dataset.nav === "next") {
            if (this.trackLoaded === this.tracks.length - 1) {
              this.trackLoaded = 0;
            } else {
              this.trackLoaded++;
            }
          } else {
            console.error("Unexpected error");
          }
          
          this.loadSong(this.trackLoaded);
          this.trackInfo[this.trackLoaded].classList.toggle(
            "highlighted-track"
          );
          if (this.isPlaying) {
            this.playAudio();
            this.isPlaying = true;
          }
          this.isaudioPaused();
          this.updateTrackInfo();
        });
      });

      this.pauseBtn.addEventListener("click", () => {
        if (this.tracks) {
          this.pauseBtn.classList.toggle("highlighted");
          if (!this.isPaused) {
            this.audio.pause();
            this.isPaused = true;
            //this.visualisation.style.display = "none";
          } else {
            this.playAudio();
            this.isPlaying = true;

            this.isPaused = false;
            //this.visualisation.style.display = "block";
          }
        }
      });

      // display time elapsed
      this.audio.addEventListener("timeupdate", (e) => {
        this.timeDisplayer.textContent =
          (e.target.currentTime / 60 < 10 ? "0" : "") +
          Math.floor(e.target.currentTime / 60) +
          ":" +
          (e.target.currentTime % 60 < 10 ? "0" : "") +
          Math.floor(e.target.currentTime % 60);
        if (e.target.duration > 0) {
          this.progressBar.value = e.target.currentTime / e.target.duration;
        } else {
          this.progressBar.value = 0;
        }
      });
      //progressBar interaction
      this.progressBar.addEventListener("input", (e) => {
        if (this.audio.duration > 0) {
          this.audio.currentTime = this.audio.duration * e.target.value;
        } else {
          this.audio.currentTime = 0;
        }
      });

      //volume controller
      this.volumeController.addEventListener("input", (e) => {
        this.audio.volume = e.target.value / 100;
        let lightness = 100 - e.target.value / 2 + "%";
        document.documentElement.style.setProperty(
          "--volume-track-lightness",
          lightness
        );
      });

      // when the track ends, move to the next track or a random one
      this.shuffleBtn.addEventListener("click", () => {
        if (this.isRepeat) {
          this.repeatBtn.classList.toggle("highlighted");
          this.isRepeat = false;
        }
        this.shuffleBtn.classList.toggle("highlighted");
        if (this.isShuffle) {
          this.isShuffle = false;
        } else {
          this.isShuffle = true;
        }
      });

      this.repeatBtn.addEventListener("click", () => {
        if (this.isShuffle) {
          this.shuffleBtn.classList.toggle("highlighted");
          this.isShuffle = false;
        }
        this.repeatBtn.classList.toggle("highlighted");
        if (this.isRepeat) {
          this.isRepeat = false;
        } else {
          this.isRepeat = true;
        }
      });

      this.audio.addEventListener("ended", () => {
        if (this.isRepeat) {
          this.audio.currentTime = 0;
          this.playAudio();
          this.isPlaying = true;
        } else if (this.trackLoaded < this.tracks.length - 1) {
          this.nextTrack();
        } else if (this.isShuffle) {
          this.nextTrack();
        } else {
          console.log("fin");
        }
      });

      // expand the playlist or the visualisation
      this.resizable.forEach((resize) => {
        resize.addEventListener("click", () => {
          let resizeParent = this.qs("." + resize.parentNode.className);
          if (resizeParent != null) {
            let minimized =
              resizeParent.getAttribute("data-minimized") || "false";
            resizeParent.setAttribute(
              "data-minimized",
              minimized == "true" ? "false" : "true"
            );
          }
        });
      });

      // creation of the playlist, because I'm a little bit lazy, JS create the array for me :D

      
      for (let i = 0; i < this.tracksNb; i++) {
        this.tracks.push({
          name: this.songList.songList[i].name,
          title: this.songList.songList[i].title,
          duration: this.songList.songList[i].duration,
          trackNumber: this.songList.songList[i].track_number,
          url: this.songList.songList[i].song_url,
        });

        /*
        this.audioForDuration = document.createElement("audio");
        this.audioForDuration.src = this.tracks[i].url;
        this.audioForDuration.dataset.id = i;

        this.audioForDuration.addEventListener("loadedmetadata", function (e) {
          _this.tracksCreated++;
          if (_this.tracksCreated === _this.tracksNb) {
            _this.createPlaylist();
          } else {
            console.log("tracks created: " + _this.tracksCreated);
          }
        });
        */
        
      }
      _this.createPlaylist();
      
    };
    if(typeof list != 'undefined')
    {
        this.init(list);
    }
  }
}
