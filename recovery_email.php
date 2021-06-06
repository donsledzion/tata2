<?php
session_start();
require_once "database.php";
if (isset($_SESSION['tata_logged_ID'])){
	header ('Location: index.php') ;
	exit();
} else if(isset($_POST['rec_email'])){
    $recovery_OK = true;
    $_SESSION['e_rec_email'] = "Przesłano formularz!" ;
    if(empty($_POST['rec_email'])){
        $recovery_OK = false;
        $_SESSION['e_rec_email'] .= " ...ale pusty :/" ;
        
    } else {
        $email = filter_input(INPUT_POST, 'rec_email');
        try {
            $emailQuery = $db->prepare('SELECT * FROM Users WHERE Email=?');
            $emailQuery->execute([$email]);
            $countEmail=$emailQuery->rowCount();        
        } catch (Exception $e){
            $recovery_OK = false;
            $_SESSION['e_rec_email'] .= " ...ale coś się wywaliło przy szukaniu w bazie :/</br>".$e->getMessage()."</br>" ;
        }
        if($countEmail<=0){
            $recovery_OK = false;
            $_SESSION['e_rec_email'] .= " ...ale nie znaleziono żadnego konta skojarzonego z takim adresem :/";
        } else {
            $_SESSION['e_rec_email'] .= " </br> znaleziono pasujących adresów: ".$countEmail."</br>";           
            
            if($recovery_OK==true){
                // Obtaining token
                $token = bin2hex(random_bytes(50));
                                
                // Obtaining token expiration date (24 hours since request)
                $tomorrow = new DateTime();
                $tomorrow->modify('+1 Day');
                $expiration = $tomorrow->format('Y-m-d H:i:s');                
                                
                // Insertion into database
                try {
                    $assignTokenQuery = $db->prepare('INSERT INTO PasswordReset VALUES(:IDRequest, :Email, :Token, :Expiration)');
                    $assignTokenQuery->bindValue(':IDRequest', null);
                    $assignTokenQuery->bindValue(':Email', $email, PDO::PARAM_STR);
                    $assignTokenQuery->bindValue(':Token', $token);
                    $assignTokenQuery->bindValue(':Expiration', $expiration);
                    $assignTokenQuery->execute();
                } catch (Exception $e) {
                    $recovery_OK = false;
                    $_SESSION['e_rec_email'] .= " ...wystąpił błąd podczas próby zresetowania hasła. Spróbuj ponownie lub skontaktuj się z administratorem!</br>";           
                    $_SESSION['e_rec_email'] .= $e->getMessage();
                }
                if($recovery_OK==true){
                    // If insertion of token succeed - sending email with token
                    
                    $emailFrom = "noreply@ulinia8.pl";
                    $emailTo = $email;
                    $subject = "Prośba zresetowania hasła \"Tata, a Marcin powiedział...\"" ;
                    $message = "<h6>Cześć!</h6>";
                    $message .= "Otrzymaliśmy prośbę zresetowania hasła do Twojego konta w serwisie <i>\"Tata, a Marcin powiedział...\"</i>";
                    $message .= "Jeśli ta prośba nie pochodzi od Ciebie to zignoruj tą wiadomość lub daj znać administracji serwisu.";
                    $message .= "<br>Jeśli jednak to Twoja prośba to kliknij w poniższy link aby przenieść się do formularza tworzenia nowego hasła.";
                    $message .= "<br><b><a href=\"http://sledzislaw.usermd.net/dzieciaki/new_password.php?token=".$token."\">RESET HASŁA</a></b>";
                    $message .= "<br>W razie jakichkolwiek problemów ze zmianą hasła prosimy o kontakt na adres e-mail: dzieciaki@ulinia8.pl<br>";
                    $message = wordwrap($message,70);                    
                    $headers = "From:".$emailFrom." \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html\r\n";
                    try {
                        $sendmail = mail($emailTo, $subject, $message, $headers);
                    } catch (Exception $e) {
                        $_SESSION['e_rec_email'] .= "</br>Exception: ".$e->getMessage()."</br>";    
                    }
                    if(!$sendmail){
                        $_SESSION['e_rec_email'] .= " ...ale nie udało się wysłać wiadomości z linkiem do resetu :/!</br>";    
                    } else {
                        header('location: pending.php?email='.$email);
                    }
                }
            }
            
        }
    }
}   
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zresetuj hasło do konta</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="container">

        <main>
            <article>
                <form action="recovery_email.php" method="post">
                    <h6>Podaj adres email użyty przy rejestracji konta</h3>
                    <input type="email" name="rec_email" placeholder="email" onfocus="this.placeholder=''" onblur="this.placeholder='email'">
                    <div class="error"><?php
                    if(isset($_SESSION['e_rec_email'])){
                        echo $_SESSION['e_rec_email'];
                        unset($_SESSION['e_rec_email']);
                    }
                    ?></div>
                    <input type="submit" value="resetuj hasło!">
                    
                </form>
                
            </article>
        </main>

    </div>
</body>
</html>
