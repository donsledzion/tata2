<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['tata_logged_ID']))
{
	header ('Location: index.php') ;
	exit();
}

if($_SESSION['someAdmin']==false)
	{
		$_SESSION['e_edit'] = 'Ups! Nie masz uprawnień do edycji!' ;
		header ('Location: showKids.php');
		exit();
	}

if (!isset($_SESSION['redirect']))
{
	// jeśli nie zostałem odesłany to należy wyczyścić 
	// pola autouzupełniania formularza
	unset($_SESSION['fill_quote']);
	unset($_SESSION['fill_quoted']);
	unset($_SESSION['fill_picture']);
	unset($_SESSION['fill_date']);	
	unset($_SESSION['redirect']);
}

$kidsQuery = $db->prepare('SELECT * FROM Kids WHERE IDAccount=? ORDER BY Birth_date ASC');
$kidsQuery ->execute([$_SESSION['IDAccount']]);
$kids = $kidsQuery->fetchAll();

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Dodaj bombelka</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="css/main.css" type="text/css">
    <link rel="stylesheet" href="css/fontello.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body>
	<header>
        <div class="headContainer">            
            Witaj <?echo(ucfirst($_SESSION['tata_logged_ID']))?>            
        </div>
        </header>
<!---------------------------------------------------------------------------------------------------->
<!---------------------------NAWIGACJA---------------------------------------------------------------->
<!---------------------------------------------------------------------------------------------------->
	<nav>
		<?php include 'navigation.php' ?>
	</nav>
<!---------------------------------------------------------------------------------------------------->
<!----------------------------  TREŚĆ ---------------------------------------------------------------->
<!---------------------------------------------------------------------------------------------------->
	<main>
            <article>
                <div class="container">
			
                    <div class="navButton" style="text-align:center;">BOMBELKI</div>
			
                    <div class="kidsNav">
                        <div class="kidNavButton" style="background-color:#37b93d; margin-bottom:30px;">DODAJ BOMBELKA <i class="icon-user-plus"></i></div>
                    </div>
<!--------------------------------------OTWARCIE FORMULARZA------------------------------------------->

                        <form action="saveNewKid.php" method="post" id="addKid" name="addKid" enctype="multipart/form-data">
                        <input type="hidden" name="cameFrom" id="cameFrom" value="addKid.php">
				
<!---------------------------------------------------------------------------------------------------->
<!----------------------------------------- PŁEĆ DZIECKA --------------------------------------------->
<!---------------------------------------------------------------------------------------------------->
                        <div style="font-size:22px; text-align:center;">
                        <fieldset form="addKid">
                            <p align="center">Wybierz płeć dziecka</p>
                                <input type="radio" id="male" name="newKidGender" value="male" <?php if($_SESSION['fill_newKidGender']=="male") { echo "checked";} ?>>
                                <label for="male">chłopiec <i class="icon-male"></i></label>
                                <input type="radio" id="female" name="newKidGender" value="female" <?php if($_SESSION['fill_newKidGender']=="female") { echo "checked";} ?>>
                                <label for="female">dziewczynka <i class="icon-female"></i></label><br>
                                <?php if(isset($_SESSION['e_fill_gender']))
                                    {
                                        echo	"<div class=\"error\">
                                                <p>".$_SESSION['e_fill_gender']."</p>
                                            </div>" ;
                                        unset($_SESSION['e_fill_gender']);
                                    }
                                ?>
                                
                        </fieldset>
                        </div>

<!---------------------------------------------------------------------------------------------------->
<!----------------------------------------- NAZWISKO DZIECKA --------------------------------------------->
<!---------------------------------------------------------------------------------------------------->

                        <input type="text" name="kidLastName" id="kidLastName" 
                            <?php if(!isset($_SESSION['fill_kidLastName'])) {
                                echo "placeholder=\"nazwisko\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='nazwisko'\">";                                
                            } else {
                                echo "value=\"".$_SESSION['fill_kidLastName']."\"";
                                echo ">";
                                unset($_SESSION['fill_kidLastName']);
                            }   
                            if(isset($_SESSION['e_fill_last_name']))
                                {
                                    echo	"<div class=\"error\">
                                            <p>".$_SESSION['e_fill_last_name']."</p>
                                        </div>" ;
                                    unset($_SESSION['e_fill_last_name']);
                                }
                            ?>
<!---------------------------------------------------------------------------------------------------->
<!----------------------------------------- IMIĘ DZIECKA --------------------------------------------->
<!---------------------------------------------------------------------------------------------------->

                        <input type="text" name="kidName" id="kidName" 
                            <?php if(!isset($_SESSION['fill_kidName'])||($_SESSION['fill_kidName']=="")) {
                                echo "placeholder=\"imię\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='imię'\">";                                
                            } else {
                                echo "value=\"".$_SESSION['fill_kidName']."\"";
                                echo ">";
                                unset($_SESSION['fill_kidName']);
                            }
                            if(isset($_SESSION['e_fill_name']))
                                {
                                    echo	"<div class=\"error\">
                                            <p>".$_SESSION['e_fill_name']."</p>
                                        </div>" ;
                                    unset($_SESSION['e_fill_name']);
                                }
                            ?>
