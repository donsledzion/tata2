<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}
/*
 * TUTAJ BĘDZIE ZAPYTANIE DOTYCZĄCE ISTNIEJĄCYCH RELACJI:
 *  - KOGO OBSERWUJĘ
 *  - KTO MNIE OBSERWUJE
	$kidsQuery = $db->prepare('SELECT * FROM Kids WHERE IDAccount=? ORDER BY Birth_date ASC');
        $kidsQuery -> execute([$_SESSION['IDAccount']]);
	$kids = $kidsQuery->fetchAll();
	$oldestKid = $kids[0][2];
	$firstBirth = $kids[0][3];
*/
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
    <style>
        label {
            font-size: 20px;
        }
    </style>
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
            <p style="font-size:18px; padding:25px 30px 10px 30px; margin:5px; text-align:center;">Możesz pomóc zapraszanej osobie i wstępnie wypełnić jej formularz rejestracyjny.</p>
            <form action="send_invitation.php" method="post">                
                <input type="email" name="invite_email" id="invite_email" value="<?php echo $_SESSION['fill_email'];?>" disabled />
                <div class="error"><?php if(isset($_SESSION['e_email'])){ echo $_SESSION['e_email']; unset($_SESSION['e_email']); } ?></div>
                
                <input type="text" name="invite_first_name" id="invite_first_name" placeholder="imię" onfocus="this.placeholder=''" onblur="this.placeholder='imię'" />
                <div class="error">Obecnie musisz podać imię zapraszanej osoby. <?php if(isset($_SESSION['e_first_name'])){ echo $_SESSION['e_first_name']; unset($_SESSION['e_first_name']); } ?></div>
                
                <input type="text" name="invite_last_name" id="invite_last_name" placeholder="nazwisko" onfocus="this.placeholder=''" onblur="this.placeholder='nazwisko'" />
                <div class="error">Obecnie musisz podać nazwisko zapraszanej osoby.<?php if(isset($_SESSION['e_last_name'])){ echo $_SESSION['e_last_name']; unset($_SESSION['e_last_name']); } ?></div>
                <input type="checkbox" name="invite_permissions" id="invite_permissions" value="parent">
                <label for="invite_permissions"><b>"Współrodzic"</b> <p style="display:inline-block; font-size:12px; vertical-align: center; color:red;">(Zaznacz jeśli zapraszana osoba ma mieć takie same uprawnienia do konta co Ty!)</p></label><br /><br />
                
                <input type="submit" name="sendInvitation" id="sendInvitation" value="wyślij zaproszenie">
            </form>
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