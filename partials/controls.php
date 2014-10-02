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
  * Structure for the controls to be included in pages
  * 
  * */   
?>

	<div id='controls'>
		<div id='loading'>
			<div class='progress'>
				<div class='inner'></div>
			</div>
		</div>
		<?php if(($user->perms['post'] || !$settings->moderated) && ($isfront || $isback)){ ?>
		<form name="submitform" id="submitform" action="/php/submit.php" method="post">
			<input type="text" style='color:orange' name="url" placeholder="<?php print $settings->addlink_message; ?>" required>
			<input type="submit" value="FETCH">
		</form>
		<?php } ?>
		<?php if(!isset($validatepage) && !isset($install)){include 'partials/menu.php';} ?>
		<div id='data' data-initload='<?php print $settings->initload; ?>'></div>
	</div>
