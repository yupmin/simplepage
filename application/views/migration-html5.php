<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="/favicon.ico">
<title><?php echo $self->head_title;?></title>
<!-- Bootstrap core CSS -->
<?php foreach ($self->stylesheet as $stylesheet): ?>
<link href="<?php echo base_url();?>public/stylesheet/<?php echo $stylesheet; ?>.css" rel="stylesheet"/>
<?php endforeach; ?>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>

<!-- Begin page content -->
<div class="container">
[[##_main_content_##]]
</div>

<footer class="footer">
	<div class="container">
	<p class="text-muted">&copy; Simpletools 2015</p>
	</div>
</footer>

<?php foreach ($self->footer_javascript as $javascript): ?>
<script src="<?php echo base_url();?>public/javascript/<?php echo $javascript; ?>.js"></script>
<?php endforeach; ?>
</body>
</html>
