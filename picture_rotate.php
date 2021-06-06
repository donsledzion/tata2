<?php
session_start();
require_once "database.php";
include 'imgMods.php';
if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}

echo "captured data:</br>";
echo "IDAccount: ".$_POST['IDAccount']."</br>";
echo "Kids default picture-name: ".$_POST['picture_name']."</br>";
echo "Rotation angle: ".$_POST['angle']."</br>";

echo "...processing rotation on 4 files..." ;

$account= filter_input(INPUT_POST, 'IDAccount');
$picture= filter_input(INPUT_POST, 'picture_name');
$angle  = filter_input(INPUT_POST, 'angle');


pic_rotate("pics/".$account."/160/".$picture, $angle);
pic_rotate("pics/".$account."/320/".$picture, $angle);
pic_rotate("pics/".$account."/480/".$picture, $angle);
pic_rotate("pics/".$account."/768/".$picture, $angle);

header('Location: showKids.php');
exit();
        