<?php

session_start();

if(isset($_SESSION['tata_logged_Email']))
{
    require_once 'database.php' ;

    $_SESSION['cameFrom'] = filter_input(INPUT_POST,'cameFrom');

    if (isset($_POST['quote']))
    {
        $_SESSION['quote'] = filter_input(INPUT_POST,'quote');
        //udana walidacja? Załóżmy, że tak!
        $all_OK = true ; //ustawienie flagi! dowolna niepoprawność zmieni flagę na false

        $currentDate = date("Ymd His") ;
        
        // przypisanie do zmiennych sesyjnych wartości przesłanych przez POST w celu automatycznego uzupełnienia formularza
        // w przypadku napotkania błędu i odesłania użytkownika do poprawienia niewłaściwych danych
        $_SESSION['fill_quote'] = $_SESSION['quote'];
        $_SESSION['fill_quoted'] = filter_input(INPUT_POST,'quoted');
        $_SESSION['fill_picture'] = filter_input(INPUT_POST,'picture');
        $_SESSION['fill_date'] = filter_input(INPUT_POST,'quote_date');

        if($_SESSION['cameFrom']=='editQuote.php'){            
            $_SESSION['oldPicture'] = basename(filter_input(INPUT_POST,'oldPicture'));
            $_SESSION['target_file'] = date("Ymd").date("His").".".strtolower(pathinfo(basename(filter_input(INPUT_POST,'oldPicture')),PATHINFO_EXTENSION));
            $_SESSION['edit_date'] = $currentDate;
        } else {
            $_SESSION['submit_data'] = $currentDate;            
        }       

        if(!isset($_POST['quoted']))
        {
                $all_OK = false ;
                $_SESSION['e_fillKid']="Kogoś musisz zacytować!";
        }
        else
        {
                $quotedQuery = $db->prepare('SELECT * FROM Kids WHERE IDKid=? ');
                $quotedQuery -> execute([filter_input(INPUT_POST,'quoted')]);
                $qQ = $quotedQuery->fetch();
                $quotedBirth = $qQ['Birth_date'];
        }		

        if(empty($_FILES["picture"]["name"])) {
            if($_SESSION['cameFrom']=='addQuote.php'){
                $picture = strtolower($qQ['Default_pic']) ; // default picture depending on quoted child
            } else {   
                $picture = filter_input(INPUT_POST,'oldPicture') ; // domyślny obrazek
            }
        }
        else
        {
                include 'pictureUpload.php' ;

                if(isset($_SESSION['e_upload']))
                {
                        header('Location: '.$_SESSION['cameFrom']) ;
                        exit();
                }	
                $picture = $_SESSION['target_file'] ;
        }

        //Sprawdź czy data podana w formularzu nie jest z przyszłości!

        if(date(strtotime($currentDate)-strtotime(filter_input(INPUT_POST,'quote_date')))<0)
        {
                $all_OK = false;
                $_SESSION['e_date'] = 'Elo, elo! Data cytatu nie może być z przyszłości!' ;
        }
        else if($_POST['quote_date']<$quotedBirth)
        {
                $all_OK = false;
                $_SESSION['e_date'] = 'Elo, elo! Cytat sprzed narodzin dziecka?!' ;
        }

        if($all_OK == true)
        {	
        $dateToInsert = "";
        $dateToInsert .= filter_input(INPUT_POST,'quote_date')." ".date("H:i:s",strtotime($currentDate));

        $currentDate = date("Y-m-d H:i:s") ;   
        /*
        $kidQuery -> prepare('SELECT Dim_name FROM Kids WHERE IDKid=:IDKid');
        $kidQuery -> bindValue(':IDKid',$_POST['quoted'], PDO::PARAM_STR);
        $kidQuery -> execute();
        $quotedKid = $kidQuery ->fetch();
         
         */
//=========================================================================================        
       if($_SESSION['cameFrom']=='editQuote.php'){
            $query = $db -> prepare('UPDATE Posts '
                    . ' SET Datestamp=:Datestamp,'
                    . ' Quote_date=:Quote_date,'
                    . ' IDKid=:IDKid,'
                    . ' Sentence=:Sentence,'
                    . ' Picture=:Picture'
                    . ' WHERE IDPost=:IDPost');
            $query->bindValue(':IDPost', filter_input(INPUT_POST,'editID'), PDO::PARAM_STR);
            $query->bindValue(':Datestamp', $currentDate);
            $query->bindValue(':Quote_date', $dateToInsert, PDO::PARAM_STR); //data zdarzenia
            $query->bindValue(':IDKid', filter_input(INPUT_POST,'quoted'), PDO::PARAM_STR);
            $query->bindValue(':Sentence', $_SESSION[quote], PDO::PARAM_STR);
            $query->bindValue(':Picture', $picture, PDO::PARAM_STR);
            $query->execute();
            $oldName = basename(filter_input(INPUT_POST,'oldPicture'));
            $_SESSION['e_edit']="hirajem</br>POST[editID]=".filter_input(INPUT_POST,'editID')."</br>";
            if(ctype_digit($oldName[0])){
                rename("pics/".$_SESSION['IDAccount']."/768/".$oldName, "pics/".$_SESSION['IDAccount']."/768/".$picture);
                rename("pics/".$_SESSION['IDAccount']."/480/".$oldName, "pics/".$_SESSION['IDAccount']."/480/".$picture);
                rename("pics/".$_SESSION['IDAccount']."/320/".$oldName, "pics/".$_SESSION['IDAccount']."/320/".$picture);
                rename("pics/".$_SESSION['IDAccount']."/160/".$oldName, "pics/".$_SESSION['IDAccount']."/160/".$picture);
            }
        } else {
//=========================================================================================

            $query = $db -> prepare('INSERT INTO Posts VALUES('
                    . ' :id,'
                    . ' :Datestamp,'
                    . ' :Quote_date,'
                    . ' :IDAuthor,'
                    . ' :IDKid,'
                    . ' :Sentence,'
                    . ' :Picture)');
            $query->bindValue(':id', NULL);
            $query->bindValue(':Datestamp', $currentDate); //data dodania wpisu
            $query->bindValue(':Quote_date', $dateToInsert); //data wypowiedzi :)
            $query->bindValue(':IDAuthor', $_SESSION['IDUser'], PDO::PARAM_STR);
            $query->bindValue(':IDKid', filter_input(INPUT_POST,'quoted'), PDO::PARAM_STR);
            $query->bindValue(':Sentence', $_SESSION['quote'], PDO::PARAM_STR);
            $query->bindValue(':Picture', $picture, PDO::PARAM_STR);
            $query->execute();
        }    
        unset($_SESSION['fill_quote']);
        unset($_SESSION['fill_quoted']);
        unset($_SESSION['fill_picture']);
        unset($_SESSION['fill_date']);		
        
        } else {
            $_SESSION['redirect'] = true ;
            header('Location: '.$_SESSION['cameFrom']) ;
            exit();
        }
	
} else {
    $_SESSION['e_fillAny']="wpisz cokolwiek!";
    $_SESSION['redirect'] = true ;
    
    header('Location: '.$_SESSION['cameFrom']) ;
    exit();
}	
    header('Location: showQuotes.php');
    exit();	
} else {
    header('Location: index.php');
}