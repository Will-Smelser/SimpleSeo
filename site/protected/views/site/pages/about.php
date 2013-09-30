<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name . ' - About';
$this->breadcrumbs=array(
	'About',
);
?>
<h2>An SEO Api for Developers</h2>

<p>A while back a friend asked me to help on some search engine
	optimization for his companies website. In general SEO is very
	simple. Just pick your keywords based on how much competition there
	is and follow some basic principles.</p>

<p>This sounds simple enough, but going through an entire site
	counting words, examining H1, H2, links, etc... is a lot of work.</p>

<p>So I did what any rational person would do, looked for tools on the
	web that would do this for me. I searched high and low and found
	nothing free and nothing that good. And the pay sites for SEO reports
	were really expensive. The free SEO tools were not very good. Hence
	this site...</p>

<p>I wrote the repetitive code too do the simple boring work of
	counting words, examining tags and other basic SEO tasks. I wrapped
	this code into a simple api that you can use to create your own
	customized reports.</p>