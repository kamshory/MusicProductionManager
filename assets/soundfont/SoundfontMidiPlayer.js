function SoundfontMidiPlayer() {
    this.timeList = [];
    this.noteList = [];
    this.loadNote = function (note) {
        this.noteList = note;
    }
    this.setInstrument = function (instrument) {
        this.instrument = instrument;
    }
    this.setAudioContext = function (audioContext) {
        this.audioContext = audioContext;
    }
    this.play = function (timestamp, active) {
        if (active) {
            let note = this.getNote(timestamp);
            if (note != null && !this.inList(note.time)) {
                this.timeList.push(time);
                // remaining
                let remaining = note.duration + note.start - timestamp;
                this.instrument.play(note.note, this.audioContext.currentTime, { duration: remaining });
                setTimeout(function () {
                    _this.removeTimeFromList(note.time);
                }, remaining);
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
        this.timeList.splice(time, 1);
    }
    this.getNote = function (time) {
        for (let i in this.noteList) {
            if (this.noteList[i].start >= time && this.noteList[i].end <= time) {
                return this.noteList[i];
            }
        }
        return null;
    }
    let _this = this;
}