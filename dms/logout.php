<?php
setcookie("LoginAdminID","",time()-1);
header("Location: login_form.php"); 
exit;
?>
