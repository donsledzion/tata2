<?php
session_start();
unset($_SESSION['tata_logged_ID']);
session_unset();

header('Location: index.php');