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
  * Form to set a new password
  * 
  * */
?>
<div id='im_static'>
	<form name="mod_reset" id="mod_reset" action="/user_mod/user/users.php" method="post">
		<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
		<input class='removefield' style='display:none' type="text" name="blinduser">
		<input class='removefield' style='display:none' type="password" name="blindpass">
		
		<fieldset>
			<div class='im_text'>Enter your new password, it must be at least 6 characters long.</div>

			<input type="password" name="password" placeholder="New password" autocomplete="off" required><br>
			<div class='im_text'>Confirm your new password.</div>
			<input type="password" name="confpassword" placeholder="Confirm Password" autocomplete="off" required><br>
			<input style='display:none' type="text" name="username" value="<?php echo $username; ?>">
		</fieldset>

		<input type="submit" value="Save">
	</form>
	<div id='im_messages'>
		<div id='mod_mess'></div>
		<div id='mod_error'></div>
		<div id='mod_spinner'>
	</div>
</div>
