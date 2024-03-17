<?php

use MagicObject\Request\InputGet;
use MusicProductionManager\Data\Entity\SongDraft;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputGet = new InputGet();
if ($inputGet->getSongDraftId() != null) {
    $song = new SongDraft(null, $database);
    try {
        $song->findOneBySongDraftId($inputGet->getSongDraftId());
?>
        <form action="">
            <div class="modal fade" id="updateSongDraftDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateSongDraftDialogLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateSongDraftDialogLabel">Update Song Draft</h5>
                            <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body song-dialog" style="position:relative">
                            <audio style="width: 100%; height: 40px;" src="<?php echo $cfg->getSongDraftBaseUrl() . "/" . $song->getSongDraftId() . "/" . basename($song->getFilePath()); ?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime()); ?>" controls></audio>

                            <form>
                                <textarea name="lyric" class="form-control"><?php echo nl2br($song->getLyric(), true);?></textarea>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success save-update-song">Save</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
<?php
    } catch (Exception $e) {
        // do nothing
    }
}
?>