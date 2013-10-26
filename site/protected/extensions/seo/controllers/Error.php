<?php
//TODO: What is the interface Control?
class Error extends Controller  implements Control{
	
	public function no_method(){
		$temp = new \api\responses\ApiResponseJSON();
		$temp->failure("Invalid Request - No Method or Class");
	} 
}
?>