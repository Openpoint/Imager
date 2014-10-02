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
  * AJAX user handling and form validation
  * 
  * */

var users = new Array();
var validate = new Array();
if($.cookie('user')){
	users.details=$.parseJSON($.cookie('user'));
}

//--------------------------------------------------------------------Form Presentation and Processing-------------------------------------------------------------
//open the login modal, get the form and present it
users.showlogin = function(){
	$('#mod_error,#mod_mess').html('');
	$.get('/user_mod/user/forms/login.html', function(respons) {
		$('.infobox .inner').show();
		$('#im_modal .infobox .inner').html('');
		$('#im_modal .infobox .inner').append(respons);
		modal.open();
		$("#login").submit(function(event){
			$("input").removeClass('error'); //clear previous errors
			$('#mod_error,#mod_mess').html('');
			$('#mod_spinner .spin_inner').show();
			event.preventDefault();	
			var postData = $(this).serializeArray();
			postData.push({name:'method',value:'login'});
			var formURL = $(this).attr("action");
			$.ajax({
				url : formURL,
				type: "POST",
				data : postData,
				dataType:'json', // what type of data do we expect back from the server
				encode: true
			})
			.success(function(data){
				//console.log(data);
				if(!data.error){
					window.location.reload();
				}else{
					validate.clear();					
					$('#mod_error').html(data.message);
					if(data.type=='wrong'){
						$("input[name='password']").addClass('error').focus();
					}
					if(data.type=='none'){
						$("input[name='username']").addClass('error').focus();
					}
				}			
			})
			.fail(function(data) {
				validate.clear();					
				$('#mod_error').html(data);
				
			});		
			
						
		});
	})
}

