<div class="span-23 last">
<?php 
if(UserModule::isAdmin()) {
?>
<div class="dashIcon span-3">
	<a href="/user/admin"><img
		src="/themes/simple/images/big_icons/icon-man-tie.png"
		alt="Admin" /> </a>
	<div class="dashIconText">
		<?php echo CHtml::link(UserModule::t('Manage'),array('/user/admin')); ?>
	</div>
</div>
<?php } ?>

<div class="dashIcon span-3">
	<a href="/user/profile"><img
		src="/themes/simple/images/big_icons/icon-person.png"
		alt="Profile" /> </a>
	<div class="dashIconText">
		<?php echo CHtml::link(UserModule::t('Profile'),array('/user/profile')); ?>
	</div>
</div>

<div class="dashIcon span-3">
    <a href="/user/profile/edit"><img
            src="/themes/simple/images/big_icons/icon-pencil.png"
            alt="Edit" /> </a>
    <div class="dashIconText">
        <?php echo CHtml::link(UserModule::t('Edit'),array('/user/profile/edit')); ?>
    </div>
</div>

<div class="dashIcon span-3">
	<a href="/user/profile/changepassword"><img
		src="/themes/simple/images/big_icons/icon-key.png"
		alt="Change Password" /> </a>
	<div class="dashIconText">
		<?php echo CHtml::link(UserModule::t('Password'),array('/user/profile/changepassword')); ?>
	</div>
</div>

<div class="dashIcon span-3">
    <a href="/user/reports/crawler"><img
            src="/themes/simple/images/big_icons/spider.png"
            alt="Crawl Site" /> </a>
    <div class="dashIconText">
        <?php echo CHtml::link(UserModule::t('Crawler'),array('/user/reports/crawler')); ?>
    </div>
</div>

<div class="dashIcon span-3">
    <a href="/user/reports"><img
            src="/themes/simple/images/big_icons/icon-chart.png"
            alt="Profile" /> </a>
    <div class="dashIconText">
        <?php echo CHtml::link(UserModule::t('Reports'),array('/user/reports')); ?>
    </div>
</div>

<div class="dashIcon span-3">
    <a href="/user/stats"><img
            src="/themes/simple/images/big_icons/icon-chart2.png"
            alt="Profile" /> </a>
    <div class="dashIconText">
        <?php echo CHtml::link(UserModule::t('Stats'),array('/user/stats')); ?>
    </div>
</div>

<div class="dashIcon span-3">
	<a href="/user/stats/credits"><img
		src="/themes/simple/images/big_icons/icon-cash-bundle.png"
		alt="View Credits" /> </a>
	<div class="dashIconText">
		<?php echo CHtml::link(UserModule::t('Credits'),array('/user/stats/credits')); ?>
	</div>
</div>

<div class="dashIcon span-3">
	<a href="/user/logout"><img
		src="/themes/simple/images/big_icons/icon-redo.png"
		alt="Logout" /> </a>
	<div class="dashIconText">
		<?php echo CHtml::link(UserModule::t('Logout'),array('/user/logout')); ?>
	</div>
</div>
</div>