<?php

use MusicProductionManager\Util\PicoHttpCache;

require_once dirname(__DIR__) . "/inc/auth.php";

PicoHttpCache::cacheLifetime(3600 * 12);

?>
<div class="modal fade" id="uploadFile" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uploadFileLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFileLabel">Add New Song</h5>
                <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body song-dialog">

                <div class="file-uploader">
                    <fieldset class="file-upload-zone upload-drop-zone-add text-center mb-3 p-4">
                        <legend class="visually-hidden">Song Uploader</legend>
                        <svg class="upload_svg" width="60" height="60" aria-hidden="true">
                            <use href="#icon-imageUpload"></use>
                        </svg>
                        <p class="small my-2">Drag &amp; drop song into this region<br><i>or</i></p>
                        <input id="upload_image_background" data-post-name="image_background" class="position-absolute invisible" type="file" accept="audio/mp3,audio/midi,application/xml,application/pdf" />
                        <label class="btn btn-primary mb-3" for="upload_image_background">Choose File</label>
                        <div class="upload_gallery d-flex flex-wrap justify-content-center gap-3 mb-0"></div>
                    </fieldset>

                    <fieldset class="song-info">
                        <legend class="visually-hidden">Song Information</legend>
                        <form>

                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td>Title</td>
                                        <td>
                                            <input type="text" class="form-control" name="title">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Album</td>
                                        <td>
                                            <select class="form-control" name="album_id" data-ajax="true" data-source="lib.ajax/album-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-album">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Track Number</td>
                                        <td>
                                            <input type="number" class="form-control" name="track_number">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Genre</td>
                                        <td>
                                            <select class="form-control" name="genre_id" data-ajax="true" data-source="lib.ajax/genre-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-genre">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Vocal</td>
                                        <td>
                                            <select class="form-control" name="artist_vocal" data-ajax="true" data-source="lib.ajax/artist-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-artist">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Composer</td>
                                        <td>
                                            <select class="form-control" name="artist_composer" data-ajax="true" data-source="lib.ajax/artist-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-artist">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Arranger</td>
                                        <td>
                                            <select class="form-control" name="artist_arranger" data-ajax="true" data-source="lib.ajax/artist-list.php">
                                                <option value="">- select -</option>
                                            </select>
                                            <button class="button-add-list button-add-general-artist">+</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Vocal</td>
                                        <td><label></label><input type="checkbox" name="vocal" value="1"> Vocal</label>
                                        </td>
                                    </tr>
                                    <input type="hidden" name="random_song_id" value="">
                                </tbody>
                            </table>
                            <div class="progress-upload">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                            <div class="loader-icon">&nbsp;</div>
                        </form>
                    </fieldset>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success save-add-general-song">OK</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
