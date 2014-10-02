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
  * Class to connect to the database. PDO logic - tested on Potgresql 9.1
  * 
  * */
?>

<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/user_mod/settings.php');

try {
	$dbh = new PDO('pgsql:dbname='.$settings->dbase['db_name'].';host='.$settings->dbase['host'].';port='.$settings->dbase['port'].'',$settings->dbase['username'],$settings->dbase['password'],
	array(PDO::ATTR_PERSISTENT => true));
} catch (PDOException $e) {
	header('Location:'.$settings->installfile);
}

class dbase {
    function dbase($dbh) {
        $this->dbh=$dbh;
    }
    public $success;	
	public function query($sql,$strings){
		try {  
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			
			$this->dbh->beginTransaction();			
			$stmt = $this->dbh->prepare($sql);
			if(isset($strings)){
				foreach($strings as $string){
					$stmt->bindParam($string['token'], $string['value']);
				}				
			}
			$stmt->execute();
			$this->dbh->commit();
			$this->success=true;
					  
		} catch (Exception $e) {
			$this->dbh->rollBack();
			echo "<br>Failed: " . $e->getMessage()."<br>";
			$this->success=false;
		}
		return $stmt;
				
	}
}

?>
