<?php
/*
 * UserMod - An ajax modal based user management system
 * Copyright (C) 2014  Michael Jonker
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * michael@piquant.ie
 * Piquant Media
 * http://www.piquant.ie
 * */ 
  
 /*
  * Class for database interactions
  * 
  * */
?>
<?php

if(isset($_POST["username"])){
	$username=$_POST["username"];
}else{
	$username=null;
}
if(isset($_POST["realname"])){
	$realname=$_POST["realname"];
}else{
	$realname=null;
}
if(isset($_POST["email"])){
	$email=$_POST["email"];
}else{
	$email=null;	
}
if(isset($_POST["password"])){
	$password=$_POST["password"];
}else{
	$password=null;	
}
if(isset($_POST["uid"])){
	$uid=intval($_POST["uid"]);
}else{
	$uid=null;	
}
if(isset($_POST["invitedby"])){
	$invitedby=intval($_POST["invitedby"]);
}else{
	$invitedby=null;	
}
if(isset($_POST["role"])){
	$role=$_POST["role"];
}else{
	$role=null;	
}
require_once $_SERVER["DOCUMENT_ROOT"].'/user_mod/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/user_mod/database.php';

class user{
	function user($dbase,$username,$email,$password,$uid,$settings,$invitedby,$role,$realname,$drole){
		$this->dbase=$dbase;
		$this->username=strtolower($username);
		$this->email=strtolower($email);		
		$this->password=$password;	
		$this->uid=$uid;
		$this->settings=$settings;
		$this->invitedby=$invitedby;
		$this->role=$role;
		$this->realname=$realname;
		$this->drole=$drole;		
	}
	//get the authtoken from user id
	public function gettoken($uid) {
		$sql="SELECT authtoken,role FROM users WHERE id=:uid";
		$strings= Array(
			Array('token'=>':uid','value'=>$uid),
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		return $response[0];
	}
	
	//get a list of all the users
	public function getusers() {
		$sql="SELECT id,username,realname,email,role,invitedby,status,date FROM users ORDER BY id ASC";
		$strings= Array(
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll(PDO::FETCH_ASSOC);
		return $response;
	}
	//get the username from the authtoken
	public function checktoken($token) {
		$sql="SELECT username FROM users WHERE authtoken=:token";
		$strings= Array(
			Array('token'=>':token','value'=>$token),
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		return $response[0]['username'];
	}
	//Get the user id, name and email from either username or email
	public function getid() {
		$sql="SELECT id,username,email FROM users WHERE username=:user OR username=:email OR email=:email OR email=:user";
		$strings= Array(
			Array('token'=>':user','value'=>$this->username),
			Array('token'=>':email','value'=>$this->email)
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		return($response);

	}
	//Get the email from user id
	public function getmail() {
		$sql="SELECT email FROM users WHERE id=:uid";
		$strings= Array(
			Array('token'=>':uid','value'=>$this->uid),
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		echo $response[0]['email'];
	}
	//Get user details from id
	public function getuser($uid,$return) {
		$sql="SELECT username,email,invitedby,realname,role,status,date FROM users WHERE id=:uid";
		$strings= Array(
			Array('token'=>':uid','value'=>$uid),
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll(PDO::FETCH_ASSOC);
		if($return){
			echo json_encode($response);
		}else{
			return $response;
		}
	}
	//Check if username and email is unique
	public function unique($excall){
		if(!$this->email){
			$this->email=$this->username;
		}
		$sql="SELECT (SELECT COUNT(username) FROM users WHERE username=:username) AS user, (SELECT COUNT(email) FROM users WHERE email=:email) AS email";		
		$strings= Array(
			Array('token'=>':username','value'=>$this->username),
			Array('token'=>':email','value'=>$this->email)
		);
			
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		if($excall){
			echo json_encode($response);
		}else{
			return $response;
		}
	}
	//Set a new password for a user
	public function setpass(){
			$this->hashpass();
			$sql = "UPDATE users SET hash='".$this->hash."',salt='".$this->salt."',status='Accepted' WHERE username='".$this->username."'";	
			$strings= Array(
			);		
			$response=$this->dbase->query($sql,$strings);
			$response=$response->fetchAll();
			$this->login();

	}
	//Delete a user
	public function deleteuser($uid){
			$sql = "DELETE FROM users WHERE id=:uid";	
			$strings= Array(
				Array('token'=>':uid','value'=>$uid)
			);		
			$response=$this->dbase->query($sql,$strings);
	}
	//Change a user role
	public function setrole($uid,$role){
			$sql = "UPDATE users SET role=:role WHERE id=:uid";	
			$strings= Array(
				Array('token'=>':uid','value'=>$uid),
				Array('token'=>':role','value'=>$role)
			);		
			$response=$this->dbase->query($sql,$strings);
	}
	//Update user details
	public function updateuser(){		
		$sql = "UPDATE users SET username=:username, realname=:realname, email=:email WHERE id=:uid";	
		$strings= Array(
			Array('token'=>':uid','value'=>$this->uid),
			Array('token'=>':username','value'=>$this->username),
			Array('token'=>':realname','value'=>$this->realname),
			Array('token'=>':email','value'=>$this->email)
		);		
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		print_r($response);			
	}	
	//reset a forgotten password by sending token link
	public function resetp(){
		$unique = $this->unique();
		if($unique[0]['user'] > 0 || $unique[0]['email'] > 0){
			$response=$this->getid();
			$this->uid=$response[0]['id'];
			$this->username=$response[0]['username'];
			$this->email=$response[0]['email'];
			$newtoken=$this->maketoken();
			$sql = "UPDATE users SET authtoken='".$newtoken."' WHERE username=:username returning authtoken";
			$strings= Array(
				Array('token'=>':username','value'=>$this->username),
			);
			
			$response=$this->dbase->query($sql,$strings);
			$response=$response->fetchAll();

			echo json_encode(array(
				'success'=>true,
				'message'=>'Success. Please check your email',
				'to'=>$this->email,
				'username'=>$this->username,
				'from'=>$this->settings->From,
				'sender'=>$this->settings->FromName,
				'token'=>$response[0]['authtoken']
			));
		}else{
			echo json_encode(array('success'=>false,'message'=>'No such user or email'));
		}
	}
	//Add a new user
	public function adduser(){
		$unique = $this->unique();
		if($unique[0]['user'] < 1 && $unique[0]['email'] < 1){
			$auth = $this->maketoken();
			$this->hashpass();
			$sql = "INSERT INTO users (username,hash,salt,email,authtoken,invitedby,date,role) VALUES (:username,'".$this->hash."','".$this->salt."',:email,'".$auth."',:invited,:date,'".$this->drole."') returning authtoken";			
			$strings= Array(
				Array('token'=>':username','value'=>$this->username),
				Array('token'=>':email','value'=>$this->email),
				Array('token'=>':invited','value'=>$this->invitedby),
				Array('token'=>':date','value'=>date('Y-m-d H:i:s'))
			);		
			$response=$this->dbase->query($sql,$strings);
			$response=$response->fetchAll();
			array_push($response,'success');
			echo json_encode($response);
		}else{
			if($unique[0]['user'] > 0){
				echo('Username "'.$this->username.'" not unique');
			}
			if($unique[0]['email'] > 0){
				echo('Email "'.$this->email.'" not unique');
			}
		}
	}
	//add the first admin user
	public function addfirstuser(){

		$unique = $this->unique();
		if($unique[0]['user'] < 1 && $unique[0]['email'] < 1){
			$auth = $this->maketoken();
			$this->hashpass();
			$sql = "INSERT INTO users (username,hash,salt,email,authtoken,date,role,status) VALUES (:username,'".$this->hash."','".$this->salt."',:email,'".$auth."',:date,'admin','Creator') returning id";			
			$strings= Array(
				Array('token'=>':username','value'=>$this->username),
				Array('token'=>':email','value'=>$this->email),
				Array('token'=>':date','value'=>date('Y-m-d H:i:s'))
			);		
			$response=$this->dbase->query($sql,$strings);
			$response=$response->fetchAll();
			$this->login();
		}else{
			if($unique[0]['user'] > 0){
				echo('Username "'.$this->username.'" not unique');
			}
			if($unique[0]['email'] > 0){
				echo('Email "'.$this->email.'" not unique');
			}
		}		
	}
	//Create a hashed password
	public function hashpass(){
			$this->salt= uniqid(mt_rand(), true);
			$this->hash = crypt($this->password,'$6$rounds=5000$'.$this->salt.'$');
	}
	//Create an authtoken
	public function maketoken(){
		$string = $_SERVER['HTTP_USER_AGENT'];
		$string .= time();
		$auth = md5($string);
		return $auth;
	}
	//Log a user in
	public function login(){
		$unique = $this->unique();
		if($unique[0]['user'] > 0 || $unique[0]['email'] > 0){
			$sql="SELECT (SELECT salt FROM users WHERE username=:username OR email=:username) AS salt, (SELECT hash FROM users WHERE username=:username OR email=:username) AS hash, (SELECT id FROM users WHERE username=:username OR email=:username) AS uid,(SELECT email FROM users WHERE username=:username OR email=:username) AS email,(SELECT username FROM users WHERE username=:username OR email=:username) AS username";

			$strings= Array(
				Array('token'=>':username','value'=>$this->username)
			);		
			$response=$this->dbase->query($sql,$strings);
			$response=$response->fetchAll();

			

			$hash =  crypt($this->password,'$6$rounds=5000$'.$response[0]['salt'].'$');
			if($hash == $response[0]['hash']){
				$uid=$response[0]['uid'];
				$username=$response[0]['username'];
				$email=$response[0]['email'];
				$auth = $this->maketoken();
				$sql="UPDATE users SET authtoken=:auth WHERE id=:uid";
				$strings= Array(
					Array('token'=>':auth','value'=>$auth),
					Array('token'=>':uid','value'=>$uid)
				);					
				$response=$this->dbase->query($sql,$strings);
				$response=$response->fetchAll();
				setcookie('user','{"uid":"'.$uid.'","authtoken":"'.$auth.'","authorised":true}',time()+(3600*24*30),'/');

				echo json_encode(array('message'=>'logged in','error'=>false));

				
			}else{
				echo json_encode(array('message'=>'Incorrect password','error'=>true,'type'=>'wrong'));
			}			
		}else{
			echo json_encode(array('message'=>'No such user or email','error'=>true,'type'=>'none'));
		}
	}
	public function logout(){
		unset($_COOKIE['user']);
		setcookie('user', null, time() - 3600,'/');
	}
}
$dbase = new dbase($dbh);
$user = new user($dbase,$username,$email,$password,$uid,$settings,$invitedby,$role,$realname,$settings->drole);
if(isset($_COOKIE['user'])){
	$this_user=json_decode($_COOKIE['user']);	
	$foo=$user->gettoken($this_user->uid);
	$user->role=$foo['role'];
	$user->perms=$settings->roles[$user->role]['perms'];
	
	if($foo['authtoken'] == $this_user->authtoken){
		$user->auth=true;
		$user->uid=$this_user->uid;
	}else{
		$user->logout();
		$user->auth	= false;
		$user->uid=null;
	}	
}else{
	$user->auth	= false;
	$user->uid=null;
}
if(isset($_POST["method"])){
	if($_POST["method"]=='login'){
		$user->login();
	}
	if($_POST["method"]=='adduser' && $user->auth){
		$user->adduser();
	}
	if($_POST["method"]=='unique'){
		$user->unique(true);
	}
	if($_POST["method"]=='getmail'){
		$user->getmail();
	}
	if($_POST["method"]=='setpass'){
		$user->setpass();
	}
	if($_POST["method"]=='resetp'){
		$user->resetp();
	}
	if($_POST["method"]=='deluser' && $user->auth){
		$user->deleteuser($_POST["duid"]);
	}
	if($_POST["method"]=='setrole' && $user->auth){
		$user->setrole($_POST["uid"],$_POST["role"]);
	}
	if($_POST["method"]=='updateuser' && $user->auth){
		$user->updateuser();
	}
	if($_POST["method"]=='getuser' && $user->auth){
		$user->getuser($_POST["guid"],true);
	}

}
if(isset($_POST["blinduser"])){
	if($_POST["blinduser"]=='firstuser'){
		$user->addfirstuser();
	}
}
?>
