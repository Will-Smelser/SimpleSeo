<?php

// this file must be stored in:
// protected/components/WebUser.php

class WebUser extends CWebUser {

	// Store model to not repeat query.
	private $UserLogin;

	// Return first name.
	// access it by Yii::app()->user->first_name
	function getFirst_Name(){
		$user = $this->loadUserLogin(Yii::app()->user->user_id);
		return $user->first_name;
	}

	// This is a function that checks the field 'role'
	// in the User model to be equal to 1, that means it's admin
	// access it by Yii::app()->user->isAdmin()
	function isAdmin(){
		$user = $this->loadUser(Yii::app()->user->user_id);
		return intval($user->user_role_id) == 1;
	}

	// Load user model.
	protected function loadUserLogin($id=null)
	{
		if($this->UserLogin===null)
		{
			if($id!==null)
				$this->UserLogin=UserLogin::model()->findByPk($id);
		}
		return $this->UserLogin;
	}
}?>
