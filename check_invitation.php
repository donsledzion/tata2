<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}

if((!isset($_POST['email']))||(empty($_POST['email']))){
    $_SESSION['e_email'] = "musisz podać jakiś adres e-mail!";
    header('Location: invite.php') ;
    exit();
} else {
    $email = filter_input(INPUT_POST,'email');
    // należy sprawdzić czy podany e-mail istnieje w bazie:
    //  1) jeśli nie istnieje zaprosić do utworzenia konta (pokazać formularz tworzenia konta)
    //  2) jeśli istnieje to należy sprawdzić czy: już nie obserwuje naszego konta:
    //      2a) jeśli obserwuje to utworzyć komunikat o błędzie i odesłać na stronę zaproszenia z komunikatem błędu
    //      2b) jeśli nie obserwuje to utworzyć zaproszenie i wysłać je do użytkownika
    $usersQuery = $db->prepare('SELECT * FROM Users WHERE Email=:Email;');
    $usersQuery->bindValue(':Email',$email);
    $usersQuery->execute();
    $usersFound = $usersQuery->rowCount();
    
    if($usersFound>0){
        // jeśli podany użytkownik istnieje w bazie to sprawdzamy czy ma relację z naszym kontem
        $user = $usersQuery->fetch(PDO::FETCH_ASSOC);    
        echo "ID mojego konta: ".$_SESSION['IDAccount']."<br />";
        echo "===========================================================<br />";
        echo "Znaleziono:<br />" ;
        echo "===========================================================<br />";
        echo "Użytkownik:<br />";
        echo "ID: ".$user['IDUser']."<br />" ;
        echo "Imię: ".$user['First_name']."<br />";
        echo "Nazwisko: ".$user['Last_name']."<br />";
        
        $relationsQuery = $db->prepare('SELECT * FROM AccountsUsersPermissions WHERE IDAccount=:IDAccount AND IDUser=:IDUser;');
        $relationsQuery->bindValue(':IDAccount', $_SESSION['IDAccount']);
        $relationsQuery->bindValue(':IDUser', $user['IDUser']);
        $relationsQuery->execute();
        $relationsFound=$relationsQuery->rowCount();
        
        if($relationsFound>0){
            // jeśli użytkownik jest już w bazie i ma uprawnienia do wglądu do naszego konta...
            $_SESSION['e_email'] = $_SESSION['tata_logged_ID']."! ".$user['First_name']." ".$user['Last_name']." już obserwuje Twoje konto ;) (ogarnij się!)";
            header('Location: invite.php') ;
            exit();
        } else {
            // jeśli użytkownik istnieje w bazie ale nie ma relacji z naszym kontem to możemy go zaprosić
            $_SESSION['msg_email'] = $user['First_name']." ".$user['Last_name']." ma już konto w serwisie - zaprosić?";
            $_SESSION['invited_ID'] = $user['IDUser'] ;
            header('Location: invite.php') ;
            exit();
        
        }
        
    } else {
        $_SESSION['fill_email'] = $email;
        header('Location: invitation_form.php');
        exit();
    }
    
}