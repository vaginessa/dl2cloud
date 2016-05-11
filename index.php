<?php
/*
Copyright 2016 Daniil Gentili
(https://daniil.it)
This file is part of the dl2cloud (https://github.com/danog/dl2cloud).
Dl2cloud is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. 
Dl2cloud is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details. 
You should have received a copy of the GNU General Public License along with the dl2cloud. 
If not, see <http://www.gnu.org/licenses/>.
*/include 'db_connect.php';
include 'functions.php';
include 'emails.php';
require_once 'Git.php';
require('PHPMailer/PHPMailerAutoload.php'); 
require_once "dropbox-sdk/Dropbox/autoload.php";
connect_db();
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error_api.log");
error_log( "Hello, errors (dl api)!" );

$token = $_POST['token'];
$password = $_POST['p'];
$username = $_POST['username'];
$action = $_POST['action'];
$targetaccount = $_POST['targetaccount'];
$target = $_POST['target'];

$newtargetaccount = $_POST['newtargetaccount'];
$data = $_POST['data'];
$url = $_POST['url'];
$dlid = $_POST['dlid'];
$filename = $_POST['filename'];
$email = $_POST['email'];


if($_GET['hash'] != "" && $_GET['account'] != "" && $_GET['action'] == "verify") {
    if(verify($_GET['email'], $_GET['account']) == true) {
     $result = "200";
     $message = "Email verified successfully!";
    } else {
     $result = "500";
     $message = "An error occurred!";
    };

} elseif($action == "signup" && $username != "" && $password != "" && $email != "") {
  signup($username, $password, $email, $pdo);

} elseif(
  ($token != "" ||
  ($username != "" && $password != "")) && 
  login($username, $password, $token, $pdo) == true) {

    if($action == "gettoken") {
     gettoken($pdo);

    } elseif($action == "addtarget" && $target != "") {
     if(addtarget($target, $targetaccount, $data, $pdo) == "true") { returnok; } else { returnerror; };

    } elseif($action == "rmtarget" && $target != "" && $targetaccount != "") {
     if(rmtarget($target, $targetaccount, $pdo) == "true") { returnok; } else { returnerror; };

    } elseif($action == "mvtarget" && $target != "" && $targetaccount != "" && $newtargetaccount != "") {
     if(mvtarget($target, $targetaccount, $newtargetaccount, $pdo) == "true") { returnok; } else { returnerror; };

    } elseif($action == "listtarget") {
     if(listtarget($pdo) == "true") { returnok; $message = "$tmpmessage"; } else { returnerror; };


    } elseif($action == "dlstatus" && $dlid != "") {
     if(dlstatus($dlid) == "true") { returnok; $message = "$tmpmessage"; } else { returnerror; };


    } elseif($action == "dl" && $url != "") {
     download($url, $filename, $target, $targetaccount, $pdo);

    } else {
     $result = "400";
     $message = "What should I do?";
    };
} else {
    $result = "200";
    $message = "This is a file download API.";
}

header('Content-type: application/json'); 

$status = array( 'result' => "$result" , 'message' => "$message");
echo json_encode( $status );

?>
