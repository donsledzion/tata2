<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Sentencje i aforyzmy naszych pociech...</title>
    <meta name="description" content="cytaty, bombelki, aforyzmy, bombelek">
    <meta name="keywords" content="cytaty, bombelek, bombelki">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="css/main.css" type="text/css">
	<link href="css/lightbox.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/fontello.css"  type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body>
    
    <!---------------------------------------------------------------------------------------------------->
    <header>
        <div class="headContainer">            
            Witaj <?echo(ucfirst($_SESSION['tata_logged_ID']))?>            
        </div>
    </header>
<!---------------------------------------------------------------------------------------------------->	

    <nav>
		<?php include 'navigation.php' ?>
    </nav>
    <main>
        <div class="relationsContainer">

            <div class="navButton" style="text-align:center;">ZAPROŚ ZNAJOMYCH</div>
            <div class="error" style="font-size:16px; text-align: center;"><b>Uwaga</b> Mechanizm kuleje jeszcze, ale już działa!</div>
            
            <?php if(isset($_SESSION['invited_ID'])){
                echo "<div class=\"invitation_msg\">".$_SESSION['msg_email']."</div>";
                echo "<form action=\"create_inner_invitation.php\" method=\"post\">";
                echo "<input type=\"number\" name=\"invitedID\" id=\"invitedID\" value=".$_SESSION['invited_ID']." hidden>" ;                
                echo "<label class=\"parent_check\"> \"współrodzic\""
                . "<input type=\"checkbox\" name=\"permission\" class=\"parent_checkbox\" id=\"permission\" value=\"1\"  />"
                        . "<span class=\"parent_checkmark\"></span></label>";
                echo "<div style=\"width:600px; margin-left:auto; margin-right:auto;\">";
                echo "<button type=\"submit\" class=\"invitation_accept\" id=\"invitation_accept\">ZAPROŚ</button>";
                echo "<button type=\"submit\" class=\"invitation_decline\" id=\"invitation_decline\" form=\"goBack\">ANULUJ</button>";
                echo "</div>" ;
                echo "</form>" ;
                unset($_SESSION['invited_ID']);
            } else {
            echo "<p style=\"font-size:18px; padding:25px 30px 10px 30px; margin:5px; text-align:center;\">Podaj adres e-mail zapraszanej osoby</p>";
            echo "<form action=\"check_invitation.php\" method=\"post\">" ;
            echo "<input type=\"email\" name=\"email\" id=\"email\" placeholder=\"e-mail\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='e-mail' /> ";
            echo "<div class=\"error\">" ;
                    if(isset($_SESSION['e_email'])){
                        echo $_SESSION['e_email'];
                        unset($_SESSION['e_email']);
                    }
                    if(isset($_SESSION['e_invitation'])){
                        echo $_SESSION['e_invitation'];
                        unset($_SESSION['e_invitation']);
                    }
            echo "<input type=\"submit\" name=\"searchEmail\" id=\"searchEmail\" value=\"sprawdź e-mail\">";        
            echo "</div>";
            
            echo "</form>";
            if(isset($_SESSION['invitation_msg'])){
                        echo "<div style=\"font-size:16px; color:green; text-align:center;\">".$_SESSION['invitation_msg']."</div>";
                        unset($_SESSION['invitation_msg']);
                    }
            }?>
            <form id="goBack" action="invite.php"></form>
        </div>
    </main>
    <!---------------------------------------------------------------------------------------------------->
    <footer>
            <div class="footContainer">
                Copyrights All Rights Reserved - Adam Chojaczyk <?php echo date("Y")?> r.
            </div>
    </footer>
	
<script src="js/lightbox-plus-jquery.js">
	lightbox.option({
      'alwaysShowNavOnTouchDevices': true
    });
</script>
<!---------------------------------------------------------------------------------------------------->
</body>
</html>