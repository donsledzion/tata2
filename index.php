<?php
session_start();
require_once "database.php";
unset($_FILES["picture"]);
if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Sentencje i aforyzmy naszych "Pociech"</title>
    <meta name="description" content="cytaty, bombelki, Julek, aforyzmy, bombelek">
    <meta name="keywords" content="cytaty, Julek, bombelek, bombelki">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
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
        
	
	<!---------------------------------------------------------------------------------------------------->
	<footer>
		<div class="footContainer">
			Copyrights All Rights Reserved - Adam Chojaczyk <?php echo date("Y")?> r.
		</div>
	</footer>
<!---------------------------------------------------------------------------------------------------->
</body>
</html>