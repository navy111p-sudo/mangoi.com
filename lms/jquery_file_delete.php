<?php

$filename = $_POST['filename'];

if( !unlink("../uploads/document_files/".$filename) ) {
  echo "failed\n";
}
else {
  echo "success\n";
}

?>