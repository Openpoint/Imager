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
  * Inclusion logic for validate password page. Include in your validation URL page
  * 
  * */
?>
<?php
if($user->auth){
	$user->logout();
	header("Refresh:0");
}
if(isset($_GET["token"])){
	$username=$user->checktoken($_GET["token"]);

	if($username){
		include $_SERVER["DOCUMENT_ROOT"].'/user_mod/user/forms/reset.php';
	}else{
		?><div class='im_text' style='color:orange;margin-top:60px'>Your token is invalid or has already been used.</div><?php
	}
}else{
	?><div class='im_text' style='color:orange;margin-top:60px'>No Token Found</div><?php
}
?>
