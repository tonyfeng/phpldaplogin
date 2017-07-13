<?php

$msg = "";
if($_SERVER['REQUEST_METHOD']=="POST"){ 
	$username = trim($_REQUEST['username']);  
	$password = trim($_REQUEST['password']); 
	$status = true;
	
	//验证参数不可为空
	if(empty($username) || empty($password)){ 
       $msg = "请输入用户及密码！"; 
	   $status = false;
	}
	
	if($status){
		//连接LDAP
		require_once 'ldap-config.php';
		$ldapConnect = ldap_connect(LDAP_SERVER_IP , LDAP_SERVER_PORT ); 
		ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapConnect, LDAP_OPT_REFERRALS, 0);	
		
		if($ldapConnect){
			$msg = "连接LDAP成功！";
			$rdn = "uid=".$username.",ou=users,dc=pyw,dc=cn";
			$bind = ldap_bind($ldapConnect, $rdn, $password);
			if($bind ){
				session_start();
				$_SESSION['username'] = $username;
				$_SESSION['loginstate'] = md5($username.time()."0#@#$@");
				ldap_close($ldapConnect);
				header('Location:ldap-home.php');
				$msg = "LDAP验证成功！";
			}else{
				$msg = "LDAP验证失败！";
			}
		}else{
			$msg = "连接LDAP失败！";
		}
		ldap_close($ldapConnect);
	}
} 
 
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head> 
<title>LDAP LOGIN</title> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<style type="text/css"> 
dt{font-weight: bold;} 
</style> 
</head> 
<body>   
<div><?php echo $msg;?></div>
<form  method="post" action=""> 
 <dl> 
   <dt>用户名</dt> 
 <dd><input type="text" name="username"  /></dd> 
  
   <dt>密码</dt> 
   <dd><input type="password" name="password" /></dd> 
   <dd><input type="submit" value="确定" /></dd> 
 </dl> 
</form> 
</body> 
</html> 