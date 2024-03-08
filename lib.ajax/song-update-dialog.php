<?php

use MagicObject\Request\InputGet;
use MusicProductionManager\Data\Entity\Song;


require_once dirname(__DIR__) . "/inc/auth.php";

$inputGet = new InputGet();
if($inputGet->getSongId() != null)
{
$song = new Song(null, $database);
try
{
$song->findOneBySongId($inputGet->getSongId());
?>
<form action="">
<div class="modal fade" id="updateSongDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateSongDialogLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateSongDialogLabel">Update Song</h5>
                <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body song-dialog" style="position:relative">

                <div class="file-uploader">
                    <fieldset class="file-upload-zone upload-drop-zone-update text-center mb-3 p-4">
                        <legend class="visually-hidden">Song Uploader</legend>
                        <svg class="upload_svg" width="60" height="60" aria-hidden="true">
                            <use href="#icon-imageUpload"></use>
                        </svg>
                        <p class="small my-2">Drag &amp; drop song into this region<br><i>or</i></p>
                        <input id="upload_image_background" data-post-name="image_background" class="position-absolute invisible" type="file" accept="audio/mp3,audio/midi,application/xml,application/vnd.recordare.musicxml+xml,audio/musicxml,application/pdf,*/musicxml" multiple />
                        <label class="btn btn-primary mb-3" for="upload_image_background">Choose File</label>
                        <div class="upload_gallery d-flex flex-wrap justify-content-center gap-3 mb-0"></div>
                    </fieldset>

                    <fieldset class="song-info">
                        <legend class="visually-hidden">Song Information</legend>
                        <form>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>
                                            <input type="text" class="form-control" name="name" value="<?php echo $song->getName();?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Title</td>
                                        <td>
                                            <input type="text" class="form-control" name="title" value="<?php echo $song->getTitle();?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Album</td>
                                        <td>
                                            <select class="form-control" name="album_id" data-value="<?php echo $song->getAlbumId();?>" data-ajax="true" data-source="lib.ajax/album-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-album">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Track Number</td>
                                        <td>
                                            <input type="number" class="form-control" name="track_number" value="<?php echo $song->getTrackNumber();?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Genre</td>
                                        <td>
                                            <select class="form-control" name="genre_id" data-value="<?php echo $song->getGenreId();?>" data-ajax="true" data-source="lib.ajax/genre-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-genre">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Vocal</td>
                                        <td>
                                            <select class="form-control" name="artist_vocalist" data-value="<?php echo $song->getArtistVocalist();?>" data-ajax="true" data-source="lib.ajax/artist-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-artist">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Composer</td>
                                        <td>
                                            <select class="form-control" name="artist_composer" data-value="<?php echo $song->getArtistComposer();?>" data-ajax="true" data-source="lib.ajax/artist-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-artist">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Arranger</td>
                                        <td>
                                            <select class="form-control" name="artist_arranger" data-value="<?php echo $song->getArtistComposer();?>" data-ajax="true" data-source="lib.ajax/artist-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-artist">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Vocal</td>
                                        <td><label></label><input type="checkbox" name="vocal" value="1" <?php echo $song->createCheckedVocal("1");?>> Vocal</label>
                                        </td>
                                    </tr>
                                    </tr>
                                    <tr>
                                        <td>Active</td>
                                        <td><label></label><input type="checkbox" name="active" value="1" <?php echo $song->createCheckedActive("1");?>> Active</label>
                                        <input type="hidden" name="song_id" value="<?php echo $song->getSongId();?>">
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                            <div class="progress-bar-container progress-bar-container-update">
                                
                            </div>
                            <div class="loader-icon">&nbsp;</div>
                        </form>
                    </fieldset>
                    
                </div>
                <audio style="width: 100%; height: 40px;" src="<?php echo $cfg->getSongBaseUrl()."/".$song->getFileName();?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime());?>" controls></audio>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success save-update-song">OK</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
</form>
<?php
}
catch(Exception $e)
{
    // do nothing
}
}
?>