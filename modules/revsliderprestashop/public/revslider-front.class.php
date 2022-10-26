<?php

/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if (!defined('ABSPATH')) exit();

$revslider_rev_start_size_loaded = false;

class RevSliderFront extends RevSliderFunctions
{

	const TABLE_OPTIONS_NAME = 'revslider_options';
	const TABLE_SLIDER			 = 'revslider_sliders';
	const TABLE_SLIDES			 = 'revslider_slides';
	const TABLE_STATIC_SLIDES	 = 'revslider_static_slides';
	const TABLE_CSS				 = 'revslider_css';
	const TABLE_LAYER_ANIMATIONS = 'revslider_layer_animations';
	const TABLE_NAVIGATIONS		 = 'revslider_navigations';
	const TABLE_SETTINGS		 = 'revslider_settings'; //existed prior 5.0 and still needed for updating from 4.x to any version after 5.x
	const CURRENT_TABLE_VERSION	 = '1.0.8';

	const YOUTUBE_ARGUMENTS		 = 'hd=1&amp;wmode=opaque&amp;showinfo=0&amp;rel=0';
	const VIMEO_ARGUMENTS		 = 'title=0&amp;byline=0&amp;portrait=0&amp;api=1';

	public function __construct()
	{
		RevLoader::add_action('wp_enqueue_scripts', array('RevSliderFront', 'add_actions'));
	}


	/**
	 * START: DEPRECATED FUNCTIONS THAT ARE IN HERE FOR OLD ADDONS TO WORK PROPERLY
	 **/


	/**
	 * END: DEPRECATED FUNCTIONS THAT ARE IN HERE FOR OLD ADDONS TO WORK PROPERLY
	 **/

