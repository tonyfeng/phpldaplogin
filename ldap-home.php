<?php 
session_start();
if(!isset($_SESSION['loginstate'])){
	header('Location:ldap-login.php');
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head> 
<title>LDAP HOME</title> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<style type="text/css"> 
dt{font-weight: bold;} 
</style> 
</head> 
<body>
<h1><?php echo $_SESSION['username'];?> , 欢迎您登录！</h1>
<ul>
	<li><a href="ldap-changepwd.php">修改用户密码</a></li>
	<li><a href="ldap-logout.php">退出</a></li>
</ul>
</body> 
</html> 