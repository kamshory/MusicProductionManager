<?php

use MusicProductionManager\Util\PicoHttpCache;

require_once dirname(__DIR__)."/inc/auth.php";
PicoHttpCache::cacheLifetime(3600*12);
?>
<form action="">
    <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="addAlbumGeneralDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addAlbumGeneralDialogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAlbumGeneralDialogLabel">Add Album</h5>
                    <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-responsive table-responsive-two-side table-borderless ">
                        <tbody>
                        <tr>
                        <td>Album Name</td>
                        <td><input type="text" class="form-control" name="name"></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><textarea class="form-control" name="description" id="description" rows="3"></textarea></td>
                        </tr>
                        <tr>
                            <td>Producer</td>
                            <td>
                                <select class="form-control" name="producer_id" data-value="" data-ajax="true" data-source="lib.ajax/producer-list.php">
                                    <option value="">- select -</option>
                                </select>
                                <button class="button-add-list button-add-general-producer" data-content="plus-sign">+</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Release Date</td>
                                <td><input type="date" class="form-control" name="release_date"></td>
                            </tr>
                        <tr>
                            <td>Sort Order</td>
                            <td><input type="number" class="form-control" name="sort_order"></td>
                        </tr>
                        <tr>
                            <td>Active</td>
                            <td><label></label><input type="checkbox" name="active" value="1"> Active</label></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save-add-general-album">OK</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</form>