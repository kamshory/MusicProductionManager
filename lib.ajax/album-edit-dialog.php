<?php

use MusicProductionManager\Data\Entity\Album;

require_once dirname(__DIR__)."/inc/auth.php";

$album = new Album(array('album_id'=>trim(@$_GET['album_id'])), $database);
$album->select();
?>
<form action="">
    <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="updateAlbumDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateAlbumDialogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateAlbumDialogLabel">Edit Album</h5>
                    <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-responsive table-responsive-two-side table-borderless ">
                        <tbody>
                            <tr>
                            <td>Album Name</td>
                                <td><input type="text" class="form-control" name="name" value="<?php echo $album->getName();?>"></td>
                            </tr>
                            <tr>
                                <td>Album Title</td>
                                <td><input type="text" class="form-control" name="title" value="<?php echo $album->getTitle();?>"></td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td><textarea class="form-control" name="description" id="description" rows="3"><?php echo $album->getDescription();?></textarea></td>
                            </tr>
                            <tr>
                            <td>Producer</td>
                                <td>
                                    <select class="form-control" name="producer_id" data-value="<?php echo $album->getProducerId();?>" data-ajax="true" data-source="lib.ajax/producer-list.php">
                                        <option value="">- select -</option>
                                    </select>
                                    <button class="button-add-list button-add-general-producer" data-content="plus-sign">+</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Release Date</td>
                                <td><input type="date" class="form-control" name="release_date" value="<?php echo $album->getReleaseDate();?>"></td>
                            </tr>
                            <tr>
                                <td>Draft</td>
                                <td><label></label><input type="checkbox" name="as_draft" value="1" <?php echo $song->createCheckedAsDraft("1");?>> Draft</label></td>
                            </tr>
                            <tr>
                                <td>Active</td>
                                <td><label></label><input type="checkbox" name="active" value="1" <?php echo $song->createCheckedActive("1");?>> Active</label></td>
                            </tr>
                        </tbody>
                        <input type="hidden" name="album_id" value="<?php echo $album->getAlbumId();?>">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save-edit-album">OK</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</form>