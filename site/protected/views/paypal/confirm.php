<?php
$this->breadcrumbs=array(
	'Paypal'=>array('/paypal'),
	'Confirm',
);

foreach(Yii::app()->user->getFlashes() as $key => $message) {
	echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
}

?>

<div>
	<h3>Payment Confirmation</h3>
	<p>
		Payment completed successfully
	</p>
</div>