<?php 
session_start();
if(isset($_SESSION['loginstate'])){
	unset($_SESSION['loginstate']);
	unset($_SESSION['username']);
}
header('Location:ldap-login.php');

?>