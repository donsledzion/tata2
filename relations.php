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
 * * 
 * 
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

            <div class="navButton" style="text-align:center;">SAMI SWOI</div>

            <div class="relationsNav">

                <a href="my_relations.php"><div class="relationsNavButton" style="background-color:#556B2F; margin-bottom:30px;">MOI SWOJACY<i class="fa-people"></i></div></a>
                <a href="my_invitations.php"><div class="relationsNavButton" style="background-color:#556B2F; margin-bottom:30px;">ZAPROSZENIA<i class="fa-people"></i></div></a>
                <a href="invite.php"><div class="relationsNavButton" style="background-color:#37b93d; margin-bottom:30px;">ZAPROŚ ZNAJOMYCH<i class="icon-user-plus"></i></div></a>
                  
                
            </div>

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