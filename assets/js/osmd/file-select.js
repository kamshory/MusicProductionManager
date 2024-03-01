let pb = null;

function handleFileSelect(evt) {
  document.querySelector("#fileloader").style.display = "none";
  document.querySelector("#container").style.display = "block";
  let files = evt.target.files; // FileList object

  let maxOSMDDisplays = 10; // how many scores can be displayed at once (in a vertical layout)
  let osmdDisplays = Math.min(files.length, maxOSMDDisplays);

  for (let i = 0, file = files[i]; i < osmdDisplays; i++) {
    let reader = new FileReader();

    reader.onload = function (e) {
      let osmd = new opensheetmusicdisplay.OpenSheetMusicDisplay("osmdCanvas", {
        // set options here
        zoom: 0.5,
        drawFromMeasureNumber: 1,
        drawUpToMeasureNumber: Number.MAX_SAFE_INTEGER, // draw all measures, up to the end of the sample
      });
      osmd.zoom = 0.5;

      osmd.load(e.target.result).then(function () {
        window.osmd = osmd; // give access to osmd object in Browser console, e.g. for osmd.setOptions()
        //console.log("e.target.result: " + e.target.result);
        osmd.render();
        pb = new PlaybackEngine();
        pb.loadScore(osmd);
        pb.setBpm(osmd.sheet.DefaultStartTempoInBpm);

        osmd.cursor.next();

        osmd.cursor.show(); // this would show the cursor on the first note

        for (
          let idx = 0;
          idx <
          osmd.cursor.iterator.currentMeasure.verticalMeasureList.length - 1;
          idx++
        ) {
          let top =
            osmd.cursor.iterator.currentMeasure.verticalMeasureList[idx].stave
              .y - osmd.cursor.cursorElement.offsetTop;
          document.querySelector(".box-" + idx).style.top = top + "px";
        }
        osmd.cursor.reset();
        pb.scroll();
      });
    };
    if (file.name.match(".*.mxl") || file.name.match(".*.xml")) {
      reader.readAsBinaryString(file);
    } else {
      reader.readAsText(file);
    }
  }
}
