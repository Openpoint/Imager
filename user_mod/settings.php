<?php
/*
 * UserMod - An ajax modal based user management system
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
  * Settings for database, email and users. 
  * 
  * */
?>

<?php
//-----------------------------------------Database---------------------------------------------------
$settings->dbase=array(
    'username'=>'',
    'password'=>'',
    'db_name'=>'',
    'host'=>'',
    'port'=>''
);
$settings->installfile='/install.php'; //The location of your install hook file

//-----------------------------------------Email---------------------------------------------------
$settings->From='noreply@imager.buzz'; 	//Use your domain name or SMTP domain name appropriately or mail will be marked as spam
$settings->FromName='Imager';
$settings->isSMTP=false; 				//are you using a SMTP server

//your SMTP Settings
$settings->Host='';
$settings->SMTPAuth=true; 				//Enable SMTP authentication
$settings->Username='';
$settings->Password='';
$settings->SMTPSecure=''; 			//Enable encryption, 'ssl' also accepted



//-----------------------------------------Roles---------------------------------------------------
//default role for new users
$settings->drole='user';
//customise 'perms' to suite your application
$settings->roles=array(
	"admin"=>array(	//do not remove or change admin from first in list
		"description"=>"Admins are Super Users. They have all privilidges including changing site settings",
		"perms"=>array(
			"post"=>true,
			"invite"=>true,
			"deluser"=>true,
			"changerole"=>true,
			"delcontent"=>true,
			"message"=>true,
			"viewusers"=>true,
			"flushcache"=>true,
			"settings"=>true
		)
		
	), 
	"supereditor"=>array(
		"description"=>"Super Editors can invite new users, manage content and users",
		"perms"=>array(
			"post"=>true,
			"invite"=>true,
			"deluser"=>true,
			"changerole"=>true,
			"delcontent"=>true,
			"message"=>true,
			"viewusers"=>true,
			"flushcache"=>true,
			"settings"=>false
		)
		
	),
	"editor"=>array(
		"description"=>"Editors can invite new users and manage content",
		"perms"=>array(
			"post"=>true,
			"invite"=>true,
			"deluser"=>false,
			"changerole"=>false,
			"delcontent"=>true,
			"message"=>true,
			"viewusers"=>true,
			"flushcache"=>true,
			"settings"=>false
		)
		
	),
	"user"=>array(
		"description"=>"Regular users can add content",
		"perms"=>array(
			"post"=>true,
			"invite"=>false,
			"deluser"=>false,
			"changerole"=>false,
			"delcontent"=>false,
			"message"=>false,
			"viewusers"=>false,
			"flushcache"=>false,
			"settings"=>false
		)
		
	)
)
?>