<!---------------------------------------------------------------------------------------------------->
<!---------------------------------- ZDROBNIENIE IMIENIA DZIECKA ------------------------------------->
<!---------------------------------------------------------------------------------------------------->

                        <input type="text" name="kidDimName" id="kidDimName" <?php if(!isset($_SESSION['fill_kidDimName'])||($_SESSION['fill_kidDimName']=="")) {
                                echo "placeholder=\"zdrobnienie\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='zdrobnienie'\">";                                
                            } else {
                                echo "value=\"".$_SESSION['fill_kidDimName']."\"";
                                echo ">";
                                unset($_SESSION['fill_kidDimName']);
                            }
                            if(isset($_SESSION['e_fill_dim_name']))
                                {
                                    echo	"<div class=\"error\">
                                            <p>".$_SESSION['e_fill_dim_name']."</p>
                                        </div>" ;
                                    unset($_SESSION['e_fill_dim_name']);
                                }
                            ?>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ DATA NARODZIN ------------------------------------------->
<!---------------------------------------------------------------------------------------------------->
					
                        <?php
                        echo "<input type=\"date\" name=\"kidBirth\" id=\"kidBirth\" style=\"width:350px; float:left\" max=\"".date("Y-m-d")."\" value=\"" ;
                            if (isset($_SESSION['fill_birthday']))
                            {
                                    echo $_SESSION['fill_birthday'] ;
                                    unset($_SESSION['fill_birthday']);
                            }
                            else
                            {
                                    echo date("Y-m-d");
                            }
                        echo "\">" ;
                        if(isset($_SESSION['e_date']))
                        {
                            echo 	"<div class=\"error\">
                                                    <p>".$_SESSION['e_date']."</p>
                                            </div>" ;
                            unset($_SESSION['e_date']);
                        }
                        ?>
                        <label for="kidBirth" style="padding-top:25px; margin-left:20px; float:left;"><b> <= Data urodzenia</b></label>
												
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ KRÓTKIE INFO -------------------------------------------->
<!---------------------------------------------------------------------------------------------------->
				
                        <textarea name="kidInfo" id="kidInfo" wrap="soft" rows="3" form="addKid"
                            <?php if(!isset($_SESSION['fill_kidInfo'])||($_SESSION['fill_kidInfo']=="")) {
                                echo "placeholder=\"...podaj krótkie info o dziecku...\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='...podaj krótkie info o dziecku...'\">";                                
                            } else {
                                echo ">";
                                
                            }
                                if (isset($_SESSION['fill_kidInfo'])&&($_SESSION['fill_kidInfo']!="")) {
                                        echo $_SESSION['fill_kidInfo'] ;
                                        unset($_SESSION['fill_kidInfo']);
                                }
                                        ?></textarea>
                        <?php if(isset($_SESSION['e_fill_info']))
                            {
                                    echo 	"<div class=\"error\">
                                                            <p>".$_SESSION['e_fill_info']."</p>
                                                    </div>" ;
                                    unset($_SESSION['e_fill_info']);
                            }
                        ?>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ ZDJĘCIE ------------------------------------------------->
<!---------------------------------------------------------------------------------------------------->
                        <input type="file" name="picture" id="picture" placeholder="fotka" onfocus="this.placeholder=''" onblur="this.placeholder='fotka'">
                        <div>
                        <?
                        if(isset($_SESSION['e_upload']))
                        {
                                echo 	"<div class=\"error\">
                                                        <p>".$_SESSION['e_upload']."</p>
                                                </div>" ;
                                unset($_SESSION['e_upload']);
                        }
                        ?>
                        </div>

                        <input type="submit" value="Dodaj dziecko!" name="submit">
															
                    </form>

<!------------------------------------------------------------------------------------------------------------>
<!------------------------------------------ ZAMKNIĘCIE FORMULARZA ------------------------------------------->
<!------------------------------------------------------------------------------------------------------------>

			</div>
		</article>
		
		<article>
			<div class="kidsContainer">							
				<div class="kidsNav">				
					<? foreach ($kids as $kid)
					{
                                            echo "<div class=\"kidNavButton\">".strtoupper($kid['kiduno'])."	<i class=\"icon-" ;
                                            if ($kid['sex']=='female') echo "fe" ;
                                            echo "male\"></i></div>" ;
                                            echo "<div class=\"kidContainer\">" ;
                                            echo "<div class=\"kidPicture\">" ;
                                            echo "<a class=\"example-image-link\" href=\"pics\\".$_SESSION['IDAccount']."\\768\\".$kid['Default_pic']."\" data-lightbox=\"example-1\"><img class=\"example-image\" src=\"pics\\".$_SESSION['IDAccount']."\\320\\".$kid['Default_pic']."\"  alt=\"image-1\" width=\"200\" /></a>";
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
		</article>
	</main>
	
	<footer>
		<div class="footContainer">
			Copyrights All Rights Reserved - Adam Chojaczyk <?php echo date("Y")?> r.
		</div>
	</footer>
</body>
</html>
