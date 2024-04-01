
function Winamp(list) {
    let _this = this;
    this.trackname = [];
    this.qsall = null; 
    this.qs = null; 
    this.root = null; 
    this.timeDisplayer = null; 
    this.trackInfoDisplayer = null; 
    this.volumeController = null; 
    this.progressBar = null; 
    this.visualisation = null; 
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
    
    this.createPlaylistItem = function(i)
    {
        let tNumber = i;
        if(i < 10)
        {
            tNumber = '0'+i;
        }
        return '<div class = "track-info' + (i === 0 ? ' highlighted-track' : '') + '" data-id = "' + i + '"><div class = "track-id">' + tNumber + ' ' + this.tracks[i].title + ' - ' + this.tracks[i].name + '</div><div class = "track-duration">' + this.trackDuration(i) + '</div></div>';
    }

    this.createPlaylist = function () {
        //first I fill the playlist with the tracks infos
        for (i = 0; i < this.tracks.length; i++) {
            this.playlist.innerHTML += this.createPlaylistItem(i);
        }

        //then I create the informations interactions (on double click / on touch)

        this.trackInfo = this.qsall('.track-info');

        this.trackInfo.forEach(track => {
            track.addEventListener('dblclick', (e) => {
                _this.trackInteraction(e);
            })
            track.addEventListener('click', (e) => {
                _this.trackInfo[_this.trackLoaded].classList.toggle('highlighted-track');
                _this.trackLoaded = e.target.dataset.id;
                _this.trackInfo[_this.trackLoaded].classList.toggle('highlighted-track');
                _this.updateTrackInfo();
            })
            track.addEventListener('touchstart', (e) => {
                _this.trackInteraction(e)
            })
        })

        this.trackInteraction = function (e) {
            this.trackInfo[this.trackLoaded].classList.toggle('highlighted-track');
            this.trackLoaded = e.target.dataset.id;
            this.trackInfo[this.trackLoaded].classList.toggle('highlighted-track');
            this.audio.src = this.tracks[this.trackLoaded].url;
            this.audio.play();
            this.updateTrackInfo();
            if (!this.play) {
                this.playBtn.classList.toggle('highlighted');
                this.stopBtn.classList.toggle('highlighted');
            }
            this.isaudioPaused();
            this.play = true;
            this.visualisation.style.display = 'block';
        }

        this.updateTrackInfo();
        this.audio.src = this.tracks[this.trackLoaded].url;
    }

    this.updateTrackInfo = function () {
        this.trackInfoDisplayer.textContent = (parseInt(this.trackLoaded, 10) + 1) + '. ' + this.tracks[this.trackLoaded].name + ' (' + this.trackDuration(this.trackLoaded) + ')'
    }

    this.trackDuration = function (place) {
        return (
            Math.floor(this.tracks[place].duration / 60) + ':' + (this.tracks[place].duration % 60 < 10 ? '0' : '') + Math.floor(this.tracks[place].duration % 60)
        );
    }

    this.isaudioPaused = function () {
        if (this.pause) {
            this.pause = false;
            this.pauseBtn.classList.toggle('highlighted');
            this.visualisation.style.display = 'block';
        }
    }
    this.hilightCurrentTrack = function(trackNumber)
    {
        let i = 0;
        this.trackInfo.forEach(track => {
            track.classList.remove('highlighted-track');
            if(i == trackNumber)
            {
                track.classList.add('highlighted-track');
            }
            i++;
        })
    }
    this.nextTrack = function() {
        if(this.shuffle)
        {
            this.trackLoaded = Math.floor(Math.random() * this.tracks.length);    
        }
        else
        {
            this.trackLoaded++;
        }
        this.hilightCurrentTrack(this.trackLoaded);
        this.audio.src = this.tracks[this.trackLoaded].url;
        this.audio.play();
        this.updateTrackInfo();
    }

    this.init = function (list) {
        this.songList = list;
        this.qsall = document.querySelectorAll.bind(document); //shortcut for querySelectorAll
        this.qs = document.querySelector.bind(document); //shortcut for querySelector
        this.root = document.querySelector(':root');
        this.timeDisplayer = this.qs('.time-displayer');
        this.trackInfoDisplayer = this.qs('.track-info-displayer');
        this.volumeController = this.qs('.volume-controller');
        this.progressBar = this.qs('.progress-bar');
        this.visualisation = this.qs('.visualisation');
        this.resizable = this.qsall('.resizable');
        this.navBtn = this.qsall('.nav-btn');
        this.prevBtn = this.qs('.prev-btn');
        this.playBtn = this.qs('.play-btn');
        this.pauseBtn = this.qs('.pause-btn');
        this.stopBtn = this.qs('.stop-btn');
        this.nextBtn = this.qs('.next-btn');
        this.shuffleBtn = this.qs('.shuffle-btn');
        this.repeatBtn = this.qs('.repeat-btn');
        this.playlist = this.qs('.playlist');
        this.audio = new Audio;
        this.tracks = []; // array with tracks info : name, title, duration and url
        this.tracksNb = this.songList.songList.length;// number of tracks
        this.tracksCreated = 0;
        this.trackInfo = []; //will store the track-info div after their creation
        this.trackLoaded = 0; //track that will be played
        this.play = false;
        this.pause = false;
        this.shuffle = false;
        this.repeat = false;
        this.lightness = '50%';

        //Now the playlist is created, let handle the buttons
        this.playBtn.addEventListener('click', () => {
            if (!this.play) {
                this.audio.src = this.tracks[this.trackLoaded].url;
                this.audio.play();
                this.play = true;
                this.playBtn.classList.toggle('highlighted');
                this.stopBtn.classList.toggle('highlighted');
                this.visualisation.style.display = 'block';
            }
        })

        this.stopBtn.addEventListener('click', () => {
            if (this.play) {
                this.audio.pause();
                this.audio.currentTime = 0;
                this.play = false;
                this.isaudioPaused();
                this.playBtn.classList.toggle('highlighted');
                this.stopBtn.classList.toggle('highlighted');
                this.visualisation.style.display = 'none';
            }
        })

        this.navBtn.forEach(navigation => {

            navigation.addEventListener('mouseup', () => {
                navigation.classList.toggle('highlighted');
            })

            navigation.addEventListener('mousedown', () => {
                navigation.classList.toggle('highlighted');
            })

            navigation.addEventListener('click', () => {
                this.trackInfo[this.trackLoaded].classList.toggle('highlighted-track');
                if (navigation.dataset.nav === 'prev') {
                    this.trackLoaded === (0) ? this.trackLoaded = (this.tracks.length - 1) : this.trackLoaded--;
                }
                else if (navigation.dataset.nav === 'next') {
                    this.trackLoaded === (this.tracks.length - 1) ? this.trackLoaded = 0 : this.trackLoaded++;
                }
                else {
                    console.error('there is a ball in the soup (a french expression)');
                }
                this.audio.src = this.tracks[this.trackLoaded].url;
                this.trackInfo[this.trackLoaded].classList.toggle('highlighted-track');
                this.play ? this.audio.play() : null;
                this.isaudioPaused();
                this.updateTrackInfo();
            })
        })

        this.pauseBtn.addEventListener('click', () => {
            if (this.tracks) {
                this.pauseBtn.classList.toggle('highlighted');
                if (!this.pause) {
                    this.audio.pause();
                    this.pause = true;
                    this.visualisation.style.display = 'none';
                }
                else {
                    this.audio.play();
                    this.pause = false;
                    this.visualisation.style.display = 'block';
                }
            }
        })



        // display time elapsed
        this.audio.addEventListener('timeupdate', (e) => {
            this.timeDisplayer.textContent = (e.target.currentTime / 60 < 10 ? '0' : '') + Math.floor(e.target.currentTime / 60) + ':' + (e.target.currentTime % 60 < 10 ? '0' : '') + Math.floor(e.target.currentTime % 60);
            if(e.target.duration > 0)
            {
                this.progressBar.value = e.target.currentTime / e.target.duration;
            }
            else
            {
                this.progressBar.value = 0;
            }
        })
        //progressBar interaction
        this.progressBar.addEventListener('input', (e) => {
            if(this.audio.duration > 0)
            {
                this.audio.currentTime = this.audio.duration * e.target.value;
            }
            else
            {
                this.audio.currentTime = 0;
            }
        })

        //volume controller
        this.volumeController.addEventListener('input', (e) => {
            this.audio.volume = (e.target.value / 100);
            lightness = (100 - (e.target.value / 2)) + '%';
            document.documentElement.style.setProperty('--volume-track-lightness', lightness);
        })

        // when the track ends, move to the next track or a random one

        this.shuffleBtn.addEventListener('click', () => {
            if (this.repeat) {
                this.repeatBtn.classList.toggle('highlighted');
                this.repeat = false;
            }
            this.shuffleBtn.classList.toggle('highlighted');
            this.shuffle ? this.shuffle = false : this.shuffle = true;
        })

        this.repeatBtn.addEventListener('click', () => {
            if (this.shuffle) {
                this.shuffleBtn.classList.toggle('highlighted');
                this.shuffle = false;
            }
            this.repeatBtn.classList.toggle('highlighted');
            this.repeat ? this.repeat = false : this.repeat = true;
        })

        this.audio.addEventListener('ended', () => {
            this.repeat ? (this.audio.currentTime = 0, this.audio.play()) : this.trackLoaded < (this.tracks.length - 1) ? this.nextTrack() : this.shuffle ? this.nextTrack() : console.log('fin')
        })

        // expand the playlist or the visualisation

        this.resizable.forEach(resize => {
            resize.addEventListener('click', () => {
                let resizeParent = this.qs('.' + resize.parentNode.className);
                resizeParent.style.height === 'auto' ? resizeParent.style.height = '2rem' : resizeParent.style.height = 'auto';
            })
        });

        // creation of the playlist, because I'm a little bit lazy, JS create the array for me :D

        for (i = 0; i < this.tracksNb; i++) {

            this.tracks.push(
                {
                    name: '',
                    title: '',
                    duration: '',
                    url: ''
                })

            
            this.tracks[i].name = 'Track ' + this.songList.songList[i].track_number;
            this.tracks[i].title = this.songList.songList[i].title;
            this.tracks[i].url = this.songList.songList[i].song_url;
            this.tracks[i].duration = this.songList.songList[i].duration;
            this.audioForDuration = document.createElement('audio');
            this.audioForDuration.src = this.tracks[i].url;           
            
            this.audioForDuration.dataset.id = i;
            this.audioForDuration.addEventListener('loadedmetadata', function (e) {
                _this.tracksCreated++;
                //once my tracks array is fill I have to add my tracks info in the playlist container
                _this.tracksCreated === _this.tracksNb ? _this.createPlaylist() : console.log('tracks created: ' + _this.tracksCreated);
            })
        }

    }

    this.init(list);


}







