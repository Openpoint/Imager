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
  * The root construct of the front page.
  * 
  * */   
?>
<?php
$isfront=true;
//enable firebug debuging with firephp http://www.firephp.org/
//include 'php/firephp.php';

require_once('php/connect.php');
$title=' Just the images...';
$locate->descrip='Imager gets just the images from the webpage you want to see and presents them in an easy, explorable interface. See, find, explore and discover images in a better way.';

include 'partials/head.php';

$imagebatch=$imager->getall();
		foreach($imagebatch as $image){
			//print($image['id'].'<br>');
		}
?>
<body id='front' <?php if($user->auth || !$settings->moderated){ ?>class='showbar'<?php }else{ ?>class='nobar'<?php } ?>>
	<div id='header'>
		<div class='inner'>
			<a><?php echo $settings->sitename; ?> </a>...  Just the images 
			<span class='blink icon-help-circled help'></span>
			<div id='alpha'>alpha</div>			
		</div>		
	</div>
	<?php include 'partials/controls.php'; ?>
	<div id='images'>
		<div id='fetching'>
			<div class='fetching'>Fetching images ....</div>
			<div class='count'></div>
		</div>
		<div class='im_inner'>
		<?php
		foreach($imagebatch as $image){			
			print("\n\t\t\t".'<div class="image" data-id="'.$image['id'].'">'."\r\n\t\t\t\t".'<a href="/page.php/'.$image['id'].'">'."\r\n\t\t\t\t\t".'<img src="'.$image['image'].'" alt="'.$image['title'].'" title="'.$image['title'].'" onerror="this.onerror=null; this.src=\'/images/missing.jpg\'"/>'."\r\n\t\t\t\t".'</a>'."\r\n\t\t\t".'</div>'."\r\n");
		}
		?>
		</div>
	<div id='bottom'>
		<div class='inner'>
			
			<div class='blink icon-shuffle shuffle'></div>
			<div class='home'><a href='/' class='blink icon-home-circled'></a></div>

		</div>
		<div class='band'></div>
	</div>
	<?php 
	include 'user_mod/modal/modal.html'; 
	if(file_exists('partials/tracking.html')){
		include 'partials/tracking.html';
	}
	?>
</body>
</html>
