let baseSoundFontUrl = "assets/soundfont/";
function SoundfontMidiPlayer() {
    this.timeList = [];
    this.noteList = [];
    this.loadNote = function (note) {
        this.noteList = note;
    }
    this.instrument = [];
    
    this.setAudioContext = function (audioContext) {
        this.audioContext = audioContext;
    }
    this.lastTime = 0;
    this.lastNote = 0;
    this.play = function (instrumentName, time, active) {
        if (active) {
            let note = this.getNote(time);
            if (note != null && !this.inList(note.time)) {
                this.timeList.push(time);
                let start = this.audioContext.currentTime;
                if(start >= 0)
                {
                    // remaining
                    let remaining = (note.duration + note.start - time) * 1000;
                    if(remaining > 0 && !_this.inList(note.time) && _this.lastTime != note.time)
                    {
                        _this.addList(note.time);
                        _this.lastTime = note.time;
                        _this.lastNote = note.note;
                        let inst = this.instrument[instrumentName];
                        inst.play(note.note, start, { duration: remaining });
                        setTimeout(function () {
                            _this.removeTimeFromList(note.time);
                        }, remaining);
                    }
                }
            }
        }
    }
    this.inList = function (time) {
        for (let i in this.timeList) {
            if (this.timeList[i] == time) {
                return true;
            }
        }
        return false;
    }
    this.removeTimeFromList = function (time) {
        this.timeList = this.timeList.splice(time, 1);
    }
    this.addList = function (time) {
        this.timeList.push(time);
    }
    this.getNote = function (time) {
        for (let i in this.noteList) {
            if (this.noteList[i].start <= time && this.noteList[i].end >= time) {
                return this.noteList[i];
            }
        }
        return null;
    }
    this.loadInstrument = function(instrumentName)
    {
        Soundfont.instrument(ac, instrumentName).then(function (instrument) {
            _this.instrument[instrumentName] = instrument;            
        })
    }
    let _this = this;
}

