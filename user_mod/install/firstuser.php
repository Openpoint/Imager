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
  * Form to set up the first run admin account
  * 
  * */
?>

		<?php if(is_writable($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php')){ ?>
			<div style='background:red;color:white;padding:5px;margin-bottom:10px;'>Please set the file "[document root]/user_mod/settings.php" to <strong>read-only</strong> before proceeding.<br><br>Reload the page when done.</div>
		<?php } ?>
			<div>Set up the the main admin user account</div>
			<form id='firstuser' name='firstuser' action="/user_mod/user/users.php" method="post">
			<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
			<input class='removefield' style='display:none' type="text" name="blinduser" value='firstuser'>
			<input class='removefield' style='display:none' type="password" name="blindpass">
				<fieldset>
					<div class='im_ftitle'>Your Username:</div>
					<input type='text' name='username' placeholder='Name' required />
					<div class='im_ftitle'>Your Email:</div>
					<input type='text' name='email' placeholder='Email' required />
					<input type='text' name='confemail' placeholder='Confirm Email' required />
				</fieldset>
				<fieldset>
					<div class='im_ftitle'>Your Password:</div>
					<input type='password' name='password' placeholder='Password' required />
					<input type='password' name='confpassword' placeholder='Confirm Password' required />
				</fieldset>
				<?php
				if(!is_writable($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php')){ ?>
				<input type='submit' value='Finish' />
				<?php } ?>
				
			</form>	
