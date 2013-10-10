<!DOCTYPE HTML>
<html>
	<head>
		<title>MegaCorp by HTML5Templates.com</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />

		<link rel="stylesheet" href="/themes/simple/css/screen.css" />
		
		<link rel="stylesheet" href="/themes/simple/css/5grid/core.css" />
		<link rel="stylesheet" href="/themes/simple/css/5grid/core-desktop.css" />
		<link rel="stylesheet" href="/themes/simple/css/5grid/core-1200px.css" />
		<link rel="stylesheet" href="/themes/simple/css/5grid/core-noscript.css" />
		<link rel="stylesheet" href="/themes/simple/css/style.css" />
		<link rel="stylesheet" href="/themes/simple/css/style-desktop.css" />
		
		
		<script src="'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="/themes/simple/css/5grid/init.js?use=mobile,desktop,1000px&amp;mobileUI=1&amp;mobileUI.theme=none"></script>
		<!--[if IE 9]><link rel="stylesheet" href="/themes/simple/css/style-ie9.css" /><![endif]-->
	</head>
	<body>

		<!-- Header -->
		<div id="header-wrapper">
		
			<header id="header">
			
			<div id="topnav">
				<div class="topnav_text">
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
			
			
			
			
			
			
			
			
				<div class="5grid-layout">
					<div class="row">
						<div class="4u" id="logo">
							<h1><a href="#" class="mobileUI-site-name">MegaCorp</a></h1>
						</div>
						<div class="8u" id="menu">
							<nav class="mobileUI-site-nav">
								<?php $this->widget('zii.widgets.CMenu',array(
		
								'items'=>array(
										array('label'=>'Home', 'url'=>array('/site/index')),
										array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
										array('label'=>'Contact', 'url'=>array('/site/contact')),
										array('label'=>'Get Started', 'url'=>array('/site/pages/getstarted')),
										array('label'=>'Examples', 'url'=>array('/site/pages/examples/index')),
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
							</nav>
						</div>
					</div>
				</div>
			</header>
			
		</div>
		<!-- Header Ends Here -->
		
		<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
			)); ?><!-- breadcrumbs -->
		<?php endif?>
	
		<!-- Wrapper -->
		<div id="wrapper" class="5grid-layout">
		<?php echo $content; ?>
		</div>
		<!-- Wrapper End -->

		<!-- Copyright -->
		<div class="5grid-layout" id="copyright">
			<div class="row">
				<div class="12u">
					<section>
						<p>&copy; Your Site Name | Images: <a href="http://fotogrph.com/">Fotogrph</a> + <a href="http://iconify.it/">Iconify.it</a> | Design: <a href="http://html5templates.com/">HTML5Templates.com</a></p>
					</section>
				</div>
			</div>
		</div>


	</body>
</html>
		
		