	/**
	 * Add all actions that the frontend needs here
	 **/
	public static function add_actions()
	{
		global $wp_version, $revslider_is_preview_mode;

		$func	 = new RevSliderFunctions();
		$css	 = new RevSliderCssParser();
		$rs_ver	 = RevLoader::apply_filters('revslider_remove_version', RS_REVISION);
		$global	 = $func->get_global_settings();
		$inc_global = $func->_truefalse($func->get_val($global, 'allinclude', true));

		$inc_footer = $func->_truefalse($func->get_val($global, array('script', 'footer'), false));
		$waitfor = array('jquery');
		//$widget	 = RevLoader::is_active_widget(false, false, 'rev-slider-widget', true);

		$load = false;
		$load = RevLoader::apply_filters('revslider_include_libraries', $load);
		$load = ($revslider_is_preview_mode === true) ? true : $load;
		$load = ($inc_global === true) ? true : $load;
		//$load = (self::has_shortcode('rev_slider') === true) ? true : $load;
		//$load = ($widget !== false) ? true : $load;

		if ($inc_global === false) {
			$output = new RevSliderOutput();
			$output->set_add_to($func->get_val($global, 'includeids', ''));
			$add_to = $output->check_add_to(true);
			$load	= ($add_to === true) ? true : $load;
		}

		if ($load === false) return false;
        if(Tools::getValue('controller')== 'AdminRevolutionsliderAjax'){
            	RevLoader::wp_enqueue_style('rs-plugin-settings', RS_PLUGIN_URL . 'public/assets/css/rs6.css', array(), $rs_ver);
        }

		/**
		 * Fix for WordPress versions below 3.7
		 **/
		$style_pre = ($wp_version < 3.7) ? '<style type="text/css">' : '';
		$style_post = ($wp_version < 3.7) ? '</style>' : '';
		$custom_css = $func->get_static_css();
		$custom_css = $css->compress_css($custom_css);
		$custom_css = (trim($custom_css) == '') ? '#rs-demo-id {}' : $custom_css;

		//wp_add_inline_style('rs-plugin-settings', $style_pre . $custom_css . $style_post);
		//wp_enqueue_script(array('jquery'));

		/**
		 * dequeue tp-tools to make sure that always the latest is loaded
		 **/
		global $wp_scripts;
		if (version_compare($func->get_val($wp_scripts, array('registered', 'tp-tools', 'ver'), '1.0'), RS_TP_TOOLS, '<')) {
			//wp_deregister_script('tp-tools');
			//wp_dequeue_script('tp-tools');
		}



if(Tools::getValue('controller')== 'AdminRevolutionsliderAjax'){
    	RevLoader::wp_enqueue_script('tp-tools', RS_PLUGIN_URL . 'public/assets/js/rbtools.min.js', $waitfor, RS_TP_TOOLS, $inc_footer);

    	if (!file_exists(RS_PLUGIN_PATH . 'public/assets/js/rs6.min.js')) {
			RevLoader::wp_enqueue_script('revmin', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.main.js', 'tp-tools', $rs_ver, $inc_footer);
			//if on, load all libraries instead of dynamically loading them
			RevLoader::wp_enqueue_script('revmin-actions', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.actions.js', 'tp-tools', $rs_ver, $inc_footer);
			RevLoader::wp_enqueue_script('revmin-carousel', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.carousel.js', 'tp-tools', $rs_ver, $inc_footer);
			RevLoader::wp_enqueue_script('revmin-layeranimation', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.layeranimation.js', 'tp-tools', $rs_ver, $inc_footer);
			RevLoader::wp_enqueue_script('revmin-navigation', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.navigation.js', 'tp-tools', $rs_ver, $inc_footer);
			RevLoader::wp_enqueue_script('revmin-panzoom', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.panzoom.js', 'tp-tools', $rs_ver, $inc_footer);
			RevLoader::wp_enqueue_script('revmin-parallax', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.parallax.js', 'tp-tools', $rs_ver, $inc_footer);
			RevLoader::wp_enqueue_script('revmin-slideanims', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.slideanims.js', 'tp-tools', $rs_ver, $inc_footer);
			RevLoader::wp_enqueue_script('revmin-video', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.video.js', 'tp-tools', $rs_ver, $inc_footer);
		} else {
			RevLoader::wp_enqueue_script('revmin', RS_PLUGIN_URL . 'public/assets/js/rs6.min.js', 'tp-tools', $rs_ver, $inc_footer);
		}
}


		//commented
		//RevLoader::add_action('wp_head', array('RevSliderFront', 'add_meta_generator'));
		RevLoader::add_action('wp_head', array('RevSliderFront', 'js_set_start_size'), 99);
		RevLoader::add_action('admin_head', array('RevSliderFront', 'js_set_start_size'), 99);
		RevLoader::add_action('wp_footer', array('RevSliderFront', 'load_icon_fonts'));
		RevLoader::add_action('wp_footer', array('RevSliderFront', 'load_google_fonts'));

		//Async JS Loading
		if ($func->_truefalse($func->get_val($global, array('script', 'defer'), false)) === true) {
			RevLoader::add_filter('clean_url', array('RevSliderFront', 'add_defer_forscript'), 11, 1);
		}

		//RevLoader::add_action('wp_before_admin_bar_render', array('RevSliderFront', 'add_admin_menu_nodes'));
		//RevLoader::add_action('wp_footer', array('RevSliderFront', 'add_admin_bar'), 99);
	}


	/**
	 * Add Meta Generator Tag in FrontEnd
	 * @since: 5.0
	 */
	public static function add_meta_generator()
	{
		echo RevLoader::apply_filters('revslider_meta_generator', '<meta name="generator" content="Powered by Slider Revolution ' . RS_REVISION . ' - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface." />' . "\n");
	}

	/**
	 * Load Used Icon Fonts
	 * @since: 5.0
	 */
	public static function load_icon_fonts()
	{
		global $fa_var, $fa_icon_var, $pe_7s_var;
		$func	= new RevSliderFunctions();
		$global	= $func->get_global_settings();
		$ignore_fa = $func->_truefalse($func->get_val($global, 'fontawesomedisable', false));

		echo ($ignore_fa === false && ($fa_icon_var == true || $fa_var == true)) ? '<link rel="stylesheet" property="stylesheet" id="rs-icon-set-fa-icon-css" href="' . RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/css/font-awesome.css" type="text/css" media="all" />' . "\n" : '';
		echo ($pe_7s_var) ? '<link rel="stylesheet" property="stylesheet" id="rs-icon-set-pe-7s-css" href="' . RS_PLUGIN_URL . 'public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" type="text/css" media="all" />' . "\n" : '';
	}


	/**
	 * Load Used Google Fonts
	 * add google fonts of all sliders found on the page
	 * @since: 6.0
	 */
	public static function load_google_fonts()
	{
		$func	= new RevSliderFunctions();
		$fonts	= $func->print_clean_font_import();
		if (!empty($fonts)) {
			echo $fonts . "\n";
		}
	}



	/**
	 * adds async loading
	 * @since: 5.0
	 */
	public static function add_defer_forscript($url)
	{
		if (strpos($url, 'rs6.min.js') === false && strpos($url, 'rbtools.min.js') === false) {
			return $url;
		} elseif (Tools::getValue('controller')== 'AdminRevolutionsliderAjax') {
			return $url;
		} else {
			return $url . "' defer='defer";
		}
	}


	/**
	 * Add Meta Generator Tag in FrontEnd
	 * @since: 5.4.3
	 * @before: add_setREVStartSize()
		//NOT COMPRESSED VERSION
		function setREVStartSize(e){	
			//window.requestAnimationFrame(function() {	
				window.RSIW = window.RSIW===undefined ? window.innerWidth : window.RSIW;	
				window.RSIH = window.RSIH===undefined ? window.innerHeight : window.RSIH;	
				try {								
					var pw = document.getElementById(e.c).parentNode.offsetWidth,
						newh;
					pw = pw===0 || isNaN(pw) ? window.RSIW : pw;
					e.tabw = e.tabw===undefined ? 0 : parseInt(e.tabw);
					e.thumbw = e.thumbw===undefined ? 0 : parseInt(e.thumbw);
					e.tabh = e.tabh===undefined ? 0 : parseInt(e.tabh);
					e.thumbh = e.thumbh===undefined ? 0 : parseInt(e.thumbh);
					e.tabhide = e.tabhide===undefined ? 0 : parseInt(e.tabhide);
					e.thumbhide = e.thumbhide===undefined ? 0 : parseInt(e.thumbhide);
					e.mh = e.mh===undefined || e.mh=="" || e.mh==="auto" ? 0 : parseInt(e.mh,0);		
					if(e.layout==="fullscreen" || e.l==="fullscreen") 						
						newh = Math.max(e.mh,window.RSIH);					
					else{					
						e.gw = Array.isArray(e.gw) ? e.gw : [e.gw];
						for (var i in e.rl) if (e.gw[i]===undefined || e.gw[i]===0) e.gw[i] = e.gw[i-1];					
						e.gh = e.el===undefined || e.el==="" || (Array.isArray(e.el) && e.el.length==0)? e.gh : e.el;
						e.gh = Array.isArray(e.gh) ? e.gh : [e.gh];
						for (var i in e.rl) if (e.gh[i]===undefined || e.gh[i]===0) e.gh[i] = e.gh[i-1];
											
						var nl = new Array(e.rl.length),
							ix = 0,						
							sl;					
						e.tabw = e.tabhide>=pw ? 0 : e.tabw;
						e.thumbw = e.thumbhide>=pw ? 0 : e.thumbw;
						e.tabh = e.tabhide>=pw ? 0 : e.tabh;
						e.thumbh = e.thumbhide>=pw ? 0 : e.thumbh;					
						for (var i in e.rl) nl[i] = e.rl[i]<window.RSIW ? 0 : e.rl[i];
						sl = nl[0];									
						for (var i in nl) if (sl>nl[i] && nl[i]>0) { sl = nl[i]; ix=i;}															
						var m = pw>(e.gw[ix]+e.tabw+e.thumbw) ? 1 : (pw-(e.tabw+e.thumbw)) / (e.gw[ix]);					
						newh =  (e.gh[ix] * m) + (e.tabh + e.thumbh);
					}				
					if(window.rs_init_css===undefined) window.rs_init_css = document.head.appendChild(document.createElement("style"));					
					document.getElementById(e.c).height = newh+"px";
					window.rs_init_css.innerHTML += "#"+e.c+"_wrapper { height: "+newh+"px }";				
				} catch(e){
					console.log("Failure at Presize of Slider:" + e)
				}					   
			//}
		  };
	 */
	public static function js_set_start_size()
	{
		global $revslider_rev_start_size_loaded;
		if ($revslider_rev_start_size_loaded === true) return false;

		$script = '<script type="text/javascript">';
		$script .= 'function setREVStartSize(e){
			//window.requestAnimationFrame(function() {				 
				window.RSIW = window.RSIW===undefined ? window.innerWidth : window.RSIW;	
				window.RSIH = window.RSIH===undefined ? window.innerHeight : window.RSIH;	
				try {								
					var pw = document.getElementById(e.c).parentNode.offsetWidth,
						newh;
					pw = pw===0 || isNaN(pw) ? window.RSIW : pw;
					e.tabw = e.tabw===undefined ? 0 : parseInt(e.tabw);
					e.thumbw = e.thumbw===undefined ? 0 : parseInt(e.thumbw);
					e.tabh = e.tabh===undefined ? 0 : parseInt(e.tabh);
					e.thumbh = e.thumbh===undefined ? 0 : parseInt(e.thumbh);
					e.tabhide = e.tabhide===undefined ? 0 : parseInt(e.tabhide);
					e.thumbhide = e.thumbhide===undefined ? 0 : parseInt(e.thumbhide);
					e.mh = e.mh===undefined || e.mh=="" || e.mh==="auto" ? 0 : parseInt(e.mh,0);		
					if(e.layout==="fullscreen" || e.l==="fullscreen") 						
						newh = Math.max(e.mh,window.RSIH);					
					else{					
						e.gw = Array.isArray(e.gw) ? e.gw : [e.gw];
						for (var i in e.rl) if (e.gw[i]===undefined || e.gw[i]===0) e.gw[i] = e.gw[i-1];					
						e.gh = e.el===undefined || e.el==="" || (Array.isArray(e.el) && e.el.length==0)? e.gh : e.el;
						e.gh = Array.isArray(e.gh) ? e.gh : [e.gh];
						for (var i in e.rl) if (e.gh[i]===undefined || e.gh[i]===0) e.gh[i] = e.gh[i-1];
											
						var nl = new Array(e.rl.length),
							ix = 0,						
							sl;					
						e.tabw = e.tabhide>=pw ? 0 : e.tabw;
						e.thumbw = e.thumbhide>=pw ? 0 : e.thumbw;
						e.tabh = e.tabhide>=pw ? 0 : e.tabh;
						e.thumbh = e.thumbhide>=pw ? 0 : e.thumbh;					
						for (var i in e.rl) nl[i] = e.rl[i]<window.RSIW ? 0 : e.rl[i];
						sl = nl[0];									
						for (var i in nl) if (sl>nl[i] && nl[i]>0) { sl = nl[i]; ix=i;}															
						var m = pw>(e.gw[ix]+e.tabw+e.thumbw) ? 1 : (pw-(e.tabw+e.thumbw)) / (e.gw[ix]);					
						newh =  (e.gh[ix] * m) + (e.tabh + e.thumbh);
					}				
					if(window.rs_init_css===undefined) window.rs_init_css = document.head.appendChild(document.createElement("style"));					
					document.getElementById(e.c).height = newh+"px";
					window.rs_init_css.innerHTML += "#"+e.c+"_wrapper { height: "+newh+"px }";				
				} catch(e){
					console.log("Failure at Presize of Slider:" + e)
				}					   
			//});
		  };';
		$script .= '</script>' . "\n";
		echo RevLoader::apply_filters('revslider_add_setREVStartSize', $script);

		$revslider_rev_start_size_loaded = true;
	}

	/**
	 * sets the post saving value to true, so that the output echo will not be done
	 **/
	public static function set_post_saving()
	{
		global $revslider_save_post;
		$revslider_save_post = true;
	}


	/**
	 * Create Tables
	 * @only_base needs to be false
	 *  it can only be true by fixing database issues
	 *  this protects that the _bkp tables are not filled after 
	 *  we are already on version 6.0
	 **/
	public static function create_tables($only_base = false)
	{
		$table_version = RevLoader::get_option('revslider_table_version', '1.0.0');

		if (version_compare($table_version, self::CURRENT_TABLE_VERSION, '<')) {
			global $wpdb;

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$sql = "CREATE TABLE " . $wpdb->prefix . self::TABLE_SLIDER . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  title tinytext NOT NULL,
			  alias tinytext,
			  params LONGTEXT NOT NULL,
			  settings text NULL,
			  type VARCHAR(191) NOT NULL DEFAULT '',
			  UNIQUE KEY id (id)
			);";
			dbDelta($sql);

			$sql = "CREATE TABLE " . $wpdb->prefix . self::TABLE_SLIDES . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  slider_id int(9) NOT NULL,
			  slide_order int not NULL,
			  params LONGTEXT NOT NULL,
			  layers LONGTEXT NOT NULL,
			  settings text NOT NULL DEFAULT '',
			  UNIQUE KEY id (id)
			);";
			dbDelta($sql);

			$sql = "CREATE TABLE " . $wpdb->prefix . self::TABLE_STATIC_SLIDES . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  slider_id int(9) NOT NULL,
			  params LONGTEXT NOT NULL,
			  layers LONGTEXT NOT NULL,
			  settings text NOT NULL,
			  UNIQUE KEY id (id)
			);";
			dbDelta($sql);

			$sql = "CREATE TABLE " . $wpdb->prefix . self::TABLE_CSS . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  handle TEXT NOT NULL,
			  settings LONGTEXT,
			  hover LONGTEXT,
			  advanced LONGTEXT,
			  params LONGTEXT NOT NULL,
			  UNIQUE KEY id (id)
			);";
			dbDelta($sql);

			$sql = "CREATE TABLE " . $wpdb->prefix . self::TABLE_LAYER_ANIMATIONS . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  handle TEXT NOT NULL,
			  params TEXT NOT NULL,
			  settings text NULL,
			  UNIQUE KEY id (id)
			);";
			dbDelta($sql);

			$sql = "CREATE TABLE " . $wpdb->prefix . self::TABLE_NAVIGATIONS . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  name VARCHAR(191) NOT NULL,
			  handle VARCHAR(191) NOT NULL,
			  type VARCHAR(191) NOT NULL,
			  css LONGTEXT NOT NULL,
			  markup LONGTEXT NOT NULL,
			  settings LONGTEXT NULL,
			  UNIQUE KEY id (id)
			);";
			dbDelta($sql);

			//create CSS entries
			$result = $wpdb->get_row("SELECT COUNT( DISTINCT id ) AS NumberOfEntrys FROM " . $wpdb->prefix . self::TABLE_CSS);
			if (!empty($result) && $result->NumberOfEntrys == 0) {
				$css_class = new RevSliderCssParser();
				$css_class->import_css_captions();
			}

			RevLoader::update_option('revslider_table_version', self::CURRENT_TABLE_VERSION);
			//$table_version = self::CURRENT_TABLE_VERSION;
		}


		/**
		 * check if table version is below 1.0.8.
		 * if yes, duplicate the tables into _bkp
		 * this way, we can revert back to v5 if any slider
		 * has issues in the v6 migration process
		 **/
		if (version_compare($table_version, '1.0.8', '<') && ($only_base === false || $only_base === '')) {
			global $wpdb;

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::TABLE_SLIDER . "_bkp LIKE " . $wpdb->prefix . self::TABLE_SLIDER . ";";
			dbDelta($sql);
			$result = $wpdb->get_row("SELECT EXISTS (SELECT 1 FROM " . $wpdb->prefix . self::TABLE_SLIDER . "_bkp) AS `exists`;", ARRAY_A);
			if (!empty($result) && isset($result['exists']) && $result['exists'] === '0') {
				$sql = "INSERT " . $wpdb->prefix . self::TABLE_SLIDER . "_bkp SELECT * FROM " . $wpdb->prefix . self::TABLE_SLIDER . ";";
				$wpdb->query($sql);
			}

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::TABLE_SLIDES . "_bkp LIKE " . $wpdb->prefix . self::TABLE_SLIDES . ";";
			dbDelta($sql);
			$result = $wpdb->get_row("SELECT EXISTS (SELECT 1 FROM " . $wpdb->prefix . self::TABLE_SLIDES . "_bkp) AS `exists`;", ARRAY_A);
			if (!empty($result) && isset($result['exists']) && $result['exists'] === '0') {
				$sql = "INSERT " . $wpdb->prefix . self::TABLE_SLIDES . "_bkp SELECT * FROM " . $wpdb->prefix . self::TABLE_SLIDES . ";";
				$wpdb->query($sql);
			}

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::TABLE_STATIC_SLIDES . "_bkp LIKE " . $wpdb->prefix . self::TABLE_STATIC_SLIDES . ";";
			dbDelta($sql);
			$result = $wpdb->get_row("SELECT EXISTS (SELECT 1 FROM " . $wpdb->prefix . self::TABLE_STATIC_SLIDES . "_bkp) AS `exists`;", ARRAY_A);
			if (!empty($result) && isset($result['exists']) && $result['exists'] === '0') {
				$sql = "INSERT " . $wpdb->prefix . self::TABLE_STATIC_SLIDES . "_bkp SELECT * FROM " . $wpdb->prefix . self::TABLE_STATIC_SLIDES . ";";
				$wpdb->query($sql);
			}

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::TABLE_CSS . "_bkp LIKE " . $wpdb->prefix . self::TABLE_CSS . ";";
			dbDelta($sql);
			$result = $wpdb->get_row("SELECT EXISTS (SELECT 1 FROM " . $wpdb->prefix . self::TABLE_CSS . "_bkp) AS `exists`;", ARRAY_A);
			if (!empty($result) && isset($result['exists']) && $result['exists'] === '0') {
				$sql = "INSERT " . $wpdb->prefix . self::TABLE_CSS . "_bkp SELECT * FROM " . $wpdb->prefix . self::TABLE_CSS . ";";
				$wpdb->query($sql);
			}

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::TABLE_LAYER_ANIMATIONS . "_bkp LIKE " . $wpdb->prefix . self::TABLE_LAYER_ANIMATIONS . ";";
			dbDelta($sql);
			$result = $wpdb->get_row("SELECT EXISTS (SELECT 1 FROM " . $wpdb->prefix . self::TABLE_LAYER_ANIMATIONS . "_bkp) AS `exists`;", ARRAY_A);
			if (!empty($result) && isset($result['exists']) && $result['exists'] === '0') {
				$sql = "INSERT " . $wpdb->prefix . self::TABLE_LAYER_ANIMATIONS . "_bkp SELECT * FROM " . $wpdb->prefix . self::TABLE_LAYER_ANIMATIONS . ";";
				$wpdb->query($sql);
			}

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::TABLE_NAVIGATIONS . "_bkp LIKE " . $wpdb->prefix . self::TABLE_NAVIGATIONS . ";";
			dbDelta($sql);
			$result = $wpdb->get_row("SELECT EXISTS (SELECT 1 FROM " . $wpdb->prefix . self::TABLE_NAVIGATIONS . "_bkp) AS `exists`;", ARRAY_A);
			if (!empty($result) && isset($result['exists']) && $result['exists'] === '0') {
				$sql = "INSERT " . $wpdb->prefix . self::TABLE_NAVIGATIONS . "_bkp SELECT * FROM " . $wpdb->prefix . self::TABLE_NAVIGATIONS . ";";
				$wpdb->query($sql);
			}
		}
	}
}

?>