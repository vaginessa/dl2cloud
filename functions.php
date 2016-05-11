<?php
/*
Copyright 2015-2016 Daniil Gentili
(https://daniil.it)
This file is part of the dl2cloud (https://github.com/danog/dl2cloud).
Dl2cloud is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. 
Dl2cloud is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details. 
You should have received a copy of the GNU General Public License along with the dl2cloud. 
If not, see <http://www.gnu.org/licenses/>.
*/
// Get token function
function gettoken($pdo) {
 global $username;
 // Prepare
 $getprepare = "SELECT token FROM members where username = ?";
 $getstmt = $pdo->prepare($getprepare);
 // get everything
 $getstmt->execute(array($username));
 $row  = $getstmt->fetch();
 extract($row);
 $GLOBALS['message'] = "$token";
 $GLOBALS['status'] = "200";

};

// Add target function
function addtarget($target, $targetaccount, $data, $pdo) {
 global $username;
 // First check and add target
 if($target != "" && $targetaccount != "" && $data != "") {

  // Check if entry exists
  $checkprepare  = "SELECT target, targetaccount, data FROM accounts WHERE target = ? AND targetaccount = ? AND data = ?";
  
  // Prepare
  $checkuserstmt = $pdo->prepare($checkprepare);

  // Check
  $checkuserstmt->execute(array($target, $targetaccount, $data));

  // Count matches
  $count  = $checkuserstmt->rowCount();

  if($count == "0"){
   
   $do  = "INSERT INTO accounts (target, targetaccount, data) VALUES (?, ?, ?)";

   $dost = $pdo->prepare($do);

   // do
   $success = $dost->execute(array($target, $targetaccount, $data));
   if(!$success) { return false; };
   switch ($target) {
       case "email":
           $hash = md5(rand(0,1000) );
           // Send email
           declareverify("$targetaccount");
           dsendmail("$targetaccount");
           break;
       case "telegram":
           $hash = md5(rand(0,1000) );
           // Send email
           declareverify("$targetaccount");
           dtelegram("$targetaccount");
           break;
   };
  } else return false;
 } else return false;
 return true;
};

// Remove target function
function rmtarget($target, $targetaccount) {
 global $username;
 // Remove target
 if($target != "" && $targetaccount != "" && $data != "") {

  // Check if entry exists
  $checkprepare  = "SELECT target, targetaccount, data FROM accounts WHERE target = ? AND targetaccount = ? AND data = ?";
  
  // Prepare
  $checkuserstmt = $pdo->prepare($checkprepare);

  // Check
  $checkuserstmt->execute(array($target, $targetaccount, $data));

  // Count matches
  $count  = $checkuserstmt->rowCount();

  if($count == "1"){
   
   $do  = "DELETE FROM accounts WHERE target = ? AND targetaccount = ? AND data = ?";
   $dost = $pdo->prepare($do);
   // do
   $success = $dost->execute(array($target, $targetaccount, $data));
   if(!$success) { return false; };
  } else return false;
 } else return false;
 return true;
};


// Rename target function
function mvtarget($target, $targetaccount, $newtargetaccount, $pdo) {
 global $username;
 // First check and add target
 if($target != "" && $targetaccount != "" && $newtargetaccount != "") {

  // Check if entry exists
  $checkprepare  = "SELECT target, targetaccount FROM accounts WHERE target = ? AND targetaccount = ?";
  
  // Prepare
  $checkuserstmt = $pdo->prepare($checkprepare);

  // Check
  $checkuserstmt->execute(array($target, $targetaccount, $newtargetaccount));

  // Count matches
  $count  = $checkuserstmt->rowCount();

  if($count == "1"){
   
   $do  = "UPDATE accounts SET targetaccount = ? WHERE target = ? AND targetaccount = ?";

   $dost = $pdo->prepare($do);

   // do
   $success = $dost->execute(array($newtargetaccount, $target, $targetaccount));
   if(!$success) { return false; };
  } else return false;
 } else return false;
 return true;
};

// List target function

function listtarget($pdo) {
 global $username;
 $stmt = $dbh->prepare("SELECT target, targetaccount FROM accounts WHERE username = ?");
 if ($stmt->execute(array($username))) {
  while ($row = $stmt->fetch()) {
    $message = "$message\n$row";
  }
 } else return false;
 $GLOBALS['tmpmessage'] = "$message";
 return true;
}

