<?php
session_start();

if (isset($_SESSION['tata_logged_ID'])){
	header ('Location: index.php') ;
	exit();
} 
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Załóż nowe konto!</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
    <style>
        label {
            padding:1px;
            display:inline-block;
            font-size:12px;
            width:320px;
            vertical-align: top;
            text-align: justify;
        }
        input {
            display:inline-block;
        }
    </style>
</head>

<body>
    <header>
		<div class="headContainer" style="font-size:22px;">
			<p>
			Wypełnij formularz rejestracji nowego konta <br />			
			</p>
		</div>
	</header>
    
    <div class="container">

        <main>
            <article>
                <form method="post" action="register_submit.php">
                    <p class="register_warning"><b>Uwaga</b> Jeśli jedno z rodziców założyło już konto to Ty tego nie rób. 
                        Aby wspólnie korzystać z jednego konta, rodzic zakładający konto powinien zaprosić drugiego rodzica korzystając z panelu administracyjnego.
                        <br />Jeśli chcesz korzystać z serwisu wyłącznie w celu obserwowania wpisów innych rodziców, również nie zakładaj tu konta - poproś ich o wysłanie Ci zaposzenia.
                    </p>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ EMAIL --------------------------------------------------->
<!---------------------------------------------------------------------------------------------------->					
                
                    <input type="email" name="email" id="email" style="width:250px;"
                           <?php if(empty($_SESSION['fill_email'])) {
                            echo "value=\"\" placeholder=\"e-mail\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='e-mail'\"/>";
                            unset($_SESSION['fill_email']);
                           } else {
                               echo "value=\"".$_SESSION['fill_email']."\"/>";
                               unset($_SESSION['fill_email']);
                           }
                           ?>
                           <label for="email"><p class="register_labels_par" >Na ten adres zostanie wysłany link aktywacyjny, będzie on również loginem użytkownika.</p></label>
                    <div>
                        <?php
                        if(isset($_SESSION['e_email'])) {
                            echo "<div class=\"error\"><p>".$_SESSION['e_email']."</p></div>" ;
                            unset($_SESSION['e_email']);
                        }
                        ?>
                    </div>
                    
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ ACCOUNT NAME -------------------------------------------->
<!---------------------------------------------------------------------------------------------------->					                    
                    <input type="text" name="accountName" id="accountName" style="width:250px;"
                           <?php if(empty($_SESSION['fill_accountName'])) {
                            echo "value=\"\" placeholder=\"nazwa konta\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='nazwa konta'\">";
                            unset($_SESSION['fill_accountName']);
                           }else {
                               echo "value=\"".$_SESSION['fill_accountName']."\">";
                               unset($_SESSION['fill_accountName']);
                           }
                           ?>
                    <label for="accountName"><p class="register_labels_par" >Podaj nazwę konta skojarzoną z rodziną, a nie koniecznie z konkretną Pociechą.</p></label> 
                    <div>
                        <?php
                        if(isset($_SESSION['e_accountName'])) {
                            echo "<div class=\"error\"><p>".$_SESSION['e_accountName']."</p></div>" ;
                            unset($_SESSION['e_accountName']);
                        }
                        ?>
                    </div>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ PASSWORD ------------------------------------------------>
<!---------------------------------------------------------------------------------------------------->					                    
                    
                    <input type="password" name="pass" id="pass" style="width:250px;" placeholder="haslo" onfocus="this.placeholder=''" onblur="this.placeholder='hasło'">
                    <label for="pass"><p class="register_labels_par" >Hasło musi mieć od 8 do 20 znaków, co najmniej jedną wielką literę oraz co najmniej jedną cyfrę.</p></label>
                    <div>
                        <?php
                        if(isset($_SESSION['e_pass'])) {
                            echo "<div class=\"error\"><p>".$_SESSION['e_pass']."</p></div>" ;
                            unset($_SESSION['e_pass']);
                        }
                        ?>
                    </div>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ PASSWORD REENTER ---------------------------------------->
<!---------------------------------------------------------------------------------------------------->					                                        
                    
                    <input type="password" name="verPass" id="verPass"  style="width:250px;" placeholder="powtórz hasło" onfocus="this.placeholder=''" onblur="this.placeholder='powtórz hasło'">
                    <div>
                        <?php
                        if(isset($_SESSION['e_verPass'])) {
                            echo "<div class=\"error\"><p>".$_SESSION['e_verPass']."</p></div>" ;
                            unset($_SESSION['e_verPass']);
                        }
                        ?>
                    </div>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ OWNERS FIRST NAME --------------------------------------->
<!---------------------------------------------------------------------------------------------------->					                                        
                    
                    <input type="text" name="firstName" id="firstName" style="width:250px;"
                    <?php if(empty($_SESSION['fill_firstName'])) {
                            echo "value=\"\" placeholder=\"imię\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='imię'\">";
                            unset($_SESSION['fill_firstName']);
                           } else {
                               echo "value=\"".$_SESSION['fill_firstName']."\">";
                               unset($_SESSION['fill_firstName']);
                           }
                           ?>
                           <label for="firstName"><p class="register_labels_par" >Imię i nazwisko <b>użytkownika konta</b> (nie Pociechy).</p></label>
                    <div>
                        <?php
                        if(isset($_SESSION['e_firstName'])) {
                            echo "<div class=\"error\"><p>".$_SESSION['e_firstName']."</p></div>" ;
                            unset($_SESSION['e_firstName']);
                        }
                        ?>
                    </div>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ OWNERS LAST NAME ---------------------------------------->
<!---------------------------------------------------------------------------------------------------->					                                                            
                    
                    <input type="text" name="lastName" id="lastName" style="width:250px;"
                           <?php if(empty($_SESSION['fill_lastName'])) {
                            echo "value=\"\" placeholder=\"nazwisko\" onfocus=\"this.placeholder=''\" onblur=\"this.placeholder='nazwisko'\">";
                            unset($_SESSION['fill_lastName']);
                           } else {
                               echo "value=\"".$_SESSION['fill_lastName']."\">";
                               unset($_SESSION['fill_lastName']);
                           }
                           ?>
                    <div>
                        <?php
                        if(isset($_SESSION['e_lastName'])){
                            echo "<div class=\"error\"><p>".$_SESSION['e_lastName']."</p></div>" ;
                            unset($_SESSION['e_lastName']);
                        }
                        ?>
                    </div>

<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ TERMS ACCEPTANCE ---------------------------------------->
<!---------------------------------------------------------------------------------------------------->	                    
                    <input type="checkbox" name="terms" id="terms" value="terms_accept">
                    <label id="terms_label" for="terms">Akceptuję <a href="#" id="terms_link">warunki korzystania z serwisu</a></label>
                    <div>
                        <?php
                        if(isset($_SESSION['e_terms'])){
                            echo "<div class=\"error\"><p>".$_SESSION['e_terms']."</p></div>" ;
                            unset($_SESSION['e_terms']);
                        }
                        ?>
                    </div>
<!---------------------------------------------------------------------------------------------------->
<!------------------------------------------ CLOSING FORM -------------------------------------------->
<!---------------------------------------------------------------------------------------------------->	                   
                    
                    <input type="text" value="register" name="register" hidden style="display:none;" />
                    <input type="submit" value="zarejestruj" name="submit" />
                    <!--<div style="color:green; font-size:18px;">
                    <?//php if(isset($_SESSION['wszystko_gra'])){ echo $_SESSION['wszystko_gra'];} ?>
                    </div>!-->
                    
                    
                </form>
                
            </article>
        </main>

    </div>
</body>
</html>
