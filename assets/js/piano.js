function Piano(elem) {
    /**
     * White tuts width
     */
    this.factor = 16;
    
    /**
     * Lowest note
     */
    this.min = 12;
    
    /**
     * Hightes
     */
    this.max = 133;
    
    /**
     * HTML element
     */
    this.element = elem;
    
    /**
     * Current time
     */
    this.time = 0;

    /**
     * Tuts property per octave
     */
    this.inf = [
        {
            type: 1,
            offset: 0,
            width: 1,
        },
        {
            type: 2,
            offset: 0.5,
            width: 0.8,
        },
        {
            type: 1,
            offset: 1,
            width: 1,
        },
        {
            type: 2,
            offset: 1.7,
            width: 0.8,
        },
        {
            type: 1,
            offset: 2,
            width: 1,
        },
        {
            type: 1,
            offset: 3,
            width: 1,
        },
        {
            type: 2,
            offset: 3.5,
            width: 0.8,
        },
        {
            type: 1,
            offset: 4,
            width: 1,
        },
        {
            type: 2,
            offset: 4.6,
            width: 0.8,
        },
        {
            type: 1,
            offset: 5,
            width: 1,
        },
        {
            type: 2,
            offset: 5.7,
            width: 0.8,
        },
        {
            type: 1,
            offset: 6,
            width: 1,
        },
    ];
    
    /**
     * Song
     */
    this.song = [];
    
    /**
     * Set note duration
     */
    this.setDuration = function()
    {
        for(let i in this.song)
        {
            this.song[i].duration = this.song[i].end - this.song[i].start;
        }
    }
    
    /**
     * Set song
     * @param {array} song 
     */
    this.setSong = function(song)
    {
        this.song = song;
        let songMin = 127;
        let songMax = 0;
        for(let i in this.song)
        {
            let val = this.song[i].note;
            if(val < songMin)
            {
                songMin = val;
            }
            if(val > songMax)
            {
                songMax = val;
            }
        }
        this.min = songMin - (songMin % 12);
        this.max = 12 + songMax - (songMax % 12);
        this.createPiano();
    }
    
    /**
     * Set time
     * @param {long} time 
     */
    this.setTime = function(time)
    {
        this.time = time;
    }
    
    /**
     * Get time
     * @param {long} time 
     * @returns {array}
     */
    this.getNotes = function(time)
    {
        let notes = [];
        for(let i in this.song)
        {
            if(this.song[i].start <= time && this.song[i].end >= time)
            {
                notes.push(this.song[i]);
            }
        }
        return notes;
    }
    
    /**
     * Draw note on
     */
    this.draw = function()
    {
        
        let notes = this.getNotes(this.time);        
        this.clearTuts();
        let lyricContainer = this.element.querySelector('.lyric-container');
        lyricContainer.innerHTML = "";
        for(let i = 0; i<notes.length;i++)
        {
            this.setNoteOn(notes[i]);
        }
    }
    
    /**
     * Initialization
     * @param {Element} elem 
     */
    this.init = function(elem)
    {
        this.elem = elem;
    }
    
    /**
     * Clear tuts
     */
    this.clearTuts = function()
    {
        let tuts = this.element.querySelectorAll('.piano-tuts');
        for(let i = 0; i < tuts.length; i++)
        {
            tuts[i].classList.remove('note-on');
        }
    }
    
    /**
     * Set note on
     * @param {Object} note 
     */
    this.setNoteOn = function(note)
    {
        let lyricContainer = this.element.querySelector('.lyric-container');
        let tuts = this.element.querySelector('.piano-tuts[data-index="'+note.note+'"]');
        if(tuts != null)
        {
            tuts.classList.add('note-on');
            let lyric = document.createElement('div');
            let left = tuts.offsetLeft - 25;
            left += tuts.offsetWidth / 2;
            lyric.style.left = left + 'px';
            lyric.classList.add('lyric-item');
            lyric.innerHTML = note.lyric;
            lyricContainer.appendChild(lyric);          
        }
    }

    /**
     * Calculate width
     * @returns {float}
     */
    this.calculateWidth = function()
    {
        let tuts = this.element.querySelectorAll('.piano-tuts.piano-tuts-white');
        return this.factor * tuts.length;        
    }

    /**
     * Create piano
     */
    this.createPiano = function () {
        let factor = this.factor;
        let min = this.min;
        let max = this.max;
        let width = 71 * factor; // 10 octave
        this.tutsWidth = width;

        this.element.innerHTML = "";
        this.element.style.width = width + "px";       
        
        let lyric = document.createElement('div');
        this.element.appendChild(lyric);
        lyric.classList.add('lyric-container');

        let inner = document.createElement('div');
        this.element.appendChild(inner);
        inner.classList.add('piano-inner');

        let octave = document.createElement('div');
        this.element.appendChild(octave);
        octave.classList.add('piano-octave');

        for (let i = min; i < max; i++) {
            let tuts = document.createElement("div");
            let mod = i % 12;
            let octave = Math.floor((i - min) / 12);
            let key = this.inf[mod];
            if (key.type == 1) {
                tuts.className = "piano-tuts piano-tuts-white";
                tuts.setAttribute("data-index", i);
                tuts.style.left = ((octave * 7 * factor + key.offset * factor) + 0) + "px";
                tuts.style.width = key.width * factor + "px";
                inner.appendChild(tuts);
            }
        }
        for (let i = min; i < max; i++) {
            let tuts = document.createElement("div");
            let mod = i % 12;
            let octave = Math.floor((i - min) / 12);
            let key = this.inf[mod];
            if (key.type == 2) {
                tuts.className = "piano-tuts piano-tuts-black";
                tuts.setAttribute("data-index", i);
                tuts.style.left = ((octave * 7 * factor + key.offset * factor) + 0) + "px";
                tuts.style.width = key.width * factor + "px";
                inner.appendChild(tuts);
            }
        }
        let octaveMin = this.min / 12;
        let octaveMax = this.max / 12;
        let offsetLeft = 0;
        for(let i = octaveMin; i<octaveMax; i++)
        {
            let marker = document.createElement('div');
            marker.classList.add('octave-marker');
            marker.style.width = ((7 * this.factor) - 1) + 'px';
            marker.style.left = (offsetLeft - 0) + 'px';
            marker.innerText = i;
            octave.appendChild(marker);
            offsetLeft += 7 * this.factor;
        }    
        let allWitdh = this.calculateWidth() ;
        this.elem.style.width = allWitdh + 'px';      
    }
    
    this.init(elem);
    
}