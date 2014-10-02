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
  * First run install script
  * 
  * */
?>

<?php 
/* Include this at the top of your originating install file location to prevent caching of file permission instructions
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
* */
class install {
    function install($dbase,$settings) {
        $this->dbase=$dbase;
        $this->settings=$settings;
    }

	public function checkdb($external){
		$sql="select 1 from users";
		$response=$this->dbase->query($sql,null);	
				
		if(!$this->dbase->success){
			if($external){
				echo 'false';
				return false;
			}else{			
				return false;
			}
		}else{
			if($external){
				echo 'true';
				return true;
			}else{			
				return true;
			}
		}		
	}
	public function checkfinished(){
		$sql="SELECT COUNT(*) FROM users";
		$response=$this->dbase->query($sql,null);	
		$response=$response->fetchAll();
		if($response[0]['count'] > 0){
			return true;
		}else{
			return false;			
		}	
		
	}
	public function makedb(){
		
		$sql=file_get_contents($_SERVER['DOCUMENT_ROOT'].'/user_mod/install/dbase.sql');
		$sql=str_replace ( '_dbowner_' , $this->settings->dbase['username'] , $sql );
		$response=$this->dbase->query($sql,null);
		$response=$response->fetchAll();		
	}
}
if (!empty($_POST)){
	if($_POST['method'] == 'checkdb'){
		require_once($_SERVER['DOCUMENT_ROOT']."/user_mod/settings.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/user_mod/database.php');
		$dbase = new dbase($dbh);
		$install = new install($dbase,$settings);
		$install->checkdb(true);	
	}
	if($_POST['blinduser'] == 'connect'){
		if($handle = file($_SERVER['DOCUMENT_ROOT']."/user_mod/settings.php")){
			$foo;
			foreach($handle as $line){
				if(strpos($line,"'username'=>") !== false){
					$line="    'username'=>'".$_POST['dbusername']."',\n";
				}
				if(strpos($line,"'password'=>") !== false){
					$line="    'password'=>'".$_POST['dbpword']."',\n";
				}
				if(strpos($line,"'db_name'=>") !== false){
					$line="    'db_name'=>'".$_POST['dbname']."',\n";
				}
				if(strpos($line,"'host'=>") !== false){
					$line="    'host'=>'".$_POST['dbhost']."',\n";
				}
				if(strpos($line,"'port'=>") !== false){
					$line="    'port'=>'".$_POST['dbport']."'\n";
				}
				$foo=$foo.$line;
			}
		}
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/user_mod/settings.php", $foo);	
	}	
}
?>
<div id='im_static'>
<?php include $_SERVER['DOCUMENT_ROOT'].'/user_mod/database.php';
if(!$dbh){
	if(!is_writable($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php')){ ?>
	<div style='background:red;color:white;padding:5px;margin-bottom:10px;'>Please set the file "[document root]/user_mod/settings.php" to <strong>writable</strong> before proceeding.<br><br>Reload the page when done.</div>
	<?php } ?>
	<div>Imager requires a PostgreSQL database version 9+</div>
	<form id='database' name='database' <?php if(is_writable($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php')){ ?>action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"<?php } ?> method="post">
	<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
	<input class='removefield' style='display:none' type="text" name="blinduser" value='connect'>
	<input class='removefield' style='display:none' type="password" name="blindpass">
		<fieldset>
			<div class='im_ftitle'>Your Database Name:</div>
			<input type='text' name='dbname' placeholder='Name' required />
			<div class='im_ftitle'>Your Database host:</div>
			<input type='text' name='dbhost' placeholder='Host' value='localhost' required />
			<div class='im_ftitle'>Your Database Port:</div>
			<input type='text' name='dbport' placeholder='Port' value='5432' required />
		</fieldset>
		<fieldset>
			<div class='im_ftitle'>Your Database Username:</div>
			<input type='text' name='dbusername' placeholder='Username' required />
			<div class='im_ftitle'>Your Database Password:</div>
			<input type='password' name='dbpword' placeholder='Password' required />
		</fieldset>
		<?php
		if(is_writable($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php')){ ?>
		<input type='submit' value='Proceed' />
		<?php } ?>			
	</form>
</div>
<?php }else{
	$dbase = new dbase($dbh);
	$install = new install($dbase,$settings);
	if(!$install->checkdb()){
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
		$dbase = new dbase($dbh);
		$install = new install($dbase,$settings);
		$install->makedb();
		include 'firstuser.php';
	}else{
		if(!$install->checkfinished()){
			include 'firstuser.php';
		}else{ ?>
			<div>Already Installed</div>
		<?php }
	}	
} ?>

