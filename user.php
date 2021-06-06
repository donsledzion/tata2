<?php
session_start();

if (isset($_SESSION['tata_logged_ID']))
{
	header ('Location: index.php') ;
	exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zaloguj do panelu użytkownika</title>
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
            <header style="text-align: center;">
                <h4><i>Tata, a Marcin powiedział...</i></h3>
            </header>
            <article>
                <form action="login.php" method="post">
                    <input type="email" name="login" placeholder="login" onfocus="this.placeholder=''" onblur="this.placeholder='login'">
                    <div class="error" style="font-size:18px;"><?php if(isset($_SESSION['e_login'])){                        
                            echo $_SESSION['e_login'];
                            unset($_SESSION['e_login']);
                        } ?>
                    </div>			
                    <input type="password" name="pass" placeholder="haslo" onfocus="this.placeholder=''" onblur="this.placeholder='hasło'">
                    <div class="error" style="font-size:18px;"><?php if(isset($_SESSION['e_pass'])){                        
                            echo $_SESSION['e_pass'];
                            unset($_SESSION['e_pass']);
                        } ?>
                    </div>						
                    <input type="submit" value="Zaloguj się!">
                    
                    <div style="float:left;"><a href="register_account.php"><div class="signInButton">Załóż nowe konto</div></a></div>
                    <div style="float:left; margin-left:30px;"><a href="recovery_email.php"><div class="recoveryButton"  style="background-color:#FF6347;">Nie pamiętasz hasła?</div></a></div>
                    <div style="clear:both;"></div>
                    <div class="error" style="font-size:18px;"><?php if(isset($_SESSION['e_general'])){                        
                        echo $_SESSION['e_general'];
                        unset($_SESSION['e_general']);
                        session_unset();
                    } ?></div>
                    <div class="error" style="font-size:18px; color:green;"><?php if(isset($_SESSION['msg_general'])){ 
                        echo $_SESSION['msg_general'];
                        unset($_SESSION['msg_general']);
                        session_unset();
                    } ?></div>
                </form>
                
            </article>
        </main>

    </div>
</body>
</html>
