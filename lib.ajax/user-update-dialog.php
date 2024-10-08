<?php

use MagicObject\Request\InputGet;
use MagicObject\Response\Generated\PicoSelectOption;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\User;

require_once dirname(__DIR__) . "/inc/auth.php";
$inputGet = new InputGet();
$user = new User(null, $database);
try {
    $user->findOneByUserId($inputGet->getUserId());
?>
    <form action="">
        <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="updateUserDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateUserDialogLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateUserDialogLabel">Update User</h5>
                        <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-responsive table-responsive-two-side table-borderless ">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td><input type="text" class="form-control" name="name" value="<?php echo $user->getName(); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td><input type="text" class="form-control" name="username" value="<?php echo $user->getUsername(); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><input type="text" class="form-control" name="email" value="<?php echo $user->getEmail(); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Password</td>
                                    <td><input type="password" class="form-control" name="password" value=""></td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td><select class="form-control" name="gender" id="gender">
                                            <option value="M" <?php echo $user->equalsGender("M") ? " selected" : ""; ?>>Man</option>
                                            <option value="W" <?php echo $user->equalsGender("W") ? " selected" : ""; ?>>Woman</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Birtth Day</td>
                                    <td><input type="date" class="form-control" name="birth_day" value="<?php echo $user->getBirthDay(); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Admin</td>
                                    <td><label></label><input type="checkbox" name="admin" value="1" <?php echo $user->createCheckeAdmin("1");?>> Admin</label></td>
                                </tr>
                                <tr>
                                    <td>Associated Artist</td>
                                    <td>
                                        <select class="form-control" name="associated_artist">
                                            <option value="">- Select One -</option>
                                            <?php echo new PicoSelectOption(new Artist(null, $database), array('value' => 'artistId', 'label' => 'name'), $user->getAssociatedArtist()); ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Blocked</td>
                                    <td><label></label><input type="checkbox" name="blocked" value="1" <?php echo $user->createCheckeBlocked("1");?>> Blocked</label></td>
                                </tr>
                                <tr>
                                    <td>Active</td>
                                    <td><label></label><input type="checkbox" name="active" value="1" <?php echo $user->createCheckedActive("1");?>> Active</label></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="user_id" value="<?php echo $user->getUserId(); ?>">
                        <button type="button" class="btn btn-success save-update-user">OK</button>
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
