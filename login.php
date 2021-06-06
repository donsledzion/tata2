<?php
session_start();

if (!isset($_SESSION['tata_logged_ID'])){
	if(isset($_POST['login'])){            
            
            if(empty($_POST['login'])){
                $_SESSION['e_login'] = 'musisz podać login!' ;
                $_SESSION['bad_attempt'] = true;
		header('Location: index.php');
		exit();
            }
            if((!isset($_POST['pass']))||(empty($_POST['pass']))){
                $_SESSION['e_pass'] = 'musisz podać hasło!' ;
                $_SESSION['bad_attempt'] = true;
		header('Location: index.php');
		exit();
            }
            
            $login = filter_input(INPUT_POST, 'login');
            $password = filter_input(INPUT_POST, 'pass');

            require_once "database.php";

            $matchPass = $db->prepare('SELECT IDUser, Email, Password, First_name FROM Users WHERE Email = :Email');
            $matchPass->bindValue(':Email', $login, PDO::PARAM_STR);
            $matchPass->execute();

            //echo $matchPass->rowCount()."<br/>";

            $user = $matchPass->fetch();

            //echo $password." ".$user['Password']."<br/>";

            if(password_verify($password, $user['Password'])){
                $_SESSION['tata_logged_ID'] = $user['First_name'] ;
                $_SESSION['tata_logged_LN'] = $user['Last_name'] ;
                $_SESSION['tata_logged_Email'] = $user['Email'] ;
                $_SESSION['IDUser'] = $user['IDUser'] ;
                unset($_SESSION['bad_attempt']);


                $adminCount = $db->prepare('	SELECT Accounts.IDAccount AS \'IDAccount\'
                                                FROM Accounts
                                                INNER JOIN AccountsUsersPermissions ON AccountsUsersPermissions.IDAccount 	  = Accounts.IDAccount
                                                INNER JOIN Users 					ON AccountsUsersPermissions.IDUser		  = Users.IDUser
                                                INNER JOIN Permissions				ON AccountsUsersPermissions.IDPermissions = Permissions.IDPermissions
                                                WHERE Users.Email = ? AND Permissions.IDPermissions = \'1\'');

                $adminCount->execute([$user['Email']]);

                $account = $adminCount->fetchAll();

                $admCount = $adminCount->rowCount();

                //$_SESSION['IDAccount'] = $account['IDAccount'];
                $_SESSION['IDAccount'] = $account[0][0];

                if($admCount==1) {
                    $_SESSION['someAdmin'] = true;                        
                }

            } else {
                $_SESSION['e_pass'] = 'nieprawidłowa kombinacja hasła i loginu';
                $_SESSION['bad_attempt'] = true ;
                header('Location: index.php');
                exit();
            }
	} else {
                $_SESSION['e_login'] = 'musisz podać login!' ;
                $_SESSION['bad_attempt'] = true;
		header('Location: index.php');
		exit();
	}
}

header('Location: index.php');
echo $_SESSION['tata_logged_ID']."<br/>" ;
echo 'dane logowania poprawne, trwa logowanie';

