<?php
/*
 * Imager - An online visual link bookmarker and image scraper
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
  * The user menu code for inclusion in relevant pages
  * 
  * */   
?>

<div id='menu'>
	<div id='menlogo' class='icon-menu'></div>
	<div id='usermenu'>
		<ul>
			<?php if($user->auth){ ?>
				<?php if($isback){ ?>
				<li  class='first' style='color:orange' onclick='imager.remove()'>Remove Page</li>
				<?php } ?>
				<li <?php if(!$isback){ ?>class='first' <?php } ?>><a href='/user.php'>Me</a></li>
				<li onclick='users.logout()'>Logout</li>
				<?php if($user->perms['flushcache']){ ?>
				<li onclick='imager.flush()'>Flush Cache</li>
				<?php } ?>
				<?php if($user->perms['viewusers']){ ?>
				<li><a href='/usermanage.php'>Users</a></li>	
				<?php } ?>
				<li onclick='users.adduser()'>Invite</li>		
			<?php }else{ ?>
			<li class='first' onclick='users.showlogin()'>Login</li>		
			<?php } ?>
		</ul>
		<div onclick='imager.menclose()' id='menclose'>x</div>
	</div>
</div>
