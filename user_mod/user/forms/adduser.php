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
  * Form to add a new user
  * 
  * */
?>

<?php
$realname=$_POST['realname'];
?>


<form name="adduser" id="adduser" action="/user_mod/user/users.php" method="post">
	<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
	<input class='removefield' style='display:none' type="text" name="blinduser">
	<input class='removefield' style='display:none' type="password" name="blindpass">
	
	<fieldset>
		<legend>New User:</legend>

		<input type="text" name="username" placeholder="New Username" autocomplete="off" required><br>
		<input type="text" name="email" placeholder="Email" autocomplete="off" required><br>
		<input type="text" name="confemail" placeholder="Confirm Email" autocomplete="off" required><br>
	</fieldset>
	<fieldset>
		<legend>Your Details:</legend>
		<input type="text" name="yourname" placeholder="Your Name" value='<?php echo $realname; ?>' autocomplete="off" required><br>
		<textarea name="message" placeholder="Optional Message" autocomplete="off"></textarea><br>
	</fieldset>
	<input type="submit" value="Invite">
</form>

