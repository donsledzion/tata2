<?php
session_start();

if (isset($_SESSION['tata_logged_ID'])){
	header ('Location: index.php') ;
	exit();
} 

function pass_check($password){
    unset($error);
    if(strlen($password)<8){
        $error .= "Hasło musi mieć co najmniej 8 znaków długości! ";
    }
    if(strlen($password)>20){
        $error .= "Hasło może mieć maksymalnie 20 znaków długości! ";
    }
    if(!preg_match("#[0-9]+#",$password)){
        $error .= "Hasło musi zawierać przynajmniej jedną cyfrę!";        
    }
    if(!preg_match("#[a-z]+#", $password )) {
        $error .= "Hasło musi zawierać przynajmniej jedną literę!";
    }
    if(!preg_match("#[A-Z]+#", $password )){
        $error .= "Hasło musi zawierać przynajmniej jedną WIELKĄ literę!";
    }
    if($error){
        $_SESSION['e_pass'] = $error;
        return false;
    }    
    return true;
}
