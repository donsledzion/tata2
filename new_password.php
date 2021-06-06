<?php
session_start();
require_once "database.php";
include 'pass_check.php'; // include file with password check function

if (isset($_SESSION['tata_logged_ID'])){
	header ('Location: index.php') ;
	exit();
} else if(isset($_POST['pass'])){
           
    
        
    
    if(!empty($_POST['pass'])) { // check if password is given
        
        $pass_OK = true;
        
    try{
        $checkTokenQuery = $db->prepare('SELECT Email FROM PasswordReset WHERE Token=?');
        $checkTokenQuery->execute([$_SESSION['token']]);
        $emails = $checkTokenQuery->fetchAll();
        $emailsCount = $checkTokenQuery->rowCount();
        if($emailsCount<=0){            
            $pass_OK = false;
            $_SESSION['e_general'] = "token nieprawidłowy lub utracił ważność!";
            header('Location: user.php');
            exit();
        } 
        $email = $emails[0][0];
        $_SESSION['e_verPass'] = "znaleziony token to: ".$_SESSION['token']."</br>";
        $_SESSION['e_verPass'] = "znaleziony e-mail to: ".$email."</br>";
    } catch (Exception $e){
        $_SESSION['e_verPass'] = "coś się wydupczyło przy szukaniu maila z tokenem | ".$e->getMessage()."</br>";
    }
        
        
        $password = filter_input(INPUT_POST, 'pass');
        if(!pass_check($password)){            
            //$_SESSION['e_pass'] = pass_check($password);
            $pass_OK = false;
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
                $pass_OK = false;
            }
        }        
    } else { // if password is not entered
        $_SESSION['e_pass'] = "podaj hasło";
            $pass_OK = false;
    }
    if($pass_OK==true){ // if new password is ok we can insert it into database
        $_SESSION['e_pass'] = "jest gitara można zmieniać hasło ;)";
        
        try{
            $updatePasswordQuery = $db->prepare('UPDATE Users SET Password=:Password WHERE Email=:Email');
            $updatePasswordQuery->bindValue(':Password',$pass_hashed);
            $updatePasswordQuery->bindValue(':Email',$email,PDO::PARAM_STR);
            $updatePasswordQuery->execute();
        } catch(Exception $e){
            $_SESSION['e_pass'] = "Błąd zapisu nowego hasła | ".$e->getMessage();
        }
        try {
            $deleteTokensQuery = $db->prepare('DELETE FROM PasswordReset WHERE Email=:Email');
            $deleteTokensQuery->bindValue(':Email',$email, PDO::PARAM_STR);
            $deleteTokensQuery->execute();
        } catch (Exception $e) {
            $_SESSION['e_pass'] = "Błąd kasowania tokenów | ".$e->getMessage();
        }
        
        
        $_SESSION['msg_general'] = "hasło zmienione pawidłowo (zapamiętaj ;) )!";
        header('Location: user.php');
    }
}
$_SESSION['token'] = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Załóż nowe konto!</title>
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
    <header>
		<div class="headContainer" style="font-size:18px;">
			<p>
                            <b>Ustaw nowe hasło do konta</b></br>
			Uwaga:</p>
                        <p>Pmiajętaj, że hasło musi mieć:</p>
                        <ul style="text-align:left">
                            <li> długośc od 8 do 20 znaków </li>
                            <li> co najmniej jedną wielką literę</li>
                            <li> co najmniej jedną cyfrę</li>
                        </ul>
		</div>
	</header>
    
    <div class="container">

        <main>
            <article>
                <form method="post" action="new_password.php">                    
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ PASSWORD ------------------------------------------------>
<!---------------------------------------------------------------------------------------------------->					                    
                    
                    <input type="password" name="pass" id="pass" placeholder="haslo" onfocus="this.placeholder=''" onblur="this.placeholder='hasło'">
                    <div>
                        <?php
                        if(isset($_SESSION['e_pass'])) {
                            echo "<div class=\"error\"><p>".$_SESSION['e_pass']."</p></div>" ;
                            unset($_SESSION['e_pass']);
                        }
                        ?>
                    </div>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ PASSWORD REENTER ---------------------------------------->
<!---------------------------------------------------------------------------------------------------->					                                        
                    
                    <input type="password" name="verPass" id="verPass" placeholder="powtórz hasło" onfocus="this.placeholder=''" onblur="this.placeholder='powtórz hasło'">
                    <div>
                        <?php
                        if(isset($_SESSION['e_verPass'])) {
                            echo "<div class=\"error\"><p>".$_SESSION['e_verPass']."</p></div>" ;
                            unset($_SESSION['e_verPass']);
                        }
                        ?>
                    </div>

<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ CLOSING FORM -------------------------------------------->
<!---------------------------------------------------------------------------------------------------->	                   
                    
                    <input type="text" value="register" name="register" hidden>
                    <input type="submit" value="zarejestruj" name="submit">
                    <!--<div style="color:green; font-size:18px;">
                    <?//php if(isset($_SESSION['wszystko_gra'])){ echo $_SESSION['wszystko_gra'];} ?>
                    </div>!-->
                    
                    
                </form>
                
            </article>
        </main>

    </div>
</body>
</html>
