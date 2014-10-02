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
  * The root construct of link pages.
  * 
  * */   
?>

<?php
$isback=true;
//enable firebug debuging with firephp http://www.firephp.org/
//include 'php/firephp.php';

require_once('php/connect.php');

//get the url from id
$url = parse_url($_SERVER["REQUEST_URI"]);

$url = intval(end(explode("/", $url['path'])));
$pid=$url;
$url = $imager->geturl($url);

include 'php/process.php';
include 'partials/head.php';


?>
<body id='back' <?php if($user->auth || !$settings->moderated){ ?>class='showbar'<?php }else{ ?>class='nobar'<?php } ?>>
	<div id='pid' style='display:none'><?php print $pid ?></div>
	<div id='header'>
		<div class='inner'>
			<div class='hlogo'>
				<a href='/'><?php echo $settings->sitename; ?>  </a>
				<span class='blink icon-help-circled help'></span>
			</div>
			<div id='remove'>Not what you expected?&nbsp;&nbsp;&nbsp;<span class='delete'>DELETE THIS PAGE</span></div>			
		</div>
		<div id='alpha'>alpha</div>
		
	</div>
	
	<?php 
	include 'partials/controls.php';
	?>
	<div id='pinfo'>
		<h1><a id='title' href='<?php print $resource=$locate->protocol.$locate->base.$locate->url.$locate->file.$locate->query; ?>' target='_blank'><?php print $title; ?></a></h1>
		<span class='description'><?php print $locate->descrip; ?></span>
	</div>

	<div id='images'>
	<div id='fetching'>
		<div class='fetching'>Fetching images ....</div>
		<div class='count'></div>
	</div>
		<div class='im_inner'>
		<?php
		foreach($imagebatch as $image){

			if($image->alt && $image->title && $image->alt != $image->title){
				$tag=$image->title.' | '.$image->alt;
			}else if($image->alt){
				$tag=$image->alt;
			}else{
				$tag=$image->title;
			}
			print("\n\t\t\t".'<div class="image">'."\r\n\t\t\t\t".'<a href="'.$image->url.'" data-lightbox="image" data-title="'.$image->alt.'">'."\r\n\t\t\t\t\t".'<img src="'.$image->url.'" alt="'.$image->alt.'" title="'.$tag.'" onerror="this.onerror=null;this.src=\'/images/missing.jpg\';" />'."\r\n\t\t\t\t".'</a>'."\r\n\t\t\t".'</div>'."\r\n");				
		}
		?>
		

		
		</div>
		
		<?php 
		//optionally include a comment system
		if($settings->disqus){
			include 'partials/comments.html';
		} 
		?>
	</div>
	

    	
	
	<div id='bottom'>
		<div class='inner'>
			<div class='lr prev'>
				<div class='tinner'></div>
				<div class='icon icon-angle-circled-left'></div>	
			</div>			
			<div class='blink icon-shuffle shuffle'></div>
			<div class='home'><a href='/' class='blink icon-home-circled'></a></div>
			<div class='lr next'>
				<div class='tinner'></div>
				<div class='icon icon-angle-circled-right'></div>				
			</div>
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