//switch to the reset password modal, get the form and present it
users.resetp=function(){
	validate.clear();
	$.get('/user_mod/user/forms/resetp.html', function(respons) {
		$('#im_modal .infobox .inner').html('');
		$('.infobox .inner').show();
		$('#im_modal .infobox .inner').append(respons);
		$("#resetp").submit(function(event){
			$("input").removeClass('error'); //clear previous errors
			validate.clear();
			event.preventDefault();	
			var postData = $(this).serializeArray();
			postData.push({name:'method',value:'resetp'});
			$.ajax({
				url : '/user_mod/user/users.php',
				type: "POST",
				data : postData,
				dataType:'json', // what type of data do we expect back from the server
				encode: true
			})
			.success(function(rdata){
				users.username=rdata.username;
				users.authtoken=rdata.token;
				if(rdata.success){
					//send the email
					$.when(users.email('resetp')).then(function(mailbody){  //construct the mail body
						htmltext=mailbody.html;
						plaintext=mailbody.plain;								
						$.ajax({
							url : '/user_mod/sendmail.php',
							type: "POST",
							data : {
								replyto:rdata.from,
								sender:rdata.sender,
								recipient:rdata.to,
								subject:'IMAGER | Password reset link for '+rdata.username,
								message:htmltext,
								altmessage:plaintext
																	
							},
							encode: true
						})
						.done(function(data){
							if(data=='success'){
								$('.infobox .inner').hide();
								validate.clear();				
								users.message('Success. Further instructions have been sent to '+rdata.to);								
								$("#resetp")[0].reset();
							}else{
								$('#mod_spinner .spin_inner').hide();
								validate.error('Sorry, an error has occurred in the mailing system')
							}																		
						})
						.fail(function(){
							$('#mod_spinner .spin_inner').hide();
							users.message('Error in mailing');
						})
						$('#mod_spinner .spin_inner').hide();					
						$('#mod_mess').html(data.message);
					})
				}else{
					$('#mod_spinner .spin_inner').hide();
					$("input[name='username']").addClass('error').focus();					
					$('#mod_error').html(rdata.message);					
				}
							
			})
			.fail(function(data) {
				$('#mod_spinner .spin_inner').hide();					
				$('#mod_error').html(data);
				
			});		
			
						
		});
	})
}
//open the new user modal, get the form and present it
users.adduser = function(){
	$.ajax({
		url:'/user_mod/user/users.php',
		type:"POST",
		data:{method:'getuser',guid:users.details.uid},
		dataType:'json',
		encode:true
	}).done(function(respons) {
		$.post('/user_mod/user/forms/adduser.php',{realname:respons[0].realname}, function(respons) {
			$('#im_modal .infobox .inner').html('');
			$('#im_modal .infobox .inner').append(respons);	
			$('#im_modal .removefield').remove(); //remove the dummy fields for autofill	
			modal.open();
			$("#adduser").submit(function(event){
				event.preventDefault();
				validate.clear();	
				var postData = $(this).serializeArray();
				
				//validate the username entry
				if(validate.username(postData[0].value)){
					$.when(validate.unique(postData[0].value,postData[1].value)).then(function(data){
						if(data){
							//validate the email entry
							if(validate.email(postData[1].value,postData[2].value)){
								postData.push({name:'password',value:users.makepass()});
								postData.push({name:'invitedby',value:parseFloat($('#im_data').attr('data-uid'))});
								postData.push({name:'method',value:'adduser'});
								var formURL = $(this).attr("action");
								//add the user
								$.ajax({
									url : '/user_mod/user/users.php',
									type: "POST",
									data : postData,
									dataType:'json', // what type of data do we expect back from the server
									encode: true
								})
								.done(function(data){
									console.log(postData);
									users.username=postData[0].value;
									users.pmessage=postData[4].value;
									users.sender=users.toTitleCase(postData[3].value); //change to initcap
									console.log(users.sender);
									users.authtoken=data[0].authtoken;
									$.when(users.from()).then(function(sender){
										//send the email
										$.when(users.email('newuser')).then(function(mailbody){  //construct the mail body
											htmltext=mailbody.html;
											plaintext=mailbody.plain;								
											$.ajax({
												url : '/user_mod/sendmail.php',
												type: "POST",
												data : {
													replyto:sender,
													sender:users.sender,
													recipient:postData[1].value,
													subject:users.sender+' has invited you to participate on their Imager',
													message:htmltext,
													altmessage:plaintext										
												},
												encode: true
											})
											.done(function(data){
												if(data=='success'){
													//change the modal success message
													users.message('Success. Further instructions have been sent to '+postData[1].value);
													$('#mod_spinner .spin_inner').hide();
													$('#mod_error').html('');
													$("#adduser")[0].reset();
												}else{
													$('#mod_spinner .spin_inner').hide();
													validate.error('Sorry, an error has occurred in the mailing system')
												}																		
											})
											.fail(function(){
												$('#mod_spinner .spin_inner').hide();
												users.message('Error in mailing');
											})											
										})
										.fail(function(data) {
											$('#mod_spinner .spin_inner').hide();
											validate.error(data)
											
										});
									})
								})
							}	
						}							
					})
				}								
			});
		})
	})	
}
//Open the confirm dialouge for deleting a user
users.deluser = function(name,id){
	users.del2=id;
	$.post('/user_mod/user/forms/deleteuser.php', {username:name},function(respons) {
		$('#im_modal .infobox .inner').html('');
		$('#im_modal .infobox .inner').append(respons);	
		$('#im_modal .removefield').remove(); //remove the dummy fields for autofill	
		modal.open();
		$('#im_cancel').click(function(){
			modal.close();
		})
		$('#im_delete').click(function(){
			users.deluser2();
		})
		users.deluser2 = function(){
			$.post('/user_mod/user/users.php', {duid:users.del2,method:'deluser'},function() {
				$("tr[data-id='"+users.del2+"']").remove();
				modal.close();
			})
			
		}
	});
}

//--------------------------------------------------------------------Form Functions-------------------------------------------------------------
//change a users role
users.setrole=function(id,role){
	$.post('/user_mod/user/users.php', {uid:id,role:role,method:'setrole'});
}

//log a user out by destroying cookie and reloading
users.logout = function(){
	$.removeCookie('user', { path: '/' });
	window.location.reload();
}
//edit user details
users.edituser = function(data){
console.log(data);
	if(data[0].value == data[6].value){
		var foofullname=false;
	}else{
		if(!data[0].value){
			var force=true;
			var foofullname=null;
		}else{		
			var foofullname=data[0].value;
			if(!validate.realname(foofullname)){			
				return false;
			}
		}
	}
	if(data[1].value == data[4].value){
		var foouser=false;
	}else{
		var foouser=data[1].value;
		if(!validate.username(foouser)){
			return false;
		}
	}
	if(data[2].value == data[5].value){
		var foomail=false;
	}else{
		var foomail=data[2].value;
		if(!validate.email(foomail,data[3].value)){
			return false;
		}
	}
	function post(){
		if(!data[0].value){
			
			data[0].value=null;
		}
		$.post( "/user_mod/user/users.php", { 
			method: "updateuser",
			username:data[1].value,
			realname:data[0].value,
			email:data[2].value 
		}).done(function(){			
			users.message('Details have been updated');
			if(window.location.search=='?new=true'){
				window.location='/';				
			}else{
				window.location.reload();
			}						
		})		
	}
	if(foomail || foouser){
		$.when(validate.unique(foouser,foomail)).then(function(data){
			if(data){
				post();
			}
		})		
	}else if(foofullname || force){
		post();
	}
}
//send a new user password to the database

