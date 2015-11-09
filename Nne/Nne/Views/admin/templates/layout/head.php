<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="Nnet csm" />
		<meta name =robots content="noindex" />
		<meta name =robots content="nofollow" />
<?

$this->loadCss(array(
		'admin/bootstrap.css',
		'admin/font-awesome.min.css',
		'admin/animate.css',
		'admin/plugin/jquery.gritter.css',
		'admin/plugin/tabdrop.css',
		'admin/plugin/bootstrap-switch.css',
		'admin/plugin/bootstrap-select2.css',
		'admin/app.css',
));
$this->loadCss($this->Css);
?>
<script type="text/javascript" src="/public/js/admin/jquery-2.js"></script>

		
		
<title><? echo stripslashes( $this->get('title'))?></title>
<body class="<?php echo $this->get('bodyClass')?> <?php echo 'page-'.$this->getAreaId();?>">
