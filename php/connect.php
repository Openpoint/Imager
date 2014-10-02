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
  * Various function calls dealing with content and connection to the database.
  * Database interaction is managed via the UserMod package.
  * 
  * */   
?>

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/settings.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/user_mod/database.php'); //include database connectivity via UserMod

//Memcached
require_once('memcache.php');

class imager {
    function imager($dbase,$memcache,$settings) {
        $this->dbase=$dbase;
        $this->memcache=$memcache;
        $this->settings=$settings;
    }
    //check if page url is already in the database
	private function dupe($url){		
		$sql="SELECT id FROM pages WHERE url = :url";
		$strings= Array(
			Array('token'=>':url','value'=>$url)
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		return $response;			
	}
	private function locate($id,$duped){
		$return=array('id'=>$id,'duped'=>$duped);
		echo json_encode($return);
		
	}
	
	//add a new page to the database
	public function add($url,$title,$image){
		//check if url has already been submitted
		$response = $this->dupe($url);
		if(sizeof($response) > 0){
			$this->locate($response[0]['id'],'duped');
		}else{		
			$sql="insert into pages (url) values (:url) RETURNING id";
			$strings= Array(
				Array('token'=>':url','value'=>$url)
			);		
			$response=$this->dbase->query($sql,$strings);
			$response=$response->fetchAll();			
			$this->locate($response[0]['id']);			
		}
	}
	//get a page url from id
	public function geturl($id){
		$sql="SELECT url FROM pages WHERE id = :id";
		$strings= Array(
			Array('token'=>':id','value'=>$id)
		);
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		return $response[0]['url'];		
	}
	
	//update the details of a page
	public function update($id,$title,$image,$size){
			$sql="UPDATE pages SET title = :title, image = :image, biggest= :size WHERE id = :id";
			$strings= Array(
				Array('token'=>':id','value'=>$id),
				Array('token'=>':title','value'=>$title),
				Array('token'=>':image','value'=>$image),
				Array('token'=>':size','value'=>$size)
			);		
			$response=$this->dbase->query($sql,$strings);
			$getall=$response->fetchAll();
			print_r(json_encode($response));		
	}
	
	//get (limit) pages from the database for the front page - *****TODO**** Progressive scroll loading for front page
	public function getall(){
		$getall = $this->memcache->get($this->settings->memcacheprefix."getall");
		if($getall === false){
			$sql="SELECT * FROM pages ORDER BY id DESC limit ".$this->settings->frontlength;
			$strings= Array(
			);		
			$response=$this->dbase->query($sql,$strings);
			$getall=$response->fetchAll();
			// cache for the front page
			$this->memcache->set($this->settings->memcacheprefix."getall", $getall, MEMCACHE_COMPRESSED, $this->settings->front_cache);
			
		}
		return $getall;		
	}
	
	//fetch an array of all page ids as well as stored biggest image size for the current page. Used for random and 'prev' 'next' functionality.
	public function getids($bigid){
		$sql="SELECT id FROM pages ORDER BY id ASC";
		$strings= Array(
		);		
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll(PDO::FETCH_COLUMN);
		if(is_numeric($bigid)){
			$sql="SELECT biggest FROM pages where id = :id";
			$strings= Array(
				Array('token'=>':id','value'=>$bigid),
			);
			$response2=$this->dbase->query($sql,$strings);
			$response2=$response2->fetchAll(PDO::FETCH_COLUMN);
		}else{
			$response2=false;
		}
		$return=array('ids'=>$response,'biggest'=>$response2);
		echo json_encode($return);		
	}

	// delete a page from the database
	public function delete($id){
		$sql="DELETE FROM pages WHERE id= :id";
		$strings= Array(
			Array('token'=>':id','value'=>$id)
		);		
		$response=$this->dbase->query($sql,$strings);
		$response=$response->fetchAll();
		$this->memcache->delete($this->settings->memcacheprefix."page".$id);

		echo json_encode('deleted');		
	}	
}

$dbase = new dbase($dbh);
$imager = new imager($dbase,$memcache,$settings);
require_once($_SERVER["DOCUMENT_ROOT"].'/user_mod/user/users.php');



if(isset($_POST["command"])&& $_POST["command"] == 'getids'){
	$imager->getids($_POST["id"]);
};

?>
