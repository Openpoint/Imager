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
  * The base install location.
  * Requires the 'UserMod' package to be included in body
  * 
  * */   
?>

<?php
//invalidate cache to allow for settings file permissions to be updated on reload
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$install=true;
$title=' Set up your database...';
$locate->descrip='Imager gets just the images from the webpage you want to see and presents them in an easy, explorable interface. See, find, explore and discover images in a better way.';

include $_SERVER['DOCUMENT_ROOT'].'/partials/head.php';
?>
<body id='dbase'>
	<div id='header'>
		<div class='inner'>
			<a>Imager </a>...  Install your Imager
			<div id='alpha'>alpha</div>			
		</div>		
	</div>
	<?php include $_SERVER['DOCUMENT_ROOT'].'/partials/controls.php'; ?>
	<?php include $_SERVER['DOCUMENT_ROOT'].'/user_mod/install/install.php'; ?>	
</body>
</html>
