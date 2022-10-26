<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="<?php //bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width">

		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php
			RevLoader::do_action( 'wp_head' );
			RevLoader::do_action( 'wp_enqueue_scripts' );
			RevLoader::rev_front_print_styles();
		?>
    <style type="text/css">
			body:before { display:none !important}
			body:after { display:none !important}
			body, body.page-template-revslider-page-template, body.page-template---publicviewsrevslider-page-template-php { background:<?php //echo $page_bg;?>}
		</style>
    <script src="<?php echo RevLoader::url();?>/admin/assets/default/js/jquery.js"></script>
		<?php
		  RevLoader::do_action( 'admin_head' );
			RevLoader::rev_front_print_head_scripts();
	 	?>
		<?php
		RevLoader::do_action('revslider_slider_init_by_data_post',array());
		?>
	</head>
	<body class="page-template-default footer-top-visible">
		<div>
		<?php
		echo $content;
		?>
		</div>
		<?php 
	 RevLoader::do_action( 'wp_footer' );
	 RevLoader::rev_front_print_footer_scripts();
		?>
	</body>
</html>