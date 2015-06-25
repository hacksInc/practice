<?php
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
require_once dirname(__FILE__) . '/../app/Pp_Controller.php';

Pp_Controller::main('Pp_Controller', array(
    '__ethna_unittest__',
    )
);
?>
