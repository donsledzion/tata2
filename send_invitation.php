<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}

if((!isset($_SESSION['fill_email']))||(empty($_SESSION['fill_email']))){
    //jeśli jakimś cudem nie ma podanego e-maila na tym etapie to powrót do formularza zaproszeniowego
    $_SESSION['e_email'] = "musisz podać jakiś adres e-mail!";
    header('Location: invitation_form.php') ;
    exit();
} else if((!empty($_POST['invite_first_name']))&&(!empty($_POST['invite_last_name']))){
    echo "Jakieś imię i nazwisko podane, to można działać...";
    // jeśli użytkownik podał imię i nazwisko to tworzę request utworzenia KONTA UŻYTKOWNIKA
    // z przypisaniem go do konkretnego konta na uprawnieniach podanych przez zapraszającego
    $first_name = filter_input(INPUT_POST,'invite_first_name');
    $last_name = filter_input(INPUT_POST,'invite_last_name');
    if(!empty($_POST['invite_permissions'])){
        $permissions = filter_input(INPUT_POST,'invite_permissions');
    } else {
        $permissions ="guest" ;
    }
    echo "<br />e-mail: ".$_SESSION['fill_email'];
    echo "<br />Imie: ".$first_name;
    echo "<br />Nazwisko: ".$last_name;
    echo "<br />Uprawnienia: ".$permissions;
    echo "<br />Zaproszenie do konta nr: ".$_SESSION['IDAccount'];
    if($permissions=="parent"){
        $permissions = '1';
    } else {
        $permissions = '2';
    }
    
    // jeśli wszystko się zgadza to można:
    // 1) utworzyć wpis w bazie danych - nowy request
    // 2) potem wysłać maila z tokenem do aktywacji. Docelowo mail powinien zawierać dwa linki:
    //      a) do akceptacji danych i aktywacji konta (na początek tylko ta opcja)
    //      b) do edycji danych a następnie do aktywacji konta
    // 3) niezależnie od opcji powyżej trzeba zaraz po aktywacji utworzyć hasło użytkownika
    // 4) w celu powyższego trzeba utworzyć silne losowe hasło na początek
    
    // generowanie startowego hasła (konto musi być jakkolwiek zabezpieczone
    // wygenerowane hasło w formie zahashowanej zostanie umieszczone w bazie
    // nikt nie dostanie informacji o tym haśle. Nowy użytkownik będzie musiał
    // w pierwszej kolejności ustawić hasło do aplikacji
    $pass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
    
    
    // generowanie tokena do aktywacji konta
    // ważność tokena zaproszeniowego to może być np. 30 dni
    $token = bin2hex(random_bytes(50));                                
        // Obtaining token expiration date (30 days since request)
        $nextMonth = new DateTime();
        $nextMonth->modify('+30 Days');
        $expiration = $nextMonth->format('Y-m-d H:i:s');
    
    try{
        $inviteQuery = $db->prepare('INSERT INTO AccountRequest VALUES('
                . ':IDRequest, '
                . ':User_email, '
                . ':User_first_name, '
                . ':User_last_name, '
                . ':User_password, '
                . ':User_avatar, '
                . ':IDAccount, '
                . ':IDPermissions, '
                . ':Account_name, '
                . ':Account_avatar, '
                . ':Token, '
                . ':Expiration);');
        $inviteQuery->bindValue(':IDRequest',null);
        $inviteQuery->bindValue(':User_email',$_SESSION['fill_email'], PDO::PARAM_STR);
        $inviteQuery->bindValue(':User_first_name',$first_name, PDO::PARAM_STR);
        $inviteQuery->bindValue(':User_last_name',$last_name, PDO::PARAM_STR);
        $inviteQuery->bindValue(':User_password',$pass, PDO::PARAM_STR);
        $inviteQuery->bindValue(':User_avatar',null);
        $inviteQuery->bindValue(':IDAccount',$_SESSION['IDAccount'], PDO::PARAM_INT);
        $inviteQuery->bindValue(':IDPermissions',$permissions, PDO::PARAM_INT);
        $inviteQuery->bindValue(':Account_name',null);
        $inviteQuery->bindValue(':Account_avatar',null);
        $inviteQuery->bindValue(':Token',$token);
        $inviteQuery->bindValue(':Expiration',$expiration);
        $inviteQuery->execute();
    } catch(Exception $e) {
        $_SESSION['e_email'] = 'Nie udało się dodać zaproszenia...<br />' ;
        $_SESSION['e_email'] .= $e->getMessage()." <br />" ;
        header('Location: invitation_form.php') ;
        exit();
    }
    // jeśli do tej pory nic się nie wysypało to można wysłać maila z linkiem aktywacyjnym.
    // link aktywacyjny powinien przekierowywać do formularza zmiany hasła
    
    $emailFrom = $_SESSION['tata_logged_Email'];
    $emailTo = $_SESSION['fill_email'];
    $subject = $_SESSION['tata_logged_ID']." ".$_SESSION['tata_logged_LN']." Cię zaprasza!" ;
    $message = "<h6>Cześć!</h6>";
    $message .= $_SESSION['tata_logged_ID']." ".$_SESSION['tata_logged_LN']." zaprasza Cię do wspólnego korzystania z serwisu<i>\"Tata, a Marcin powiedział...\"</i>";
    $message .= ", w którym wspólnie możecie podziwiać mądrości dzieciaków.";
    $message .= "<br />Możesz kliknąć w poniższy link aktywacyjny ,który zabierze Cię do formularza ustawienia hasła do serwisu.";
    $message .= "<br><b><a href=\"http://sledzislaw.usermd.net/dzieciaki/activateAccount.php?token=".$token."\">RESET HASŁA</a></b>";
    $message .= "<br />Po ustawieniu hasła możesz zalogować się korzystając za pomocą tego adresu e-mail oraz nowego hasła.";
    $message .= "<br />W razie jakichkolwiek problemów ze zmianą hasła prosimy o kontakt na adres e-mail: dzieciaki@ulinia8.pl<br />";
    $message .= "<br />Udanej zabawy :)<br />";
    $message = wordwrap($message,50);                    
    $headers = "From:".$emailFrom." \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html\r\n";
    try {
        $sendmail = mail($emailTo, $subject, $message, $headers);
    } catch (Exception $e) {
        $_SESSION['e_rec_email'] .= "</br>Exception: ".$e->getMessage()."</br>";    
    }
    if(!$sendmail){
        $_SESSION['e_rec_email'] .= " ...ale nie udało się wysłać wiadomości z linkiem do ustawienia hasła :/!</br>";    
    } else {
        header('location: pending.php?email='.$email);
    }
    
    
    
    
    
} else {
    // jeśli nie ma imienia i nazwiska to będzie powrót do formularza zaproszeniowego
    //  w taki sposób aby wyglądało, że tylko pojawił się przycisk wysłania zaproszenia
    $_SESSION['e_email']= "Albo imię albo nazwisko podaj bo jeszcze pracuję nad tym, ok?";
    header('Location: invitation_form.php') ;
    exit();
}