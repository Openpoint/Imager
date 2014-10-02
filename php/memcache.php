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
  * Instantiate the memcache servers defined in /settings.php
  * 
  * */   
?>
<?php
$MEMCACHE_SERVERS = $settings->memcacheservers;
$memcache = new Memcache();
foreach($MEMCACHE_SERVERS as $server){
    $memcache->addServer($server);
}
?>
