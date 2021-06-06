<?php
session_start();
require_once "database.php";
require_once 'pass_check.php'; // include file with password check function
unset($_SESSION['wszystko_gra']);
if (isset($_SESSION['tata_logged_ID'])){
	header ('Location: index.php') ;
	exit();
} else if(isset($_POST['register'])) {    
    
    $all_OK = true;    
    
    //=========================================================================
    // E-mail validation
    //=========================================================================
    if(!empty($_POST['email'])){
        $_SESSION['fill_email']=filter_input(INPUT_POST,'email');
        if(!filter_var($_SESSION['fill_email'], FILTER_VALIDATE_EMAIL)){
            $_SESSION['e_email'] = "to nie jest poprawny e-mail!";
            $all_OK = false;
        } else {
            $emailQuery = $db->prepare("SELECT * FROM Users where Email=?");
            $emailQuery->execute([$_SESSION['fill_email']]);
            $users = $emailQuery->rowCount();
            if($users > 0) {
                $_SESSION['e_email'] = "Już istnieje konto zarejestrowane na ten adres e-mail!";
                $all_OK = false;                        
            }
        }
    } else {
        $_SESSION['e_email'] = "musisz podać adres e-mail!";
        $all_OK = false;
    }
    
    //=========================================================================
    // Account ID validation
    //=========================================================================
    if(isset($_POST['IDAccount'])){
        if(!empty($_POST['IDAccount'])){
            $_SESSION['IDAccount']=filter_input(INPUT_POST,'IDAccount');
            if(!empty($_POST['Permissions'])){
                $_SESSION['IDPermissions']=filter_input(INPUT_POST,'Permissions');
            } else {
                $_SESSION['e_permissions'] = "dodając użytkownika do konta musisz nadać mu uprawnienia. Domyślnie dodano jako gościa!";
            }
        } else {
            $_SESSION['IDAccount']=null;
            $_SESSION['IDPermissions']='1';
        }
    } else {
        $_SESSION['IDAccount']=null;
        $_SESSION['IDPermissions']='1';
    } 
    
    //=========================================================================
    // Account name validation
    //=========================================================================
    if(!empty($_POST['accountName'])){
        $_SESSION['fill_accountName']=filter_input(INPUT_POST,'accountName');        
    } else {
        $_SESSION['e_accountName'] = "musisz podać nazwę konta - cokolwiek ;)!";
        $all_OK = false;
    }
    //=========================================================================
    // Password validation
    //=========================================================================
    if(!empty($_POST['pass'])) { // check if password is given
        //include 'pass_check.php'; // include file with password check function
        $password = filter_input(INPUT_POST, 'pass');
        if(!pass_check($password)){            
            //$_SESSION['e_pass'] = pass_check($password);
            $all_OK = false;
        } else {
            if(!empty($_POST['verPass'])){ // check if password is reentered
                $password2 = filter_input(INPUT_POST, 'verPass');
                if($password == $password2) {
                    $pass_hashed = password_hash($password, PASSWORD_DEFAULT);
                } else {
                    $_SESSION['e_pass'] = "hasła muszą być identyczne";
                    $_SESSION['e_verPass'] = "hasła muszą być identyczne";
                }
            } else { // if password isn't reentered
                $_SESSION['e_verPass'] = "powtórz hasło";
                $all_OK = false;
            }
        }        
    } else { // if password is not entered
        $_SESSION['e_pass'] = "podaj hasło";
            $all_OK = false;
    }
    
    
    //=========================================================================
    // First name validation
    //=========================================================================
    if(!empty($_POST['firstName'])){
        $firstName = filter_input(INPUT_POST,'firstName');        
        if(!preg_match("/^([a-zA-ZąĄćĆęĘłŁńŃóÓśŚźŹżŻ' ]+)$/",$firstName)){
            $_SESSION['e_firstName'] = "\"".$firstName."\" zawiera niedozwolone znaki" ;
            $all_OK = false;
        } else {
            $_SESSION['fill_firstName'] = $firstName;
        }
        
        //$_SESSION['fill_firstName'] = $firstName;
    } else {
        $_SESSION['e_firstName'] = "Podaj imię - bez tego ani rusz!";
        $all_OK = false;
    }
    //=========================================================================
    // Last name validation
    //=========================================================================
    if(!empty($_POST['lastName'])){
        $lastName = filter_input(INPUT_POST,'lastName');        
        if(!preg_match("/^([a-zA-ZąĄćĆęĘłŁńŃóÓśŚźŹżŻ' ]+)$/",$firstName)){
            $_SESSION['e_lastName'] = "\"".$lastName."\" zawiera niedozwolone znaki" ;
            $all_OK = false;
        } else {
            $_SESSION['fill_lastName'] = $lastName;
        }
        
        $_SESSION['fill_lastName'] = $lastName;
    } else {
        $_SESSION['e_lastName'] = "Podaj nazwisko!";
        $all_OK = false;
    }
    //=========================================================================
    // Terms of service acceptation validation
    //=========================================================================    
    if($_POST['terms']!="terms_accept"){
        $_SESSION['e_terms'] = "musisz zaakceptować warunki korzystania z serwisu!";
        $all_OK = false;
    } 
    
    
    
    
    
    //=========================================================================
    // All fields set properly - can proceed to data insertion
    //=========================================================================    
    unset($_POST['register']);
        
    if($all_OK == false){
        header('Location: register_account.php');
        exit();
    } else {
    //=========================================================================
    // If there is no problem reported we can start PDO transaction ===========
    // OR EVEN BETTER! TO ACQUIRING THE TOKEN !!!
    //=========================================================================    
        
        $token = bin2hex(random_bytes(50));                                
        // Obtaining token expiration date (7 days since request)
        $nextWeek = new DateTime();
        $nextWeek->modify('+7 Days');
        $expiration = $nextWeek->format('Y-m-d H:i:s');
        
        $request_OK = true;
        try {
            $accountRequestQuery = $db->prepare('INSERT INTO '
                    . 'AccountRequest '
                    . 'VALUES('
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
            $accountRequestQuery->bindValue(':IDRequest', null);
            $accountRequestQuery->bindValue(':User_email', $_SESSION['fill_email'], PDO::PARAM_STR );
            $accountRequestQuery->bindValue(':User_first_name', $_SESSION['fill_firstName'], PDO::PARAM_STR );
            $accountRequestQuery->bindValue(':User_last_name', $_SESSION['fill_lastName'], PDO::PARAM_STR );
            $accountRequestQuery->bindValue(':User_password', $pass_hashed, PDO::PARAM_STR );
            $accountRequestQuery->bindValue(':User_avatar', null);
            $accountRequestQuery->bindValue(':IDAccount', $_SESSION['IDAccount'], PDO::PARAM_INT);
            $accountRequestQuery->bindValue(':IDPermissions', $_SESSION['IDPermissions'], PDO::PARAM_INT);
            $accountRequestQuery->bindValue(':Account_name', $_SESSION['fill_accountName'], PDO::PARAM_STR );
            $accountRequestQuery->bindValue(':Account_avatar', null);
            $accountRequestQuery->bindValue(':Token', $token);
            $accountRequestQuery->bindValue(':Expiration', $expiration);
            $accountRequestQuery->execute();           
        } catch (Exception $e){
            $request_OK = false;
            $_SESSION['e_reg_email'] .= " ...wystąpił błąd podczas próby utworzenia konta tymczasowego. Spróbuj ponownie lub skontaktuj się z administratorem!<br />";           
            $_SESSION['e_reg_email'] .= $e->getMessage();
        }
        if($request_OK){
            // If insertion of token succeed - sending email with token

            $emailFrom = "noreply@ulinia8.pl";
            $emailTo = $_SESSION['fill_email'];
            $subject = "Witamy w serwisie \"Tata, a Marcin powiedział...\"" ;                    
            $message = "<h6>Cześć!</h6>";
            $message .= "Otrzymaliśmy prośbę o utworzenie konta w serwisie <i>\"Tata, a Marcin powiedział...\"</i>";
            $message .= "<br />Jeśli to nie Ty zakładałaś / zakładałeś konto to zignoruj tą wiadomość lub daj znać administracji serwisu.";
            $message .= "<br>Jeśli jednak to Twoja prośba to kliknij w poniższy link aby aktywować konto i dokończyć rejestrację.";
            $message .= "<br><b><a href=\"http://sledzislaw.usermd.net/dzieciaki/activateAccount.php?token=".$token."\">AKTYWUJ KONTO</a></b>";
            $message .= "<br>W razie jakichkolwiek problemów z aktywacją lub zalogowaniem na konto prosimy o kontakt na adres e-mail: dzieciaki@ulinia8.pl<br />";
            $message = wordwrap($message,70);                    
            $headers = "From:".$emailFrom." \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html\r\n";
            try {
                $sendmail = mail($emailTo, $subject, $message, $headers);
            } catch (Exception $e) {
                $_SESSION['e_reg_email'] .= "</br>Exception: ".$e->getMessage()."</br>";    
            }
            if(!$sendmail){
                $_SESSION['e_reg_email'] .= " ...ale nie udało się wysłać wiadomości z linkiem do aktywacji :/!</br>";    
            } else {
                header('location: newAccPending.php?email='.$_SESSION['fill_email']);
                exit();
            }
        }     
        header('Location: index.php');
        exit();
    }
    
    
}
?>