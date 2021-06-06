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
$invitedQuery = $db->prepare('SELECT Invited.First_name, Invited.Last_name, Invited.Email '
        . 'FROM InnerInvitations INNER JOIN Users AS Invited ON Invited.IDUser = InnerInvitations.IDInvited '
        . 'INNER JOIN Users AS Inviting ON Inviting.IDUser = InnerInvitations.IDInviting '
        . 'WHERE Inviting.IDUser=:IDUser;');
$invitedQuery->bindValue(':IDUser',$_SESSION['IDUser']);
$invitedQuery->execute();
$invitesCount = $invitedQuery->rowCount();
if($invitesCount>0){
    $inviteds = $invitedQuery->fetchAll(PDO::FETCH_ASSOC);
}
 
$invitingQuery = $db->prepare('SELECT Inviting.First_name, Inviting.Last_name, Inviting.Email, Accounts.Name '
                            . 'FROM InnerInvitations INNER JOIN Users AS Invited ON Invited.IDUser = InnerInvitations.IDInvited '
                            . 'INNER JOIN Users AS Inviting ON Inviting.IDUser = InnerInvitations.IDInviting '
                            . 'INNER JOIN Accounts ON Accounts.IDAccount = InnerInvitations.IDAccount '
                            . 'WHERE Invited.IDUser=:IDUser');
$invitingQuery->bindValue(':IDUser',$_SESSION['IDUser']);
$invitingQuery->execute();
$invitingsCount = $invitingQuery->rowCount();
if($invitingsCount>0){
    $invitings = $invitingsQuery->fetchAll(PDO::FETCH_ASSOC);
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

            <div class="navButton" style="text-align:center;">ZAPROSZENIA</div>

            <div class="relationsNav">

                <div class="relationsNavButton" style="background-color:#556B2F; margin-bottom:30px;">Kto mnie zaprasza<i class="fa-people"></i></div>
                 <?php
                if($invitingsCount>0){
                    foreach($invitings as $inviting){
                        echo "<div style=\"font-size:22px; text-align:center;\">".$inviting['First_name']." ".$inviting['Last_name']." - ".$invited['Email']." - ".$invited['Email']."</div>" ;
                    }
                }else {
                    echo "<div style=\"font-size:22px; text-align:center;\">Nie masz żadnych zaproszeń</div>" ;
                }
                ?>
                
                
                <div class="relationsNavButton" style="background-color:#556B2F; margin-bottom:30px;">Kogo zapraszam<i class="fa-people"></i></div>
                <?php
                if($invitesCount>0){
                    foreach($inviteds as $invited){
                        echo "<div style=\"font-size:22px; text-align:center;\">".$invited['First_name']." ".$invited['Last_name']." - ".$invited['Email']."</div>" ;
                    }
                }else {
                    echo "<div style=\"font-size:22px; text-align:center;\">Aktualnie nikogo nie zapraszasz</div>" ;
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