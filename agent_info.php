<?php
//print alert agent info
$agent = $_SERVER['HTTP_USER_AGENT'];

// alert
echo "<script>alert('".$agent."');</script>";
?>