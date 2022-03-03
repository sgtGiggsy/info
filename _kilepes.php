<?php
$kerdesid = $_SESSION[getenv('SESSION_NAME').'kerdesid'];
session_destroy();
session_set_cookie_params('604800');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION[getenv('SESSION_NAME').'kerdesid'] = $kerdesid;
header('Location: ./index.php');
?>