// DL status function
function dlstatus($dlid){
 $line = '';

 $f = fopen("https://api.travis-ci.org/jobs/$dlid/log.txt?deansi=true", 'r');
 if($f === false) { return false; };
 $cursor = -1;

 fseek($f, $cursor, SEEK_END);
 $char = fgetc($f);


 /**
  * Trim trailing newline chars of the file
  */
 while ($char === "\n" || $char === "\r") {
     fseek($f, $cursor--, SEEK_END);
     $char = fgetc($f);
 }

 /**
  * Read until the start of file or first newline char
  */
 while ($char !== false && $char !== "\n" && $char !== "\r") {
    /**
     * Prepend the new char
     */
    $line = $char . $line;
    fseek($f, $cursor--, SEEK_END);
    $char = fgetc($f);
 }

 $GLOBALS['tmpmessage'] = $line;
 return true;
};

// Create user function
function create_user($username, $password, $email, $pdo) {
 // If variables aren't empty and email is valid
 if($username != "" && $password != "" && $email != "" && !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {

  // Create salt
  $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));

  // Hash passwd
  $password = hash('sha512', $password.$random_salt);

  // Hash API token
  $token = hash('sha512', $password.$username);

  // Check if user exists
  $checkprepare  = "SELECT username, password, salt, token FROM members WHERE username = ? AND password = ? AND salt = ? AND token = ?";

  // Prepare
  $checkuserstmt = $pdo->prepare($checkprepare);

  // Check
  $checkuserstmt->execute(array($username, $password, $random_salt, $token));
  
  // Count matches
  $count  = $checkuserstmt->rowCount();

  if($count == "0"){
   // Insert user
   $createuserprepare = "INSERT INTO members (username, password, salt, token) VALUES (?, ?, ?, ?)";

   // Prepare stmt
   $createuserstmt = $pdo->prepare($createuserprepare);

   // Let's do this!
   $result = $createuserstmt->execute(array($username, $password, $random_salt, $token));
   if(!$result) { return false; };
   // Register email and send verification email.
   addtarget("email", "Default email address", $email, $pdo);

  } else return false;
 } else return false;
};

// Login function
function login($username, $password, $token, $mysqli, $pdo) {
  if($username != "" && $password != "" && $token == "" ) {
   $sql  = "SELECT id, username, password, salt FROM members WHERE username = ? LIMIT 1";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array($username));
   $row  = $stmt->fetch();
   extract($row);
   $password = hash('sha512', $password.$salt); 
   if($stmt->rowCount() == 1) {
      if($db_password == $password) {
         $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); 
         $GLOBALS['username'] = $username;
         return true;
      } else { return false; };
   } else { return false; };

  } elseif($token != "" && $username == "" && password == "") {
   $sql  = "SELECT id, username, password, salt FROM members WHERE token = ? LIMIT 1";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array($token));
   $row  = $stmt->fetch();
   extract($row);
   $db_token = hash('sha512', $username.$password); 
   if($stmt->rowCount() == 1) {
      if($db_token == $token) {
         $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); 
         $_GLOBAL['username'] = $username;
         return true;
      } else { return false; };
   } else { return false; };
  } else { return false; };
};

function verify($account, $hash, $pdo) {
   global $username;
   $verifysql  = "SELECT targetaccount, hash FROM accounts WHERE targetaccount = ?";

   $verifystmt = $pdo->prepare($verifysql);
   $verifystmt->execute(array($account));

   $count = $verifystmt->rowCount();
   if($count > 0){
    $sqlyay = "UPDATE accounts SET hash = '' WHERE targetaccount = ? AND hash = ? LIMIT 1";
    $stmtyay = $pdo->prepare($sqlyay);
    $stmtyay->execute(array($account, $hash));
    if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $account)){
     declarewelcome();
     dsendmail("$email");
    };
   } else return false;

   return true;
};
function returnok() {
   $GLOBALS['status'] = "200";
   $GLOBALS['message'] = "All good!";
};

function returnerror() {
   $GLOBALS['status'] = "404";
   $GLOBALS['message'] = "An error occurred!";
};

// MAIN DL FUNCTION 
function download($url, $filename, $target, $targetaccount, $pdo) {
 $rand = md5(rand(0,1000) );

 $repo = Git::create("$rand", "git@github.com:/video-dl/video-dl");
 $repo->set_env ( "user.name", "Video download" );
 $repo->set_env ( "user.email", "daniil@daniil.it" );
 $repo->checkout("$rand");


 $repo->add('.');
 $repo->commit("Started job $rand");
 $repo->push('origin', "$rand");
};



?>
