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
  * Delete an user modal confirmation
  * 
  * */
?>
<?php
$username=$_POST["username"];
?>
<div>Are you sure you want to delete the user <strong class='im_caps'><?php echo $username?></strong></div>
<div>This cannot be undone.</div>
<div>
	<input id='im_delete' style='background:orange;border-color:orange' type='submit' value='YES'>
	<input id='im_cancel' type='submit' value='CANCEL'>
</div>
