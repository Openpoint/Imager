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
  * The root construct of the user management page.
  * 
  * */   
?>

<?php
$isfront=false;
$userpage=true;
//enable firebug debuging with firephp http://www.firephp.org/
//include 'php/firephp.php';

require_once('php/connect.php');

$title=' Just the images...';
$locate->descrip='Imager gets just the images from the webpage you want to see and presents them in an easy, explorable interface. See, find, explore and discover images in a better way.';

include 'partials/head.php';

?>
<body id='usermanage'>
	<div id='header'>
		<div class='inner'>
			<span  class='hlogo'><a href='/'>Imager </a>...  Manage Users</span>
			<div id='alpha'>alpha</div>			
		</div>		
	</div>
	<?php include 'partials/controls.php'; ?>
	<?php if($user->perms['viewusers']){ ?>
	<div id='form' class='centerdiv'>
		<?php include 'user_mod/user/forms/userform.php'; ?>
	</div>
	<?php }else{ ?>
	<div class='im_text' style='color:orange;margin-top:60px'>You do not have permission to view this page</div>
	<?php } 
	include 'user_mod/modal/modal.html';
	?>
</body>
</html>
