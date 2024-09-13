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
                        <div class="modal-body song-draft-dialog" style="position:relative">
                            <audio style="width: 100%; height: 40px;" src="<?php echo $cfg->getSongDraftBaseUrl() . "/" . $song->getSongDraftId() . "/" . basename($song->getFilePath()); ?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime()); ?>" controls></audio>

                            <form>
                                <div style="padding: 5px 0">
                                    <input type="text" name="name" class="form-control" value="<?php echo $song->getName();?>" placeholder="Name">
                                </div>
                                <div style="padding: 5px 0">
                                    <input type="text" name="title" class="form-control" value="<?php echo $song->getTitle();?>" placeholder="Title">
                                </div>
                                <div style="padding: 5px 0">
                                <textarea name="lyric" class="form-control" style="height:180px" spellcheck="false"><?php echo $song->getLyric();?></textarea>
                                </div>                               
                                <div style="padding: 5px 0">
                                <label><input type="checkbox" name="active" value="1" <?php echo $song->createCheckedActive("1");?>> Active</label>
                                </div> 
                                <input type="hidden" name="song_draft_id" value="<?php echo $song->getSongDraftId();?>">                              
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success save-update-song-draft">Save</button>
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