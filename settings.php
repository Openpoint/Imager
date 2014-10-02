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
  * The non-user Imager default settings. For user related settings, see /user_mod/settings.php
  * 
  * */   
?>

<?php
$settings=new stdClass();

$settings->sitename=Imager;										//the preferred name of your imager
$settings->moderated=true; 										//can just verified users add in links? 
$settings->addlink_message='Add Your Own Website Page Link'; 	//the placeholder text for the URL input bar
$settings->pinterest=true; 										//do you want to add Pinterest hover pins to images?
$settings->disqus=false; 										//do you want to add Disqus comments to pages? You will have to set up a Disqus account for your domain first.

//-----------------------------------------Cache---------------------------------------------------

$settings->front_cache=10; 	//How long in MINUTES to cache the front page
$settings->back_cache=1; 	//How long in DAYS to cache the back pages
$settings->clearfrontondelete=true; //flush the front page cache when a page is deleted
$settings->clearfrontonadd=true; //flush the front page cache when a page is added

$settings->memcacheprefix=$_SERVER['HTTP_HOST'];
$settings->memcacheservers= array(
    "127.0.0.1", //web1
);

//-----------------------------------------Whitelist---------------------------------------------------
/* a comma seperated list of domains you want to limit linking to, eg: 
 * twitter.com,pinterest.com,4chan.org
 * 
 * Not yet implemented - for beta release
 */
$settings->allowed_domains='

';

//-----------------------------------------Performance---------------------------------------------------
$settings->initload=3000; //the max-time for getting the first batch of images and displaying them in SECONDS
$settings->frontlength=250; //the max amount of links to fetch for the front page load - *****TODO**** Progressive scroll loading for front page


//-----------------------------------------Database and Email---------------------------------------------
//Please change settings in '[root]/user_mod/settings.php'

$settings->useragent_change = array(
'You’re using a web browser that isn’t supported by Facebook',
'Update Your Browser',
'get the latest version of your preferred browser'
);
$settings->allowed_domains = preg_replace('/\s+/', '', $settings->allowed_domains);
if (strlen($settings->allowed_domains) > 0){	
	$foo = explode(',',$settings->allowed_domains);
	$settings->allowed_domains=array();
	foreach($foo as $domain){
		array_push($settings->allowed_domains,$domain);	
	}
}else{
	$settings->allowed_domains=false;	
}
$settings->front_cache=$settings->front_cache*60;
$settings->back_cache=$settings->back_cache*24*60*60;

include 'user_mod/settings.php'
?>

