<?php

use Pico\Util\PicoHttpCache;

require_once dirname(__DIR__)."/inc/auth.php";
PicoHttpCache::cacheLifetime(3600*12);
?>
<form action="">
    <div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="addUserDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addUserDialogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserDialogLabel">Add User</h5>
                    <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="dialog-table">
                        <tbody>
                            <tr>
                                <td>Real Name</td>
                                <td><input type="text" class="form-control" name="name" value=""></td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td><input type="text" class="form-control" name="username" value=""></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><input type="text" class="form-control" name="email" value=""></td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td><input type="text" class="form-control" name="password" value=""></td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td><select class="form-control" name="gender" id="gender">
                                <option value="M">Man</option>
                                <option value="W">Woman</option>
                                </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Birtth Day</td>
                                <td><input type="date" class="form-control" name="birth_day" value=""></td>
                            </tr>
                            <tr>
                                <td>Active</td>
                                <td><label></label><input type="checkbox" name="active" value="1"> Active</label></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save-add-user">OK</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</form>