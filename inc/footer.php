</div>
<div class="px-6 text-center">
</div>


<style>
    body {
    margin: 24px;
    }


    .upload-drop-zone-add {
    color: #0f3c4b;
    background-color: var(--colorPrimaryPale, #c8dadf);
    outline: 2px dashed var(--colorPrimaryHalf, #c1ddef);
    outline-offset: -12px;
    transition:
        outline-offset 0.2s ease-out,
        outline-color 0.3s ease-in-out,
        background-color 0.2s ease-out;
    }
    .upload-drop-zone-add.highlight {
    outline-offset: -4px;
    outline-color: var(--colorPrimaryNormal, #0576bd);
    background-color: var(--colorPrimaryEighth, #c8dadf);
    }
    .upload_svg {
    fill: var(--colorPrimaryNormal, #0576bd);
    }
    
    .upload_img {
    width: calc(33.333% - (2rem / 3));
    object-fit: contain;
    }
</style>
<script src="lib/upload-song.js">
    

</script>
<!-- Modal -->





<div class="lazy-dom-container">
    <div class="lazy-dom song-upload-dialog" data-url="lib.ajax/song-upload-dialog.php"></div>
    <div class="lazy-dom album-add-general-dialog" data-url="lib.ajax/album-add-general-dialog.php"></div>
    <div class="lazy-dom genre-add-general-dialog" data-url="lib.ajax/genre-add-general-dialog.php"></div>
    <div class="lazy-dom artist-add-general-dialog" data-url="lib.ajax/artist-add-general-dialog.php"></div>
</div>
</div>
</div>

<div class="file-uploader">
  <input id="song_file_uploader" data-post-name="image_background" class="position-absolute invisible" type="file" accept="audio/mp3,audio/midi,application/xml,application/vnd.recordare.musicxml+xml,audio/musicxml,application/pdf,*/musicxml" multiple />
</div>


</body>
</html>