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
  * Client side actions pertaining to Imager. For Users and Modals look under the /user_mod directory.
  * 
  * */

var size = new Array();
var images = new Array();
var imager = new Array();

imager.gotimer;
imager.reload2=true;
imager.busyremove=false;
imager.busyupdating=false;
var count=0;
var giveup;
var giveup_timer;
var scrolltop=$.cookie('scrolltop');

//------------------------------------------------Page Functions---------------------------------

//main call to fetch and process images
imager.gopage=function(){
	
	var batch = images;
	images=new Array();
	
	if(!imager.reload){
		$('#images .im_inner').html('');
	}
	
	if(batch.length > 0){
		//sort by image size and get biggest for front page
		if($('#front').length==0){
			batch.sort(imager.ascending);
			if(typeof imager.firstimage == 'undefined'){
				imager.firstimage=new Array();
				imager.firstimage.src = batch[0].elem.find('img').attr('src');
				imager.firstimage.size= batch[0].origsize;
			}
		//sort by image id for front
		}else{
			batch.sort(imager.id);

		}

		delete imager.newimages;
		imager.newimages=new Array();					
		$.each(batch,function(index,value){
			if(!imager.reload){	
				$('#images .im_inner').append(value.elem);
			}else{
				//console.log(value.elem[0]);
				imager.newimages.push(value.elem[0]);
			}
		})

		$('#images .im_inner').css({'visibility':'visible'}).animate({'opacity':1},function(){
			//scroll the front page if coming back	
			if(scrolltop > 0 && $('#front').length > 0){
				$(window).scrollTop(scrolltop);
				scrolltop=0;
				$.cookie('scrolltop', 0);
				
			}		
		});
		
							
		//submit the back page image and title data to database for link saving
		if($('#back').length > 0){
			imager.busyupdating=true;
			if(imager.biggest < imager.firstimage.size){			
				$.ajax({
					url : '/php/update.php',
					type: "POST",
					data : {
					id:$('#pid').html(),
					image:imager.firstimage.src,
					size:imager.firstimage.size,
					title:$('#title').html()						
					},
					dataType:'json',
					encode: true
				}).done(function(data){
					//data: return data from server
					//console.log(data);
					imager.busyupdating=false;
				})
			}else{
				imager.busyupdating=false;
			}
		//add front page links and scroll cookie
		}else{					
			$('.image a').click(function(e){
				$.cookie('scrolltop', $(window).scrollTop());
				$(window).location($(this).attr('href'));
				e.preventDefault()		
			})
		}
		
		
		
		//do the magic packery arranging - check the license terms!!!!
		if(typeof imager.$container == 'undefined'){
			imager.$container = $('#images .im_inner');
			imager.$container.packery({
				gutter:0,
				itemSelector: '.image' ,
				columnWidth: 25,
			});
		}else{
			imager.$container.append(imager.newimages);
			imager.$container.packery('appended', imager.newimages);
		}	
		
		$('#fetching').css({'display':'none'});
	}else{
		//imager.remove();
	}
	imager.oldimages = images;
	clearTimeout(imager.gotimer);
}
//bypass stuck images and proceed
imager.repeater=function(time){
	imager.gotimer=setTimeout(function(){		
		imager.gopage();
		imager.reload=true;
		if(imager.remaining > 0){
			imager.repeater(giveup/2);
		}
	},time)
}
//delete the page
imager.remove=function(vol){
	$('#fetching').css({'display':'block'}).html('Removing the page.....');
	$.ajax({
		url : '/php/update.php',
		type: "POST",
		data : {
		id:$('#pid').html(),
		image:'delete'						
		},
		dataType:'json',
		encode: true
	}).done(function(data){
		imager.busyremove=false;
		//console.log(data);
		if(!vol){
			imager.shuffle();
		}
	})	
}
//flush the cache
imager.flush=function(){
	$('#fetching').css({'display':'block'}).html('Flushing the cache.....');
	$.ajax({
		url : '/php/update.php',
		type: "POST",
		data : {
		image:'flush',
		id:parseInt($('#pid').html())					
		},
		encode: true
	}).done(function(data){
		//data: return data from server
		setTimeout(function(){
			$('#fetching').animate({'opacity':0},function(){
				$('#fetching').css({'display':'none','opacity':1})			
			})
		},2000);
	})	
}
//Sort the array of images biggest to smallest
imager.ascending = function( a, b ) {
	return b.origsize-a.origsize;
}
//Sort the array of images by id (order added for front);
imager.id = function( a, b ) {
	return b.id-a.id;
}
//parse the images and assign size brackets
imager.maxsize = function(fsize){
	if($(window).width() < 400){
		var factor=2;
	}else{
		var factor=1;
	}
		
	var container=new Array();
	container.ratio=size[0]/size[1];
	
	if(fsize[0] >= fsize[1]){
		if(fsize[0] >= 1200){
			container.width = 400/factor;
		}else if(fsize[0] >= 900){
			container.width = 350/factor;
		}else if(fsize[0] >= 700){
			container.width = 300/factor;
		}else if(fsize[0] >= 500){
			container.width = 250/factor;
		}else if(fsize[0] >= 300){
			container.width = 200/factor;
		}else if(fsize[0] >= 100){
			container.width = 100/factor;
		}else if(fsize[0] >= 50){
			container.width = 50/factor;
		}
		container.height = Math.floor((fsize[1]/fsize[0])*container.width);
		container.css="width:100%; height:auto";
	}else{		
		if(fsize[1] >= 1200){
			container.height = 400/factor;
		}else if(fsize[1] >= 900){
			container.height = 350/factor;
		}else if(fsize[1] >= 700){
			container.height = 300/factor;
		}else if(fsize[1] >= 500){
			container.height = 250/factor;
		}else if(fsize[1] >= 300){
			container.height = 200/factor;
		}else if(fsize[1] >= 100){
			container.height = 100/factor;
		}else if(fsize[1] >= 50){
			container.height = 50/factor;
		}
		if(container.ratio*container.height >= 50){
			container.width = Math.floor((container.ratio*container.height)/50)*50;
		}else{
			container.width = 50/factor;			
		}
		container.height = Math.floor(container.width/container.ratio);		
		container.css="height:100%; width:auto";			
	}
	container.totsize=container.height*container.width;	
	return container;
}
//do the loading countdown
imager.timer=function(){
	var timer=setTimeout(function(){
		giveup_timer--;
		$('#fetching .count').html(giveup_timer);
		//console.log(giveup_timer);
		if(giveup_timer > 0){
			imager.timer()
		}
	},500);
}
//prepare what is seen from the cookie
if(typeof $.cookie('random.seen')=='undefined'){
	imager.seen=new Array();
}else{
	var seen=$.cookie('random.seen');
	imager.seen=$.map(seen.split(','),function(value){return parseInt(value);})	
}
//do a random shuffle
imager.shuffle=function(){
	imager.new_id = imager.idarray[Math.floor(Math.random()*imager.idarray.length)];
	imager.all_length=imager.idarray.length;

	if($.inArray(imager.new_id,imager.seen) == -1 && imager.new_id != parseInt($('#pid').html())){
		window.location='/page.php/'+imager.new_id;
	}else{
		if(imager.seen.length < imager.all_length-1){
			imager.shuffle();
		}else{
			$.removeCookie("random.seen");
			delete imager.seen;
			imager.seen=new Array();
			window.location='/';
		}
	}	
}
//close the user menu
imager.menclose=function(){
	$.cookie("menu", 'close',{ path: '/'});
	$('#usermenu').stop().animate({'height':0});
	$('#menlogo').attr('class','icon-menu');
}
//abort if no useable images are found
imager.abort=function(x){
	if($('#back').length > 0){
		//console.log(x);
		imager.remove(true);
		modal.open('/partials/modals/fail.html');
	}
	
}
//retry fetching a page
imager.retry=function(){
	$('#submitform input[name="url"]').val($('#title').attr('href'));
	$('#submitform').submit();
}
//------------------------------------------------End Functions---------------------------------

