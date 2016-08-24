<?php
  ini_set('display_errors', true);
  error_reporting(E_ALL);

  include './vendor/autoload.php';

  if(empty($_FILES['image']['name'])){
    header("Location: index.php");
    die();
  }

  $debug = false;

  $caption = $_POST['caption'];

  $users = array();
  $accounts = array(
    [
      "htmlEl" => "AC1",
      "username" => "USER1",
      "password" => "PASS1"
    ],
    [
      "htmlEl" => "AC2",
      "username" => "USER2",
      "password" => "PASS2"
    ],
    [
      "htmlEl" => "AC3",
      "username" => "USER3",
      "password" => "PASS3"
    ],
    [
      "htmlEl" => "AC4",
      "username" => "USER4",
      "password" => "PASS4"
    ],
    [
      "htmlEl" => "AC5",
      "username" => "USER5",
      "password" => "PASS5"
    ]
  );

  $path = "uploads/";

  $img = $_FILES['image']['tmp_name'];
  $dst = $path . hash('ripemd160', $_FILES['image']['name']);


  if (($img_info = getimagesize($img)) === FALSE)
    die("Image not found or not an image");

  $width = $img_info[0];
  $height = $img_info[1];

  switch ($img_info[2]) {
    case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);  break;
    case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); break;
    case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);  break;
    default : die("Unknown filetype");
  }

  $tmp = imagecreatetruecolor($width, $height);
  imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
  imagejpeg($tmp, $dst.".jpg");

  foreach ($accounts as $account){
    if (!empty($_POST[$account['htmlEl']]) && $_POST[$account['htmlEl']]) {
      array_push($users, ["username" => $account['username'], "password" => $account['password']]);
    }
  }

  foreach ($users as $user) {
    uploadImage($user['username'], $user['password'], $debug, $dst.'.jpg', $caption);
    if(end($users)['username']=== $user['username']){
      header("Location: uploaded.php");
      die();
    }
   }

  function uploadImage($username, $password, $debug, $photo, $caption){
    $i = new \InstagramAPI\Instagram($username, $password, $debug);

    try {
        $i->login();
    } catch (Exception $e) {
        $e->getMessage();
        exit();
    }

    try {
        $i->uploadPhoto($photo, $caption);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
  }
?>
