<?php

use MusicProductionManager\Util\PicoHttpCache;

require_once dirname(__DIR__)."/inc/auth.php";
PicoHttpCache::cacheLifetime(3600*12);
?>
<form action="">
    <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="addGenreDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addGenreDialogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGenreDialogLabel">Add Genre</h5>
                    <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-responsive table-responsive-two-side table-borderless ">
                        <tbody>
                            <tr>
                                <td>Genre</td>
                                <td><input type="text" class="form-control" name="name"></td>
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
                        <input type="hidden" name="genre_id">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save-genre">OK</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</form>