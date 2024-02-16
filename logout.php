<?php
@session_start();
unset($_SESSION['suser']);
unset($_SESSION['spass']);
session_destroy();
header("Location: index.php");