<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['tata_logged_ID'])){
	header('Location: user.php') ;
	exit();
}

	$kidsQuery = $db->prepare('SELECT * FROM Kids WHERE IDAccount=? ORDER BY Birth_date ASC');
        $kidsQuery -> execute([$_SESSION['IDAccount']]);
	$kids = $kidsQuery->fetchAll();
	$oldestKid = $kids[0][2];
	$firstBirth = $kids[0][3];

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Sentencje i aforyzmy naszych pociech...</title>
    <meta name="description" content="cytaty, bombelki, Julek, aforyzmy, bombelek">
    <meta name="keywords" content="cytaty, Julek, bombelek, bombelki">
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
        <div class="kidsContainer">

            <div class="navButton" style="text-align:center;">BOMBELKI</div>

            <div class="kidsNav">

                <a href="addKid.php"><div class="kidNavButton" style="background-color:#37b93d; margin-bottom:30px;">DODAJ BOMBELKA <i class="icon-user-plus"></i></div></a>

                <?php foreach ($kids as $kid)
                {
                    echo "<div class=\"kidNavButton\">".strtoupper($kid['Dim_name'])."	<i class=\"icon-" ;
                    if ($kid['Gender']=='female') { echo "fe" ; }
                    echo "male\"></i></div>" ;
                    echo "<div class=\"kidContainer\">" ;
                    echo "<div class=\"kidPicture\">" ;
                    echo "<a class=\"example-image-link\" href=\"pics\\".$_SESSION['IDAccount']."\\768\\".$kid['Default_pic']."\" data-lightbox=\"example-1\"><img class=\"example-image\" src=\"pics\\".$_SESSION['IDAccount']."\\320\\".$kid['Default_pic']."\"  alt=\"image-1\" width=\"200\" /></a>";
                    
                    
                    echo "<div class=\"rotation_buttons\" style=\"width:150px; margin-left:auto; margin-right:auto; text-align:center;\">";
                    
                    echo "<div style=\"float:left;\">";
                        echo "<form action=\"picture_rotate.php\" method=\"POST\">";
                        echo "<input type=\"number\" name=\"angle\" value=\"90\" hidden>";
                        echo "<input type=\"text\" name=\"IDAccount\" value=\"".$_SESSION['IDAccount']."\" hidden>";
                        echo "<input type=\"text\" name=\"picture_name\" value=\"".$kid['Default_pic']."\" hidden>";
                        echo "<button type=\"submit\"><i class=\"fa fa-undo\" aria-hidden=\"true\"></i></button>";
                        echo "</form>";
                        echo "</div>";
                        
                        echo "<div style=\"float:left; font-size:14px; margin-top:15px; margin-left:20px; margin-right:20px;\">Obrót</div>";
                        echo "<div style=\"float:left;\">";
                        echo "<form action=\"picture_rotate.php\" method=\"POST\">";
                        echo "<input type=\"text\" name=\"IDAccount\" value=\"".$_SESSION['IDAccount']."\" hidden>";
                        echo "<input type=\"text\" name=\"picture_name\" value=\"".$kid['Default_pic']."\" hidden>";
                        echo "<input type=\"number\" name=\"angle\" value=\"270\" hidden>";
                        echo "<button type=\"submit\"><i class=\"fa fa-repeat\" aria-hidden=\"true\"></i></button>";
                        echo "</form>";
                    echo "</div>";
                    echo "<div style=\"clear:both;\"></div>";
                    
                    echo "</div>";
                    
                    echo "</div>" ;
                    echo "<div class=\"kidInfo\">";
                            echo "<u>NARODZINY:</u></br>".$kid['Birth_date']."</br></br>";
                            echo "INFO:</br>".$kid['About']."</br>";
                    echo "</div>" ;
                    echo "<div style=\"clear:both;\"></div>";
                    echo "</div>" ;

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