users.newpass = function(data){
	
	data.push({name:'method',value:'setpass'});
	if(validate.password(data[2].value,data[3].value)){
		validate.clear();
		$.ajax({
			url : '/user_mod/user/users.php',
			type: "POST",
			data : data,
			dataType:'json', // what type of data do we expect back from the server
			encode: true
		})
		.success(function(data){
			var query=window.location.search;
			query=query.split('&');
			if(query[1]=='new=true'){
				window.location='/user.php?new=true';				
			}else{
				window.location='/';
			}			
		})
		.fail(function(data) {
			//console.log(data);				
		});	
	}else{
		$('#mod_spinner .spin_inner').hide();
	}
}
//get the logged in users email to put in the replyto field
users.from=function(){
	var from = new jQuery.Deferred();
	$.ajax({
		url : '/user_mod/user/users.php',
		type: "POST",
		data : {
		uid:$('#data').attr('data-uid'),
		method:'getmail'						
		},
		encode: true
	}).done(function(data){		
		from.resolve(data);	
	})
	return from.promise();	
}
//construct the mail body
users.email=function(type){
	if(type == 'resetp'){
		var template='passreset';
	}
	if(type == 'newuser'){
		var template='newuser';
					
	}
	var mailbody=new Object();	
	var mail = new jQuery.Deferred();
	//replace the tokens with values
	function tokener(data){
		data=data.replace(/:url/g,window.location.origin);
		data=data.replace(/:username/g,users.username);
		data=data.replace(/:sender/g,users.sender);
		data=data.replace(/:message/g,users.pmessage);
		data=data.replace(/:hmessage/g,users.hmessage);
		data=data.replace(/:tokenpath/g,document.location.origin+'/validate.php?token='+users.authtoken);
		return data;
	}
	$.when(		
			$.get( "/user_mod/user/mail_templates/"+template+".html", function(data) {
				if(typeof(users.pmessage)!='undefined'){
					users.hmessage=users.pmessage.replace(/\n/g, '<br/>'); //replace newlines with <br/> for html
				}				
				mailbody.html=tokener(data); //replace the tokens with values			
			}),
			$.get( "/user_mod/user/mail_templates/"+template+".txt", function(data) {
				//replace the tokens with values
				mailbody.plain=tokener(data);		
			})
	).then(function(){
		mail.resolve(mailbody);
	})
	return mail.promise();

}

//create a random initial password for new users
users.makepass=function()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@{}£$%&*()";

    for( var i=0; i < 9; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}
//capitailise the senders name
users.toTitleCase=function(str)
{
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}
//send a private mail ----- not yet implemented
users.mail=function(){
	alert('Mailing system not yet implemented');
}
//set the modal message
users.message=function(message){
	$('#mod_mess').show().html(message);
}
//set the modal error message
validate.error=function(message){
	$('#mod_error').show().html(message);
}


//-----------------------------------------------------------Validation------------------------------------------
//clear previous validation errors and show the spinner
validate.clear=function(){
	$("input").removeClass('error');
	$('#mod_error').html('').css({'opacity':1,'display':'block'});
	$('#mod_mess').html('').css({'opacity':1,'display':'block'});
	$('#mod_spinner .spin_inner').show();	
}

