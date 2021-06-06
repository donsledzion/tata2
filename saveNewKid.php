<?php

session_start();

if(isset($_SESSION['tata_logged_ID'])&&($_SESSION['someAdmin']==true))
{
    echo "GOT IN!</br>";
    // ========================================================================
    // ======== SPISANIE DO ZMIENNYCH SESYJNYCH TEGO CO PRZYSZŁO W POST =======
    // ========================================================================
    
    $_SESSION['fill_newKidGender'] = filter_input(INPUT_POST, 'newKidGender');
    echo "Gender is: ".$_SESSION['fill_newKidGender']."</br>";
    
    $_SESSION['fill_kidLastName'] = filter_input(INPUT_POST, 'kidLastName');
    echo "LastName is: ".$_SESSION['fill_kidLastName']."</br>";
    
    $_SESSION['fill_kidName'] = filter_input(INPUT_POST, 'kidName');
    echo "Name is: ".$_SESSION['fill_kidName']."</br>";
    
    $_SESSION['fill_kidDimName'] = filter_input(INPUT_POST, 'kidDimName');
    echo "DimName is: ".$_SESSION['fill_kidDimName']."</br>";
    
    $_SESSION['fill_kidBirth'] = filter_input(INPUT_POST, 'kidBirth');
    echo "Birth day is: ".$_SESSION['fill_kidBirth']."</br>";
    
    $_SESSION['fill_kidInfo'] = filter_input(INPUT_POST, 'kidInfo');
    echo "Kid's info: ".$_SESSION['fill_kidInfo']."</br>";
    
    
    
    // ODNOTOWANIE W ZMIENNEJ SESYJNEJ INFORMACJI SKĄD JEST PRZEKIEROWANIE
    // MOŻLIWE, ŻE Z "addKid.php" lub z "editKid.php"
    $_SESSION['cameFrom'] = filter_input(INPUT_POST, 'cameFrom');
    
    //udana walidacja? Załóżmy, że tak!
    $all_OK = true ; //ustawienie flagi! dowolna niepoprawność zmieni flagę na false
    $assignDefault = false;
    
    if(isset($_SESSION['fill_newKidGender'])) {
        echo "Coś tam podali</br>";
    } else {
        // jeśli nie podano płci to generujemy błąd i powrót do "cameFrom"
        echo "Nie podano płci!";
        $all_OK = false;
        $_SESSION['e_fill_gender']="Podaj płeć dziecka!";       
    }
    // jeśli podano płeć to w ogóle możemy coś działać...
    // 
    // POŁĄCZENIE Z BAZĄ DANYCH
    require_once 'database.php' ;

    // sprawdzam czy podano nazwisko dziecka
    if(isset($_SESSION['fill_kidLastName'])&&($_SESSION['fill_kidLastName']!="")){
        // jeśli podano to upewniam się, że pierwsza litera jest wielka
        echo "Podali nazwisko!</br>";
        $_SESSION['fill_kidLastName'] = ucfirst($_SESSION['fill_kidLastName']);
        echo "-->".$_SESSION['fillkidLastName']."<--</br>";
    } else {
        echo "Nie podali nazwiska</br>";
        // jeśli nie podano nazwiska dziecka to ustawiam domyślne nazwisko zalogowanego użytkownika
        $lastNameQuery = $db->prepare('SELECT Last_name FROM Users WHERE Email=?');
        $lastNameQuery->execute([$_SESSION['tata_logged_Email']]);
        $_SESSION['fill_kidLastName'] = $lastNameQuery->fetchColumn();
        $_SESSION['e_fill_last_name'] = "nazwisko ustawione domyślnie!";

    }
    // sprawdzam czy podano imię dziecka
    if(isset($_SESSION['fill_kidName'])&&($_SESSION['fill_kidName']!="")){
        echo "Podali imię!</br>";
        // jeśli podano imię to upewniam się, że pierwsza litera jest wielka
        $_SESSION['fill_kidName'] = ucfirst($_SESSION['fill_kidName']);
    } else {
        echo "Nie podali imienia - bardzo źle</br>";
        // jeśli nie podano imienia to generuję błąd i odsyłam do formularza            
        $_SESSION['e_fill_name'] = "Musisz podać imię dziecka!";            
        $all_OK = false;            
    }
    // sprawdzam czy podano zdrobnienie imienia dziecka
    if(isset($_SESSION['fill_kidDimName'])&&($_SESSION['fill_kidDimName']!="")){
        echo "Podali zdrobnienie!</br>";
        // jeśli podano zdrobnienie to upewniam się, że pierwsza litera jest wielka
        $_SESSION['fill_kidDimName'] = ucfirst($_SESSION['fill_kidDimName']);
    } else if (!isset($_SESSION['e_fill_name'])) {
        // jeśli nie podano zdrobnienia ale podano imię to przypisuję zdrobnienie
        // takie samo jak imię i generuję bład. 
        // błąd wyświetli się tylko w formie ostrzeżenia w przypadku gdy jakieś poważniejsze
        // braki odeślą z powrotem do pliku formularza.
        $_SESSION['fill_kidDimName'] = $_SESSION['fill_kidName'];
        $_SESSION['e_fill_dim_name'] = "Zdrobnienie ustawione domyślnie takie jak imię!";
    } else {
        $_SESSION['e_fill_dim_name'] = "Podaj zdrobnienie imienia!";
    }      
    

    // sprawdzam czy podano datę narodzin dziecka
    if(isset($_SESSION['fill_kidBirth'])&&($_SESSION['fill_kidBirth']!="")){
        $currentDate = date("Ymd His") ;
        if(date(strtotime($currentDate)-strtotime($_SESSION['fill_kidBirth']))<0){
            $all_OK = false; // błąd jeśli data narodzin jest z przyszłości!
            $_SESSION['e_date'] = 'Elo, elo! Data cytatu nie może być z przyszłości!' ;
        }
    }
    
    // spradzam czy podano info-notkę o dziecku
    if(!isset($_SESSION['fill_kidInfo'])||($_SESSION['fill_kidInfo']=="")){
        if(!isset($_SESSION['e_fill_name'])){ // jeśli nie dodano to tworzę automatyczną notkę na podstawie pozostałych danych
            $_SESSION['fill_kidInfo']=$_SESSION['fill_kidName']." ".$_SESSION['fill_kidLastName'] ;
            if($_SESSION['fill_kidDimName']!=$_SESSION['fill_kidName']) {
                if($_SESSION['fill_newKidGender']=="male") {
                    $genderChoice = "którego" ;                    
                } else if ($_SESSION['fill_newKidGender']=="female") {
                    $genderChoice = "którą" ;                        
                }
                $_SESSION['fill_kidInfo'] = $_SESSION['fill_kidInfo']." na ".$genderChoice." mówimy ".$_SESSION['fill_kidDimName'] ;
            }
        } else {
            $_SESSION['e_fill_info']="Dodaj krótkie info o dziecku!" ;
        }
    }     
            
    if(!empty($_FILES["picture"]["name"])) {        
        $_SESSION['upload_came_from'] = "saveNewKid.php";
        include 'pictureUpload.php' ;
        if(isset($_SESSION['e_upload'])) {
            //jeśli wystąpił błąd przesyłania pliku to należy nadać zdjęcie domyślne - nadać nazwę...
            $assignDefault = true;
            //$_SESSION['fill_defaultPicture'] = strtolower($_SESSION['fill_kidDimName']).".png";
            //...oraz skopiować ten plik z katalogu domyślnego do folderu konta! - uzupełnić
            //$all_OK = false;
        } else {
            $_SESSION['fill_defaultPicture'] = $_SESSION['target_file'];
        }
    } else {
        $assignDefault = true;
        //$_SESSION['fill_defaultPicture'] = strtolower($_SESSION['fill_kidDimName']).".png";
        
    }    
    
     if($all_OK==true) { 
         
         
        // jeśli flaga nie została w żadnym miejscu zmieniona na FALSE to możemy 
        // zapisać dane w bazie
        // w zależności od tego skąd trafiliśmy do tego pliku będzie wykonany
        // INSERT lub UPDATE bazy
        if($_SESSION['cameFrom']=='addKid.php'){
            
            if($assignDefault==true){
                include 'imgMods.php';
                $_SESSION['fill_defaultPicture'] = strtolower(removeSpecialChars($_SESSION['fill_kidDimName'])).".png";                
                $target_dir = 'pics/'.$_SESSION['IDAccount'].'/';
                resize_picture('img/default'.$_SESSION['fill_newKidGender'].".png",768,$target_dir.'768/'.$_SESSION['fill_defaultPicture']);
                resize_picture('img/default'.$_SESSION['fill_newKidGender'].".png",480,$target_dir.'480/'.$_SESSION['fill_defaultPicture']);
                resize_picture('img/default'.$_SESSION['fill_newKidGender'].".png",320,$target_dir.'320/'.$_SESSION['fill_defaultPicture']);
                resize_picture('img/default'.$_SESSION['fill_newKidGender'].".png",160,$target_dir.'160/'.$_SESSION['fill_defaultPicture']);
                $assignDefault=false;
            }
            
            $insertKidQuery = $db-> prepare('INSERT INTO Kids VALUES('
                    . ':IDKid,'
                    . ':IDAccount,'
                    . ':First_name,'
                    . ':Last_name,'
                    . ':Dim_name,'
                    . ':Birth_date,'
                    . ':About,'
                    . ':Gender,'
                    . ':Default_pic'
                    . ')');
            $insertKidQuery -> bindValue(':IDKid',NULL);
            $insertKidQuery -> bindValue(':IDAccount',  $_SESSION['IDAccount']);
            $insertKidQuery -> bindValue(':First_name', $_SESSION['fill_kidName']);
            $insertKidQuery -> bindValue(':Last_name',  $_SESSION['fill_kidLastName']);
            $insertKidQuery -> bindValue(':Dim_name',   $_SESSION['fill_kidDimName']);
            $insertKidQuery -> bindValue(':Birth_date', $_SESSION['fill_kidBirth']);
            $insertKidQuery -> bindValue(':About',      $_SESSION['fill_kidInfo']);
            $insertKidQuery -> bindValue(':Gender',     $_SESSION['fill_newKidGender']);
            $insertKidQuery -> bindValue(':Default_pic',$_SESSION['fill_defaultPicture']);
            $insertKidQuery ->execute();           
        } else if($_SESSION['cameFrom']=='editKid.php') {
            echo '</br>nothing here yet</br>';
        }
        unset($_SESSION['fill_newKidGender']);
        unset($_SESSION['fill_kidName']);
        unset($_SESSION['fill_kidLastName']);
        unset($_SESSION['fill_kidDimName']);
        unset($_SESSION['fill_kidBirth']);
        unset($_SESSION['fill_kidInfo']);
        unset($_SESSION['fill_defaultPicture']);
        unset($_SESSION['e_fill_gender']);
        unset($_SESSION['e_fill_last_name']);
        unset($_SESSION['e_fill_name']);
        unset($_SESSION['e_fill_dim_name']);
        unset($_SESSION['e_fill_date']);
        unset($_SESSION['e_fill_info']);
        unset($_SESSION['e_upload']);
         
         
    } else {
        header('Location: '.$_SESSION['cameFrom']);
        exit();
    }
    
     header('Location: showKids.php');
     exit();

    }   
    

   
    
    
    /*
    
    
    //==========================================================================================
    //==========================================================================================
    //==========================================================================================
    // TU SIE ZACZYNA STARE
    //
    //==========================================================================================
    //==========================================================================================
    //==========================================================================================

    
    
     
//=========================================================================================        
       if($_SESSION['cameFrom']=='editQuote.php'){
            $query = $db -> prepare('UPDATE kids_post '
                    . ' SET datestamp=:datestamp,'
                    . ' quote_date=:quote_date,'
                    . ' bombelek=:bombelek,'
                    . ' sentence=:sentence,'
                    . ' picture=:picture'
                    . ' WHERE id=:editID');
            $query->bindValue(':editID', $_POST[editID], PDO::PARAM_STR);
            $query->bindValue(':datestamp', $currentDate);
            $query->bindValue(':quote_date', $dateToInsert, PDO::PARAM_STR); //data zdarzenia
            $query->bindValue(':bombelek', $_POST[quoted], PDO::PARAM_STR);
            $query->bindValue(':sentence', $_SESSION[quote], PDO::PARAM_STR);
            $query->bindValue(':picture', $picture, PDO::PARAM_STR);
            //$query->execute();
            $oldName = basename($_POST['oldPicture']);
            
            if(ctype_digit($oldName[0])){
                //rename("pics/768/".$oldName, "pics/768/".$picture);
                //rename("pics/480/".$oldName, "pics/480/".$picture);
               // rename("pics/320/".$oldName, "pics/320/".$picture);
                //rename("pics/160/".$oldName, "pics/160/".$picture);
            }
        } else {
//=========================================================================================

            $query = $db -> prepare('INSERT INTO kids_post VALUES('
                    . ':id,'
                    . ' :datestamp,'
                    . ' :quote_date,'
                    . ' :author,'
                    . ' :bombelek,'
                    . ' :sentence,'
                    . ' :picture)');
            $query->bindValue(':id', NULL);
            $query->bindValue(':datestamp', $currentDate); //data dodania wpisu
            $query->bindValue(':quote_date', $dateToInsert); //data wypowiedzi :)
            $query->bindValue(':author', $_SESSION[tata_logged_ID], PDO::PARAM_STR);
            $query->bindValue(':bombelek', $_POST[quoted], PDO::PARAM_STR);
            $query->bindValue(':sentence', $_SESSION[quote], PDO::PARAM_STR);
            $query->bindValue(':picture', $picture, PDO::PARAM_STR);
            //$query->execute();
        }    
        unset($_SESSION['fill_quote']);
        unset($_SESSION['fill_quoted']);
        unset($_SESSION['fill_picture']);
        unset($_SESSION['fill_date']);		
       
     * 
     */ 
