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
 * 
 */	
    $observersQuery = $db->prepare('SELECT  Users.First_name, '
                                            . 'Users.Last_name '
                                            . 'FROM AccountsUsersPermissions '
                                            . 'INNER JOIN Users ON Users.IDUser = AccountsUsersPermissions.IDUser '
                                            . 'INNER JOIN Accounts ON Accounts.IDAccount = AccountsUsersPermissions.IDAccount '
                                            . 'WHERE Accounts.IDAccount=:IDAccount');
    $observersQuery->bindValue(':IDAccount',$_SESSION['IDAccount']);
    $observersQuery->execute();
    $observers = $observersQuery->fetchAll();
    
    $observedQuery = $db->prepare('SELECT Accounts.Name '
                                . 'FROM AccountsUsersPermissions '
                                . 'INNER JOIN Accounts ON AccountsUsersPermissions.IDAccount = Accounts.IDAccount '
                                . 'INNER JOIN Users ON Users.IDUser = AccountsUsersPermissions.IDUser '
                                . 'WHERE Users.IDUser=:IDUser');
    $observedQuery->bindValue(':IDUser',$_SESSION['IDUser']);
    $observedQuery->execute();
    $observed = $observedQuery->fetchAll();
    
    
    
    
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

            <div class="navButton" style="text-align:center;">MOI SWOJACY</div>

            <div class="relationsNav">
                <!--
                <a href="#"><div class="relationsNavButton" style="background-color:#7FFFD4; margin-bottom:30px;">MOI SWOJACY<i class="fa-people"></i></div></a>
                <a href="invite.php"><div class="relationsNavButton" style="background-color:#37b93d; margin-bottom:30px;">ZAPROŚ ZNAJOMYCH<i class="icon-user-plus"></i></div></a>
                  !-->
                <div class="relationsNavButton" style="background-color:#37b93d; margin-bottom:15px; margin-top:15px; font-size:22px; width:320px; margin-left:auto; margin-right:auto;">KTO ŚLEDZI NASZE KONTO:</div>
                <?php 
                    foreach ($observers as $observer) {
                        echo "<div class=\"observerButton\">".$observer['First_name']." ".$observer['Last_name']."</div>";
                    }                
                ?>		
                <div class="relationsNavButton" style="background-color:#37b93d; margin-bottom:15px; margin-top:15px; font-size:22px; width:320px; margin-left:auto; margin-right:auto;">A KOGO JA ŚLEDZĘ:</div>
                <?php 
                    foreach ($observed as $observ) {
                        echo "<div class=\"observerButton\">".$observ['Name']."</div>";
                    }                
                ?>		
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