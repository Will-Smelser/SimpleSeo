<?php
class PaypalController extends Controller
{
	
	
	private function makeTotal($type, $count){
		
		//make sure type is valid
		if(!isset(Apicredits::$types[$type]))
			return 0.0;
		
		//make sure the count is valid
		if(intval($count) <= 0)
			return 0.0;
		
		$count = intval($count);
		$info = Apicredits::$types[$type];
		//validate count more
		if($count%$info['caseSize'] !== 0)
			return 0.0;
		
		$unit = $info['unit'];
		return $unit * $count;
	}
	
	public function actionIndex(){
		if (Yii::app()->user->isGuest) {
			Yii::app()->user->setFlash('notice', 'You must be logged in to make purchases.');
		}
		$this->render('index');
	}
	
	public function actionBuy($type,$count){
		
		if (Yii::app()->user->isGuest) {
			Yii::app()->user->setFlash('error', 'You must be logged in to make purchases.  Please Login.');
			$this->redirect('/user/login');
			Yii::app()->end();
			return;
		}
		
		$total = $this->makeTotal($type,$count);

		if($total == 0.0){
			Yii::app()->user->setFlash('error', "Invalid data given.  Please try again.");
			$this->render('index');
			Yii::app()->end();
			return;
		}
		
		// set 
		Yii::app()->session['theTotal'] = $total;
		Yii::app()->session['theQty'] = $count;
		Yii::app()->session['theType'] = $type;
		$paymentInfo['Order']['theTotal'] = $total;
		$paymentInfo['Order']['description'] = sprintf(Apicredits::$types[$type]['desc'],$count);
		$paymentInfo['Order']['quantity'] = $count;

		// call paypal 
		$result = Yii::app()->Paypal->SetExpressCheckout($paymentInfo); 
		
		//Detect Errors 
		if(!Yii::app()->Paypal->isCallSucceeded($result)){ 
			if(Yii::app()->Paypal->apiLive === true){
				//Live mode basic error message
				$error = 'We were unable to process your request. Please try again later';
			}else{
				//Sandbox output the actual error message to dive in.
				$error = $result['L_LONGMESSAGE0'];
			}
			Yii::app()->user->setFlash('error', $error);
			$this->render('index');
			Yii::app()->end();
			
		}else { 
			// send user to paypal 
			$token = urldecode($result["TOKEN"]); 
			
			$payPalURL = Yii::app()->Paypal->paypalUrl.$token; 
			$this->redirect($payPalURL); 
		}
	}

	public function actionConfirm()
	{

		$token = trim($_GET['token']);
		$payerId = trim($_GET['PayerID']);
		
		$qty = Yii::app()->session['theQty'];
		$type = Yii::app()->session['theType'];
		$total = Yii::app()->session['theTotal'];
		
		Yii::app()->session['theTotal'] = 0.00;
		Yii::app()->session['theQty'] = 0;
		Yii::app()->session['theType'] = null;

		$result = Yii::app()->Paypal->GetExpressCheckoutDetails($token);
		
		$result['PAYERID'] = $payerId; 
		$result['TOKEN'] = $token; 
		$result['ORDERTOTAL'] = $total;

		//Detect errors 
		if(!Yii::app()->Paypal->isCallSucceeded($result)){ 
			if(Yii::app()->Paypal->apiLive === true){
				//Live mode basic error message
				$error = 'We were unable to process your request. Please try again later';
			}else{
				//Sandbox output the actual error message to dive in.
				$error = $result['L_LONGMESSAGE0'];
			}
			echo $error;
			Yii::app()->end();
		}else{ 
			
			$paymentResult = Yii::app()->Paypal->DoExpressCheckoutPayment($result);
			//Detect errors  
			if(!Yii::app()->Paypal->isCallSucceeded($paymentResult)){
				if(Yii::app()->Paypal->apiLive === true){
					//Live mode basic error message
					$error = 'We were unable to process your request. Please try again later';
				}else{
					//Sandbox output the actual error message to dive in.
					$error = $paymentResult['L_LONGMESSAGE0'];
				}
			
				Yii::app()->user->setFlash('error', $error);
				
			}else{
				//payment was completed successfully
				
				$id = intval(Yii::app()->user->id);
				$credits = Apicredits::model()->findByAttributes(array('user_id'=>$id,'type'=>$type));
				if(empty($credits)){
					$credits = new Apicredits();
					$credits->user_id = $id;
					$credits->type = $type;
					$credits->cnt = $qty;
				}else{
					$credits->cnt = $credits->cnt + $qty;
				}
			
				if(!$credits->save()){
					Yii::app()->user->setFlash('error', 'Error adding credits.  Email has been sent to solve the problem.');
					mail('willsmelser@gmial.com','Failed Purchase, add Credits',"user_id:$id\ntype:$type\nqty:$qty");
				}else{
					Yii::app()->user->setFlash('success', 'Success! Added '.$qty.' credits.');
				}
			}
			$this->render('confirm');
		}
	}
        
    public function actionCancel()
	{
		//The token of the cancelled payment typically used to cancel the payment within your application
		$token = $_GET['token'];
		
		$this->render('cancel');
	}
	
	public function actionDirectPayment(){ 
		$paymentInfo = array('Member'=> 
			array( 
				'first_name'=>'name_here', 
				'last_name'=>'lastName_here', 
				'billing_address'=>'address_here', 
				'billing_address2'=>'address2_here', 
				'billing_country'=>'country_here', 
				'billing_city'=>'city_here', 
				'billing_state'=>'state_here', 
				'billing_zip'=>'zip_here' 
			), 
			'CreditCard'=> 
			array( 
				'card_number'=>'number_here', 
				'expiration_month'=>'month_here', 
				'expiration_year'=>'year_here', 
				'cv_code'=>'code_here' 
			), 
			'Order'=> 
			array('theTotal'=>1.00) 
		); 

	   /* 
		* On Success, $result contains [AMT] [CURRENCYCODE] [AVSCODE] [CVV2MATCH]  
		* [TRANSACTIONID] [TIMESTAMP] [CORRELATIONID] [ACK] [VERSION] [BUILD] 
		*  
		* On Fail, $ result contains [AMT] [CURRENCYCODE] [TIMESTAMP] [CORRELATIONID]  
		* [ACK] [VERSION] [BUILD] [L_ERRORCODE0] [L_SHORTMESSAGE0] [L_LONGMESSAGE0]  
		* [L_SEVERITYCODE0]  
		*/ 
	  
		$result = Yii::app()->Paypal->DoDirectPayment($paymentInfo); 
		
		//Detect Errors 
		if(!Yii::app()->Paypal->isCallSucceeded($result)){ 
			if(Yii::app()->Paypal->apiLive === true){
				//Live mode basic error message
				$error = 'We were unable to process your request. Please try again later';
			}else{
				//Sandbox output the actual error message to dive in.
				$error = $result['L_LONGMESSAGE0'];
			}
			echo $error;
			
		}else { 
			//Payment was completed successfully, do the rest of your stuff
		}

		Yii::app()->end();
	} 
}