<?php
if (!isset($_SESSION['tata_logged_Email']))
{
	header('Location: user.php') ;
	exit();
}
echo "<div class=\"navContainer\" style=\"margin-top:20px;\">";
//echo		    "<a href=\"index.php\"><div class=\"navButton\">STRONA GŁÓWNA</div></a>";
echo "<div class=\"navButton\"><a href=\"showQuotes.php\">PRZEGLĄDAJ CYTATY</a></div>";
    if ($_SESSION['someAdmin']==true){
        echo "<div class=\"navButton\"><a href=\"showKids.php\">BOMBELKI</a></div>";			
        echo "<a href=\"addQuote.php\"><div class=\"navButton\">DODAJ CYTAT</div></a>";
    }
echo "<div class=\"navButton\"><a href=\"relations.php\">SAMI SWOI</a></div>";
echo "<div class=\"navButton\"><a href=\"logout.php\">WYLOGUJ</a></div>";
echo "<div style=\"clear:both;\"></div>";
echo "</div>";
?>