<!DOCTYPE html>
<html>
<head>

<?php require_once $this->configFile; ?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_API_JS; ?>SeoApi.js" id="seo-api-init" name-space="SeoApi" ></script>


<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php echo $content; ?>
</body>
</html>