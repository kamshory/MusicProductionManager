<?php

use Pico\Data\Entity\Artist;
use Pico\Exceptions\NoRecordFoundException;
use Pico\Request\PicoRequest;

require_once dirname(__DIR__)."/inc/auth.php";
$inputGet = new PicoRequest(INPUT_GET);
$artist = new Artist(null, $database);
?>
<form action="">
    <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="updateArtistDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateArtistDialogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateArtistDialogLabel">Update Artist</h5>
                    <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <?php
                        try
                        {                     
                        $artist->findOneByArtistId($inputGet->getArtistId());
                        ?>
                        <table class="dialog-table">
                            <tbody>
                                <tr>
                                    <td>Real Name</td>
                                    <td><input type="text" class="form-control" name="name" value="<?php echo $artist->getName();?>"></td>
                                </tr>
                                <tr>
                                    <td>Stage Name</td>
                                    <td><input type="text" class="form-control" name="stage_name" value="<?php echo $artist->getStageName();?>"></td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td><select class="form-control" name="gender" id="gender">
                                    <option value="M"<?php echo $artist->equalsGender("M")?" selected":"";?>>Man</option>
                                    <option value="W"<?php echo $artist->equalsGender("W")?" selected":"";?>>Woman</option>
                                    </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Birtth Day</td>
                                    <td><input type="date" class="form-control" name="birth_day" value="<?php echo $artist->getBirthDay();?>"></td>
                                </tr>
                                <tr>
                                    <td>Active</td>
                                    <td><label></label><input type="checkbox" name="active" value="1" <?php echo $artist->getActive() == 1 ?' checked':'';?>> Active</label></td>
                                </tr>
                            </tbody>
                            <input type="hidden" name="artist_id" value="<?php echo $artist->getArtistId();?>">
                        </table>
                        <?php
                        }
                        catch(NoRecordFoundException $e)
                        {
                            ?>
                            <div class="alert alert-warning"><?php echo $e->getMessage();?></div>
                            <?php
                        }
                        catch(Exception $e)
                        {
                            ?>
                            <div class="alert alert-warning">Unexpected error occured</div>
                            <?php
                        }
                        ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save-edit-artist">OK</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</form>