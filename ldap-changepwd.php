<?php 
session_start();
if(!isset($_SESSION['loginstate'])){
	header('Location:ldap-login.php');
}

$msg = "";
if($_SERVER['REQUEST_METHOD']=="POST"){ 
	$username = trim($_REQUEST['username']);  
	$password = trim($_REQUEST['password']); 
	$password1 = trim($_REQUEST['password1']); 
	$password2 = trim($_REQUEST['password2']); 
	$status = true;
	//验证参数不可为空
	if(empty($username) || empty($password) || empty($password1) || empty($password2)){ 
		$msg = "请输入旧密码及新密码！";
		$status = false;	   
	   
	}
	
	if($password1 != $password2 && $status){
		$msg = "确认密码不正确！"; 
		$status = false;	 
	}
	
	if(strlen($password1) < 6 && $status){
		$msg = "密码长度不足6位字符"; 
		$status = false;	 
	}
	
	if($password == $password1 && $status){
		$msg = "新密码不能与旧密码相同！"; 
		$status = false;	 
	}
	
	if($username == 'Manager' && $status){
		$msg = "Manager用户无权修改！"; 
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
				$msg = "LDAP验证成功！";
				
				//获取用户信息
				$dn = "dc=pyw,dc=cn";
				$attributes = sprintf("objectClass=*"); 
				$sr = ldap_search($ldapConnect, $dn, $attributes);
				$entry = ldap_get_entries($ldapConnect, $sr);
				for ($i=0; $i<$entry["count"]; $i++){ 
					$user = isset($entry[$i]["uid"][0]) ? $entry[$i]["uid"][0] : '';
					if($user == $username){
						$userDn = $entry[$i]["dn"];
					}						
				} 				
				//修改密码
				$values["userpassword"][0] = "{MD5}".base64_encode(pack("H*",md5($password1))); 
				$rs = ldap_mod_replace($ldapConnect,$userDn,$values);   
				if($rs){ 
					$msg="密码修改成功!"; 
				}else{ 
					$msg = "密码修改失败!"; 
				} 
			}else{
				$msg = "旧密码不正确！";
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
<div><a href="ldap-home.php">首页</a></div>   
<h1>修改用户密码</h1>
<div><?php echo $msg;?></div>
<form  method="post" action=""> 
 <dl> 
   <dt>用户名</dt> 
   <dd><input type="text" name="username"  value="<?php echo $_SESSION['username'];?>" readonly/></dd> 
  
   <dt>旧密码</dt> 
   <dd><input type="password" name="password" /></dd> 
   <dt>新密码</dt> 
   <dd><input type="password" name="password1" /></dd> 
   <dt>确认密码</dt> 
   <dd><input type="password" name="password2" /></dd> 
   <dd><input type="submit" value="修改" /></dd> 
 </dl> 
</form> 
</body> 
</html> 