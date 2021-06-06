<?php
session_start();
require_once "database.php";

if (isset($_SESSION['tata_logged_ID'])){
	header ('Location: index.php') ;
	exit();
} else {
    
    try {
        $_SESSION['token'] = $_GET['token'];
        echo "token: ".$_SESSION['token']."<br />";
        $tokenQuery = $db->prepare('SELECT * FROM AccountRequest WHERE Token=?');
        $tokenQuery->execute([$_SESSION['token']]);
        $tokenCount=$tokenQuery->rowCount();
        $newAcc = $tokenQuery->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Coś poszło nie tak:<br />".$e->getMessage() ;
    }
    $request_OK = true;
    
    if($tokenCount<=0) {
        $request_OK = false;
        $_SESSION['msg_activate'] = 'Token nieprawidłowy lub utracił ważność.' ;
        header('Location: accErrorInvalidToken.php');
        exit();
    }    
    if(empty($newAcc['User_email'])) {
        $request_OK = false;
        $_SESSION['msg_activate'] = 'Brak adresu e-mail we wpisie [błąd].' ;
    } else {
        // jeśli jest e-mail przypisany do tokena to trzeba sprawdzić czy:
        // - czy nie ma już użytkownika z takim e-mail'em
        // - token nie utracił ważności        
        //=====================================================================
        try {
            $usersQuery = $db->prepare('SELECT * FROM Users WHERE Email=?');
            $usersQuery->execute([$newAcc['User_email']]);
            $usersQuery->rowCount();
            $usersCount = $usersQuery->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Coś poszło nie tak:<br />".$e->getMessage() ;
        }
        if($usersCount>0){
            $request_OK = false;
            $_SESSION['msg_activate'] = 'Już istnieje użytkownik o takim adresie e-mail. [błąd].' ;
            header('Location accErrorUserExists.php?email='.$newAcc['User_email']);
            exit();
            
        }
        
    }
    if(empty($newAcc['User_first_name'])) {
        $request_OK = false;
        $_SESSION['msg_activate'] = 'Brak imienia użytkownika we wpisie [błąd].' ;
    }
    if(empty($newAcc['User_last_name'])) {
        $request_OK = false;
        $_SESSION['msg_activate'] = 'Brak nazwiska użytkownika we wpisie [błąd].' ;
    }
    if(empty($newAcc['User_password'])) {
        $request_OK = false;
        $_SESSION['msg_activate'] = 'Brak adresu e-mail we wpisie [błąd].' ;
    }
    if(!empty($newAcc['IDAccount'])) {
        $account_match = true;
        $_SESSION['msg_activate'] = 'Użytkownik przypisany do konta:...' ;
        // teraz tak:
        // Nowy użytkownik musi być tworzony:
        //  - albo razem z nowym kontem 
        //  - albo na zaproszenie czyli przypisany (już na konkretnych uprawnieniach) do istniejącego konta
        // więc w bazie danych musi być kolejna kolumna w tabeli AccountRequest - uprawnienia
        // po weryfikacji tokena będzie tworzony nowy użytkownik i nowe konto oraz wpis w tabeli UsersAccountsPermissions na uprawnienia rodzica
        // albo będzie tworzony nowy użytkownik i tylko wpis w tabeli UsersAccountsPermissions na uprawnienia wybrane przez zapraszającego
        
        try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();    // OPENING OF TRANSACTION //

            $insertUserQuery = $db->prepare('INSERT INTO Users VALUES(:IDUser, :Email, :First_name, :Last_name, :Password, :Avatar);');
            $insertUserQuery->bindValue(':IDUser', NULL);
            $insertUserQuery->bindValue(':Email',$newAcc['User_email'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':First_name', $newAcc['User_first_name'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':Last_name', $newAcc['User_last_name'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':Password', $newAcc['User_password'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':Avatar', $newAcc['User_avatar']);
            $insertUserQuery->execute();
            $lastUserID = $db->lastInsertId();        
            $_SESSION['wszystko_gra'] .= '<br /> Użytkownik został utwozony ;)';


            $insertUserAccountRelation = $db->prepare('INSERT INTO AccountsUsersPermissions VALUES(:IDAccount, :IDUser, :IDPermissions);');
            $insertUserAccountRelation->bindValue(':IDAccount', $newAcc['IDAccount'], PDO::PARAM_INT);
            $insertUserAccountRelation->bindValue(':IDUser', $lastUserID, PDO::PARAM_INT);
            $insertUserAccountRelation->bindValue(':IDPermissions', $newAcc['IDPermissions'], PDO::PARAM_INT);
            $insertUserAccountRelation->execute();
            //$_SESSION['wszystko_gra'] .= ' </br> Konto użytkownika i dzieciaków sparowane ;)!.;)';

            $db->commit();              // CLOSING TRANSACTION
            $_SESSION['wszystko_gra'] .= ' Konto utworzone - możesz się zalogować';
            $fail = false;
        } catch (Exception $e){
            $db->rollBack();
            $_SESSION['wszystko_gra'] .= ' NIE WSZYSTKO GRA :/';
            $_SESSION['wszystko_gra'] .= "   ->".$e->getMessage();
            $fail = true;
        }
        //if adding new entries succeeded we can delete activate request
        // IMPORTANT! We need to delete all requests from given e-mail
        // ALSO IMPORTANT - at the begining we shoud check if given token is'n related with existing user (e-mail)
        if($fail==false){
            try {
                $deleteTokensQuery = $db->prepare('DELETE FROM AccountRequest WHERE User_email=:Email');
                $deleteTokensQuery->bindValue(':Email',$newAcc['User_email'], PDO::PARAM_STR);
                $deleteTokensQuery->execute();
                $fail = false;
            } catch (Exception $e) {                
                $_SESSION['e_del_token'] = "Błąd kasowania tokenów | ".$e->getMessage();
                $fail = true;
            }
        }   
        $token = bin2hex(random_bytes(50));
                                
        // Obtaining token expiration date (24 hours since request)
        $tomorrow = new DateTime();
        $tomorrow->modify('+1 Day');
        $expiration = $tomorrow->format('Y-m-d H:i:s');                

        // Insertion into database
        try {
            $assignTokenQuery = $db->prepare('INSERT INTO PasswordReset VALUES(:IDRequest, :Email, :Token, :Expiration)');
            $assignTokenQuery->bindValue(':IDRequest', null);
            $assignTokenQuery->bindValue(':Email', $newAcc['User_email'], PDO::PARAM_STR);
            $assignTokenQuery->bindValue(':Token', $token);
            $assignTokenQuery->bindValue(':Expiration', $expiration);
            $assignTokenQuery->execute();
        } catch (Exception $e) {
            $recovery_OK = false;
            $_SESSION['e_rec_email'] .= " ...wystąpił błąd podczas próby zresetowania hasła. Spróbuj ponownie lub skontaktuj się z administratorem!</br>";           
            $_SESSION['e_rec_email'] .= $e->getMessage();
        }
        
        
        header('Location: new_password.php?token='.$token );
        exit();
        
    } else {
        // if IDAccount is not set that means there has to be request for creating new account with new user
        try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();    // OPENING OF TRANSACTION //

            $insertUserQuery = $db->prepare('INSERT INTO Users VALUES(:IDUser, :Email, :First_name, :Last_name, :Password, :Avatar);');
            $insertUserQuery->bindValue(':IDUser', NULL);
            $insertUserQuery->bindValue(':Email',$newAcc['User_email'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':First_name', $newAcc['User_first_name'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':Last_name', $newAcc['User_last_name'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':Password', $newAcc['User_password'], PDO::PARAM_STR );
            $insertUserQuery->bindValue(':Avatar', $newAcc['User_avatar']);
            $insertUserQuery->execute();
            $lastUserID = $db->lastInsertId();        
            $_SESSION['wszystko_gra'] .= '<br /> Użytkownik został utwozony ;)';




            $insertAccountQuery = $db->prepare('INSERT INTO Accounts VALUES(:IDAccount, :Name, :Avatar);');
            $insertAccountQuery->bindValue(':IDAccount', NULL);
            $insertAccountQuery->bindValue(':Name', $newAcc['Account_name'], PDO::PARAM_STR);
            $insertAccountQuery->bindValue(':Avatar', $newAcc['Account_avatar']);
            $insertAccountQuery->execute();
            $lastAccountID = $db->lastInsertId();
            //$_SESSION['wszystko_gra'] .= ' </br> Konto dla użytkownika zostało utwozone!.;)';

            $insertUserAccountRelation = $db->prepare('INSERT INTO AccountsUsersPermissions VALUES(:IDAccount, :IDUser, :IDPermissions);');
            $insertUserAccountRelation->bindValue(':IDAccount', $lastAccountID, PDO::PARAM_INT);
            $insertUserAccountRelation->bindValue(':IDUser', $lastUserID, PDO::PARAM_INT);
            $insertUserAccountRelation->bindValue(':IDPermissions', $newAcc['IDPermissions'], PDO::PARAM_INT);
            $insertUserAccountRelation->execute();
            //$_SESSION['wszystko_gra'] .= ' </br> Konto użytkownika i dzieciaków sparowane ;)!.;)';

            $db->commit();              // CLOSING TRANSACTION
            $_SESSION['wszystko_gra'] .= ' Konto utworzone - możesz się zalogować';
            $fail = false;
        } catch (Exception $e){
            $db->rollBack();
            $_SESSION['wszystko_gra'] .= ' NIE WSZYSTKO GRA :/';
            $_SESSION['wszystko_gra'] .= "   ->".$e->getMessage();
            $fail = true;
        }
        //if adding new entries succeeded we can delete activate request
        // IMPORTANT! We need to delete all requests from given e-mail
        // ALSO IMPORTANT - at the begining we shoud check if given token is'n related with existing user (e-mail)
        if($fail==false){
            try {
                $deleteTokensQuery = $db->prepare('DELETE FROM AccountRequest WHERE User_email=:Email');
                $deleteTokensQuery->bindValue(':Email',$newAcc['User_email'], PDO::PARAM_STR);
                $deleteTokensQuery->execute();
                $fail = false;
            } catch (Exception $e) {                
                $_SESSION['e_del_token'] = "Błąd kasowania tokenów | ".$e->getMessage();
                $fail = true;
            }
        }        
    }
    if($fail==false){
        header('Location: accActivatedPending.php?name='.$newAcc['User_first_name']);
        exit();
    } else {
    
        echo "znaleziono pasujących tokenów: ".$tokenCount;
        echo "<br />email: ".$newAcc['User_email'];
        echo "<br />User name: ".$newAcc['User_first_name'];
        echo "<br />User last name: ".$newAcc['User_last_name'];
        echo "<br />User password: not so fast :P";
        if(!empty($newAcc['IDAccount'])) { 
            echo "<br />IDAccount: ".$newAcc['IDAccount'];         
        } else if(!empty($newAcc['Account_name'])) {
            echo "<br />Account name: ".$newAcc['Account_name'];
        }
        echo "<br />Expiration of token: ".$newAcc['Expiration'];

        echo "<br />Status dodawania wpisow: ".$_SESSION['wszystko_gra'] ;
        echo "<br />Status kasowania tokena: ".$_SESSION['e_del_token'] ;
    }
}