$(window).resize(function(){
	$('#controls input[type=submit]').css({'left':$('#submitform').width()/2-25});
});
$(document).ready(function(){
	
	$('#controls input[type=submit]').css({'left':$('#submitform').width()/2-25});
	$('.help').click(function(){ //help modal
		modal.open('/partials/modals/info.html');
	})
	
	giveup=parseFloat($('#data').attr('data-initload'));
	giveup_timer=Math.ceil(giveup/500);
	if($.cookie('imager.first')=='true'){
		$.cookie('imager.first',false)
		$('#remove').css({'display':'block'});
		$('.delete').click(function(){
			imager.remove();	
		});		
	}
	$('#alpha').click(function(){		
		//imager.remove();
	});
	
	$('.piquant').click(function(e){
		window.open($(e.delegateTarget).attr('href'),'_blank');
	});
	
	//menu controls
	var menheight = $('#usermenu').height();
	if($.cookie("menu")=='close' || !$.cookie("menu")){
		$('#usermenu').height(0);
	}else{
		$('#menlogo').attr('class','icon-menu-outline');
	}
	$('#menlogo').click(function(){
		if($('#usermenu').height() == 0){
			$.cookie("menu", 'open',{ path: '/'});
			$('#usermenu').stop().animate({'height':menheight});
			$('#menlogo').attr('class','icon-menu-outline');
		}else{
			imager.menclose();
		}
	});
	//keep track of what has been seen
	if($('#back').length > 0){
		if(typeof $.cookie('random.seen')=='undefined'){			
			imager.seen.push(parseInt($('#pid').html()));
			$.cookie('random.seen', imager.seen.join(','), { expires: 30, path: '/' });
		}else{			
			if($.inArray(parseInt($('#pid').html()),imager.seen) == -1){
				$.cookie('random.seen',$.cookie('random.seen')+','+$('#pid').html(), { expires: 30, path: '/' });
				imager.seen=$.map(seen.split(','),function(value){return parseInt(value);})
			}
		}

	}
	

	//data call returning id array for random + next and previous functionality as well as stored biggest image size for this page

	if($('#pid').length > 0 || $('#front').length > 0){
		$.ajax({
			url : '/php/connect.php',
			type: "POST",
			data : {
			command:'getids',
			id:parseInt($('#pid').html())						
			},
			dataType:'json',
			encode: true
		}).done(function(data){
			

			var id=(parseInt($('#pid').html()));
			imager.position=$.inArray(id,data.ids);
			imager.idarray=data.ids;
			if(!isNaN(parseFloat(data.biggest[0]))){
				imager.biggest=data.biggest[0];
			}
			if(imager.position < imager.idarray.length-1){		
				$('.next').click(function(){	
					var id=imager.idarray[imager.position+1];
					window.location='/page.php/'+id;
				})
			}else{
				$('.next').hide();
			}
			if(imager.position > 0){		
				$('.prev').click(function(){	
					var id=imager.idarray[imager.position-1];
					window.location='/page.php/'+id;
				})
			}else{
				$('.prev').hide();
			}
		}).error(function(data){

		})
	}

	
	//peek the page header
	$(window).scroll(function(){
		$('#images').css({'minHeight':$(window).height()});
		var top = $(window).scrollTop();
		if(top > $('#header').height()){
			$('#controls').css({'position':'fixed','top':0,'left':0});
			$('#images').css({'marginTop':$('#controls').outerHeight()});
			$('#menclose').show();
		}else{
			$('#controls').css({'position':'relative'})
			$('#images').css({'marginTop':0});
			$('#menclose').hide();
		}
	})
	
	//countdown the page load
	$('#fetching .count').html(giveup_timer);
	imager.timer();
	imager.ilength = 0;
	$('.image img').each(function(){
		if($(this).attr('src')){
			imager.ilength++;
		}
	})
	if(imager.ilength == 0){
		imager.abort('one');
	}
	
	//Get a random page link processing
	$('.shuffle').click(function(){
		imager.shuffle();
	})
	
	
	//bypass stuck images and proceed
	imager.repeater(giveup);

	
	//parse all images and process
	if($('.image img').length > 0){
		$('.image img').each(function(){
			if($(this).attr('src')){ //discard if no source
				var thisimg=$(this)
				
				//var imgLoad=$(this).imagesLoaded(function(){ //process each image when loaded
				$(this).error(function(){
					count++;
					imager.ilength--;
					$(this).stop();
					//update the progress bar
					$('#loading .inner').css({
						'width':count/imager.ilength*100+'%'			
					});						
					imager.remaining=imager.ilength-count;

				})
				$(this)[0].onload=function(){ //process each image when loaded


					var block=false;
					var icontainer = $(thisimg).parents('.image');
					$(icontainer).imagesReady(function(maxWidth, maxHeight, maxHeightToWidth) {	//get the image sizes for processing
						if(imager.reload && imager.reload2){
							imager.reload2=false;
							images=new Array();
						}				
						if(maxWidth > 150 && maxHeight > 150){ //discard anything below 150px
							size[0]=maxWidth;
							size[1]=maxHeight;
							size = imager.maxsize(size);	
							$(icontainer).width(size.width).height(size.height);
							$(icontainer).find('img').attr('style',size.css);
							count++	
						}else{
							$(icontainer).parents('.image').remove();
							block=true;
							count++
						}
						
						//construct a new array of images
						if(!block){
							var foo =new Object();
							foo['size']=size.totsize;
							foo['id']=$(icontainer).attr('data-id');
							foo['elem']=icontainer;
							foo['origsize']=maxWidth*maxHeight;
							images.push(foo);
						}
						
						//update the progress bar
						$('#loading .inner').css({
							'width':count/imager.ilength*100+'%'			
						});
						
						imager.remaining=imager.ilength-count;
						//trigger when all images are processed
						if(imager.ilength-count == 0){
							//console.log('done');					
							imager.gopage();
							function stopper(){
								if(!imager.busyupdating){
									if(document.readyState=='loading'){
										window.stop();
									};
									//----------------------------------------------TO CHECK-----------------------------------------------------------------------------
									
									//window.stop(); //this is stopping the gifs as well. observe if page is loading indefinitely on image failure and find a way to kill.									
								}else{
									//console.log(imager.busyupdating);
									setTimeout(function(){stopper()},200)
								}
							}
							if($('#images img').length == 0){
								imager.abort('two');
							}else{
								stopper();
							}
						}
					});
				};
			}
		})
	}

	
	//submit new user added page to the database
	$("#submitform").submit(function(event){
		$.cookie('scrolltop', 0);		
		var postData = $(this).serializeArray();
		var formURL = $(this).attr("action");
		$.ajax({
			url : formURL,
			type: "POST",
			data : postData,
			dataType:'json', // what type of data do we expect back from the server
            encode: true
		})
		.done(function(data){
			//console.log(data);
			if(data.duped!='duped'){
				$.cookie('imager.first',true);
			}else{
				$.cookie('imager.first',false);				
			}
			window.location='/page.php/'+data.id;			
		})
		.fail(function(data) {
			//console.log(data);
			
		});		
		event.preventDefault();
					
	});
})
