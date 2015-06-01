<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="/favicon.ico">
<title><?php echo $self->head_title;?></title>
<!-- Bootstrap -->
<?php foreach ($self->stylesheet as $stylesheet): ?>
<link href="<?php echo base_url();?>public/stylesheet/<?php echo $stylesheet; ?>.css" rel="stylesheet"/>
<?php endforeach; ?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<header class="header">
[[##_header_##]]
</header>

<div class="container">
<div class="main_content">
[[##_main_content_##]]
</div>

<hr>
<footer class="footer">
[[##_footer_##]]
</footer>
</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<?php foreach ($self->footer_javascript as $javascript): ?>
<script src="<?php echo base_url();?>public/javascript/<?php echo $javascript; ?>.js"></script>
<?php endforeach; ?>
<?php get_script_csrf_token(); ?>
</body>
</html>