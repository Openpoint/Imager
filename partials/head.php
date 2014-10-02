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
  * Head to be included in all pages
  * 
  * */   
?>
<!doctype html>

<html lang="en">
<head>
	
	<!--
	................................................................................
	 O$~~+DO....N......OO~:,=DO.    +O     +$.    ..8Z:.     $NZ.   .N,..????D?????. 
	 O? ...D?...N.... ,D=.....:N....+O.....+$......8=,D .... $I87 ...N,..... 8: ....
	 O?   .D= ..N....   I8..  .~D.  +O.    +$.   .:O..O?     $I.O$. .N,..... 8: ....
	 OD88D8=....N... ~...,D+...,N,..+O.....+$.... D,...D.... $I .D? ,N,..... 8: ....
	.O? ........N... O,    +D..~D   +O.   .+$.  .?Z.   7I.   $I ..D?.N,..... 8: ....
	.O? ........N....,N......87N,...,D.....$I... D.....,N... $I ...DIN,..... 8: ....
	 O? ........N......OO~,,~8Z8.....+N~,:OZ....$O??????O7 . $I ...,NN,......8: ....
	................................................................................
	-->
	
	<meta name="designer" content="Piquant Media">	

	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<title><?php print 'Imager | '.$title; ?></title>
	<meta name="description" content="<?php print $locate->descrip; ?>">
	<link rel="icon" type="image/png" href="/favicon.png">
	<!--
	
	Resources are sent to MIN for compression. Reverse the commenting logic for development
		
	<link rel="stylesheet" href="/css/reset.css" />
	<link rel="stylesheet" href="/css/style.css" />
	<link rel="stylesheet" href="/css/fonts/stylesheet.css" />
	<link rel="stylesheet" href="/css/fonts/fontello/css/fontello.css" />	
	<link rel="stylesheet" href="/user_mod/modal/css/modal.css" />
	<link rel="stylesheet" href="/user_mod/modal/css/fontello/css/mod_animation.css" />
	<link rel="stylesheet" href="/user_mod/modal/css/fontello/css/mod_fontello.css" />
	<link rel="stylesheet" href="/js/lightbox/css/lightbox.css" />	
	<script src="/js/jquery-1.11.1.min.js"></script>
	<script src="/js/jquery.cookie.js"></script>
	<script src="/js/imagesloaded.pkgd.min.js"></script>
	<script src="/js/jquery.images-ready.js"></script>
	<script src="/js/packery.pkgd.min.js"></script>
	<script src="/user_mod/modal/js/modal.js"></script>
	<script src="/user_mod/user/js/users.js"></script>
	<script src="/js/script.js"></script>
	<script src="/js/lightbox/js/lightbox.min.js"></script>
	-->	
	<link rel="stylesheet" href="/min/g=css" />
	<script src="/min/g=js"></script>

	
	<?php if($settings->pinterest){ ?>
		<!-- Please call pinit.js only once per page -->
		<script type="text/javascript" async  data-pin-shape="round" data-pin-height="32" data-pin-hover="false" src="//assets.pinterest.com/js/pinit.js"></script>
	<?php } ?>
</head>
<?php flush(); ?>
