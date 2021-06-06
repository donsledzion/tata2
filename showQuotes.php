<?php
session_start();
require_once "database.php";
unset($_FILES);
if (!isset($_SESSION['tata_logged_Email']))
{
	header('Location: user.php') ;
	exit();
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Sentencje i aforyzmy naszych pociech...</title>
    <meta name="description" content="cytaty, bombelki, Julek, Hanka, aforyzmy, bombelek">
    <meta name="keywords" content="cytaty, Julek, Hanka, bombelek, bombelki">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">    

    <link rel="stylesheet" href="css/main.css">
	<link href="css/lightbox.css" rel="stylesheet" />
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
	
	<main>
	
	<?php
		$loggedEmail = $_SESSION['tata_logged_Email'] ;
	   //$quotesQuery = $db->query('SELECT * FROM Post ORDER BY quote_date DESC');
		$quotesQuery = $db->prepare(' SELECT Posts.Datestamp AS \'datestamp\', '
		.'Posts.IDPost AS \'id\', '
		.'Posts.Quote_date AS \'quote_date\', '
		.'Posts.Sentence AS \'sentence\', '
		.'Posts.Picture AS \'picture\', '
		.'Kids.First_name AS \'imie\', '
                .'Kids.Last_name AS \'nazwisko\', '
		.'Kids.Dim_name AS \'bombelek\', '
		.'Accounts.Name, '
		.'Accounts.IDAccount AS \'accountID\', '
		.'Permissions.P_read, '
		.'Permissions.P_write '
		.'FROM Users '
		.'INNER JOIN AccountsUsersPermissions ON AccountsUsersPermissions.IDUser = Users.IDUser '
		.'INNER JOIN Accounts ON ( AccountsUsersPermissions.IDUser = Users.IDUser AND AccountsUsersPermissions.IDAccount = Accounts.IDAccount) '
		.'INNER JOIN Permissions ON ( AccountsUsersPermissions.IDUser = Users.IDUser AND AccountsUsersPermissions.IDAccount = Accounts.IDAccount AND AccountsUsersPermissions.IDPermissions = Permissions.IDPermissions) '
		.'INNER JOIN Kids ON Kids.IDAccount = Accounts.IDAccount '
		.'INNER JOIN Posts ON Kids.IDKid = Posts.IDKid '
		.'WHERE Users.Email=:Email '
		.'ORDER BY Posts.Quote_date DESC');
		$quotesQuery->bindValue(':Email', $loggedEmail, PDO::PARAM_STR);
		$quotesQuery->execute();
		$quotes = $quotesQuery->fetchAll();
		   
		   //print_r($records);	
		foreach($quotes as $quote)
		{
                        $showDate = date("d-m-Y",strtotime($quote['quote_date']));
			echo "<div class=\"post\">
				
				<div class=\"quote\"><blockquote>".htmlentities($quote['sentence'])."</blockquote>"
                                . "<p class=\"quote_date\"><abbr title=\"{$quote['imie']} {$quote['nazwisko']}\">{$quote['bombelek']} </abbr> {$showDate}</p></div>
				
				<div class=\"picture\">
					<a class=\"example-image-link\" href=\"pics\\".$quote['accountID']."\\768\\".$quote['picture']."\" data-lightbox=\"example-1\"><img class=\"example-image\" src=\"pics\\".$quote['accountID']."\\320\\".$quote['picture']."\"  alt=\"image-1\" width=\"200\" /></a> 
				</div>
				
				<div style=\"clear:both\">
				</div>";
				if($quote['P_write']==true)
				{
					$_SESSION['edit_id'] = $quote['id'];
					$_SESSION['edit_ds'] = $quote['datestamp'];
					$_SESSION['edit_qd'] = $quote['quote_date'];
					$_SESSION['edit_kid'] = $quote['bombelek'];
					$_SESSION['edit_sentence'] = $quote['sentence'];
					$_SESSION['edit_picture'] = $quote['picture'];
					echo 	"
							<div style=\"float:left;\">
								<form action=\"editQuote.php\" method=\"post\" name=\"editQuote\" id=\"editQuote\">
									<input type = \"hidden\" name=\"postID\" id=\"postID\" value=\"".$quote['id']."\">
									<input type = \"submit\" style=\"width:300px; background-color:#9de1a1\" name=\"editSubmit\" value=\"edytuj\">
								</form>
							</div>
							
							<div style=\"float:left; margin-left:35px;\">
								<form action=\"deleteQuote.php\" method=\"post\" name=\"deleteQuote\" id=\"deleteQuote\">
									<input type = \"hidden\" name=\"deleteID\" id=\"deleteID\" value=\"".$quote['id']."\">
									<input type = \"hidden\" name=\"deletePicName\" id=\"deletePicName\" value=\"".$quote['picture']."\">
									<input type = \"submit\" style=\"width:300px;background-color:#ff8080;\" name=\"deleteSubmit\" value=\"usuń\" >
								</form>
							</div>
							<div style=\"clear:both;\"></div>
							";
				}
				
			echo "</div>
			";
		}
	?>

	</main>
	
	<!--------------------------------<img src=\"pics\\".$quote['picture']."\" width=\"200\">-------------------------------------------------------------------->
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