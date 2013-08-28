<?php
class Error extends Controller  implements Control{
	
	public function no_method(){
		(new \api\responses\ApiResponseJSON())->failure("Invalid Request - No Method or Class");
	}
}
?>