validate.lower=function(string){
	if(string){
		return string.toLowerCase();
	}
}
validate.realname=function(string){
	if(string.length == 0){
		return true;
	}
	var number = string.match(/\d+/g);
	if (number != null) {
			$('#mod_spinner .spin_inner').hide();
			$("input[name='realname']").addClass('error').focus();
			validate.error('Your Real Name cannot contain numbers');
			return false
	}
	if(string.length <= 50 && string.length >= 6){
		return true;
	}else{
		$('#mod_spinner .spin_inner').hide();
		$("input[name='realname']").addClass('error').focus();
		validate.error('Your Real Name must be between 6 and 50 characters long');
		return false
	}
}
validate.username=function(username){
	username=validate.lower(username);
	if(username.indexOf(" ") < 0){
		if(username.length > 3){
			return true
		}else{
			
			$('#mod_spinner .spin_inner').hide();
			$("input[name='username']").addClass('error').focus();
			validate.error('Username must be at least 4 characters');
			return false			
		}
	}else{
		$('#mod_spinner .spin_inner').hide();
		$("input[name='username']").addClass('error').focus();
		validate.error('Username cannot contain spaces');
		return false		
	}	
}
validate.unique=function(username,email){
	username=validate.lower(username);
	if(email){
		email=validate.lower(email);
	}else{
		email=username;
	}
	var send = new jQuery.Deferred();
	$.ajax({
		url : '/user_mod/user/users.php',
		type: "POST",
		data : {
		username:username,
		email:email,
		method:'unique'						
		},
		dataType:'json', // what type of data do we expect back from the serve
		encode: true
	}).done(function(data){		
		if(parseFloat(data[0].user) > 0){
			$('#mod_spinner .spin_inner').hide();
			$("input[name='username']").addClass('error').focus();
			validate.error('That username already exists');
			send.resolve(false);			
		}else{
			if(parseFloat(data[0].email) > 0){
				$('#mod_spinner .spin_inner').hide();
				$("input[name='email']").addClass('error').focus();
				validate.error('That email already exists');
				send.resolve(false);			
			}else{
				validate.error('');
				send.resolve(true);	
			}
		}
	})
	return send.promise();	
}

validate.password=function(one,two){
	if(one.indexOf(" ") < 0){
		if (one==two){
			if (one.length < 6){
				$('#mod_spinner .spin_inner').hide();
				$("input[name='password']").addClass('error').focus();
				validate.error('Password needs to be at least 6 characters');
				return false
			}else{
				return true;			
			}

		}else{
			$('#mod_spinner .spin_inner').hide();
			$("input[name='password']").addClass('error').focus();
			$("input[name='confpassword']").addClass('error');
			validate.error('Passwords do not match');
			return false
		}
	}else{
		$('#mod_spinner .spin_inner').hide();
		$("input[name='password']").addClass('error').focus();
		validate.error('Password cannot contain spaces');
		return false
	}
}
validate.email=function(one,two){
	one=validate.lower(one);
	two=validate.lower(two);
	if(one.indexOf(".") < 0 || one.indexOf("@") < 0){
		$('#mod_spinner .spin_inner').hide();
		$("input[name='email']").addClass('error').focus();
		validate.error('Email is not a valid format');
		return false
	}else{
		if (one==two){
			return true;			
		}else{
			$('#mod_spinner .spin_inner').hide();
			$("input[name='email']").addClass('error').focus();
			$("input[name='confemail']").addClass('error');
			validate.error('Emails do not match');
			return false
		}
	}
}

$(document).ready(function(){


	//reset the user management page on load
	$('#userroles').each(function(){
		this.reset();
	});
	//control the submit and spinner on the non modal validate page
	$("#mod_reset").submit(function(event){
		validate.clear();
		event.preventDefault();	
		var data = $(this).serializeArray();		
		users.newpass(data);				
	});	
	//control the submit and spinner on the non modal user details page
	var oldrealname=$("input[name='realname']").val();
	var oldusername=$("input[name='username']").val();
	var oldemail=$("input[name='email']").val();
	$("#mod_details").submit(function(event){
		validate.clear();
		event.preventDefault();	
		var data = $(this).serializeArray();
		data.push({name:'oldusername',value:oldusername});
		data.push({name:'oldemail',value:oldemail});
		data.push({name:'oldrealname',value:oldrealname});	
		users.edituser(data);				
	});
	//submit changes to user roles
	$('#userroles select[name="role"]').change(function(){
		users.setrole($(this).parents('tr').attr('data-id'),$(this).val())
	})
	//validate the non modal firstuser form at setup
	$('#firstuser').submit(function(event){
		event.preventDefault();	
		var data = $(this).serializeArray();
		
		$('input').removeClass('error');
		if(validate.username(data[2].value)){
			$('input').removeClass('error');
			if(validate.email(data[3].value,data[4].value)){
				$('input').removeClass('error');
				if(validate.password(data[5].value,data[6].value)){
					$('input').removeClass('error');
					$.when(validate.unique(data[2].value,data[3].value)).then(function(data2){
						if(data2){
							$.ajax({
								url : '/user_mod/user/users.php',
								type: "POST",
								data : data,
								dataType:'json', // what type of data do we expect back from the server
								encode: true
							}).done(function(){
								window.location='/';
							})
						}
					})
						
					
					
				}				
			}
		}		
	})

})
