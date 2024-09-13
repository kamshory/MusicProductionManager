class Karaoke {
  constructor(data, elementSelector) {
    this.scale = 1;
    this.threshold = 30;
    this.last = 0;
    this.elem = null;
    this.subtitle = null;
    this.height = 0;
    this.duration = 0;

    /**
     * Check if line is timestamp or not
     * @param {String} text
     * @returns {Bool}
     */
    this.isTimestamp = function (text) {
      return text.indexOf("-->") > -1;
    };

    /**
     * Parse timestamp
     * @param {String} text
     * @returns
     */
    this.parseTimestamp = function (text) {
      let arr = text.split("-->");
      return {
        start: arr[0].trim(),
        end: arr[1].trim(),
      };
    };

    this.parseTimestampMills = function (text) {
      let ts = this.parseTimestamp(text);
      let start = this.convertTimestampToMills(ts.start);
      let end = this.convertTimestampToMills(ts.end);
      return {
        start: start,
        end: end,
      };
    };

    this.convertTimestampToMills = function (text) {
      let secStr = "";
      let msStr = "0";
      if (text.indexOf(",") > -1) {
        let arr = text.split(",");
        secStr = arr[0];
        msStr = arr[1];
      } else if (text.indexOf(".") > -1) {
        let arr = text.split(".");
        secStr = arr[0];
        msStr = arr[1];
      } else {
        secStr = text;
      }
      let ms1 = this.dmsToDdFromString(secStr) * 3600000;
      let ms2 = parseFloat(msStr);
      return ms1 + ms2;
    };

    this.truncate = function (val) {
      return Math.floor(val);
    };

    /**
     * Concatenantion text1 and text2 separated by carriage return
     * @param {String} text1
     * @param {String} text2
     * @returns Combination of text1 and text2 separated by carriage return
     */
    this.concat = function (text1, text2) {
      let arr = [];
      if (text1 != null) {
        arr.push(text1);
      }
      if (text2.length > 0) {
        arr.push(text2);
      }
      return arr.join("\r\n");
    };

    /**
     * Convert float to hour, minute and second with padding
     * @param {float} d
     * @param {float} fixed
     * @param {float} padding
     * @returns {String}
     */
    this.dToDmsReal = function (d, fixed, padding) {
      if (typeof fixed == "undefined" || fixed == null) {
        fixed = 3;
      }
      let absDd = Math.abs(d);
      let deg = absDd | 0;
      let frac = absDd - deg;
      let min = (frac * 60) | 0;
      let sec = frac * 3600 - min * 60;
      sec = sec.toFixed(fixed);
      if (padding) {
        if (deg < 10) {
          deg = "0" + deg;
        }
        if (min < 10) {
          min = "0" + min;
        }
        if (sec < 10) {
          sec = "0" + sec;
        }
      }
      return [deg, min, sec];
    };

    /**
     * Convert hour to hour, minte and second with zero padding and colon separator
     * @param {Number} dd
     * @returns {String}
     */
    this.ddToDms = function (dd) {
      let absDd = Math.abs(dd);
      let deg = absDd | 0;
      let frac = absDd - deg;
      let min = (frac * 60) | 0;
      let sec = frac * 3600 - min * 60;
      sec = Math.round(sec * 1000) / 1000;
      return {
        d: deg,
        m: min,
        s: sec,
      };
    };

    /**
     * Convert hour, minte and second to hour
     * @param {Integer} d
     * @param {Integer} m
     * @param {float} s
     * @returns {String}
     */
    this.dmsToDd = function (d, m, s) {
      let degrees = typeof d !== "undefined" ? parseFloat(d) : 0;
      let minutes = typeof m !== "undefined" ? parseFloat(m) / 60 : 0;
      let seconds = typeof s !== "undefined" ? parseFloat(s) / 3600 : 0;
      return degrees + minutes + seconds;
    };

    /**
     * Split hour, minute and second into array
     * @param {String} dmsStr
     * @returns {Array}
     */
    this.dmsToDdFromString = function (dmsStr) {
      let dms = dmsStr.split(":");
      return this.dmsToDd(dms[0], dms[1], dms[2]);
    };

    /**
     * Convert milisecond to timestamp string
     * @param {float} ms
     * @returns {String}
     */
    this.ddToDmsSrtFormat = function (ms) {
      let dms = this.ddToDms(ms);
      let d = dms.d;
      let m = dms.m;
      let s = dms.s.toFixed(3);
      if (d < 10) {
        d = "0" + d;
      }
      if (m < 10) {
        m = "0" + m;
      }
      if (s < 10) {
        s = "0" + s;
      }
      return d + ":" + m + ":" + s.replace(".", ",");
    };

    /**
     * Parse string to floating point
     * @param {String} value
     * @returns {float}
     */
    this.parseFloat = function (value) {
      if (value != null && typeof value == "string") {
        if (value.length > 0) {
          value = value.replace(/[^.\d]/g, "");
          if (value == "") {
            return 0;
          }
          return Number(value);
        }
      } else {
        return value;
      }
      return 0;
    };

    /**
     * Convert millisecond to unit
     * @param {float} value
     * @returns {float} unit
     */
    this.msToUnit = function (value) {
      return value * this.scale * this.zoom;
    };

    /**
     * Convert unit to millisecond
     * @param {float} value
     * @returns {float}
     */
    this.unitToMs = function (value) {
      return value / (this.scale * this.zoom);
    };

    /**
     * Create data
     * @param {Object} tsData
     * @param {String} text
     * @returns {Object}
     */
    this.createData = function (tsData, text) {
      if (tsData) {
        let timestamp = this.parseTimestampMills(tsData);
        let start = timestamp.start;
        let end = timestamp.end;
        let duration = end - start;
        return {
          start: start,
          end: end,
          duration: duration,
          text: text,
        };
      }
      return {
        start: 0,
        end: 0,
        duration: 0,
        text: text,
      };
    };

    /**
     * Get minimum duration
     * @param {Object} data
     * @returns {float}
     */
    this.getMinimumDuration = function (data) {
      let min = -1;
      for (let i in data.data) {
        if (min > data.data[i].duration || min == -1) {
          min = data.data[i].duration;
        }
      }
      return min;
    };

    /**
     * Parse raw data
     * @param {String} rawData
     * @returns {Object}
     */
    this.parseRawData = function (rawData) {
      rawData = rawData.trim().replace(/\r?\n/g, "\r\n");
      let tempData = rawData.split(/\n/g);
      let j = -1;
      let data = [];
      let nonempty = 0;
      for (let i = 0; i < tempData.length; i++) {
        tempData[i] = tempData[i].trim();
        if (this.isTimestamp(tempData[i])) {
          j++;
          data[j] = this.createData(tempData[i]);
        } else {
          if (j > -1) {
            data[j].text = this.concat(data[j].text, tempData[i]);
          }
          nonempty++;
        }
      }
      if (typeof data[j].text == "undefined") {
        data[j].text = "";
      }
      return { data: data, nonempty: nonempty, totalLength: tempData.length };
    };

    this.renderPrompt = function (data, selector) {
      this.subtitle = data;
      let elem = document.querySelector(selector);
      this.elem = elem;
      let minDur = this.getMinimumDuration(data);
      let minHeight = 32; // minimum height
      this.scale = minHeight / minDur;
      elem.innerHTML = "";
      for (let i in data.data) {
        let dur = data.data[i].duration;
        let start = data.data[i].start;
        let height = this.scale * dur;
        let top = this.scale * start;
        let textContainer = document.createElement("div");
        textContainer.style.height = Math.round(height) + "px";
        textContainer.style.top = Math.round(top) + "px";
        textContainer.innerText = data.data[i].text;
        textContainer.setAttribute("data-index", i);
        elem.appendChild(textContainer);
      }
      elem.style.height = this.scale * this.duration + "px";
    };

    this.getIndex = function (ellapsed) {
      for (let i in this.subtitle.data) {
        if (
          this.subtitle.data[i].start <= ellapsed &&
          this.subtitle.data[i].end >= ellapsed
        ) {
          return parseInt(i);
        }
      }
      return -1;
    };

    this.animate = function () {
      let now = new Date().getTime();

      if (now - this.last >= this.threshold) {
        this.height = this.elem.parentNode.offsetHeight;
        let ellapsed = now - this.getStart();
        let offset = this.height / 4;
        let top = offset - ellapsed * this.scale;

        let selected = this.getIndex(ellapsed);
        this.markSelected(selected);

        this.elem.style.top = top + "px";
        this.last = now;
      }
    };

    this.updatePosition = function (ellapsed, offset) {
      offset = offset || 0;
      this.height = this.elem.parentNode.offsetHeight;
      let offset2 = this.height / 4;
      let top = offset2 - ellapsed * this.scale;

      let selected = this.getIndex(ellapsed);
      this.markSelected(selected);

      this.elem.style.top = (offset + top) + "px";
    };

    this.readStored = false;
    this.offsetStored = 0;

    this.getStart = function () {
      if (this.readStored) {
        return this.offsetStored;
      } else {
        let stored = localStorage.getItem("offset_" + this.songId);
        if (typeof stored == "undefined" || stored == null) {
          return this.start;
        } else {
          stored = stored.replace(/[^0-9.]/g, "");
          if (stored == "") {
            stored = "0";
          }
          let parsed = parseInt(stored);
          if (parsed == 0) {
            return this.start;
          }
          this.offsetStored = parsed;
          this.readStored = true;
          return parsed;
        }
      }
    };

    this.markSelected = function (selected) {
      if (this.lastSelected != selected) {
        if (selected == -1) {
          // clean up
          this.cleanup();
        } else {
          // mark up
          this.markup(selected);
        }
      }
      this.lastSelected = selected;
    };
    this.cleanup = function () {
      let numb = this.elem.childNodes.length;
      for (let i = 0; i < numb; i++) {
        this.elem.childNodes[i].classList.remove("marked");
      }
    };
    this.markup = function (selected) {
      this.cleanup();
      this.elem.childNodes[selected].classList.add("marked");
    };

    this.lastSelected = -1;
    this.songId = "";

    this.init = function (data, elementSelector) {
      this.start = data.start;
      this.songId = data.song_id;
      this.duration = data.duration;
      let parsed = this.parseRawData(data.subtitle);
      this.subtitle = parsed;
      this.renderPrompt(parsed, elementSelector);
    };

    let _this = this;
    this.init(data, elementSelector);
  }
}
