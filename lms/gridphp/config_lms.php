<?php

// PHP Grid database connection settings, Only need to update these in new project

// replace {{dbtype}} with one of these: mysqli,oci8 (for oracle),mssqlnative,postgres,sybase
define("PHPGRID_DBTYPE","mysqli"); 
define("PHPGRID_DBHOST","localhost");
// define("PHPGRID_DBUSER","root");
// define("PHPGRID_DBPASS","");
// define("PHPGRID_DBNAME","griddemo");
define("PHPGRID_DBUSER","mangoi");
define("PHPGRID_DBPASS","mi!@#2019");
define("PHPGRID_DBNAME","mangoi");

// database charset
define("PHPGRID_DBCHARSET","utf8");

// Basepath for lib
define("PHPGRID_LIBPATH",dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR);