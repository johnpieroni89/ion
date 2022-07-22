<?php 

include('../autoload.php');

$scopes = [
    'CHARACTER_AUTH',
    'CHARACTER_READ'
];

$auth = new SWC();
$auth->AttemptAuthorize($scopes);

var_dump($_POST);

var_dump($_GET);

?>