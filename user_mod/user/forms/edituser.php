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
  * Form to edit a user's details
  * 
  * */
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/user_mod/user/users.php');
$foo=$user->getuser($user->uid);

$user->email=$foo[0]['email'];
$user->username=$foo[0]['username'];
$user->realname=$foo[0]['realname'];
?>

<div id='im_static'>
	<form name="mod_details" id="mod_details" action="/user_mod/user/users.php" method="post">
		
		<fieldset>
			<div class='im_ftitle'>Your real name:</div>
			<input type="text" name="realname" value="<?php echo $user->realname; ?>" placeholder="Your real name (optional)" autocomplete="off">
		</fieldset>
		<fieldset>
			<div class='im_ftitle'>Your preferred username:</div>
			<input type="text" name="username" value="<?php echo $user->username; ?>" placeholder="Your Username" autocomplete="off" required><br>
		</fieldset>
		<fieldset>
			<div class='im_ftitle'>Your preferred email:</div>
			<input type="text" name="email" value="<?php echo $user->email; ?>" placeholder="Your Email" autocomplete="off" required><br>
			<input type="text" name="confemail" placeholder="Confirm Email" autocomplete="off"><br>
		</fieldset>
			<input type="submit" value="Save">		
	</form>	

	<div id='im_messages'>
		<div id='mod_mess'></div>
		<div id='mod_error'></div>
		<div id='mod_spinner'>
	</div>
</div>
