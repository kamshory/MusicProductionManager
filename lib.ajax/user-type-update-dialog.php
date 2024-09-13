<?php

use MagicObject\Request\InputGet;
use MusicProductionManager\Data\Entity\UserType;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputGet = new InputGet();
$userType = new UserType(null, $database);
try {
    $userType->findOneByUserTypeId($inputGet->getUserTypeId());
?>
    <form action="">
        <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="updateUserTypeDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateUserTypeDialogLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateUserTypeDialogLabel">Update User Type</h5>
                        <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-responsive table-responsive-two-side table-borderless ">
                            <tbody>
                                <tr>
                                    <td>User Type</td>
                                    <td><input type="text" class="form-control" name="name" value="<?php echo $userType->getName(); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Sort Order</td>
                                    <td><input type="number" class="form-control" name="sort_order" value="<?php echo $userType->getSortOrder(); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Admin</td>
                                    <td><label></label><input type="checkbox" name="admin" value="1" <?php echo $user->createCheckeAdmin("1");?>> Admin</label></td>
                                </tr>
                                <tr>
                                    <td>Active</td>
                                    <td><label></label><input type="checkbox" name="active" value="1" <?php echo $user->createCheckedActive("1");?>> Active</label></td>
                                </tr>
                            </tbody>
                            <input type="hidden" name="user_type_id" value="<?php echo $userType->getUserTypeId(); ?>">
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success save-edit-user-type">OK</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
} catch (Exception $e) {
?>
    <div class="alert alert-warning" role="alert">
        Unexpected error occured
    </div>
<?php
}
