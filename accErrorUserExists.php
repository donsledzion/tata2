<?php
session_start();
require_once "database.php";
if (isset($_SESSION['tata_logged_ID'])){
	header ('Location: index.php') ;
	exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Nie można aktywować konta!</title>
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

	<form action="logout.php" method="post" style="text-align: center;">
		<p>
			Olaboga! Niepowodzenie! Już istnieje użytkownik o adresie e-mail <?php echo $_GET['email']; ?> <br />                        
		</p>
                <p>
                    Jeśli nie pamiętasz hasła do serwisu to skorzystaj z opcji odzyskiwania hasła (na stronie logowania).
                </p>
            <input type="submit" value="powrót na stronę logowania">
            
	</form>
		
</body>
</html>