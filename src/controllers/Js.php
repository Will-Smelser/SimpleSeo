<?php

class Js extends Controller{
	
	public function Js($namespace,$args=null){
		header("Content-type: application/javascript");
		
		if(isset($args[0]) && !empty($namespace) && file_exists(SEO_PATH_API_JS . $args[0])){
			echo str_replace('/*namespace*/',"'$namespace'", file_get_contents(SEO_PATH_API_JS . $args[0] ) );
		}else{
			$file = isset($args[0]) ? $args[0] : 'NULL';
			$namespace = !empty($namespace) ? $namespace : 'NULL';
			echo "console.log('SEOAPI LOAD FAILED: failed to load $file in $namespace');";
		}
	}
}
?>