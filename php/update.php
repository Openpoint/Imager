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
  * Update content and cache
  * 
  * */   
?>

<?php
//require_once('FirePHPCore/FirePHP.class.php');
require_once('memcache.php');
include 'connect.php';
$locater=new stdClass();
$locater->id=htmlspecialchars($_POST["id"]);
$locater->title=htmlspecialchars($_POST["title"]);
$locater->image=htmlspecialchars($_POST["image"]);
$locater->size=htmlspecialchars($_POST["size"]);

//$firephp = FirePHP::getInstance(true);
//$firephp->log($locater);

if($locater->image == 'delete'){
	$imager->delete($locater->id);
	if($settings->clearfrontondelete){
		$memcache->delete($settings->memcacheprefix."getall");
	}
}else if($locater->image =='flush'){
	//$memcache->flush(1); //does not work for multi tenancy as it clears the whole store, not only for this site. Memcache clear by prefix is a complicated workaround I don't understand.
	$memcache->delete($settings->memcacheprefix.'getall');	
	$memcache->delete($settings->memcacheprefix."page".$locater->id);
	return('flushed');
}else{
	$imager->update($locater->id,$locater->title,$locater->image,$locater->size);		
}
?>
