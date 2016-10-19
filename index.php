<?php
include_once('./bootstrap.php');
$http = new http_request($_SERVER['REQUEST_METHOD'], $_GET, $_POST, $_SERVER);
$http->process();
