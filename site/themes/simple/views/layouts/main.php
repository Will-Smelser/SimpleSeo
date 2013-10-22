<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/buttons.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/icons.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/tables.css" />
    
    <!-- 
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/mbmenu.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/mbmenu_iestyles.css" />
	 -->

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div id="page">
	<div class="bg-brushed">
	<div class="bg-fade-up">
	<div id="topnav">
		<div class="topnav_text container">
			<?php
			$this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>' | Rights', 'url'=>array('/rights'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
				array('url'=>Yii::app()->getModule('user')->loginUrl, 'label'=>' | '.Yii::app()->getModule('user')->t("Login"), 'visible'=>Yii::app()->user->isGuest),
				array('url'=>Yii::app()->getModule('user')->registrationUrl, 'label'=>' | '.Yii::app()->getModule('user')->t("Register"), 'visible'=>Yii::app()->user->isGuest),
				array('url'=>Yii::app()->getModule('user')->profileUrl, 'label'=>' | My Account', 'visible'=>!Yii::app()->user->isGuest),
				array('url'=>Yii::app()->getModule('user')->logoutUrl, 'label'=>' | '.Yii::app()->getModule('user')->t("Logout").' ('.Yii::app()->user->name.')', 'visible'=>!Yii::app()->user->isGuest),
			)));
			?>
			
		</div>
	</div>
	<div id="header">
		<div id="logo" class="container">
			<div class="span-7" id="logo-div">
			<img src="/themes/simple/images/logo.png" /><?php //echo CHtml::encode(Yii::app()->name); ?>
			</div>
			
			<div class="span-17 last">
			
			
				<div id="mainmenu" >
					<div class="">
				    <?php
				    $getstarted = (Yii::app()->request->url == '/site/pages/getstarted')?true:false;
				    $examples = (strpos(Yii::app()->request->url,'/site/pages/examples')!==false)?true:false;
				    
				    $this->widget('zii.widgets.CMenu',array(
						
						'items'=>array(
								array('label'=>'Home', 'url'=>array('/site/index')),
								array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
								array('label'=>'Contact', 'url'=>array('/site/contact')),
								array('label'=>'Get Started', 'url'=>array('/site/pages/getstarted'),'active'=>$getstarted),
								array('label'=>'Examples', 'url'=>array('/site/pages/examples/index'),'active'=>$examples),
								/*
								array('url'=>Yii::app()->getModule('user')->loginUrl, 'label'=>Yii::app()->getModule('user')->t("Login"), 'visible'=>Yii::app()->user->isGuest),
								array('url'=>Yii::app()->getModule('user')->registrationUrl, 'label'=>Yii::app()->getModule('user')->t("Register"), 'visible'=>Yii::app()->user->isGuest),
								array('url'=>Yii::app()->getModule('user')->profileUrl, 'label'=>Yii::app()->getModule('user')->t("Profile"), 'visible'=>!Yii::app()->user->isGuest),
								array('url'=>Yii::app()->getModule('user')->logoutUrl, 'label'=>Yii::app()->getModule('user')->t("Logout").' ('.Yii::app()->user->name.')', 'visible'=>!Yii::app()->user->isGuest),
								*/
								
								/* array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest), */
								/* array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest), */
								
						
						
							)
						)); ?>
					</div>
					</div> <!--mainmenu -->
			
			</div>
			
			
		</div>
	</div><!-- header -->
	
	</div>
	</div>
	
	<div class="blue-bg" >
	<div class="bg-fade-up">
	<div class="breadcrumbs crumb-bottom" >
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,'htmlOptions'=>array('class'=>'container')
		)); ?><!-- breadcrumbs -->
	<?php endif?>
	</div>
	</div>
	</div>
	
	
	<?php echo $content; ?>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by webapplicationthemes.com<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>