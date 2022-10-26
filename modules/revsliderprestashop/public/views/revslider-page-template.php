<?php
/**
 * Template Name: Slider Revolution Blank Template
 * Template Post Type: post, page
 * The template for displaying RevSlider on a blank page
 */

if(!defined('ABSPATH')) exit();
$page_bg = '';
$page_bg = ($page_bg == '' || $page_bg == 'transparent') ? 'transparent' : $page_bg.";";
?>
<!DOCTYPE html>
<html <?php //language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php //bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php //bloginfo('pingback_url'); ?>">
		<?php
			RevLoader::do_action( 'wp_head' );
		  RevLoader::do_action( 'wp_enqueue_scripts' );
			RevLoader::rev_front_print_styles();
		?>
		<script src="<?php echo RevLoader::url();?>/admin/assets/default/js/jquery.js"></script>
		<?php
		  RevLoader::do_action( 'admin_head' ); 
			RevLoader::rev_front_print_head_scripts();
	 	?>
		<style type="text/css">
			body:before { display:none !important}
			body:after { display:none !important}
			body, body.page-template-revslider-page-template, body.page-template---publicviewsrevslider-page-template-php { background:<?php echo $page_bg;?>}
		</style>

	</head>

	<body class="wp-admin wp-core-ui js toplevel_page_revslider auto-fold admin-bar branch-5-5 version-5-5 admin-color-fresh locale-en-us customize-support sticky-menu svg" <?php //body_class(); ?>>

		<?php RevLoader::do_action('rs_page_template_pre_content'); ?>
		<div>
		
		<?php
		
		echo $content;
		
		?>

		</div>
		<?php RevLoader::do_action('rs_page_template_post_content'); ?>
		<?php 
         RevLoader::do_action( 'wp_footer' );
         RevLoader::rev_front_print_footer_scripts();
		?>
	</body>
</html>