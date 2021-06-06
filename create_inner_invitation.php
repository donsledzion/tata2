<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}

if((!isset($_POST['invitedID']))||(empty($_POST['invitedID']))||(!isset($_SESSION['IDUser']))||(empty($_SESSION['IDUser']))||($_SESSION['someAdmin']==false)){
    header('Location: user.php') ;
	exit();
}
$invited = filter_input(INPUT_POST, 'invitedID');
if(isset($_POST['permission'])){    
    $permissions = filter_input(INPUT_POST, 'permission');
} else {
    $permissions = '2' ;
}
$today = new DateTime();
$invitationDate = $today->format('Y-m-d H:i:s');


echo "<br />Zapraszajacy (KONTO): ".$_SESSION['IDAccount'];
echo "<br />Zapraszajacy (USER): ".$_SESSION['IDUser'];
echo "<br />Zapraszany   (USER): ".$invited;
echo "<br />Uprawnienia: ".$permissions;
echo "<br />Data wysłania: ".$invitationDate;

try{
    $innerInvitationQuery = $db->prepare('INSERT INTO InnerInvitations VALUES(:IDInvitation, :IDAccount, :IDInviting, :IDInvited, :IDPermissions, :InvitationDate);');
    $innerInvitationQuery->bindValue(':IDInvitation',null);
    $innerInvitationQuery->bindValue(':IDAccount',$_SESSION['IDAccount'], PDO::PARAM_INT);
    $innerInvitationQuery->bindValue(':IDInviting',$_SESSION['IDUser'], PDO::PARAM_INT);
    $innerInvitationQuery->bindValue(':IDInvited',$invited, PDO::PARAM_INT);
    $innerInvitationQuery->bindValue(':IDPermissions',$permissions, PDO::PARAM_INT);
    $innerInvitationQuery->bindValue(':InvitationDate',$invitationDate);
    $innerInvitationQuery->execute();
} catch(Exception $e) {
    $_SESSION['e_invitation'] = $e->getMessage();
    header('Location: invite.php') ;
    exit();
}
$_SESSION['invitation_msg'] = 'zaproszenie wysłane';
header('Location: invite.php') ;
exit();