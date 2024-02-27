<?php

use Pico\Data\Entity\Genre;
use Pico\Request\PicoRequest;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new PicoRequest(INPUT_GET);
$genre = new Genre(null, $database);
try
{
$genre->findOneByGenreId($inputGet->getGenreId());
?>
<form action="">
    <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="editGenreDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editGenreDialogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGenreDialogLabel">Edit Genre</h5>
                    <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="dialog-table">
                        <tbody>
                            <tr>
                                <td>Genre Name</td>
                                <td><input type="text" class="form-control" name="name" value="<?php echo $genre->getName();?>"></td>
                            </tr>
                            <tr>
                                <td>Active</td>
                                <td><label></label><input type="checkbox" name="active" value="1" <?php echo $genre->getActive() == 1 ?' checked':'';?>> Active</label></td>
                            </tr>
                        </tbody>
                        <input type="hidden" name="genre_id" value="<?php echo $genre->getGenreId();?>">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save-edit-genre">OK</button>
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
    ?>
    <div class="alert alert-warning" role="alert">
    Unexpected error occured
    </div>
    <?php
}