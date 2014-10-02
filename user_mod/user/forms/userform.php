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
  * Structure for the user management page
  * 
  * */
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/user_mod/user/users.php');
$allusers=$user->getusers();
$userssorted=array();
foreach($allusers as $fuser){
	$userssorted[$fuser['id']]=new stdClass();
	$userssorted[$fuser['id']]->username=$fuser['username'];
	$userssorted[$fuser['id']]->realname=$fuser['realname'];
	$userssorted[$fuser['id']]->email=$fuser['email'];
	$userssorted[$fuser['id']]->role=$fuser['role'];
	$userssorted[$fuser['id']]->status=$fuser['status'];
	$userssorted[$fuser['id']]->invitedby=$fuser['invitedby'];
	$userssorted[$fuser['id']]->date=$fuser['date'];
}

?>

<div id="im_roles">
	<div class='inner'>
		<?php
		foreach($settings->roles as $key=>$value){
			echo ('<label>'.$key.'</label>');
			echo ('<p>'.$value['description'].'</p>');
		}
		?>
	</div>
</div>
<script src='/user_mod/user/js/sorttable.min.js'></script>
<div id='im_static' class='im_users'>
	<div class='inner'>
		<form id='userroles' name='userroles'>
			<table class='sortable'>
				<thead>
					<tr>
						<th>Username</th>
						<th>Real Name</th>
						<th>Role</th>
						<th>Invited By</th>
						<th>Date</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					//get the logged in user role
					$foo=$user->getuser($user->uid);
					$user->role=$foo[0]['role'];
					
					//process table of users
					foreach($userssorted as $id=>$tuser){ ?>
						<?php if($id != $user->uid){ ?>
							<tr data-id='<?php echo $id; ?>'>
								<td>
									<div class='im_uname'><?php echo $tuser->username; ?></div>
									<div class='im_useractions'>
										<?php if($user->perms['deluser']&&($tuser->role!='admin' || $user->role=='admin')){ ?><span class='im_delete' onclick='users.deluser("<?php echo $tuser->username.'",'.$id; ?>)'>delete</span><?php } ?>
										<?php if($user->perms['message']){ ?><span onclick="users.mail()">message</span><?php } ?>
									</div>
								</td>
								<td><?php echo $tuser->realname; ?></td>
								<td>
									<?php if($user->perms['changerole'] && ($tuser->role != 'admin' || $user->role=='admin')){ ?>
									<select name='role'>									
										<?php foreach($settings->roles as $role=>$values){ ?>
											<option value='<?php echo $role; ?>'<?php if($tuser->role == $role){echo " selected='selected'";}?>>
											<?php echo $role;?>
											</option>
											
										<?php } ?>
									</select>
									<?php }else{
										echo $tuser->role;
									} ?>

								</td>
								<td><?php echo $userssorted[$tuser->invitedby]->username; ?></td>
								<td><?php echo $tuser->date; ?></td>
								<td><?php echo $tuser->status; ?></td>
							</tr>
						<?php } ?>			
					<?php } ?>
				</tbody>
			</table>
		</form>
	</div>
</div>
