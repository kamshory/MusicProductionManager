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
            if (note != null) {
                let start = this.audioContext.currentTime;
                if(start >= 0)
                {
                    // remaining
                    let remaining = (note.duration + note.start - time) * 1000;
                    if(remaining > 0 && _this.lastTime != note.time)
                    {
                        _this.lastTime = note.time;
                        _this.lastNote = note.note;
                        let inst = this.instrument[instrumentName];
                        inst.play(note.note, start, { duration: remaining });
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
    this.lastIndex = 0;
    this.lastTimeSecond = 0;
    this.getStartIndex = function(time)
    {
        return time > this.lastTimeSecond ? this.lastIndex : 0;
    }
    this.getNote = function (timeSecond) {
        let startIndex = this.getStartIndex();
        for (let index = startIndex; index < this.noteList.length; index++) {
            if (this.noteList[index].start <= timeSecond && this.noteList[index].end >= timeSecond) {
                this.lastIndex = index;
                return this.noteList[index];
            }
        }
        return null;
    }
    this.loadInstrument = function(instrumentName)
    {
        Soundfont.instrument(this.audioContext, instrumentName).then(function (instrument) {
            _this.instrument[instrumentName] = instrument;   
        })
    }
    let _this = this;
}

