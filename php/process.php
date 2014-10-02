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
  * The main image scraping logic
  * 
  * */   
?>
<?php
class image {
	public function setVal($key,$newval){
		$this->$key = $newval;
	}	
}

//process the url into components
function urler($url){
	$locate=new stdClass();
	$locate->query=explode('?',$url);
	$locate->url=$locate->query[0];
	if(isset($locate->query[1])){
		$locate->query='?'.$locate->query[1];		
	}else{
		$locate->query=null;	
	}
	$stripfile = explode("://", $locate->url);
	if($stripfile[0] == 'http' || $stripfile[0] == 'https'){
		$locate->url=$stripfile[1];
		$locate->protocol=$stripfile[0].'://';
	}else{
		$locate->protocol='http://';
	}
	$stripfile = explode("/", $locate->url);
	$locate->base = $stripfile[0].'/';
	array_shift($stripfile);
	if(strpos(end($stripfile),'.') !== false ){
		$locate->file = end($stripfile);
		array_pop($stripfile);
		$locate->url = implode('/',$stripfile).'/';	
	}else{
		$locate->file = null;
		$locate->url = implode('/',$stripfile);	
	}
	return $locate;
}
$store = $memcache->get($settings->memcacheprefix."page".$pid);

if($store === false || count($store[1]) < 1){
	//parse and format the page url


	$locate=urler($url);

	//get the page data for scraping

	$curl=$locate->protocol.$locate->base.$locate->url.$locate->file.$locate->query;
	$_curl = curl_init();
	curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, 1);
	curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($_curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($_curl, CURLOPT_FOLLOWLOCATION, 1);
	//curl_setopt($_curl, CURLOPT_COOKIEJAR, $_SERVER["DOCUMENT_ROOT"].'/cookies/cookies.txt');
	if(strpos($locate->base,'facebook.com')> -1){
		curl_setopt($_curl, CURLOPT_FOLLOWLOCATION, 0);		
	}
	curl_setopt($_curl, CURLOPT_URL, $curl);
	$html = curl_exec($_curl);

	$getagain=false;
	foreach($settings->useragent_change as $needle){
		if(strpos($html,$needle) > -1){
			$getagain=true;
		}
	}
	if(strlen($html) == 0 || $getagain){
		curl_setopt($_curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36');
		$html = curl_exec($_curl);			
	}
	//file_put_contents($_SERVER["DOCUMENT_ROOT"].'/php/test.html', $html);

	if(!$html){
		
	}else{
		$replace=array('-->','<!--','<![endif]','<!'); //strip out comments as some sites use this as placeholder for images
		$html = str_replace($replace,'',$html);


		libxml_use_internal_errors(true);
		$dom = new domDocument;
		$dom->loadHTML($html);
		$dom->preserveWhiteSpace = false;



		//scrape the page for tags and images
		$images = $dom->getElementsByTagName('img');
		$body = $dom->getElementsByTagName('body');
		$divs = $dom->getElementsByTagName('a');

		$title = $dom->getElementsByTagName('title')->item(0)->textContent;

		$meta = $dom->getElementsByTagName('meta');
		foreach($meta as $tag){
			$foo=$tag->getAttribute('name');
			if (strtolower($foo) == 'description'){
				$locate->descrip = $tag->getAttribute('content');
			}
		}
		if(!isset($locate->descrip)){
			$locate->descrip=null;
		}
		//process string for de-duping
		function splitter($needle){
			$needle=explode('/',$needle);
			$needle=end($needle);
			$needle=explode('?',$needle);
			return current($needle);
		}

		$newimg=array();
		$testimage=array();
		// get the image src's
		foreach($images as $key=>$tag){	
			$foo= $tag->getAttribute('src');

			if(strpos($foo,'.') !== false && (strpos(strtolower($foo),'.jpg') !== false || strpos(strtolower($foo),'.png') !== false || strpos(strtolower($foo),'.gif') !== false || strpos(strtolower($foo),'.jpeg') !== false || strpos(strtolower($foo),'.bmp') !== false)){
				$timg=new stdClass();
				$timg->img=urldecode($tag->getAttribute('src'));
				
				array_push($testimage,splitter($timg->img));
				if($tag->getAttribute('alt')){
					$timg->alt=$tag->getAttribute('alt');
				}
				if($tag->getAttribute('title')){
					$timg->title=$tag->getAttribute('title');
				}
				if($tag->getAttribute('data-thumb')){
					$timg->thumb=$tag->getAttribute('data-thumb');//youtube
				}
				array_push($newimg,$timg);
				array_push($testimage,splitter($timg->img));
			}
		}

		// get attribute links to images
		foreach ($images as $image) {
			foreach ($image->attributes as $attrName => $attrNode) {
				$attrName=strtolower($attrName);
				if(strpos($attrName,'src') !== false){
					$attrNode=$attrNode->nodeValue;
					if(strpos($attrNode,'.') !== false && (strpos(strtolower($attrNode),'.jpg') !== false || strpos(strtolower($attrNode),'.png') !== false || strpos(strtolower($attrNode),'.gif') !== false || strpos(strtolower($attrNode),'.jpeg') !== false || strpos(strtolower($attrNode),'.bmp') !== false)){
						$timg=new stdClass();
						$timg->img=$attrNode;
						
						//de-dupe
						if(!in_array(splitter($timg->img),$testimage)){
							array_push($newimg,$timg);
							array_push($testimage,splitter($timg->img));					
						}				
					}
				}
			}
		}

		// get href links to images
		foreach($divs as $key=>$tag){
			$foo=$tag->getAttribute('href');
			if(strpos($foo,'.') !== false && (strpos(strtolower($foo),'.jpg') !== false || strpos(strtolower($foo),'.png') !== false || strpos(strtolower($foo),'.gif') !== false || strpos(strtolower($foo),'.jpeg') !== false || strpos(strtolower($foo),'.bmp') !== false)){
				$timg=new stdClass();
				$timg->img=$tag->getAttribute('href');
				
				//de-dupe
				if(!in_array(splitter($timg->img),$testimage)){
					array_push($newimg,$timg);
					array_push($testimage,splitter($timg->img));					
				}
			}
		}

		//get the background images
		$pattern = '/url\(([^\)]+)/i';
		preg_match_all($pattern,$html,$out);
		$urls = $out[1];
		foreach($urls as $foo){	
			if(strpos($foo,'.') !== false && (strpos(strtolower($foo),'.jpg') !== false || strpos(strtolower($foo),'.png') !== false || strpos(strtolower($foo),'.gif') !== false || strpos(strtolower($foo),'.jpeg') !== false || strpos(strtolower($foo),'.bmp') !== false)){
				$timg=new stdClass();
				$timg->img=$foo;
				if(!in_array(splitter($timg->img),$testimage)){
					array_push($newimg,$timg);
				}
				
			}
		}


		//get an array of image urls
		$imagebatch=array();
		$count=0;
		$i_url=array();
		foreach ($newimg as $image) {
			$foo=new stdClass();
			$foo->img=array();
			array_push($foo->img,$image->img);
			if(isset($image->thumb)){
				array_push($foo->img,$image->thumb);//youtube
			}
			if(isset($image->alt)){
				$foo->alt=$image->alt;
			}
			if(isset($image->title)){
				$foo->title=$image->title;
			}
			
			foreach($foo->img as $tempimg){
				$tempimg=explode('?',$tempimg);

				$foo->img=$tempimg[0];
				if(isset ($tempimg[1])){
					$tempimg[1]=str_replace('/','%2F',$tempimg[1]);
					$tempimg[1]=str_replace(':','%3A',$tempimg[1]);
					$foo->query='?'.$tempimg[1];
				}else{
					$foo->query=null;
				}
				if (!in_array($foo, $i_url)) {
					array_push($i_url,$foo);
				}
			}	
		}

		//parse and format image urls into array
		foreach ($i_url as $key=>$this_url) {

			$rawpath = $this_url->img;
			$pathtest=explode("/", $rawpath);
			foreach($pathtest as $key => $value){
				if($value == ''){
					unset($pathtest[$key]);
				}
			}
			reset($pathtest);

			$expath=implode("/",$pathtest);
			$pathtest=current($pathtest);

			if($pathtest == 'http:' || $pathtest == 'https:'){
				$resource = $rawpath;
			}else if(strpos($pathtest,'.') !== false && (strpos(strtolower($pathtest),'.jpg') !== false || strpos(strtolower($pathtest),'.png') !== false || strpos(strtolower($pathtest),'.gif') !== false || strpos(strtolower($pathtest),'.jpeg') !== false || strpos(strtolower($pathtest),'.bmp') !== false)){
				$resource=$locate->protocol.$locate->base.$locate->url.$pathtest;

			}else if($pathtest == '..'){
				$rawpath = trim($rawpath,"/");
				$pathback=count(explode("..", $rawpath));
				$rawpath=str_replace('../','', $rawpath);
				$pathfull=count(explode('/',$locate->url));
				$stepback=$pathfull-$pathback;
				$url = implode('/',array_slice(explode('/',$locate->url), 0, $stepback)).'/';
				$resource=$locate->protocol.$locate->base.$url.$rawpath;	
			}else if(strpos($pathtest,'.') !== false){
				$resource=$locate->protocol.'/'.$expath;		
			}else if($rawpath[0]=='/'){
				$rawpath = trim($rawpath,"/");
				$resource=$locate->protocol.$locate->base.$rawpath;
			}else{
				$rawpath = trim($rawpath,"/");
				$resource=$locate->protocol.$locate->base.$locate->url.$rawpath;
			}
			$filename=explode('/', $rawpath);
			$filename=end($filename);
			if((strpos($filename,'.') !== false && (strpos(strtolower($filename),'.jpg') !== false || strpos(strtolower($filename),'.png') !== false || strpos(strtolower($filename),'.gif') !== false || strpos(strtolower($filename),'.jpeg') !== false || strpos(strtolower($filename),'.bmp') !== false))||(strpos($this_url->query,'.') !== false && (strpos(strtolower($this_url->query),'.jpg') !== false || strpos(strtolower($this_url->query),'.png') !== false || strpos(strtolower($this_url->query),'.gif') !== false || strpos(strtolower($this_url->query),'.jpeg') !== false || strpos(strtolower($this_url->query),'.bmp') !== false))){					
				$imagebatch[$count] = new image;
				$imagebatch[$count]->setVal('url',$resource.$this_url->query);
				$imagebatch[$count]->setVal('filename',$filename);
				if(isset($this_url->alt)){
					$imagebatch[$count]->setVal('alt',$this_url->alt);
				}else{
					$imagebatch[$count]->setVal('alt',null);
				}
				if(isset($this_url->title)){	
					$imagebatch[$count]->setVal('title',$this_url->title);
				}else{
					$imagebatch[$count]->setVal('title',null);
				}		
				$count++;
			}
		}
		$store=array('1'=>$imagebatch,'2'=>$title,'3'=>$locate);
		$memcache->set($settings->memcacheprefix."page".$pid, $store, MEMCACHE_COMPRESSED, $settings->back_cache);
	}

}else{
	//echo 'memcache';
}
$imagebatch=$store['1'];
$title=$store['2'];
$locate=$store['3'];
?>
