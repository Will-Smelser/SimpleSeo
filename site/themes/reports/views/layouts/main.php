<!DOCTYPE html>
<html>
<head>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_API_JS; ?>SeoApi.js" data-seoapi-ns="_SeoApi_" ></script>

<link rel="stylesheet" type="text/css" href="http://www.w3.org/StyleSheets/Core/parser.css?family=5&doc=Sampler" />
<link rel="stylesheet" type="text/css" href="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>css/custom-theme/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>css/report_basic.css" />


<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php echo $content; ?>
</body>

</html>