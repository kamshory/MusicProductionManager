if (window.Element && !Element.prototype.closest) {
    Element.prototype.closest =
        function (s) {
            let matches = (this.document || this.ownerDocument).querySelectorAll(s);
            let i;
            let el = this;
            do {
                i = matches.length;
                while (--i >= 0 && matches.item(i) !== el) {
                    // do nothing
                }
            } while ((i < 0) && (el = el.parentElement));
            return el;
        };
}

class Dubbing {
    constructor(editorCtrl, rawData, path) {
        this.editorCtrl = editorCtrl;
        this.rawData = rawData;
        this.path = path;
        this.audio = null;
        this.resolution = 1;
        this.scale = 0.05;
        this.unit = 'px';
        this.startWidth = 0;
        this.data = [];
        this.duration = 0;
        this.durationMinimum = 0;
        this.durationMin = 100;
        this.editMode = '';
        this.ended = false;
        this.indexToEdit = -1;
        this.elementToEdit = null;
        this.currentDataOffsetLeft = 0;
        this.currentDataTimeStart = 0;
        this.startX = 0;
        this.startWidth = 0;
        this.editorListElement = null;
        this.editorMapElement = null;
        this.sampleRate = 8000;
        this.ready = false;
        this.startPlayingTime = 0;
        this.scroll = false;
        this.diplayedText = '';
        this.diplayedTextLast = '';
        this.editor = null;
        this.isMouseDown = false;
        this.waveformArray = [];
        this.lastIndex = -1;
        this.zoom = 1;
        this.zoomLevelIndexOriginal = 4;
        this.zoomLevelIndex = this.zoomLevelIndexOriginal;
        this.zoomLevel = [
            0.125,
            0.25,
            0.5,
            0.75,
            1,
            1.25,
            1.5,
            1.75,
            2
        ];

        this.buttonContentPlay = '<i class="srt-button-play"></i></i>';
        this.buttonContentPause = '<i class="srt-button-pause"></i></i>';
        this.buttonContentStop = '<i class="srt-button-stop"></i></i>';
        this.buttonContentTrash = '<i class="srt-button-delete"></i></i>';

        /**
         * Get closest parent
         * @param {HTMLObjectElement} el
         * @param {String} selector
         * @returns {HTMLObjectElement} HTMLObjectElement
         */
        this.closest = function (el, selector) // NOSONAR
        {
            let moyang = el.closest(selector);
            if (typeof moyang != 'undefined') {
                return moyang;
            }
            if (typeof selector === 'string') {
                let matches = el.webkitMatchesSelector ? 'webkitMatchesSelector' : (el.msMatchesSelector ? 'msMatchesSelector' : 'matches'); //NOSONAR
                while (el.parentElement) {
                    if (el[matches](selector)) {
                        return el;
                    }
                    el = el.parentElement;
                }
            } else {
                while (el.parentElement) {
                    if (el === selector) {
                        return el;
                    }
                    el = el.parentElement;
                }
            }
            return null;
        };

        /**
         * Get final data
         * @returns {String}
         */
        this.getFinalResult = function () {
            return this.getFinalSubtitleData();
        }

        /**
         * 
         * @returns Get final data
         */
        this.getFinalSubtitleData = function () {
            return this.rawData;
        }

        /**
         * Get element with selector given
         * @param {String} selector
         * @returns {HTMLObjectElement} HTMLObjectElement
         */
        this.querySelector = function (selector) {
            return this.editor.querySelector(selector);
        };

        /**
         * Get elements with selector given
         * @param {String} selector
         * @returns {Array} HTMLObjectElement
         */
        this.querySelectorAll = function (selector) {
            return this.editor.querySelectorAll(selector);
        };

        /**
         * Get editor element
         * @returns {HTMLObjectElement} HTMLObjectElement
         */
        this.getEditor = function () {
            return this.editor;
        };

        /**
         * Convert float to hour, minute and second with padding
         * @param {float} d 
         * @param {float} fixed 
         * @param {float} padding 
         * @returns {String}
         */
        this.dToDmsReal = function (d, fixed, padding) {
            if (typeof fixed == 'undefined' || fixed == null) {
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
                    deg = '0' + deg;
                }
                if (min < 10) {
                    min = '0' + min;
                }
                if (sec < 10) {
                    sec = '0' + sec;
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
                s: sec
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
            let degrees = typeof (d) !== "undefined" ? parseFloat(d) : 0;
            let minutes = typeof (m) !== "undefined" ? parseFloat(m) / 60 : 0;
            let seconds = typeof (s) !== "undefined" ? parseFloat(s) / 3600 : 0;
            return degrees + minutes + seconds;
        };

        /**
         * Split hour, minute and second into array
         * @param {String} dmsStr
         * @returns {Array}
         */
        this.dmsToDdFromString = function (dmsStr) {
            let dms = dmsStr.split(':');
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
                d = '0' + d;
            }
            if (m < 10) {
                m = '0' + m;
            }
            if (s < 10) {
                s = '0' + s;
            }
            return d + ':' + m + ':' + s.replace('.', ',');
        };

        /**
         * Parse string to floating point
         * @param {String} value
         * @returns {float}
         */
        this.parseFloat = function (value) {
            if (value != null && typeof value == 'string') {
                if (value.length > 0) {
                    value = value.replace(/[^.\d]/g, '');
                    if (value == '') {
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
         * Sort data
         */
        this.sortData = function () {
            let sortedData = this.data.sort(function (p1, p2) {
                return _this.sortCallback(p1, p2);
            });
            this.data = JSON.parse(JSON.stringify(sortedData));
            this.rawData = this.renderDataToText(sortedData);
            this.placeRawData(this.rawData);
        };

        /**
         * Play audio
         * @param {float} second
         */
        this.play = function (seconds) {
            if (this.ended) {
                this.startPlayingTime = 0;
                this.audio.currentTime = 0;
            }
            if (this.audio.paused) {
                if (this.startPlayingTime > 0) {
                    seconds = this.startPlayingTime;
                }
                if (seconds && seconds > 0) {
                    this.audio.currentTime = seconds;
                }
                this.audio.play();
                this.updateAudioTimePosition();
            }
            this.ended = false;
            this.onPlay();
        };

        /**
         * Get text at specific second
         * @param {float} seconds
         * @returns {String} Text at specific second
         */
        this.getDataBySecond = function (seconds) {
            let ms = seconds * 1000;
            for (let i in this.data) {
                if (ms >= this.data[i].start && ms <= this.data[i].end) {
                    return { index: i, data: this.data[i] };
                }
            }
            return null;
        };



        /**
         * Get text an specifict time
         * @param {float} seconds 
         * @returns {String} Text
         */
        this.getText = function (seconds) {
            let srtData = this.getDataBySecond(seconds);
            if (srtData != null) {
                return srtData.data.text;
            }
            return '';
        }

        /**
         * Stop playing audio
         */
        this.stop = function () {
            this.pause();
            this.audio.currentTime = 0;
            this.setStartPlayingTime(0);
            this.onStop();
        };

        /**
         * Pause playing audio
         */
        this.pause = function (resetTimeline) {
            this.audio.pause();
            this.onPause();
            if(resetTimeline)
            {
                this.resetAllTimeline();
            }
        };

        /**
         * Detect if audio status is palying or not
         * @returns {Bool}
         */
        this.isPlaying = function () {
            return this.audio != null && !this.audio.paused;
        };

        /**
         * Set start playing time
         * @param {float} seconds 
         */
        this.setStartPlayingTime = function (seconds) {
            this.startPlayingTime = seconds;
        }

        this._onTextShow = function(index, text)
        {
            this.lastIndex = index;
            this.onTextShow(index, text);
        }

        this._onTextClear = function()
        {
            this.onTextClear();
        }

        this.onTextShow = function(index, text)
        {

        }

        this.onTextClear = function()
        {

        }

        /**
         * Update playing time
         */
        this.updateAudioTimePosition = function () {
            if (this.audio != null) {
                let index = -1;
                let seconds = this.audio.currentTime;
                this.setStartPlayingTime(seconds);
                let left = this.updatePlayingTime(seconds);
                if (this.scroll) {
                    let width = (this.querySelector('.srt-map').parentNode.offsetWidth / 2);
                    this.querySelector('.srt-map').scrollLeft = left - width;
                    let tooltipObj = this.querySelector('.srt-time-position-pointer');
                    if (tooltipObj) {
                        let tooltipId = tooltipObj.getAttribute('aria-describedby');
                        if (tooltipId) {
                            document.querySelector('#' + tooltipId + ' .tooltip-inner').innerText = _this.ddToDmsSrtFormat(seconds / 3600);
                        }
                    }
                }             
                
                let srtData = this.getDataBySecond(this.audio.currentTime);
                if (srtData) {
                    this.diplayedText = srtData.data.text;
                    index = srtData.index;                  
                }
                else {
                    this.diplayedText = '';
                }

                if (this.diplayedText != '' && this.diplayedTextLast != this.diplayedText) {
                    // text on
                    this.querySelector('.text-display-inner').innerHTML = this.diplayedText;
                    this.diplayedTextLast = this.diplayedText;                   
                    this._onTextShow(index, this.diplayedText);
                }
                if (this.diplayedText == '' && this.diplayedTextLast != this.diplayedText) {
                    // text off
                    this.querySelector('.text-display-inner').innerHTML = '';
                    this.diplayedTextLast = this.diplayedText;
                    this._onTextClear();
                }
            }
            if (!this.audio.paused) {
                window.requestAnimationFrame(() => this.updateAudioTimePosition());
            }
        };

        /**
         * Show text
         * @param {String} text 
         */
        this.showTextManual = function (index, text) {
            this.querySelector('.text-display-inner').innerHTML = text;
            this._onTextShow(index, text);
        }

        this.clearTextManual = function()
        {
            this.querySelector('.text-display-inner').innerHTML = '';
            this._onTextClear();
        }

        /**
         * Set total duration
         * @param {float} duration
         */
        this.setDuration = function (duration) {
            this.duration = duration;
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
                    text: text
                };
            }
            return {
                start: 0,
                end: 0,
                duration: 0,
                text: text
            };
        };

        /**
         * Append text to data
         * @param {Integer} index
         * @param {String} text
         */
        this.appendText = function (index, text) {
            if (this.data.length >= index - 1 && typeof this.data[index] != 'undefined') {
                let textTmp = this.data[index].text;
                let arr = [];
                if (textTmp != null) {
                    arr.push(textTmp);
                }
                if (text.length > 0) {
                    arr.push(text);
                }
                text = arr.join('\r\n');
                this.data[index].text = text;
            }
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
                }
                else {
                    if (j > -1) {
                        data[j].text = this.concat(data[j].text, tempData[i]);
                    }
                    nonempty++;
                }
            }
            if(typeof data[j].text == 'undefined')
            {
                data[j].text = '';
            }
            return { data: data, nonempty: nonempty, totalLength: tempData.length };
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
            return arr.join('\r\n');
        }

        /**
         * Sort data by it's start time
         * @param {Object} p1
         * @param {Object} p2
         * @returns {Number}
         */
        this.sortCallback = function (p1, p2) {
            if (p1.start > p2.start) {
                return 1;
            } else if (p1.start < p2.start) {
                return -1;
            } else {
                return 0;
            }
        };

        /**
         * Check if line is timestamp or not
         * @param {String} text
         * @returns {Bool}
         */
        this.isTimestamp = function (text) {
            return text.indexOf('-->') > -1;
        };

        /**
         * Parse timestamp
         * @param {String} text 
         * @returns 
         */
        this.parseTimestamp = function (text) {
            let arr = text.split('-->');
            return {
                start: arr[0].trim(),
                end: arr[1].trim()
            };
        };

        this.parseTimestampMills = function (text) {
            let ts = this.parseTimestamp(text);
            let start = this.convertTimestampToMills(ts.start);
            let end = this.convertTimestampToMills(ts.end);
            return {
                start: start,
                end: end
            };
        };

        this.convertTimestampToMills = function (text) {
            let secStr = '';
            let msStr = '0';
            if (text.indexOf(',') > -1) {
                let arr = text.split(',');
                secStr = arr[0];
                msStr = arr[1];
            } else if (text.indexOf('.') > -1) {
                let arr = text.split('.');
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

        this.createEditorContainer = function () {
            this.editorListElement.innerHTML = '';
            this.editorMapElement.innerHTML = '';
            let mapController = this.createDiv('div');
            mapController.className = 'srt-container';
            mapController.style.width = this.msToUnit(this.durationMinimum) + this.unit;
            this.editorMapElement.appendChild(mapController);
        };

        this.appendController = function (srtData, index, afterElement) {
            let controller = this.createController(srtData, index);
            this.movableElement(controller, index, function (param) {
                _this.sundulKiriMove(param);
            }, function (param) {
                _this.sundulKananMove(param);
            });

            if (afterElement) {
                afterElement.after(controller);
            } else {
                this.editorMapElement.querySelector('.srt-container').appendChild(controller);
            }
        };

        this.createController = function (srtData, index) {
            let objectWrapper = this.createDiv('div');
            objectWrapper.classList.add('srt-controller');
            objectWrapper.classList.add('srt-prevent-select');

            let objectInner = this.createDiv('div');
            objectInner.classList.add('srt-object-inner');

            let textContainer = this.createDiv('div');
            textContainer.classList.add('srt-text-container');
            textContainer.innerText = srtData.text;

            let resizer = this.createDiv('div');
            resizer.classList.add('srt-resizer');

            objectInner.appendChild(textContainer);
            objectInner.appendChild(resizer);
            objectWrapper.appendChild(objectInner);

            objectWrapper.style.left = this.msToUnit(srtData.start) + this.unit;
            objectWrapper.style.width = this.msToUnit(srtData.duration) + this.unit;
            objectWrapper.setAttribute('data-index', index);
            return objectWrapper;
        };

        this.movableElement = function (divOverlay, index, sundulKiriMove, sundulKananMove) {
            let objectInner = divOverlay.querySelector('.srt-object-inner');
            objectInner.classList.add('srt-movable');

            let mover = objectInner.querySelector('.srt-text-container');
            mover.addEventListener('dblclick', function (e) {
                let index = parseInt(_this.closest(e.target, '.srt-controller').getAttribute('data-index'));
                let elem = _this.getTimelineByIndex(index);
                _this.setActiveData(index);
                _this.lastIndex = index;
                elem.querySelector('textarea').focus();
            });
            mover.addEventListener('mousedown', function (e) {
                _this.isMouseDown = true;
                _this.startX = e.clientX;
                _this.indexToEdit = e.target.parentNode.parentNode.getAttribute('data-index');
                _this.elementToEdit = _this.getDataByIndex(_this.indexToEdit);
                _this.editMode = 'move';
                _this.currentDataOffsetLeft = _this.elementToEdit.offsetLeft - e.clientX;
                _this.currentDataTimeStart = _this.data[_this.indexToEdit].start;

                if (!_this.isPlaying()) {
                    let left = _this.elementToEdit.offsetLeft;
                    let seconds = _this.unitToMs(left) / 1000;
                    _this.ended = false;
                    _this.updatePointerPosition(left);
                    _this.setStartPlayingTime(seconds);
                }
                _this.resetZIndex();
                _this.elementToEdit.style.zIndex = "10"
                _this.elementToEdit.setAttribute('data-edit', 'true');
                _this.showTextManual(_this.indexToEdit, _this.data[_this.indexToEdit].text);
            }, true);


            _this.getEditor().addEventListener('mousemove', function (e) {
                if (_this.isMouseDown) {
                    if (_this.editMode == 'move') {
                        _this.elementToEdit.style.left = (e.clientX + _this.currentDataOffsetLeft) + 'px';
                    }
                    if (_this.editMode == 'resize') {
                        let startWidth = _this.startWidth;
                        let startX = _this.startX;
                        let clientX = e.clientX;
                        let newWidth = (startWidth + clientX - startX);
                        _this.elementToEdit.style.width = newWidth + 'px';
                    }
                }
            }, true);

            document.addEventListener('mouseup', function (e) {
                if (_this.isMouseDown) {
                    if (_this.editMode == 'move' && !_this.isPlaying()) {
                        let left = _this.elementToEdit.offsetLeft;
                        let seconds = _this.unitToMs(left) / 1000;
                        _this.updatePointerPosition(left);
                        _this.setStartPlayingTime(seconds);
                    }
                    _this.editMode = '';

                    if (typeof sundulKiriMove == 'function') {
                        setTimeout(function () {
                            sundulKiriMove(_this.indexToEdit);
                        }, 100)

                    }
                    if (typeof sundulKananMove == 'function') {
                        setTimeout(function () {
                            sundulKananMove(_this.indexToEdit);
                        }, 100)

                    }
                    _this.updateData();
                }
                _this.isMouseDown = false;
            }, true);

            let resizer = objectInner.querySelector('.srt-resizer');
            resizer.addEventListener('mousedown', function (e) {
                _this.indexToEdit = e.target.parentNode.parentNode.getAttribute('data-index');
                _this.elementToEdit = _this.getDataByIndex(_this.indexToEdit);
                _this.startWidth = _this.parseFloat(_this.elementToEdit.style.width);
                _this.startX = e.clientX;
                _this.editMode = 'resize';
                _this.isMouseDown = true;
            }, true);
        };

        this.resetZIndex = function () {
            let elems = this.querySelectorAll('.srt-controller');
            let max = elems.length;
            for (let i = 0; i < max; i++) {
                elems[i].style.zIndex = '';
                elems[i].removeAttribute('data-edit');
            }
        }

        this.getDataByIndex = function (index) {
            return this.getControllerByIndex(index);
        };
        

        this.sundulKiriMove = function (index) {
            if (index >= 0 && this.data.length > 0) {
                if (index == 0) {
                    // paling kiri
                    if (this.data[index].start < 0) {
                        let seconds = 0;
                        this.data[index].start = seconds;
                        this.ended = false;
                        this.updatePlayingTime(seconds);
                        this.setStartPlayingTime(seconds);
                        this.updateController(index);
                    }
                }
                else {
                    let prevIndex = parseInt(index) - 1;
                    if (this.data[index].start < this.data[prevIndex].start) {
                        if (this.data[prevIndex].duration > this.durationMin) {
                            this.data[prevIndex].duration = this.durationMin;
                        }
                        this.data[prevIndex].end = this.data[prevIndex].start + this.data[prevIndex].duration;
                        this.data[index].start = this.data[prevIndex].end;
                        this.data[index].end = this.data[index].start + this.data[index].duration;
                    }
                    else if (this.data[index].start < this.data[prevIndex].end) {
                        this.data[prevIndex].end = this.data[index].start;
                        this.data[prevIndex].duration = this.data[prevIndex].end - this.data[prevIndex].start;
                        this.data[index].end = this.data[index].start + this.data[index].duration;
                    }
                    let seconds = this.data[index].start / 1000;
                    this.ended = false;
                    this.updatePlayingTime(seconds);
                    this.setStartPlayingTime(seconds);
                    this.updateController(prevIndex);
                    this.updateController(index);
                }
            }
        };

        this.sundulKananMove = function (index) {
            if (this.data.length > 1 && index < this.data.length - 1) {
                let nextIndex = parseInt(index) + 1;
                if (this.data[index].start + this.data[index].duration > this.data[nextIndex].start + this.data[nextIndex].duration) {
                    // ga bisa resize apapun
                    this.data[index].start = _this.currentDataTimeStart;
                    this.updateController(index);
                }
                else if (this.data[index].start + this.data[index].duration > this.data[nextIndex].start) {
                    // kena sundul
                    if (this.data[index].start < this.data[nextIndex].start) {
                        // masih bisa resize current data
                        this.data[index].duration = this.data[nextIndex].start - this.data[index].start;
                        this.updateController(index);
                    }
                    else if (this.data[index].start < this.data[nextIndex].end) {
                        // resize next data
                        this.data[nextIndex].start = this.data[index].start + this.data[index].duration;
                        this.data[nextIndex].duration = this.data[nextIndex].end - this.data[nextIndex].start;
                        this.updateController(nextIndex);
                    }
                    let seconds = this.data[index].start / 1000;
                    this.ended = false;
                    this.updatePlayingTime(seconds);
                    this.setStartPlayingTime(seconds);
                }
            }
        };

        this.updateData = function () {
            let newSrt = [];
            let elems = this.querySelectorAll('.srt-controller');
            for (let i in elems) {
                let elem = elems[i];
                if (typeof elem.className != 'undefined' && elem.classList.contains('srt-controller')) {
                    let text = elem.querySelector('.srt-object-inner .srt-text-container').innerText.trim();
                    let timestamp = this.getTimestampFromController(elem.offsetLeft, elem.style.width);
                    let start = timestamp.start;
                    let end = timestamp.end;
                    let duration = timestamp.duration;
                    newSrt.push({
                        start: start,
                        end: end,
                        duration: duration,
                        text: text
                    });
                }
            }
            this.data = newSrt;
            this.updateRawData();
        };

        this.updateRawData = function () {
            let srtText = this.renderDataToText(this.data);
            this.placeRawData(srtText);
            this.rawData = srtText;
        };

        this.renderDataToText = function (srtArray) {
            srtArray = srtArray || this.data;
            let newText = [];
            for (let i in srtArray) {
                let srtObject = srtArray[i];
                let ts = this.createTimestamp(srtObject.start, srtObject.end);
                newText.push(ts);
                newText.push(srtObject.text);
                newText.push('');
            }
            return newText.join('\r\n');
        };

        this.createTimestamp = function (start, end) {
            let startStr = this.ddToDmsSrtFormat(start / 3600000);
            let endStr = this.ddToDmsSrtFormat(end / 3600000);
            let timestamp = startStr + ' --> ' + endStr;
            return timestamp;
        };

        this.getTimestampFromController = function (start, duration) {
            let startMs = start / (this.scale * this.zoom);
            let durationMs = this.parseFloat(duration) / (this.scale * this.zoom);
            let endMs = startMs + durationMs;
            return {
                start: startMs,
                end: endMs,
                duration: durationMs
            };
        };

        this.renderData = function () {
            this.data.forEach((srtData, index) => {
                this.appendController(srtData, index);
             });
        };

        this.createDiv = function () {
            return document.createElement('div');
        };

        

        this._onEnded = function (e) {
            this.resetAllTimeline();
            this.ended = true;
            this.onEnded();
        };

        this.onPlay = function () {
        };

        this.onPause = function () {
        };

        this.onStop = function () {
        };

        this.onEnded = function () {
        };

        this.onDeleteData = function (index, countData) {
            if (countData > 1) {
                this.deleteData(index);
            }
        };
        this.onDrawWaveformStart = function () {
        };
        this.onDrawWaveformFinish = function () {
        };
        this.onDrawWaveformError = function () {
        };
        this.onUpdateText = function (index, text) {
        };

        this.resetAllTimeline = function () {
            let elems = this.querySelectorAll('.srt-list-item .button-play');
            let max = elems.length;
            for (let i = 0; i < max; i++) {
                elems[i].setAttribute('data-play', 'false');
                elems[i].innerHTML = this.buttonContentPlay;
            }
        };

        this._onTogglePlayPause = function (e) {
            let targetElem = e.target;
            if (!targetElem.classList.contains('button-play')) {
                targetElem = _this.closest(targetElem, '.button-play');
            }

            let status = targetElem.getAttribute('data-play') || 'false';
            if (status == 'false') {
                // sel all siblings
                this.resetAllTimeline();
                targetElem.innerHTML = this.buttonContentPause;
                targetElem.setAttribute('data-play', 'true');
                let elem = _this.closest(targetElem, '.srt-list-item');
                let start = elem.querySelector('.srt-time-edit-start').value;
                let startSeconds = this.convertTimestampToMills(start) / 1000;
                this.ended = false;
                this.setStartPlayingTime(startSeconds);
                this.audio.currentTime = startSeconds;
                this.play(startSeconds);
            } else {
                targetElem.setAttribute('data-play', 'false');
                targetElem.innerHTML = this.buttonContentPlay;
                this.pause();
            }
        };

        this._onDeleteData = function (e) {
            let targetElem = e.target;
            let elem = _this.closest(targetElem, '.srt-list-item');
            let index = parseInt(elem.getAttribute('data-index'));
            let countData = elem.parentNode.childNodes.length;
            this.onDeleteData(index, countData);
        };

        this.deleteData = function (index) {
            let elemTimeline = this.getTimelineByIndex(index);
            let elemDrag = this.getControllerByIndex(index);
            if(elemDrag)
            {
                elemDrag.parentNode.removeChild(elemDrag);
                elemTimeline.parentNode.removeChild(elemTimeline);

                // update index on UI
                let elems = _this.querySelector('.srt-list-container').childNodes;
                let nodeListControllerCount = elems.length;
                let i;
                for (i = 0; i < nodeListControllerCount; i++) {
                    elems[i].setAttribute('data-index', i);
                }
                let elemTimelineNew = _this.querySelector('.srt-container').childNodes;
                let nodeListTimelineCount = elems.length;
                for (i = 0; i < nodeListTimelineCount; i++) {
                    elemTimelineNew[i].setAttribute('data-index', i);
                }
                let newData = [];
                for (i = 0; i < index; i++) {
                    newData.push(JSON.parse(JSON.stringify(this.data[i])));
                }
                for (i = index + 1; i < this.data.length; i++) {
                    newData.push(JSON.parse(JSON.stringify(this.data[i])));
                }
                this.data = JSON.parse(JSON.stringify(newData));
                this.updateRawData();
            }
        }

        this._onUpdateText = function (e) {
            let targetElem = e.target;
            let elem = _this.closest(targetElem, '.srt-list-item');
            let index = parseInt(elem.getAttribute('data-index'));
            let text = targetElem.value;
            this.updateTextController(index, text);
            if (typeof this.data[index] != 'undefined') {
                this.data[index].text = text;
            }
            this.onUpdateText(index, text);
        };

        this.updateTextController = function (index, text) {
            // find controller
            _this.querySelector('.srt-controller[data-index="' + index + '"] .srt-text-container').innerText = text;
        };

        this.getDuration = function (start, end) {
            let startMs = this.convertTimestampToMills(start);
            let endMs = this.convertTimestampToMills(end);
            return endMs - startMs;
        };

        this.getCursorPos = function (el) {
            let pos = 0;
            if ('selectionStart' in el) {
                pos = el.selectionStart;
            } else if ('selection' in document) {
                el.focus();
                let Sel = document.selection.createRange();
                let SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            return pos;
        };

        this.placeRawData = function (text) {
            this.querySelector('.srt-text-raw').value = text;
        };

        this.renderConstroller = function () {
            this.placeRawData(this.rawData);
            this.createEditorContainer();
            this.renderData();
        };

        this.getChunkSize = function () {
            return Math.round(this.sampleRate / (1000 * this.resolution * this.scale));
        };

        this.getControllerByIndex = function (index) {
            return this.querySelector('.srt-controller[data-index="' + index + '"]');
        };

        this.getTimelineByIndex = function(index)
        {
            return this.querySelector('.srt-list-item[data-index="' + index + '"]');
        }

        /**
         * Draw audio waveform
         * @param {String} path
         * @param {Integer} resolution
         */
        this.drawAudioWaveform = function (path, resolution) {
            resolution = resolution || 1;
            this.resolution = resolution;
            _this.onDrawWaveformStart();
            let chunkSize = this.getChunkSize() * resolution;
            let audioContext = new AudioContext({
                sampleRate: this.sampleRate
            });
            let ajaxRequest = new XMLHttpRequest();
            ajaxRequest.open("GET", path, true);
            ajaxRequest.responseType = "arraybuffer";

            ajaxRequest.onload = () => {
                audioContext.decodeAudioData(ajaxRequest.response).then((decodedData) => {
                    let float32Array = decodedData.getChannelData(0);
                    let array = [];
                    let i = 0;
                    let j = 0;
                    let length = float32Array.length;

                    while (i < length) {
                        j = i;
                        i += chunkSize;
                        array.push(
                            float32Array.slice(j, i).reduce(function (total, value) {
                                return Math.max(total, Math.abs(value));
                            })
                        );
                    }

                    _this.waveformArray = array;
                    _this.renderWaveform();
                }).catch((err) => {
                    // handle exception here
                    _this.onDrawWaveformError();
                });
            };
            ajaxRequest.send();
        };


        /**
         * Create timeline
         */
        this.drawTimeline = function () {
            let inc1 = this.zoom > 0.25 ? 0.5 : 1;
            let inc2 = this.zoom > 0.25 ? 5 : 10;
            let canvas = this.querySelector('.srt-timeline-canvas');
            let width = Math.round(this.msToUnit(this.duration * 1000));
            let ctx = canvas.getContext('2d');
            canvas.setAttribute('width', width);
            canvas.setAttribute('height', 40);
            this.querySelector('.srt-timestamp').style.width = width + this.unit;
            this.querySelector('.srt-edit-area').style.width = width + this.unit;
            ctx.textAlign = "center";
            ctx.font = "12px Arial";
            ctx.fillStyle = "#555555";
            ctx.strokeStyle = '#555555';
            ctx.beginPath();
            ctx.lineWidth = 1;
            let j = -1;
            let y1 = 10;
            let y2 = 14;
            let y3 = 26;
            for (let i = 0; i < this.duration; i += inc1) {
                let x = Math.round(this.msToUnit(i * 1000));
                ctx.moveTo(x, 0);
                if (i > j + inc1) {
                    let text = this.dToDmsReal(i / 3600, 0, true).join(':');
                    try {
                        ctx.fillText(text, x, y3);
                    }
                    catch (ex) {

                    }
                    j += inc2;
                    ctx.lineTo(x, y2);
                }
                else {
                    ctx.lineTo(x, y1);
                }
            }
            ctx.stroke();
        };

        this.renderWaveform = function () {

            let array = _this.waveformArray;
            let canvas = this.querySelector(".srt-timeline-canvas-edit");
            let margin = 0;
            let height = canvas.height;
            let centerHeight = Math.ceil(height / 2);
            let scaleFactor = (height - margin * 2) / 2;
            canvas.width = array.length * this.zoom;
            let ctx = canvas.getContext("2d");
            ctx.beginPath();
            ctx.strokeWith = Math.ceil(this.zoom);
            ctx.strokeStyle = 'rgb(88, 175, 215)';

            for (let i = 0; i < array.length; i++) {
                let x = Math.round(margin + (Number(i) * this.zoom));
                ctx.moveTo(x, centerHeight - array[i] * scaleFactor);
                ctx.lineTo(x, centerHeight + array[i] * scaleFactor);
            }
            ctx.stroke();
            _this.onDrawWaveformFinish();
        };

        this.updateZoom = function (zoom) {
            this.zoom = zoom;
            this.updateControllerScale();
        };

        this.updateControllerScale = function () {
            this.renderWaveform();
            for (let i in this.data) {
                this.updateController(i);
            }
            _this.drawTimeline();
            this.updatePlayingTime(this.startPlayingTime);
        };

        this.updateController = function (index) {
            let elem = this.getControllerByIndex(index);
            elem.style.left = this.msToUnit(this.data[index].start) + this.unit;
            elem.style.width = this.msToUnit(this.data[index].duration) + this.unit;
        }

        this.setAudioTimePosition = function (position) {
            let seconds = this.unitToMs(position) / 1000;
            this.audio.currentTime = seconds;
            this.setStartPlayingTime(seconds);
        };

        this.updatePointerPosition = function (left) {
            // trim container
            left = left - 3;
            this.querySelector('.srt-time-position-pointer').style.left = left + this.unit;
        };


        this.updatePlayingTime = function (seconds) {
            let left = this.msToUnit(seconds * 1000);
            this.updatePointerPosition(left);
            return left;
        };

        /**
         * Gets element's x position relative to the visible viewport.
         * @param Element el
         */
        this.getAbsoluteOffsetLeft = function (el) {
            let offset = 0;
            let currentElement = el;
            while (currentElement !== null) {
                offset += currentElement.offsetLeft;
                offset -= currentElement.scrollLeft;
                currentElement = currentElement.offsetParent;
            }
            return offset;
        };

        /**
         * Gets element's y position relative to the visible viewport.
         * @param Element el
         */
        this.getAbsoluteOffsetTop = function (el) {
            let offset = 0;
            let currentElement = el;
            while (currentElement !== null) {
                offset += currentElement.offsetTop;
                offset -= currentElement.scrollTop;
                currentElement = currentElement.offsetParent;
            }
            return offset;
        };

        this.onMouseWheel = function (e) {
            if (e.ctrlKey == true) {
                e.preventDefault();
                if (e.deltaY > 0) {
                    this.zoomLevelIndex--;
                    if (this.zoomLevelIndex < 0) {
                        this.zoomLevelIndex = 0;
                    }
                }
                else {
                    this.zoomLevelIndex++;
                    if (this.zoomLevelIndex > this.zoomLevel.length - 1) {
                        this.zoomLevelIndex = this.zoomLevel.length - 1;
                    }
                }
                this.querySelector('.srt-zoom-control').value = this.zoomLevelIndex;
            }
            let zoom = this.zoomLevel[this.zoomLevelIndex];
            this.updateZoom(zoom);
        };

        this.distributedParsing = function (rawData) {
            let data = [];
            rawData = rawData.trim().replace(/\r?\n/g, "\r\n");
            let tempData = rawData.split(/\n/g);
            let count = tempData.length;
            let scale = this.duration * 1000 / count;
            let duration = scale * 0.95;
            let milliseconds = 0;
            let text = "";
            for (let i = 0; i < tempData.length; i++) {
                milliseconds = scale * i;
                text = tempData[i].trim();
                if (text.length > 0) {
                    let srtData = { start: milliseconds, duration: duration, end: milliseconds + scale, text: text };
                    data.push(srtData);
                }
            }
            return data;
        };

        this.renderAll = function () {
            if (this.data.length > 0) {
                this.sortData();
                this.durationMinimum = this.data[this.data.length - 1].end / 1000;
                if (this.duration < this.durationMinimum) {
                    this.duration = this.durationMinimum;
                }
            }
            this.renderConstroller();
        };

        this.toggleScroll = function()
        {
            _this.scroll = !_this.scroll;
        };

        this.initData = function(rawData, path)
        {
            this.rawData = rawData;
            this.path = path;
            this.data = this.parseRawData(this.rawData).data;
            this.renderAll();
        };

        this.resetZoom = function()
        {
            _this.zoomLevelIndex = _this.zoomLevelIndexOriginal;
            _this.querySelector('.srt-zoom-control').value = _this.zoomLevelIndex;
            let zoom = _this.zoomLevel[_this.zoomLevelIndex];
            _this.updateZoom(zoom);
        };

        this.initEditor = function () {
            this.audio = new Audio(this.path);
            this.duration = this.audio.duration;
            this.editor = document.querySelector(this.editorCtrl);
            this.querySelector('.srt-zoom-control').value = this.zoomLevelIndexOriginal;
            this.querySelector('.srt-zoom-control').addEventListener('change', function(e){
                _this.zoomLevelIndex = e.target.value;
                if (_this.zoomLevelIndex < 0) {
                    _this.zoomLevelIndex = 0;
                }        
                if (_this.zoomLevelIndex > _this.zoomLevel.length - 1) {
                    _this.zoomLevelIndex = _this.zoomLevel.length - 1;
                }
                let zoom = _this.zoomLevel[_this.zoomLevelIndex];
                _this.updateZoom(zoom);
            });
            this.audio.addEventListener('loadedmetadata', function (e) {
                _this.duration = _this.audio.duration;
                _this.ready = true;
                _this.drawTimeline();
            });
            this.audio.onended = function (e) {
                _this._onEnded(e);
            };
            this.audio.load();
            this.editorListElement = this.querySelector('.srt-list-container');
            this.editorMapElement = this.querySelector('.srt-map-srt-container');

            this.querySelector('.srt-timeline-canvas').addEventListener('click', function (e) {
                let offset = _this.getAbsoluteOffsetLeft(e.target);
                let pos = e.clientX + _this.querySelector('.srt-map').scrollLeft - offset;
                let seconds = _this.unitToMs(pos) / 1000;
                _this.updatePointerPosition(pos);
                let srtData = _this.getDataBySecond(seconds);
                if (srtData != null) {                  
                    _this.showTextManual(srtData.index, srtData.data.text);
                }
                else
                {
                    _this.clearTextManual();
                }
                _this.ended = false;
                _this.setStartPlayingTime(seconds);
                _this.audio.currentTime = seconds;
            });
            
            // Ctrl + wheel to change zoom level
            this.querySelector('.srt-map').addEventListener('wheel', function (e) {
                _this.onMouseWheel(e);
            });

            this.querySelector('.srt-text-raw').addEventListener('paste', function (e) {
                e.preventDefault();
                e.stopPropagation();
                let rawData = e.clipboardData.getData("text");
                let parsedData = _this.parseRawData(rawData);
                if (parsedData.data.length == 0 && parsedData.nonempty > 1 && _this.data.length < 2) {
                    // gagal parsing
                    let parsedData2 = _this.distributedParsing(rawData);
                    _this.data = parsedData2;
                    _this.renderAll();
                }
            });

            // Ctrl + 0 to zoom level = 1
            document.body.addEventListener('keydown', function (e) {
                if (e.ctrlKey && window.event.keyCode == 48) {
                    _this.resetZoom();
                }
            });

            let tooltipElement = _this.querySelector('[data-toggle="tooltip"]');
            var tooltip = new bootstrap.Tooltip(tooltipElement, { // NOSONAR
                boundary: document.body
            });

            tooltipElement.addEventListener('mouseover', function (e) {
                let el = e.target;
                let left = el.offsetLeft + 3;
                let hour = _this.unitToMs(left) / 3600000;
                let text = _this.ddToDmsSrtFormat(hour);
                el.setAttribute('data-bs-original-title', text);
            });     
        };

        let _this = this;
        _this.initEditor();
        _this.drawAudioWaveform(path, 1);
        _this.initData(rawData, path);
    }
}
