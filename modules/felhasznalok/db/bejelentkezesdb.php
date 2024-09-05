<?php

$con = mySQLConnect(false);
purifyPost();

$stmt = $con->prepare('UPDATE bejelentkezesek SET felbontas=? WHERE id=?');
$stmt->bind_param('si', $_POST['felbontas'], $_POST['loginid']);
$stmt->execute();

?>