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
  * Open and close a modal
  * 
  * */

var modal=new Array();
modal.loaded=false;
modal.open=function(source){
	$('#im_mask').css({'display':'block','opacity':1});
	$('#im_modal').stop().css({'display':'block','top':$(window).scrollTop()+150+'px'}).animate({'opacity':1});
	if(source){
		$('#im_modal .infobox .inner').load(source,function(){
			modal.loaded=true;
		});
	}
}
modal.close=function(){
	modal.loaded=false;
	$('#im_modal,#im_mask').stop().animate({'opacity':0},function(){
		$('#im_modal,#im_mask').css({'display':'none'})			
	});	
}
$(document).ready(function(){
	
	$('#mod_close, #im_mask ').click(function(e){
		modal.close();
	})
	$('#im_modal .infobox').click(function(e){
		e.stopPropagation();
	});		
})
