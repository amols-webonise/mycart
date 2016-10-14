<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once('./db/config.php');
include_once('./db/connection.php');
include_once('./errorlog.php');
include_once('./statuscode.php');
include_once('./http_request.php');
include_once('./http_response.php');
$http = new http_request($_SERVER['REQUEST_METHOD'], $_GET, $_POST, $_SERVER);
$http->process();
