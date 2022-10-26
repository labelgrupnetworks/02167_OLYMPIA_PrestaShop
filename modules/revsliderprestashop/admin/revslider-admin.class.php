<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class RevSliderAdmin extends RevSliderFunctionsAdmin {

	// private $theme_mode = false;
	private $view            = 'slider';
	private $user_role       = 'admin';
	private $global_settings = array();
	private $screens         = array(); // holds all RevSlider Relevant screens in it
	private $allowed_views   = array( 'sliders', 'slider', 'slide', 'update' ); // holds pages, that are allowed to be included
	private $pages           = array( 'revslider' ); // , 'revslider_navigation', 'rev_addon', 'revslider_global_settings'
	private $dev_mode        = false;
	private $path_views;
	public $show_content = null;


	/**
	 * START: DEPRECATED FUNCTIONS PRIOR 6.2.0 THAT ARE IN HERE FOR OLD THEMES TO WORK PROPERLY
	 **/

	/**
	 * Activate the Plugin through the ThemePunch Servers
	 *
	 * @before: RevSliderOperations::checkPurchaseVerification();
	 * @moved   to RevSliderLicense::activate_plugin();
	 **/
	public function activate_plugin( $code ) {
		$rs_license = new RevSliderLicense();
		return $rs_license->activate_plugin( $code, false );
	}


	/**
	 * Deactivate the Plugin through the ThemePunch Servers
	 *
	 * @before: RevSliderOperations::doPurchaseDeactivation();
	 * @moved   to RevSliderLicense::deactivate_plugin();
	 **/
	public function deactivate_plugin() {
		$rs_license = new RevSliderLicense();
		return $rs_license->deactivate_plugin();
	}

	/**
	 * END: DEPRECATED FUNCTIONS THAT ARE IN HERE FOR OLD ADDONS TO WORK PROPERLY
	 **/


	/**
	 * construct admin part
	 **/
	public function __construct( $content = true ) {
		parent::__construct();

		if ( ! file_exists( RS_PLUGIN_PATH . 'admin/assets/js/plugins/utils.min.js' ) && ! file_exists( RS_PLUGIN_PATH . 'admin/assets/js/modules/editor.min.js' ) ) {
			$this->dev_mode = true;
		}

		$this->path_views      = RS_PLUGIN_PATH . 'admin/views/';
		$this->global_settings = $this->get_global_settings();

		$this->set_current_page();
		$this->set_user_role();
		// track
		$this->do_update_checks();
		$this->add_actions();
		// $this->add_filters();
		// edit by dev

		$code       = RevLoader::get_option( 'revslider-code', '' );
		$rs_license = new RevSliderLicense();
		$rs_license->activate_plugin( $code, true );

		if ( $content == false ) {
			return;
		}
		$this->processAdmin();

	}

	public function processAdmin() {
		// var_dump(RevLoader::is_ajax());die();
		RevLoader::loadAllAddons();
		if ( ! RevLoader::is_ajax() ) {
			$this->display_admin_page();
		}
	}

	/**
	 * enqueue all admin styles
	 **/
	public function enqueue_admin_styles() {

		
		//RevLoader::wp_enqueue_style( 'rs-open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,300,700,600,800' );
		//RevLoader::wp_enqueue_style( 'rs-roboto', '//fonts.googleapis.com/css?family=Roboto' );
		//RevLoader::wp_enqueue_style( 'tp-material-icons', '//fonts.googleapis.com/icon?family=Material+Icons' );
		// wp_enqueue_style('revslider-global-styles', RS_PLUGIN_URL . 'admin/assets/css/global.css', array(), RS_REVISION);
		// RevLoader::wp_enqueue_style(array('wp-jquery-ui', 'wp-jquery-ui-core', 'wp-jquery-ui-dialog', 'wp-color-picker'));
		//RevLoader::wp_enqueue_style( 'revbuilder-color-picker-css', RS_PLUGIN_URL . 'admin/assets/css/tp-color-picker.css', array(), RS_REVISION );

		if ( in_array( $this->get_val( $_GET, 'page' ), $this->pages ) ) {

			//RevLoader::wp_enqueue_style( 'revbuilder-select2RS', RS_PLUGIN_URL . 'admin/assets/css/select2RS.css', array(), RS_REVISION );
			// wp_enqueue_style('codemirror-css', RS_PLUGIN_URL .'admin/assets/css/codemirror.css', array(), RS_REVISION);
			//RevLoader::wp_enqueue_style( 'rs-frontend-settings', RS_PLUGIN_URL . 'public/assets/css/rs6.css', array(), RS_REVISION );
			//RevLoader::wp_enqueue_style( 'rs-icon-set-fa-icon-', RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/css/font-awesome.css', array(), RS_REVISION );
			//RevLoader::wp_enqueue_style( 'rs-icon-set-pe-7s-', RS_PLUGIN_URL . 'public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css', array(), RS_REVISION );
			// commented
			// RevLoader::wp_enqueue_style('revslider-basics-css', RS_PLUGIN_URL . 'admin/assets/css/basics.css', array(), RS_REVISION); //'rs-new-plugin-settings'
			//RevLoader::wp_enqueue_style( 'revslider-basics-css-1', RS_PLUGIN_URL . 'admin/assets/css/basics.css', array(), RS_REVISION ); // 'rs-new-plugin-settings'
			//RevLoader::wp_enqueue_style( 'rs-new-plugin-settings', RS_PLUGIN_URL . 'admin/assets/css/builder.css', array( 'revslider-basics-css' ), RS_REVISION );
			if ( RevLoader::is_rtl() ) {
				// track
				// RevLoader::wp_enqueue_style('rs-new-plugin-settings-rtl', RS_PLUGIN_URL . 'admin/assets/css/builder-rtl.css', array('rs-new-plugin-settings'), RS_REVISION);
			}
		}
		
	}

	/**
	 * enqueue all admin scripts
	 **/
	public function enqueue_admin_scripts() {

		if ( in_array( $this->get_val( $_GET, 'page' ), $this->pages ) ) {
			global $wp_scripts;
			$view = $this->get_val( $_GET, 'view' );

			$wait_for = array( 'media-editor', 'media-audiovideo' );
			if ( RevLoader::is_admin() ) {
				$wait_for[] = 'mce-view';
				$wait_for[] = 'image-edit';
			}
			$wait_for = array();

			// RevLoader::wp_enqueue_script('tp-tools', RS_PLUGIN_URL . 'public/assets/js/rbtools.min.js', $wait_for, RS_TP_TOOLS);
			RevLoader::wp_enqueue_script( 'tp-tools-edited', RS_PLUGIN_URL . 'public/assets/js/rbtools.min.js', $wait_for, RS_TP_TOOLS );

			if ( $this->dev_mode ) {
				// track not used
				// RevLoader::wp_enqueue_script( 'revbuilder-admin-1', RS_PLUGIN_URL . 'admin/assets/js/modules/admin.js', array( 'jquery' ), RS_REVISION, false );
				// RevLoader::wp_localize_script( 'revbuilder-admin-1', 'RVS_LANG', $this->get_javascript_multilanguage() ); // Load multilanguage for JavaScript
				// RevLoader::wp_enqueue_script( 'revbuilder-basics', RS_PLUGIN_URL . 'admin/assets/js/modules/basics.js', array( 'jquery' ), RS_REVISION, false );
				// RevLoader::wp_enqueue_script( 'revbuilder-select2RS', RS_PLUGIN_URL . 'admin/assets/js/plugins/select2RS.full.min.js', array( 'jquery' ), RS_REVISION, false );
				// RevLoader::wp_enqueue_script( 'revbuilder-color-picker-js', RS_PLUGIN_URL . 'admin/assets/js/plugins/tp-color-picker.min.js', array( 'jquery', 'revbuilder-select2RS', 'wp-color-picker' ), RS_REVISION );
				// RevLoader::wp_enqueue_script( 'revbuilder-clipboard', RS_PLUGIN_URL . 'admin/assets/js/plugins/clipboard.min.js', array( 'jquery' ), RS_REVISION, false );
				// RevLoader::wp_enqueue_script( 'revbuilder-objectlibrary', RS_PLUGIN_URL . 'admin/assets/js/modules/objectlibrary.js', array( 'jquery' ), RS_REVISION, false );
				// RevLoader::wp_enqueue_script( 'revbuilder-optimizer', RS_PLUGIN_URL . 'admin/assets/js/modules/optimizer.js', array( 'jquery' ), RS_REVISION, false );
			} else {
				// commented
				// RevLoader::wp_enqueue_script('revbuilder-admin', RS_PLUGIN_URL . 'admin/assets/js/modules/admin.min.js', array('jquery'), RS_REVISION, false);
				// RevLoader::wp_localize_script('revbuilder-admin', 'RVS_LANG', $this->get_javascript_multilanguage()); //Load multilanguage for JavaScript
				RevLoader::wp_enqueue_script( 'revbuilder-admin-1', RS_PLUGIN_URL . 'admin/assets/js/modules/admin.min.js', array( 'jquery' ), RS_REVISION, false );
				RevLoader::wp_localize_script( 'revbuilder-admin-1', 'RVS_LANG', $this->get_javascript_multilanguage() ); // Load multilanguage for JavaScript
				RevLoader::wp_enqueue_script( 'revbuilder-utils', RS_PLUGIN_URL . 'admin/assets/js/plugins/utils.min.js', array( 'jquery', 'wp-color-picker' ), RS_REVISION, false );
			}

			if ( $view == 'slide' && $this->dev_mode ) {
				// track
				// wp_enqueue_script('revbuilder-help', RS_PLUGIN_URL . 'admin/assets/js/modules/helpinit.js', array('jquery', 'revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-toolbar', RS_PLUGIN_URL . 'admin/assets/js/modules/rightclick.js', array('jquery', 'revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-effects', RS_PLUGIN_URL . 'admin/assets/js/modules/timeline.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-layer', RS_PLUGIN_URL . 'admin/assets/js/modules/layer.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-layertools', RS_PLUGIN_URL . 'admin/assets/js/modules/layertools.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-quick-style', RS_PLUGIN_URL . 'admin/assets/js/modules/quickstyle.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-navigations', RS_PLUGIN_URL . 'admin/assets/js/modules/navigation.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-layeractions', RS_PLUGIN_URL . 'admin/assets/js/modules/layeractions.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-layerlist', RS_PLUGIN_URL . 'admin/assets/js/modules/layerlist.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-slide', RS_PLUGIN_URL . 'admin/assets/js/modules/slide.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder-slider', RS_PLUGIN_URL . 'admin/assets/js/modules/slider.js', array('jquery','revbuilder-admin'), RS_REVISION, false);
				// wp_enqueue_script('revbuilder', RS_PLUGIN_URL . 'admin/assets/js/builder.js', array('jquery','revbuilder-admin', 'jquery-ui-sortable'), RS_REVISION, false);
			} elseif ( $view == 'slide' && ! $this->dev_mode ) {
				RevLoader::wp_enqueue_script( 'revbuilder-editor', RS_PLUGIN_URL . 'admin/assets/js/modules/editor.min.js', array( 'jquery', 'revbuilder-admin', 'jquery-ui-sortable' ), RS_REVISION, false );
			}

			if ( $view == '' || $view == 'sliders' ) {
				if ( $this->dev_mode ) {
					RevLoader::wp_enqueue_script( 'revbuilder-overview', RS_PLUGIN_URL . 'admin/assets/js/modules/overview.js', array( 'jquery' ), RS_REVISION, false );
				} else {
					RevLoader::wp_enqueue_script( 'revbuilder-overview', RS_PLUGIN_URL . 'admin/assets/js/modules/overview.min.js', array( 'jquery' ), RS_REVISION, false );
				}

				if ( ! file_exists( RS_PLUGIN_PATH . 'public/assets/js/rs6.min.js' ) ) {
					// track
					// wp_enqueue_script('revmin', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.main.js', 'tp-tools', RS_REVISION, false);
					// //if on, load all libraries instead of dynamically loading them
					// wp_enqueue_script('revmin-actions', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.actions.js', 'tp-tools', RS_REVISION, false);
					// wp_enqueue_script('revmin-carousel', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.carousel.js', 'tp-tools', RS_REVISION, false);
					// wp_enqueue_script('revmin-layeranimation', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.layeranimation.js', 'tp-tools', RS_REVISION, false);
					// wp_enqueue_script('revmin-navigation', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.navigation.js', 'tp-tools', RS_REVISION, false);
					// wp_enqueue_script('revmin-panzoom', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.panzoom.js', 'tp-tools', RS_REVISION, false);
					// wp_enqueue_script('revmin-parallax', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.parallax.js', 'tp-tools', RS_REVISION, false);
					// wp_enqueue_script('revmin-slideanims', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.slideanims.js', 'tp-tools', RS_REVISION, false);
					// wp_enqueue_script('revmin-video', RS_PLUGIN_URL . 'public/assets/js/dev/rs6.video.js', 'tp-tools', RS_REVISION, false);
				} else {
					RevLoader::wp_enqueue_script( 'revmin', RS_PLUGIN_URL . 'public/assets/js/rs6.min.js', array( 'jquery', 'tp-tools' ), RS_REVISION, false );
				}
			}
		}

		// include all media upload scripts
		// $this->add_media_upload_includes();
	}

	/**
	 * add all js and css needed for media upload
	 */
	protected static function add_media_upload_includes() {
		if ( function_exists( 'wp_enqueue_media' ) ) {
			// wp_enqueue_media();
		}
		// RevLoader::wp_enqueue_script('thickbox');
		// RevLoader::wp_enqueue_script('media-upload');
		// RevLoader::wp_enqueue_style('thickbox');
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		RevLoader::load_plugin_textdomain( 'revslider', false, dirname( RS_PLUGIN_SLUG_PATH ) . '/languages/' );
		RevLoader::load_plugin_textdomain( 'revsliderhelp', false, dirname( RS_PLUGIN_SLUG_PATH ) . '/languages/' );
	}

	/**
	 * set the user role, to restrict plugin usage to certain groups
	 *
	 * @since: 6.0
	 **/
	public function set_user_role() {
		$this->user_role = $this->get_val( $this->global_settings, 'permission', 'admin' );
	}

	/**
	 * check if we need to search for updates, if yes. Do them
	 **/
	private function do_update_checks() {
		$upgrade   = new RevSliderUpdate( RS_REVISION );
		$library   = new RevSliderObjectLibrary();
		$template  = new RevSliderTemplate();
		$validated = RevLoader::get_option( 'revslider-valid', 'false' );
		$stablev   = RevLoader::get_option( 'revslider-stable-version', '0' );

		$uol = ( isset( $_REQUEST['update_object_library'] ) ) ? true : false;
		$library->_get_list( $uol );

		$us = ( isset( $_REQUEST['update_shop'] ) ) ? true : false;
		$template->_get_template_list( $us );
		
		$upgrade->force = ( in_array( $this->get_val( $_REQUEST, 'checkforupdates', 'false' ), array( 'true', true ), true ) ) ? true : false;
		$upgrade->_retrieve_version_info();

		if ( $validated === 'true' || version_compare( RS_REVISION, $stablev, '<' ) ) {
			$upgrade->add_update_checks();
		}
	}

	/**
	 * Add Classes to the WordPress body
	 *
	 * @since 6.0
	 */
	function modify_admin_body_class( $classes ) {
		$classes .= ( $this->get_val( $_GET, 'page' ) == 'revslider' && $this->get_val( $_GET, 'view' ) == 'slide' ) ? ' rs-builder-mode' : '';
		$classes .= ( $this->_truefalse( $this->get_val( $this->global_settings, 'highContrast', false ) ) === true && $this->get_val( $_GET, 'page' ) === 'revslider' ) ? ' rs-high-contrast' : '';

		return $classes;
	}


	/**
	 * Add all actions that the backend needs here
	 **/
	public function add_actions() {

		RevLoader::add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		// track
		// RevLoader::add_action('admin_head', array($this, 'hide_notices'), 1);
		// RevLoader::add_action('admin_menu', array($this, 'add_admin_pages'));
		// RevLoader::add_action('add_meta_boxes', array($this, 'add_slider_meta_box'));
		// RevLoader::add_action('save_post', array($this, 'on_save_post'));
		RevLoader::add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		RevLoader::add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		RevLoader::add_action( 'wp_ajax_revslider_ajax_action', array( $this, 'do_ajax_action' ) ); // ajax response to save slider options.
		// RevLoader::add_action('wp_ajax_revslider_ajax_call_front', array($this, 'do_front_ajax_action'));
		// RevLoader::add_action('wp_ajax_nopriv_revslider_ajax_call_front', array($this, 'do_front_ajax_action')); //for not logged in users

		// if(isset($pagenow) && $pagenow == 'plugins.php'){
		// RevLoader::add_action('admin_notices', array($this, 'add_plugins_page_notices'));
		// }

		// RevLoader::add_action('admin_init', array($this, 'merge_addon_notices'), 99);
		// RevLoader::add_action('admin_init', array($this, 'add_suggested_privacy_content'), 15);
	}

	/**
	 * Add all filters that the backend needs here
	 **/
	public function add_filters() {
		RevLoader::add_filter( 'admin_body_class', array( $this, 'modify_admin_body_class' ) );
		RevLoader::add_filter( 'plugin_locale', array( $this, 'change_lang' ), 10, 2 );
	}

	/**
	 * Change the language of the Sldier Backend even if WordPress is set to be a different language
	 *
	 * @since: 6.1.6
	 **/
	public function change_lang( $locale, $domain = '' ) {
		return ( in_array( $domain, array( 'revslider', 'revsliderhelp' ), true ) ) ? $this->get_val( $this->global_settings, 'lang', 'default' ) : $locale;
	}

	/**
	 * add addon merged notices
	 *
	 * @since: 6.2.0
	 **/
	public function add_addon_plugins_page_notices() {
		?>
<div class="error below-h2 soc-notice-wrap revaddon-notice" style="display: none;">
    <p><?php echo RevLoader::__( 'Action required for Slider Revolution AddOns: Please <a href="https://classydevs.com/docs/slider-revolution-6-prestashop/quick-setup/install-activate/" target="_blank">install</a>/<a href="https://classydevs.com/docs/slider-revolution-6-prestashop/quick-setup/register-plugin-2" target="_blank">activate</a>/<a href="https://classydevs.com/docs/slider-revolution-6-prestashop/quick-setup/register-plugin/" target="_blank">update</a> Slider Revolution</a>', 'revslider' ); ?><span
            data-addon="rs-addon-notice" data-noticeid="rs-addon-merged-notices" style="float: right; cursor: pointer"
            class="revaddon-dismiss-notice dashicons dashicons-dismiss"></span></p>
</div>
<?php
	}


	/**
	 * Show message for activation benefits
	 **/
	public static function show_purchase_notice( $plugin_file, $plugin_data, $plugin_status ) {
		?>
<p>
    <?php RevLoader::_e( 'Activate Slider Revolution for <a href="https://classydevs.com/slider-revolution-prestashop/" target="_blank">Premium Benefits (e.g. Live Updates)</a>.', 'revslider' ); ?>
</p>
<?php
	}


	/**
	 * Return the default suggested privacy policy content.
	 *
	 * @return string The default policy content.
	 */
	public function get_default_privacy_content() {
		return RevLoader::__(
			'<h2>In case you’re using Google Web Fonts (default) or playing videos or sounds via YouTube or Vimeo in Slider Revolution we recommend to add the corresponding text phrase to your privacy police:</h2>
		<h3>YouTube</h3> <p>Our website uses plugins from YouTube, which is operated by Google. The operator of the pages is YouTube LLC, 901 Cherry Ave., San Bruno, CA 94066, USA.</p> <p>If you visit one of our pages featuring a YouTube plugin, a connection to the YouTube servers is established. Here the YouTube server is informed about which of our pages you have visited.</p> <p>If you\'re logged in to your YouTube account, YouTube allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your YouTube account.</p> <p>YouTube is used to help make our website appealing. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>Further information about handling user data, can be found in the data protection declaration of YouTube under <a href="https://www.google.de/intl/de/policies/privacy" target="_blank">https://www.google.de/intl/de/policies/privacy</a>.</p>
		<h3>Vimeo</h3> <p>Our website uses features provided by the Vimeo video portal. This service is provided by Vimeo Inc., 555 West 18th Street, New York, New York 10011, USA.</p> <p>If you visit one of our pages featuring a Vimeo plugin, a connection to the Vimeo servers is established. Here the Vimeo server is informed about which of our pages you have visited. In addition, Vimeo will receive your IP address. This also applies if you are not logged in to Vimeo when you visit our plugin or do not have a Vimeo account. The information is transmitted to a Vimeo server in the US, where it is stored.</p> <p>If you are logged in to your Vimeo account, Vimeo allows you to associate your browsing behavior directly with your personal profile. You can prevent this by logging out of your Vimeo account.</p> <p>For more information on how to handle user data, please refer to the Vimeo Privacy Policy at <a href="https://vimeo.com/privacy" target="_blank">https://vimeo.com/privacy</a>.</p>
		<h3>Google Web Fonts</h3> <p>For uniform representation of fonts, this page uses web fonts provided by Google. When you open a page, your browser loads the required web fonts into your browser cache to display texts and fonts correctly.</p> <p>For this purpose your browser has to establish a direct connection to Google servers. Google thus becomes aware that our web page was accessed via your IP address. The use of Google Web fonts is done in the interest of a uniform and attractive presentation of our plugin. This constitutes a justified interest pursuant to Art. 6 (1) (f) DSGVO.</p> <p>If your browser does not support web fonts, a standard font is used by your computer.</p> <p>Further information about handling user data, can be found at <a href="https://developers.google.com/fonts/faq" target="_blank">https://developers.google.com/fonts/faq</a> and in Google\'s privacy policy at <a href="https://www.google.com/policies/privacy/" target="_blank">https://www.google.com/policies/privacy/</a>.</p>
		<h3>SoundCloud</h3><p>On our pages, plugins of the SoundCloud social network (SoundCloud Limited, Berners House, 47-48 Berners Street, London W1T 3NF, UK) may be integrated. The SoundCloud plugins can be recognized by the SoundCloud logo on our site.</p>
		<p>When you visit our site, a direct connection between your browser and the SoundCloud server is established via the plugin. This enables SoundCloud to receive information that you have visited our site from your IP address. If you click on the “Like” or “Share” buttons while you are logged into your SoundCloud account, you can link the content of our pages to your SoundCloud profile. This means that SoundCloud can associate visits to our pages with your user account. We would like to point out that, as the provider of these pages, we have no knowledge of the content of the data transmitted or how it will be used by SoundCloud. For more information on SoundCloud’s privacy policy, please go to https://soundcloud.com/pages/privacy.</p><p>If you do not want SoundCloud to associate your visit to our site with your SoundCloud account, please log out of your SoundCloud account.</p>',
			'revslider'
		);
	}

	/**
	 * The Ajax Action part for backend actions only
	 **/
	public function do_ajax_action() {

		// track commnet for ajax some time work and some time not work
		// @ini_set('memory_limit', RevLoader::apply_filters('admin_memory_limit', WP_MAX_MEMORY_LIMIT));

		$slider = new RevSliderSlider();
		$slide  = new RevSliderSlide();

		$action = $this->get_request_var( 'client_action' );
		$data   = $this->get_request_var( 'data' );
		$data   = ( $data == '' ) ? array() : $data;
		$nonce  = $this->get_request_var( 'nonce' );
		$nonce  = ( empty( $nonce ) ) ? $this->get_request_var( 'rs-nonce' ) : $nonce;

		try {
			if ( RS_DEMO ) {
				switch ( $action ) {
					case 'get_template_information_short':
					case 'import_template_slider':
					case 'install_template_slider':
					case 'install_template_slide':
					case 'get_list_of':
					case 'get_global_settings':
					case 'get_full_slider_object':
					case 'subscribe_to_newsletter':
					case 'check_system':
					case 'load_module':
					case 'get_addon_list':
					case 'get_layers_by_slide':
					case 'silent_slider_update':
					case 'get_help_directory':
					case 'set_tooltip_preference':
					case 'load_builder':
					case 'load_library_object':
					case 'get_tooltips':
						// case 'preview_slider':
						// these are all okay in demo mode
						break;
					default:
						$this->ajax_response_error( RevLoader::__( 'Function Not Available in Demo Mode', 'revslider' ) );
						exit;
					break;
				}
			}

			// track
			/*
			if(!current_user_can('administrator') && RevLoader::apply_filters('revslider_restrict_role', true)){
			switch($action){
			case 'activate_plugin':
			case 'deactivate_plugin':
			case 'import_template_slider':
			case 'install_template_slider':
			case 'install_template_slide':
			case 'import_slider':
			case 'delete_slider':
			case 'create_navigation_preset':
			case 'delete_navigation_preset':
			case 'save_navigation':
			case 'delete_animation':
			case 'save_animation':
			case 'check_system':
			case 'fix_database_issues':
			case 'trigger_font_deletion':
			case 'get_v5_slider_list':
			case 'reimport_v5_slider':
			$this->ajax_response_error(RevLoader::__('Function Only Available for Adminstrators', 'revslider'));
			exit;
			break;
			default:
			$return = RevLoader::apply_filters('revslider_admin_onAjaxAction_user_restriction', true, $action, $data, $slider, $slide, $operations);
			if($return !== true){
			$this->ajax_response_error(RevLoader::__('Function Only Available for Adminstrators', 'revslider'));
			exit;
			}
			break;
			}
			}

			if(wp_verify_nonce($nonce, 'revslider_actions') == false){
			//check if it is wp nonce and if the action is refresh nonce
			$this->ajax_response_error(RevLoader::__('Bad Request', 'revslider'));
			exit;
			}
			*/

			switch ( $action ) {
				// case 'add_new_hook':
				// $f = new SdsRevHooksClass();
				//
				// $result = $f->addNewHook($data);
				//
				// if ($result === true) {
				// self::ajaxResponseSuccessRedirect(
				// __("Hook successfully created!", "revslider"),
				// self::getViewUrl(self::VIEW_SLIDERS)
				// );
				// } else {
				// self::ajaxResponseError($result, false);
				// }
				// break;
				// case 'removes_hooks':
				// if (!@RevsliderPrestashop::getIsset($data['hookname'])) {
				// self::ajaxResponseError(__('Hook not found', REVSLIDER_TEXTDOMAIN), false);
				// }
				//
				// $f = new SdsRevHooksClass();
				//
				// $result = $f->removeHookByHookname($data['hookname']);
				//
				// if ($result === true) {
				// self::ajaxResponseSuccess(__("Hook successfully removed!", REVSLIDER_TEXTDOMAIN), array('data' => $result));
				// } else {
				// self::ajaxResponseError($result, false);
				// }
				// break;
				case 'activate_plugin':
					$result     = false;
					$code       = trim( $this->get_val( $data, 'code' ) );
					$selling    = $this->get_addition( 'selling' );
					$rs_license = new RevSliderLicense();

					if ( ! empty( $code ) ) {
						 $result = $rs_license->activate_plugin( $code, false );
					} else {
						$error = ( $selling === true ) ? RevLoader::__( 'The License Key needs to be set!', 'revslider' ) : RevLoader::__( 'The Purchase Code needs to be set!', 'revslider' );
						$this->ajax_response_error( $error );
						exit;
					}

					if ( $result === true ) {
						$this->ajax_response_success( RevLoader::__( 'Plugin successfully activated', 'revslider' ) );
					} elseif ( $result === false ) {
						$error = ( $selling === true ) ? RevLoader::__( 'License Key is invalid', 'revslider' ) : RevLoader::__( 'Purchase Code is invalid', 'revslider' );
						$this->ajax_response_error( $error );
					} else {
						if ( $result == 'exist' ) {
							$error = ( $selling === true ) ? RevLoader::__( 'License Key already registered!', 'revslider' ) : RevLoader::__( 'Purchase Code already registered!', 'revslider' );
							$this->ajax_response_error( $error );
						} elseif ( $result == 'banned' ) {
							$error = ( $selling === true ) ? RevLoader::__( 'License Key was locked, please contact the ClassyDevs support!', 'revslider' ) : RevLoader::__( 'Purchase Code was locked, please contact the ClassyDevs support!', 'revslider' );
							$this->ajax_response_error( $error );
						}
						$error = ( $selling === true ) ? RevLoader::__( 'License Key could not be validated', 'revslider' ) : RevLoader::__( 'Purchase Code could not be validated', 'revslider' );
						$this->ajax_response_error( $error );
					}
					break;
				case 'deactivate_plugin':
					$rs_license = new RevSliderLicense();
					$result     = $rs_license->deactivate_plugin();

					if ( $result ) {
						$this->ajax_response_success( RevLoader::__( 'Plugin deregistered', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Deregistration failed!', 'revslider' ) );
					}
					break;
				case 'add_custom_hook':

					$hookname       = trim( $this->get_val( $data, 'hookname' ) );

					if(!isset($hookname) || $hookname == ""){
						$this->ajax_response_error( RevLoader::__( 'You must put a hookname.', 'revslider' ) );
						break;
					}

					$existing_custom_hooks = RevLoader::get_option( 'revslider-custom-hooks' );
					$existing_custom_hooks = Tools::jsonDecode($existing_custom_hooks, true);
					
					if(isset($existing_custom_hooks)){
						if(is_array($existing_custom_hooks)){
							if(in_array($hookname, $existing_custom_hooks)){
								$this->ajax_response_error( RevLoader::__( 'This hook is already added!!!', 'revslider' ) );
								break;
							}
							$existing_custom_hooks[$hookname] = $hookname;
						}
					}else{
						$existing_custom_hooks = array();
						$existing_custom_hooks[$hookname] = $hookname;
					}
					RevLoader::update_option( 'revslider-custom-hooks', Tools::jsonEncode($existing_custom_hooks) );
					$this->ajax_response_success( RevLoader::__( 'Hook added succesfully!!!', 'revslider' ) );
					break;
				case 'dismiss_dynamic_notice':
					$ids               = $this->get_val( $data, 'id', array() );
					$notices_discarded = RevLoader::get_option( 'revslider-notices-dc', array() );
					if ( ! empty( $ids ) ) {
						foreach ( $ids as $_id ) {
							$notices_discarded[] = RevLoader::esc_attr( trim( $_id ) );
						}

						RevLoader::update_option( 'revslider-notices-dc', $notices_discarded );
					}

					$this->ajax_response_success( RevLoader::__( 'Saved', 'revslider' ) );
					break;
				case 'check_for_updates':
					// track
					$update        = new RevSliderUpdate( RS_REVISION );
					$update->force = true;

					$update->_retrieve_version_info();
					$version = RevLoader::get_option( 'revslider-latest-version', RS_REVISION );

					if ( $version !== false ) {
						$this->ajax_response_data( array( 'version' => $version ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Connection to Update Server Failed', 'revslider' ) );
					}
					break;
				case 'update_module_now':
					$down_url  = RevLoader::get_option( 'revslider-down-package', '' );
					$down_v    = RevLoader::get_option( 'revslider-latest-version', RS_REVISION );
					$down_path = _PS_MODULE_DIR_;
					$newfile   = $down_path . '/revsliderprestashop.zip';

					$ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_URL, $down_url );
					curl_setopt( $ch, CURLOPT_HEADER, false );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
					curl_setopt( $ch, CURLOPT_USERAGENT, $ua );
					curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
					curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
					curl_setopt( $ch, CURLOPT_MAXREDIRS, 20 );
					$result = curl_exec( $ch );
					file_put_contents( $newfile, $result );
					$last = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
					if ( curl_errno( $ch ) ) {
						 $this->ajax_response_error( RevLoader::__( 'Update Failed', 'revslider' ) );
					} else {
						$zip = new \ZipArchive();
						if ( $zip->open( $newfile ) === true ) {
							$zip->extractTo( _PS_MODULE_DIR_ );
							$zip->close();
						}
						RevLoader::update_option( 'revslider-down-package', '' );

						$this->ajax_response_success( RevLoader::__( 'Udated Succesfully', 'revslider' ) );

					}
					curl_close( $ch );
					break;
				case 'get_template_information_short':
					$templates = new RevSliderTemplate();
					$sliders   = $templates->get_tp_template_sliders();

					$this->ajax_response_data( array( 'templates' => $sliders ) );
					break;
				/*
				case 'get_template_slides':
						$slider_id         = $this->get_val($data, 'slider_id');
						$templates         = new RevSliderTemplate();
						$template_slider = $slider->init_by_id($slider_id);
						$slides             = $templates->get_tp_template_slides($template_slider);

						$this->ajax_response_data(array('template_slides' => $slides));
				break;*/
				case 'import_template_slider': // before: import_slider_template_slidersview
					$uid       = $this->get_val( $data, 'uid' );
					$install   = $this->get_val( $data, 'install', true );
					$templates = new RevSliderTemplate();
					$filepath  = $templates->_download_template( $uid );

					if ( $filepath !== false ) {
						$templates->remove_old_template( $uid );
						$slider = new RevSliderSliderImport();
						$return = $slider->import_slider( false, $filepath, $uid, false, true, $install );

						if ( $this->get_val( $return, 'success' ) == true ) {
							$new_id = $this->get_val( $return, 'sliderID' );
							if ( intval( $new_id ) > 0 ) {
								$map       = $this->get_val( $return, 'map', array() );
								$folder_id = $this->get_val( $data, 'folderid', -1 );
								if ( intval( $folder_id ) > 0 ) {
									$folder = new RevSliderFolder();
									$folder->add_slider_to_folder( $new_id, $folder_id, false );
								}

								$new_slider = new RevSliderSlider();
								$new_slider->init_by_id( $new_id );
								$data = $new_slider->get_overview_data();

								$hiddensliderid = $templates->get_slider_id_by_uid( $uid );

								$templates->_delete_template( $uid ); // delete template file

								$this->ajax_response_data(
									array(
										'slider'         => $data,
										'hiddensliderid' => $hiddensliderid,
										'map'            => $map,
										'uid'            => $uid,
									)
								);
							}
						}

						$templates->_delete_template( $uid ); // delete template file

						$error = ( $this->get_val( $return, 'error' ) !== '' ) ? $this->get_val( $return, 'error' ) : RevLoader::__( 'Slider Import Failed', 'revslider' );
						$this->ajax_response_error( $error );
					}
					$this->ajax_response_error( RevLoader::__( 'Template Slider Import Failed', 'revslider' ) );
					break;
				case 'install_template_slider':
					$id     = $this->get_val( $data, 'sliderid' );
					$new_id = $slider->duplicate_slider_by_id( $id, true );
					if ( intval( $new_id ) > 0 ) {
						$new_slider = new RevSliderSlider();
						$new_slider->init_by_id( $new_id );
						$data       = $new_slider->get_overview_data();
						$slide_maps = $slider->get_map();
						$map        = array(
							'slider' => array( 'template_to_duplication' => array( $id => $new_id ) ),
							'slides' => $slide_maps,
						);
						$this->ajax_response_data(
							array(
								'slider'         => $data,
								'hiddensliderid' => $id,
								'map'            => $map,
							)
						);
					}
					$this->ajax_response_error( RevLoader::__( 'Template Slider Installation Failed', 'revslider' ) );
					break;
				case 'install_template_slide':
					$template  = new RevSliderTemplate();
					$slider_id = intval( $this->get_val( $data, 'slider_id' ) );
					$slide_id  = intval( $this->get_val( $data, 'slide_id' ) );

					if ( $slider_id == 0 || $slide_id == 0 ) {
					} else {
						$new_slide_id = $slide->duplicate_slide_by_id( $slide_id, $slider_id );

						if ( $new_slide_id !== false ) {
							$slide->init_by_id( $new_slide_id );
							$_slides[] = array(
								'order'  => $slide->get_order(),
								'params' => $slide->get_params(),
								'layers' => $slide->get_layers(),
								'id'     => $slide->get_id(),
							);

							$this->ajax_response_data( array( 'slides' => $_slides ) );
						}
					}

					$this->ajax_response_error( RevLoader::__( 'Slide duplication failed', 'revslider' ) );
					break;
				case 'import_slider':
					$import = new RevSliderSliderImport();
					$return = $import->import_slider();

					if ( $this->get_val( $return, 'success' ) == true ) {
						$new_id = $this->get_val( $return, 'sliderID' );

						if ( intval( $new_id ) > 0 ) {
							$folder    = new RevSliderFolder();
							$folder_id = $this->get_val( $data, 'folderid', -1 );
							if ( intval( $folder_id ) > 0 ) {
								$folder->add_slider_to_folder( $new_id, $folder_id, false );
							}

							$new_slider = new RevSliderSlider();
							$new_slider->init_by_id( $new_id );
							$data = $new_slider->get_overview_data();

							$this->ajax_response_data(
								array(
									'slider'         => $data,
									'hiddensliderid' => $new_id,
								)
							);
						}
					}

					$error = ( $this->get_val( $return, 'error' ) !== '' ) ? $this->get_val( $return, 'error' ) : RevLoader::__( 'Slider Import Failed', 'revslider' );

					$this->ajax_response_error( $error );
					break;
				case 'add_to_media_library':
					$this->ajax_response_data( array( 'media_library' => 'No need to this media.' ) );

					// $return = $this->import_upload_media();
					// if($this->get_val($return, 'error', false) !== false){
					// $this->ajax_response_error($this->get_val($return, 'error', false));
					// }else{
					// $this->ajax_response_data($return);
					// }
					break;
				case 'adjust_modal_ids':
					$map = $this->get_val( $data, 'map', array() );

					if ( ! empty( $map ) ) {
						$slider_map = array();
						$slider_ids = $this->get_val( $map, 'slider_map', array() );
						$slides_ids = $this->get_val( $map, 'slides_map', array() );

						$ztt = $this->get_val( $slider_ids, 'zip_to_template', array() );
						$ztd = $this->get_val( $slider_ids, 'zip_to_duplication', array() );
						$ttd = $this->get_val( $slider_ids, 'template_to_duplication', array() );
						$s_a = array();
						if ( ! empty( $slides_ids ) ) {
							foreach ( $slides_ids as $k => $v ) {
								if ( is_array( $v ) ) {
									foreach ( $v as $vk => $vv ) {
										   $s_a[ $vk ] = $vv;
									}
									unset( $slides_ids[ $k ] );
								}
							}
						}

						if ( ! empty( $ztt ) ) {
							foreach ( $ztt as $old => $new ) {
								 $slider = new RevSliderSliderImport();
								 $slider->init_by_id( $new );

								 $slider->update_modal_ids( $ztt, $slides_ids );
							}
						}

						if ( ! empty( $ztd ) ) {
							foreach ( $ztd as $old => $new ) {
								$slider = new RevSliderSliderImport();
								$slider->init_by_id( $new );
								$slider->update_modal_ids( $ztd, $s_a );
							}
						}

						if ( ! empty( $ttd ) ) {
							foreach ( $ttd as $old => $new ) {
								$slider = new RevSliderSliderImport();
								$slider->init_by_id( $new );
								$slider->update_modal_ids( $ttd, $slides_ids );
							}
						}

						$this->ajax_response_data( array() );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Slider Map Empty', 'revslider' ) );
					}
					break;
				case 'adjust_js_css_ids':
					$map = $this->get_val( $data, 'map', array() );

					if ( ! empty( $map ) ) {
						$slider_map = array();
						foreach ( $map as $m ) {
							$slider_ids = $this->get_val( $m, 'slider_map', array() );
							if ( ! empty( $slider_ids ) ) {
								foreach ( $slider_ids as $old => $new ) {
									$slider = new RevSliderSliderImport();
									$slider->init_by_id( $new );

									$slider_map[] = $slider;
								}
							}
						}

						if ( ! empty( $slider_map ) ) {
							foreach ( $slider_map as $slider ) {
								foreach ( $map as $m ) {
									$slider_ids = $this->get_val( $m, 'slider_map', array() );
									$slide_ids  = $this->get_val( $m, 'slide_map', array() );
									if ( ! empty( $slider_ids ) ) {
										foreach ( $slider_ids as $old => $new ) {
											$slider->update_css_and_javascript_ids( $old, $new, $slide_ids );
										}
									}
								}
							}
						}
					}
					break;
				case 'export_slider':
					$export = new RevSliderSliderExport();
					$id     = intval( $this->get_request_var( 'id' ) );
					$return = $export->export_slider( $id );
					// will never be called if all is good
					$this->ajax_response_data( $return );
					break;
				case 'export_slider_html':
					$export = new RevSliderSliderExportHtml();
					$id     = intval( $this->get_request_var( 'id' ) );
					$return = $export->export_slider_html( $id );

					// will never be called if all is good
					$this->ajax_response_data( $return );
					break;
				case 'delete_slider':
					$id = $this->get_val( $data, 'id' );
					$slider->init_by_id( $id );
					$result = $slider->delete_slider();
					$this->ajax_response_success( RevLoader::__( 'Slider Deleted', 'revslider' ) );
					break;
				case 'duplicate_slider':
					$id     = $this->get_val( $data, 'id' );
					$new_id = $slider->duplicate_slider_by_id( $id );
					if ( intval( $new_id ) > 0 ) {
						$new_slider = new RevSliderSlider();
						$new_slider->init_by_id( $new_id );
						$data = $new_slider->get_overview_data();
						$this->ajax_response_data( array( 'slider' => $data ) );
					}

					$this->ajax_response_error( RevLoader::__( 'Duplication Failed', 'revslider' ) );
					break;
				case 'save_slide':
					$slide_id  = $this->get_val( $data, 'slide_id' );
					$slider_id = $this->get_val( $data, 'slider_id' );
					$return    = $slide->save_slide( $slide_id, $data, $slider_id );

					if ( $return ) {
						$this->ajax_response_success( RevLoader::__( 'Slide Saved', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Slide not found', 'revslider' ) );
					}
					break;
				case 'save_slide_advanced':
					$slide_id  = $this->get_val( $data, 'slide_id' );
					$slider_id = $this->get_val( $data, 'slider_id' );
					$return    = $slide->save_slide_advanced( $slide_id, $data, $slider_id );

					if ( $return ) {
						$this->ajax_response_success( RevLoader::__( 'Slide Saved', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Slide not found', 'revslider' ) );
					}
					break;
				case 'save_slider':
					$slider_id      = $this->get_val( $data, 'slider_id' );
					$slider_params      = $this->get_val( $data, 'params' );
					$slider_params      = json_decode($slider_params, true);
					$mod_obj = Module::getInstanceByName('revsliderprestashop');
					if(isset($slider_params['layout']['displayhook']) && $slider_params['layout']['displayhook'] != ''){
						$mod_obj->registerHook($slider_params['layout']['displayhook']);
					}
					$slide_ids      = $this->get_val( $data, 'slide_ids', array() );
					$return         = $slider->save_slider( $slider_id, $data );
					$missing_slides = array();
					$delete_slides  = array();

					if ( $return !== false ) {
						if ( ! empty( $slide_ids ) ) {
							$slides = $slider->get_slides( false, true );

							// get the missing Slides (if any at all)
							foreach ( $slide_ids as $slide_id ) {
								$found = false;
								foreach ( $slides as $_slide ) {
									if ( $_slide->get_id() !== $slide_id ) {
										   continue;
									}

									$found = true;
								}
								if ( ! $found ) {
									$missing_slides[] = $slide_id;
								}
							}

							// get the Slides that are no longer needed and delete them
							foreach ( $slides as $key => $_slide ) {
								$id = $_slide->get_id();
								if ( ! in_array( $id, $slide_ids ) ) {
									$delete_slides[] = $id;
									unset( $slides[ $key ] ); // remove none existing slides for further ordering process
								}
							}

							if ( ! empty( $delete_slides ) ) {
								foreach ( $delete_slides as $delete_slide ) {
									$slide->delete_slide_by_id( $delete_slide );
								}
							}

							// change the order of slides
							foreach ( $slide_ids as $order => $id ) {
								$new_order = $order + 1;
								$_slide->change_slide_order( $id, $new_order );
							}
						}

						$this->ajax_response_data(
							array(
								'missing' => $missing_slides,
								'delete'  => $delete_slides,
							)
						);
					} else {
						$this->ajax_response_error( RevLoader::__( 'Slider not found', 'revslider' ) );
					}
					break;
				case 'delete_slide':
					$slide_id = intval( $this->get_val( $data, 'slide_id', '' ) );
					$return   = ( $slide_id > 0 ) ? $slide->delete_slide_by_id( $slide_id ) : false;

					if ( $return !== false ) {
						$this->ajax_response_success( RevLoader::__( 'Slide deleted', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Slide could not be deleted', 'revslider' ) );
					}
					break;
				case 'duplicate_slide':
					$slide_id  = intval( $this->get_val( $data, 'slide_id', '' ) );
					$slider_id = intval( $this->get_val( $data, 'slider_id', '' ) );

					$new_slide_id = $slide->duplicate_slide_by_id( $slide_id, $slider_id );
					if ( $new_slide_id !== false ) {
						$slide->init_by_id( $new_slide_id );
						$_slide = $slide->get_overview_data();

						$this->ajax_response_data( array( 'slide' => $_slide ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Slide could not duplicated', 'revslider' ) );
					}
					break;
				case 'update_slide_order':
					$slide_ids = $this->get_val( $data, 'slide_ids', array() );

					// change the order of slides
					if ( ! empty( $slide_ids ) ) {
						foreach ( $slide_ids as $order => $id ) {
							$new_order = $order + 1;
							$slide->change_slide_order( $id, $new_order );
						}

						$this->ajax_response_success( RevLoader::__( 'Slide order changed', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Slide order could not be changed', 'revslider' ) );
					}
					break;
				case 'getSliderImage':
					// Available Sliders
					$slider     = new RevSliderSlider();
					$arrSliders = $slider->get_sliders();
					$post60     = ( version_compare( $slider->get_setting( 'version', '1.0.0' ), '6.0.0', '<' ) ) ? false : true;
					// Given Alias
					$alias  = $this->get_val( $data, 'alias' );
					$return = array_search( $alias, $arrSliders );

					foreach ( $arrSliders as $sliderony ) {
						if ( $sliderony->get_alias() == $alias ) {
							$slider_found = $sliderony->get_overview_data();
							$return       = $slider_found['bg']['src'];
							$title        = $slider_found['title'];
						}
					}

					if ( ! $return ) {
						$return = '';
					}

					if ( ! empty( $title ) ) {
						$this->ajax_response_data(
							array(
								'image' => $return,
								'title' => $title,
							)
						);
					} else {
						$this->ajax_response_error( RevLoader::__( 'The Slider with the alias "' . $alias . '" is not available!', 'revslider' ) );
					}

					break;
				case 'getSliderSizeLayout':
					// Available Sliders
					$slider     = new RevSliderSlider();
					$arrSliders = $slider->get_sliders();
					$post60     = ( version_compare( $slider->get_setting( 'version', '1.0.0' ), '6.0.0', '<' ) ) ? false : true;
					// Given Alias
					$alias = $this->get_val( $data, 'alias' );

					$return = array_search( $alias, $arrSliders );

					foreach ( $arrSliders as $sliderony ) {
						if ( $sliderony->get_alias() == $alias ) {
							$slider_found = $sliderony->get_overview_data();
							$return       = $slider_found['size'];
							$title        = $slider_found['title'];
						}
					}

					$this->ajax_response_data(
						array(
							'layout' => $return,
							'title'  => $title,
						)
					);
					break;
				case 'get_list_of':
					$type = $this->get_val( $data, 'type' );
					switch ( $type ) {
						case 'sliders':
							$slider     = new RevSliderSlider();
							$arrSliders = $slider->get_sliders();
							$return     = array();
							foreach ( $arrSliders as $sliderony ) {
								$return[ $sliderony->get_id() ] = array(
									'slug'    => $sliderony->get_alias(),
									'title'   => $sliderony->get_title(),
									'type'    => $sliderony->get_type(),
									'subtype' => $sliderony->get_param( array( 'source', 'post', 'subType' ), false ),
								);
							}
							$this->ajax_response_data( array( 'sliders' => $return ) );
							break;
						// commented this active will fire when maintanace setting enable
						case 'pages':
							// $pages = get_pages(array());
							$return = array();
							// foreach($pages as $page){
							// if(!$page->post_password){
							// $return[$page->ID] = array('slug' => $page->post_name, 'title' => $page->post_title);
							// }
							// }
							$this->ajax_response_data( array( 'pages' => $return ) );
							break;
						case 'posttypes':
							$this->ajax_response_data( array( 'posttypes' => 'No need to posttypes' ) );
							// $args = array(
							// 'public' => true,
							// '_builtin' => false,
							// );
							// $output = 'objects';
							// $operator = 'and';
							// $post_types = get_post_types($args, $output, $operator);
							// $return['post'] = array('slug' => 'post', 'title' => RevLoader::__('Posts', 'revslider'));

							// foreach($post_types as $post_type){
							// $return[$post_type->rewrite['slug']] = array('slug' => $post_type->rewrite['slug'], 'title' => $post_type->labels->name);
							// if(!in_array($post_type->name, array('post', 'page', 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'custom_changeset', 'user_request'))){
							// $taxonomy_objects = get_object_taxonomies($post_type->name, 'objects');
							// if(!empty($taxonomy_objects)){
							// $return[$post_type->rewrite['slug']]['tax'] = array();
							// foreach($taxonomy_objects as $name => $tax){
							// $return[$post_type->rewrite['slug']]['tax'][$name] = $tax->label;
							// }
							// }
							// }
							// }

							// $this->ajax_response_data(array('posttypes' => $return));
							break;
					}
					break;
				case 'load_wordpress_object':
					$id   = $this->get_val( $data, 'id', 0 );
					$type = $this->get_val( $data, 'type', 'full' );
					// track
					// $file = wp_get_attachment_image_src($id, $type);
					$file = false;
					if ( $file !== false ) {
						$this->ajax_response_data( array( 'url' => $this->get_val( $file, 0 ) ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'File could not be loaded', 'revslider' ) );
					}
					break;
				case 'get_global_settings':
					$this->ajax_response_data( array( 'global_settings' => $this->global_settings ) );
					break;
				case 'update_global_settings':
					$global = $this->get_val( $data, 'global_settings', array() );
					if ( ! empty( $global ) ) {
						$return = $this->set_global_settings( $global );
						if ( $return === true ) {
							$this->ajax_response_success( RevLoader::__( 'Global Settings saved/updated', 'revslider' ) );
						} else {
							$this->ajax_response_error( RevLoader::__( 'Global Settings not saved/updated', 'revslider' ) );
						}
					} else {
						$this->ajax_response_error( RevLoader::__( 'Global Settings not saved/updated', 'revslider' ) );
					}
					break;
				case 'create_navigation_preset':
					$nav    = new RevSliderNavigation();
					$return = $nav->add_preset( $data );

					if ( $return === true ) {
						$this->ajax_response_success( RevLoader::__( 'Navigation preset saved/updated', 'revslider' ), array( 'navs' => $nav->get_all_navigations_builder() ) );
					} else {
						if ( $return === false ) {
							$return = RevLoader::__( 'Preset could not be saved/values are the same', 'revslider' );
						}

						$this->ajax_response_error( $return );
					}
					break;
				case 'delete_navigation_preset':
					$nav    = new RevSliderNavigation();
					$return = $nav->delete_preset( $data );

					if ( $return === true ) {
						$this->ajax_response_success( RevLoader::__( 'Navigation preset deleted', 'revslider' ), array( 'navs' => $nav->get_all_navigations_builder() ) );
					} else {
						if ( $return === false ) {
							$return = RevLoader::__( 'Preset not found', 'revslider' );
						}

						$this->ajax_response_error( $return );
					}
					break;
				case 'save_navigation': // also deletes if requested
					$_nav        = new RevSliderNavigation();
					$navs        = (array) $this->get_val( $data, 'navs', array() );
					$delete_navs = (array) $this->get_val( $data, 'delete', array() );

					if ( ! empty( $delete_navs ) ) {
						foreach ( $delete_navs as $dnav ) {
							$_nav->delete_navigation( $dnav );
						}
					}

					if ( ! empty( $navs ) ) {
						$_nav->create_update_full_navigation( $navs );
					}

					$navigations = $_nav->get_all_navigations_builder();

					$this->ajax_response_data( array( 'navs' => $navigations ) );
					break;
				case 'delete_animation':
					$animation_id = $this->get_val( $data, 'id' );
					$admin        = new RevSliderFunctionsAdmin();
					$return       = $admin->delete_animation( $animation_id );
					if ( $return ) {
						$this->ajax_response_success( RevLoader::__( 'Animation deleted', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Deletion failed', 'revslider' ) );
					}
					break;
				case 'save_animation':
					$admin     = new RevSliderFunctionsAdmin();
					$id        = $this->get_val( $data, 'id', false );
					$type      = $this->get_val( $data, 'type', 'in' );
					$animation = $this->get_val( $data, 'obj' );

					if ( $id !== false ) {
						$return = $admin->update_animation( $id, $animation, $type );
					} else {
						$return = $admin->insert_animation( $animation, $type );
					}

					if ( intval( $return ) > 0 ) {
						$this->ajax_response_data( array( 'id' => $return ) );
					} elseif ( $return === true ) {
						$this->ajax_response_success( RevLoader::__( 'Animation saved', 'revslider' ) );
					} else {
						if ( $return == false ) {
							$this->ajax_response_error( RevLoader::__( 'Animation could not be saved', 'revslider' ) );
						}
						$this->ajax_response_error( $return );
					}
					break;
				case 'get_slides_by_slider_id':
					$sid     = intval( $this->get_val( $data, 'id' ) );
					$slides  = array();
					$_slides = $slide->get_slides_by_slider_id( $sid );

					if ( ! empty( $_slides ) ) {
						foreach ( $_slides as $slide ) {
							$slides[] = $slide->get_overview_data();
						}
					}

					$this->ajax_response_data( array( 'slides' => $slides ) );
					break;
				case 'get_full_slider_object':
					$slide_id     = $this->get_val( $data, 'id' );
					$slide_id     = RevSliderFunctions::esc_attr_deep( $slide_id );
					$slider_alias = $this->get_val( $data, 'alias', '' );
					$slider_alias = RevSliderFunctions::esc_attr_deep( $slider_alias );

					if ( $slider_alias !== '' ) {
						$slider->init_by_alias( $slider_alias );
						$slider_id = $slider->get_id();
					} else {
						if ( strpos( $slide_id, 'slider-' ) !== false ) {
							$slider_id = str_replace( 'slider-', '', $slide_id );
						} else {
							$slide->init_by_id( $slide_id );

							$slider_id = $slide->get_slider_id();
							if ( intval( $slider_id ) == 0 ) {
								 $this->ajax_response_error( RevLoader::__( 'Slider could not be loaded', 'revslider' ) );
							}
						}

						$slider->init_by_id( $slider_id );
					}
					if ( $slider->inited === false ) {
						$this->ajax_response_error( RevLoader::__( 'Slider could not be loaded', 'revslider' ) );
					}

					// create static Slide if the Slider not yet has one
					$static_slide_id = $slide->get_static_slide_id( $slider_id );
					$static_slide_id = ( intval( $static_slide_id ) === 0 ) ? $slide->create_slide( $slider_id, '', true ) : $static_slide_id;

					$static_slide = false;
					if ( intval( $static_slide_id ) > 0 ) {
						$static_slide = new RevSliderSlide();
						$static_slide->init_by_static_id( $static_slide_id );
					}

					$slides        = $slider->get_slides( false, true );
					$_slides       = array();
					$_static_slide = array();

					if ( ! empty( $slides ) ) {
						foreach ( $slides as $s ) {
							$_slides[] = array(
								'order'  => $s->get_order(),
								'params' => $s->get_params(),
								'layers' => $s->get_layers(),
								'id'     => $s->get_id(),
							);
						}
					}

					if ( ! empty( $static_slide ) ) {
						$_static_slide = array(
							'params' => $static_slide->get_params(),
							'layers' => $static_slide->get_layers(),
							'id'     => $static_slide->get_id(),
						);
					}

					$obj = array(
						'id'              => $slider_id,
						'alias'           => $slider->get_alias(),
						'title'           => $slider->get_title(),
						'slider_params'   => $slider->get_params(),
						'slider_settings' => $slider->get_settings(),
						'slides'          => $_slides,
						'static_slide'    => $_static_slide,
					);

					$this->ajax_response_data( $obj );
					break;
				case 'load_builder':
					ob_start();
					include_once RS_PLUGIN_PATH . 'admin/views/builder.php';
					$builder = ob_get_contents();
					ob_clean();
					ob_end_clean();

					$this->ajax_response_data( $builder );
					break;
				case 'create_slider_folder':
					$folder = new RevSliderFolder();
					$title  = $this->get_val( $data, 'title', RevLoader::__( 'New Folder', 'revslider' ) );
					$parent = $this->get_val( $data, 'parentFolder', 0 );
					$new    = $folder->create_folder( $title, $parent );

					if ( $new !== false ) {
						$overview_data = $new->get_overview_data();
						$this->ajax_response_data( array( 'folder' => $overview_data ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Folder Creation Failed', 'revslider' ) );
					}
					break;
				case 'delete_slider_folder':
					$id     = $this->get_val( $data, 'id' );
					$folder = new RevSliderFolder();
					$is     = $folder->init_folder_by_id( $id );
					if ( $is === true ) {
						$folder->delete_slider();
						$this->ajax_response_success( RevLoader::__( 'Folder Deleted', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Folder Deletion Failed', 'revslider' ) );
					}
					break;
				case 'update_slider_tags':
					$id   = $this->get_val( $data, 'id' );
					$tags = $this->get_val( $data, 'tags' );

					$return = $slider->update_slider_tags( $id, $tags );
					if ( $return == true ) {
						$this->ajax_response_success( RevLoader::__( 'Tags Updated', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Failed to Update Tags', 'revslider' ) );
					}
					break;
				case 'save_slider_folder':
					$folder    = new RevSliderFolder();
					$children  = $this->get_val( $data, 'children' );
					$folder_id = $this->get_val( $data, 'id' );

					$return = $folder->add_slider_to_folder( $children, $folder_id );

					if ( $return == true ) {
						$this->ajax_response_success( RevLoader::__( 'Slider Moved to Folder', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Failed to Move Slider Into Folder', 'revslider' ) );
					}
					break;
				case 'update_slider_name':
				case 'update_folder_name':
					$slider_id = $this->get_val( $data, 'id' );
					$new_title = $this->get_val( $data, 'title' );

					$slider->init_by_id( $slider_id, $new_title );
					$return = $slider->update_title( $new_title );
					if ( $return != false ) {
						$this->ajax_response_data( array( 'title' => $return ), RevLoader::__( 'Title updated', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Failed to update Title', 'revslider' ) );
					}
					break;
				case 'preview_slider':
					$slider_id   = $this->get_val( $data, 'id' );
					$slider_data = $this->get_val( $data, 'data' );
					$title       = RevLoader::__( 'Slider Revolution Preview', 'revslider' );

					if ( intval( $slider_id ) > 0 && empty( $slider_data ) ) {
						$slider->init_by_id( $slider_id );

						// check if an update is needed
						if ( version_compare( $slider->get_param( array( 'settings', 'version' ) ), RevLoader::get_option( 'revslider_update_version', '6.0.0' ), '<' ) ) {
							$upd = new RevSliderPluginUpdate();
							$upd->upgrade_slider_to_latest( $slider );
							$slider->init_by_id( $slider_id );
						}

						$output = new RevSliderOutput();
						ob_start();
						$slider  = $output->add_slider_to_stage( $slider_id );
						$content = ob_get_contents();
						ob_clean();
						ob_end_clean();
						// track
						// $content = '[rev_slider alias="' .  RevLoader::esc_attr($slider->get_alias()) . '"]';
					} elseif ( ! empty( $slider_data ) ) {
						$_slides      = array();
						$_static      = array();
						$slides       = array();
						$static_slide = array();

						$_slider = array(
							'id'       => $slider_id,
							'title'    => 'Preview',
							'alias'    => 'preview',
							'settings' => json_encode( array( 'version' => RS_REVISION ) ),
							// track
							// 'params'  => stripslashes($this->get_val($slider_data, 'slider'))
							'params'   => $this->get_val( $slider_data, 'slider' ),
						);
						$slide_order = json_decode( stripslashes( $this->get_val( $slider_data, array( 'slide_order' ) ) ), true );

						foreach ( $slider_data as $sk => $sd ) {
							if ( in_array( $sk, array( 'slider', 'slide_order' ), true ) ) {
								 continue;
							}

							if ( strpos( $sk, 'static_' ) !== false ) {
								$_static = array(
									'params' => stripslashes( $this->get_val( $sd, 'params' ) ),
									'layers' => stripslashes( $this->get_val( $sd, 'layers' ) ),
								);
							} else {
								$_slides[ $sk ] = array(
									'id'          => $sk,
									'slider_id'   => $slider_id,
									'slide_order' => array_search( $sk, $slide_order ),
									'params'      => stripslashes( $this->get_val( $sd, 'params' ) ),
									// track
									// 'layers'  => stripslashes($this->get_val($sd, 'layers')),
									'layers'      => $this->get_val( $sd, 'layers' ),
									'settings'    => array( 'version' => RS_REVISION ),
								);
							}
						}
						$output = new RevSliderOutput();
						$slider->init_by_data( $_slider );
						if ( $slider->is_stream() || $slider->is_posts() ) {
							$slides = $slider->get_slides_for_output();
						} else {
							if ( ! empty( $_slides ) ) {
								// reorder slides
								usort( $_slides, array( $this, 'sort_by_slide_order' ) );
								foreach ( $_slides as $_slide ) {
									$slide = new RevSliderSlide();
									$slide->init_by_data( $_slide );
									if ( $slide->get_param( array( 'publish', 'state' ), 'published' ) === 'unpublished' ) {
										continue;
									}
									 $slides[] = $slide;
								}
							}
						}
						if ( ! empty( $_static ) ) {
							$slide = new RevSliderSlide();
							$slide->init_by_data( $_static );
							$static_slide = $slide;
						}
						$output->set_slider( $slider );
						$output->set_current_slides( $slides );
						$output->set_static_slide( $static_slide );
						$output->set_preview_mode( true );

						ob_start();
						$slider  = $output->add_slider_to_stage( $slider_id );
						$content = ob_get_contents();
						ob_clean();
						ob_end_clean();
					}

					// get dimensions of slider
					$size = array(
						'width'  => $slider->get_param( array( 'size', 'width' ), array() ),
						'height' => $slider->get_param( array( 'size', 'height' ), array() ),
						'custom' => $slider->get_param( array( 'size', 'custom' ), array() ),
					);

					if ( empty( $size['width'] ) ) {
						$size['width'] = array(
							'd' => $this->get_val( $this->global_settings, array( 'size', 'desktop' ), '1240' ),
							'n' => $this->get_val( $this->global_settings, array( 'size', 'notebook' ), '1024' ),
							't' => $this->get_val( $this->global_settings, array( 'size', 'tablet' ), '778' ),
							'm' => $this->get_val( $this->global_settings, array( 'size', 'mobile' ), '480' ),
						);
					}
					if ( empty( $size['height'] ) ) {
						$size['height'] = array(
							'd' => '868',
							'n' => '768',
							't' => '960',
							'm' => '720',
						);
					}

					global $revslider_is_preview_mode;
					$revslider_is_preview_mode = true;
					include_once RS_PLUGIN_PATH . 'public/includes/functions-public.class.php';
					$rev_slider_front = new RevSliderFront();

					// $post = $this->create_fake_post($content, $title);

					ob_start();
					include RS_PLUGIN_PATH . 'public/views/revslider-page-template.php';
					$html = ob_get_contents();
					ob_clean();
					ob_end_clean();

					$this->ajax_response_data(
						array(
							'html'       => $html,
							'size'       => $size,
							'layouttype' => $slider->get_param(
								'layouttype',
								'fullwidth'
							),
						)
					);
					exit;
				break;
				case 'subscribe_to_newsletter':
					$email = $this->get_val( $data, 'email' );
					if ( ! empty( $email ) ) {
						$return = ThemePunch_Newsletter::subscribe( $email );

						if ( $return !== false ) {
							if ( ! isset( $return['status'] ) || $return['status'] === 'error' ) {
								$error = $this->get_val( $return, 'message', RevLoader::__( 'Invalid Email', 'revslider' ) );
								$this->ajax_response_error( $error );
							} else {
								$this->ajax_response_success( RevLoader::__( 'Success! Please check your E-Mails to finish the subscription', 'revslider' ), $return );
							}
						}
						$this->ajax_response_error( RevLoader::__( 'Invalid Email/Could not connect to the Newsletter server', 'revslider' ) );
					}

					$this->ajax_response_error( RevLoader::__( 'No Email given', 'revslider' ) );
					break;
				case 'check_system':
					// recheck the connection to themepunch server
					$update        = new RevSliderUpdate( RS_REVISION );
					$update->force = true;
					$update->_retrieve_version_info();

					$fun    = new RevSliderFunctionsAdmin();
					$system = $fun->get_system_requirements();

					$this->ajax_response_data( array( 'system' => $system ) );
					break;
				case 'load_module':
					$module              = $this->get_val( $data, 'module', array( 'all' ) );
					$module_uid          = $this->get_val( $data, 'module_uid', false );
					$module_slider_id    = $this->get_val( $data, 'module_id', false );
					$refresh_from_server = $this->get_val( $data, 'refresh_from_server', false );
					$get_static_slide    = $this->_truefalse( $this->get_val( $data, 'static', false ) );



					if ( $module_uid === false ) {
						$module_uid = $module_slider_id;
					}
					RevLoader::update_template_json('rs-templates');
					RevLoader::update_library_json('rs-library');
					$admin   = new RevSliderFunctionsAdmin();
					$modules = $admin->get_full_library( $module, $module_uid, $refresh_from_server, $get_static_slide );

					$this->ajax_response_data( array( 'modules' => $modules ) );
					break;
				case 'set_favorite':
					$do   = $this->get_val( $data, 'do', 'add' );
					$type = $this->get_val( $data, 'type', 'slider' );
					$id   = RevLoader::esc_attr( $this->get_val( $data, 'id' ) );

					$favorite = new RevSliderFavorite();
					$favorite->set_favorite( $do, $type, $id );

					$this->ajax_response_success( RevLoader::__( 'Favorite Changed', 'revslider' ) );
					break;
				case 'load_library_object':
					$library = new RevSliderObjectLibrary();

					$cover = false;
					$id    = $this->get_val( $data, 'id' );
					$type  = $this->get_val( $data, 'type' );
					if ( $type == 'thumb' ) {
						$thumb = $library->_get_object_thumb( $id, 'thumb' );
					} elseif ( $type == 'video' ) {
						$thumb = $library->_get_object_thumb( $id, 'video_full', true );
						$cover = $library->_get_object_thumb( $id, 'cover', true );
					} elseif ( $type == 'layers' ) {
						$thumb = $library->_get_object_layers( $id );
					} else {
						$thumb = $library->_get_object_thumb( $id, 'orig', true );
						if ( isset( $thumb['error'] ) && $thumb['error'] === false ) {
							$orig = $this->get_val( $thumb, 'url', false );
							$url  = $library->get_correct_size_url( $id, $type );
							if ( $url !== '' ) {
								$thumb['url'] = $url;
							}
						}
					}

					if ( isset( $thumb['error'] ) && $thumb['error'] !== false ) {
						$this->ajax_response_error( RevLoader::__( 'Object could not be loaded', 'revslider' ) );
					} else {
						if ( $type == 'layers' ) {
							$return = array( 'layers' => $this->get_val( $thumb, 'data' ) );
						} else {
							$return = array( 'url' => $this->get_val( $thumb, 'url' ) );
						}

						if ( $cover !== false ) {
							if ( isset( $cover['error'] ) && $cover['error'] !== false ) {
								$this->ajax_response_error( RevLoader::__( 'Video cover could not be loaded', 'revslider' ) );
							}

							$return['cover'] = $this->get_val( $cover, 'url' );
						}

						$this->ajax_response_data( $return );
					}
					break;
				case 'create_slide':
					$slider_id = $this->get_val( $data, 'slider_id', false );
					$amount    = $this->get_val( $data, 'amount', 1 );
					$amount    = intval( $amount );
					$slide_ids = array();

					if ( intval( $slider_id ) > 0 && ( $amount > 0 && $amount < 50 ) ) {
						for ( $i = 0; $i < $amount; $i++ ) {
							$slide_ids[] = $slide->create_slide( $slider_id );
						}
					}

					if ( ! empty( $slide_ids ) ) {
						$this->ajax_response_data( array( 'slide_id' => $slide_ids ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Could not create Slide', 'revslider' ) );
					}
					break;
				case 'create_slider':
					/**
					 * 1. create a blank Slider
					 * 2. create a blank Slide
					 * 3. create a blank Static Slide
					 */

					$slide_id  = false;
					$slider_id = $slider->create_blank_slider();
					if ( $slider_id !== false ) {
						$slide_id = $slide->create_slide( $slider_id ); // normal slide
						$slide->create_slide( $slider_id, '', true ); // static slide
					}

					if ( $slide_id !== false ) {
						$this->ajax_response_data(
							array(
								'slide_id'  => $slide_id,
								'slider_id' => $slider_id,
							)
						);
					} else {
						$this->ajax_response_error( RevLoader::__( 'Could not create Slider', 'revslider' ) );
					}
					break;
				case 'get_addon_list':
					$addon  = new RevSliderAddons();
					$addons = $addon->get_addon_list();

					RevLoader::update_option( 'rs-addons-counter', 0 ); // set the counter back to 0

					$this->ajax_response_data( array( 'addons' => $addons ) );
					break;
				case 'get_layers_by_slide':
					$slide_id = $this->get_val( $data, 'slide_id' );

					$slide->init_by_id( $slide_id );
					$layers = $slide->get_layers();

					$this->ajax_response_data( array( 'layers' => $layers ) );
					break;
				case 'activate_addon':
					$handle = $this->get_val( $data, 'addon' );
					$update = $this->get_val( $data, 'update', false );
					$addon  = new RevSliderAddons();

					$return = $addon->install_addon( $handle, $update );

					if ( $return === true ) {
						// return needed files of the plugin somehow
						$data = array();
						$data = RevLoader::apply_filters( 'revslider_activate_addon', $data, $handle );

						$this->ajax_response_data( array( $handle => $data ) );
					} else {
						$error = ( $return === false ) ? RevLoader::__( 'AddOn could not be activated', 'revslider' ) : $return;

						$this->ajax_response_error( $error );
					}
					break;
				case 'deactivate_addon':
					$handle = $this->get_val( $data, 'addon' );
					$addon  = new RevSliderAddons();
					$return = $addon->deactivate_addon( $handle );

					if ( $return ) {
						// return needed files of the plugin somehow
						$this->ajax_response_success( RevLoader::__( 'AddOn deactivated', 'revslider' ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'AddOn could not be deactivated', 'revslider' ) );
					}
					break;
				case 'create_draft_page':
					$this->ajax_response_data( array( 'create_draft_page' => 'No need to draft page' ) );
					// $admin        = new RevSliderFunctionsAdmin();
					// $response    = array('open' => false, 'edit' => false);
					// $slider_ids = $this->get_val($data, 'slider_ids');
					// $modals        = $this->get_val($data, 'modals', array());
					// $additions    = $this->get_val($data, 'additions', array());
					// $page_id    = $admin->create_slider_page($slider_ids, $modals, $additions);

					// if($page_id > 0){
					// $response['open'] = get_permalink($page_id);
					// $response['edit'] = get_edit_post_link($page_id);
					// }
					// $this->ajax_response_data($response);
					break;
				case 'generate_attachment_metadata':
					$this->generate_attachment_metadata();
					$this->ajax_response_success( '' );
					break;
				case 'export_layer_group': // developer function only :)
					$title   = $this->get_val( $data, 'title', $this->get_request_var( 'title' ) );
					$videoid = intval( $this->get_val( $data, 'videoid', $this->get_request_var( 'videoid' ) ) );
					$thumbid = intval( $this->get_val( $data, 'thumbid', $this->get_request_var( 'thumbid' ) ) );
					$layers  = $this->get_val( $data, 'layers', $this->get_request_var( 'layers' ) );

					$export = new RevSliderSliderExport( $title );
					$url    = $export->export_layer_group( $videoid, $thumbid, $layers );

					$this->ajax_response_data( array( 'url' => $url ) );
					break;
				case 'silent_slider_update':
					$upd    = new RevSliderPluginUpdate();
					$return = $upd->upgrade_next_slider();

					$this->ajax_response_data( $return );
					break;
				case 'load_wordpress_image':
					$id   = $this->get_val( $data, 'id', 0 );
					$type = $this->get_val( $data, 'type', 'orig' );

					// $img = wp_get_attachment_image_url($id, $type);
					$img = '';
					if ( empty( $img ) ) {
						$this->ajax_response_error( RevLoader::__( 'Image could not be loaded', 'revslider' ) );
					}

					$this->ajax_response_data( array( 'url' => $img ) );
					break;
				case 'load_library_image':
					$images   = ( ! is_array( $data ) ) ? (array) $data : $data;
					$images   = RevSliderFunctions::esc_attr_deep( $images );
					$images   = self::esc_js_deep( $images );
					$img_data = array();

					if ( ! empty( $images ) ) {
						$templates = new RevSliderTemplate();
						$obj       = new RevSliderObjectLibrary();

						foreach ( $images as $image ) {
							$type = $this->get_val( $image, 'librarytype' );
							$img  = $this->get_val( $image, 'id' );
							$ind  = $this->get_val( $image, 'ind' );
							$mt   = $this->get_val( $image, 'mediatype' );
							switch ( $type ) {
								case 'moduletemplates':
								case 'moduletemplateslides':
									$img        = $templates->_check_file_path( $img, true );
									$img_data[] = array(
										'ind'       => $ind,
										'url'       => $img,
										'mediatype' => $mt,
									);
									break;
								case 'image':
								case 'images':
								case 'layers':
								case 'objects':
									$get = ( $mt === 'video' ) ? 'video_thumb' : 'thumb';
									$img = $obj->_get_object_thumb( $img, $get, true );
									if ( $this->get_val( $img, 'error', false ) === false ) {
										$img_data[] = array(
											'ind'       => $ind,
											'url'       => $this->get_val( $img, 'url' ),
											'mediatype' => $mt,
										);
									}
									break;
								case 'videos':
									$get = ( $mt === 'img' ) ? 'video' : 'video_thumb';
									$img = $obj->_get_object_thumb( $img, $get, true );
									if ( $this->get_val( $img, 'error', false ) === false ) {
										$img_data[] = array(
											'ind'       => $ind,
											'url'       => $this->get_val( $img, 'url' ),
											'mediatype' => $mt,
										);
									}
									break;
							}
						}
					}

					$this->ajax_response_data( array( 'data' => $img_data ) );
					break;
				case 'get_help_directory':
					include_once RS_PLUGIN_PATH . 'admin/includes/help.class.php';

					if ( class_exists( 'RevSliderHelp' ) ) {
						$help_data = RevSliderHelp::getIndex();
						$this->ajax_response_data( array( 'data' => $help_data ) );
					} else {
						$return = '';
					}
					break;
				case 'get_tooltips':
					include_once RS_PLUGIN_PATH . 'admin/includes/tooltips.class.php';

					if ( class_exists( 'RevSliderTooltips' ) ) {
						$tooltips = RevSliderTooltips::getTooltips();
						$this->ajax_response_data( array( 'data' => $tooltips ) );
					} else {
						$return = '';
					}
					break;
				case 'set_tooltip_preference':
					RevLoader::update_option( 'revslider_hide_tooltips', true );
					$return = 'Preference Updated';
					break;
				case 'save_color_preset':
					$presets       = $this->get_val( $data, 'presets', array() );
					$color_presets = RSColorpicker::save_color_presets( $presets );
					$this->ajax_response_data( array( 'presets' => $color_presets ) );
					break;
				case 'get_facebook_photosets':
					if ( ! empty( $data['url'] ) ) {
						$facebook = new RevSliderFacebook();
						$return   = $facebook->get_photo_set_photos_options( $data['url'], $data['album'], $data['app_id'] );

						if ( empty( $return ) ) {
							$error = RevLoader::__( 'Could not fetch Facebook albums', 'revslider' );
							$this->ajax_response_error( $error );
						} else {
							if ( ! isset( $return[0] ) || $return[0] != 'error' ) {
								 $this->ajax_response_success( RevLoader::__( 'Successfully fetched Facebook albums', 'revslider' ), array( 'html' => implode( ' ', $return ) ) );
							} else {
								$error = $return[1];
								$this->ajax_response_error( $error );
							}
						}

						/*
						if(!empty($return) && ( isset($return[0]) ) ){
							$this->ajax_response_success(RevLoader::__('Successfully fetched Facebook albums', 'revslider'), array('html' => implode(' ', $return)));
						}else{
							$error = RevLoader::__('Could not fetch Facebook albums', 'revslider');
							$this->ajax_response_error($error);
						}*/
					} else {
						$this->ajax_response_success( RevLoader::__( 'Cleared Albums', 'revslider' ), array( 'html' => implode( ' ', $return ) ) );
					}
					break;
				case 'get_flickr_photosets':
					$error = RevLoader::__( 'Could not fetch flickr photosets', 'revslider' );
					if ( ! empty( $data['url'] ) && ! empty( $data['key'] ) ) {
						$flickr  = new RevSliderFlickr( $data['key'] );
						$user_id = $flickr->get_user_from_url( $data['url'] );
						$return  = $flickr->get_photo_sets( $user_id, $data['count'], $data['set'] );
						if ( ! empty( $return ) ) {
							$this->ajax_response_success( RevLoader::__( 'Successfully fetched flickr photosets', 'revslider' ), array( 'data' => array( 'html' => implode( ' ', $return ) ) ) );
						} else {
							$error = RevLoader::__( 'Could not fetch flickr photosets', 'revslider' );
						}
					} else {
						if ( empty( $data['url'] ) && empty( $data['key'] ) ) {
							$this->ajax_response_success( RevLoader::__( 'Cleared Photosets', 'revslider' ), array( 'html' => implode( ' ', $return ) ) );
						} elseif ( empty( $data['url'] ) ) {
							$error = RevLoader::__( 'No User URL - Could not fetch flickr photosets', 'revslider' );
						} else {
							$error = RevLoader::__( 'No API KEY - Could not fetch flickr photosets', 'revslider' );
						}
					}

					$this->ajax_response_error( $error );
					break;
				case 'get_youtube_playlists':
					if ( ! empty( $data['id'] ) ) {
						$youtube = new RevSliderYoutube( trim( $data['api'] ), trim( $data['id'] ) );
						$return  = $youtube->get_playlist_options( $data['playlist'] );
						$this->ajax_response_success( RevLoader::__( 'Successfully fetched YouTube playlists', 'revslider' ), array( 'data' => array( 'html' => implode( ' ', $return ) ) ) );
					} else {
						$this->ajax_response_error( RevLoader::__( 'Could not fetch YouTube playlists', 'revslider' ) );
					}
					break;
				case 'fix_database_issues':
					RevLoader::update_option( 'revslider_table_version', '1.0.0' );

					RevSliderFront::create_tables( true );

					$this->ajax_response_success( RevLoader::__( 'Slider Revolution database structure was updated', 'revslider' ) );
					break;
				case 'trigger_font_deletion':
					$this->delete_google_fonts();

					$this->ajax_response_success( RevLoader::__( 'Downloaded Google Fonts will be updated', 'revslider' ) );
					break;
				case 'get_same_aspect_ratio':
					$images = $this->get_val( $data, 'images', array() );
					$return = $this->get_same_aspect_ratio_images( $images );

					$this->ajax_response_data( array( 'images' => $return ) );
					break;
				case 'get_addons_sizes':
					$addons = $this->get_val( $data, 'addons', array() );
					$sizes  = $this->get_addon_sizes( $addons );

					$this->ajax_response_data( array( 'addons' => $sizes ) );
					break;
				case 'get_v5_slider_list':
					$admin   = new RevSliderFunctionsAdmin();
					$sliders = $admin->get_v5_slider_data();

					$this->ajax_response_data( array( 'slider' => $sliders ) );
					break;
				case 'reimport_v5_slider':
					$status = false;
					if ( ! empty( $data['id'] ) ) {
						$admin  = new RevSliderFunctionsAdmin();
						$status = $admin->reimport_v5_slider( $data['id'] );
					}
					if ( $status === false ) {
						$this->ajax_response_error( RevLoader::__( 'Slider could not be transfered to v6', 'revslider' ) );
					} else {
						$this->ajax_response_success( RevLoader::__( 'Slider transfered to v6', 'revslider' ) );
					}
					break;
				default:
					$return = ''; // ''is not allowed to be added directly in apply_filters(), so its needed like this
					$return = RevLoader::apply_filters( 'revslider_do_ajax', $return, $action, $data );

					// track custom work for addons
					if ( 'wp_ajax_save_values_revslider-weather-addon' == $action ) {
						RevLoader::update_option( 'revslider_weather_addon', $data['revslider_weather_form'] );
						$return = 'Saved';
					} elseif ( 'wp_ajax_get_values_revslider-weather-addon' == $action ) {
						$return = RevLoader::values_weather();
					} elseif ( 'wp_ajax_enable_revslider-maintenance-addon' == $action ) {
						RevLoader::change_addon_status_overwrite( 1 );
						$return = RevLoader::__( 'maintenance AddOn enabled', 'revslider-maintenance-addon' );
					} elseif ( 'wp_ajax_disable_revslider-maintenance-addon' == $action ) {
						RevLoader::change_addon_status_overwrite( 0 );
						$return = RevLoader::__( 'maintenance AddOn disabled', 'revslider-maintenance-addon' );
					} elseif ( 'wp_ajax_get_values_revslider-maintenance-addon' == $action ) {
						$return = RevLoader::values_maintenance_overwrite();
					} elseif ( 'wp_ajax_save_values_revslider-maintenance-addon' == $action ) {
						$return = RevLoader::save_maintenance_overwrite();
						if ( empty( $return ) || ! $return ) {
							$return = RevLoader::__( 'Configuration could not be saved', 'revslider-maintenance-addon' );
						} else {
							$return = RevLoader::__( 'Maintenance Configuration saved', 'revslider-maintenance-addon' );
						}
					} elseif ( 'wp_ajax_enable_revslider-backup-addon' == $action ) {
						RevLoader::change_backup_addon_status( 1 );
						$return = RevLoader::__( 'Backups AddOn enabled', 'revslider-backup-addon' );
					} elseif ( 'wp_ajax_disable_revslider-backup-addon' == $action ) {
						RevLoader::change_backup_addon_status( 1 );
						$return = RevLoader::__( 'Backups AddOn disabled', 'revslider-backup-addon' );
					} elseif ( 'fetch_slide_backups' == $action ) {
						$slide_data = RevLoader::fetch_slide_backups_overwrite( $data['slideID'], true );
						$return     = array( 'data' => $slide_data );
					} elseif ( 'restore_slide_backup' == $action ) {
						$backup_id  = intval( $data['id'] );
						$slide_id   = $data['slide_id'];
						$session_id = RevLoader::esc_attr( $data['session_id'] );
						$response   = RevLoader::restore_slide_backup( $backup_id, $slide_id, $session_id );
						if ( $response !== true ) {
							$f = new RevSliderFunctions();
							$f->throw_error( RevLoader::__( 'Backup restoration failed...', 'rs_backup' ) );
						}
						$return = RevLoader::__( 'Backup restored, redirecting...', 'rs_backup' );
					}elseif('wp_ajax_save_values_revslider-domain-switch-addon' == $action){
						
						$revslider_domain_switch = array();
 
						if(isset($data['revslider_domain_switch_form'])){
						 parse_str($data['revslider_domain_switch_form'], $revslider_domain_switch);
						 
						 if(!isset($revslider_domain_switch['revslider-domain-switch-addon-old']) || empty($revslider_domain_switch['revslider-domain-switch-addon-old'])) return Revloader::__('Old domain can not be empty');
						 if(!isset($revslider_domain_switch['revslider-domain-switch-addon-new']) || empty($revslider_domain_switch['revslider-domain-switch-addon-new'])) return Revloader::__('New domain can not be empty');
						 
						 $rso = str_replace('/', '\/', $revslider_domain_switch['revslider-domain-switch-addon-old']);
						 $rsn = str_replace('/', '\/', $revslider_domain_switch['revslider-domain-switch-addon-new']);
						 
						 //go through all tables and replace image URLs with new names
						 global $wpdb;
						 
						 $sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_SLIDER. " SET `params` = replace(`params`, %s, %s)", array($rso, $rsn));
						 $wpdb->query($sql);
						 $sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_SLIDES. " SET `params` = replace(`params`, %s, %s)", array($rso, $rsn));
						 $wpdb->query($sql);
						 $sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_SLIDES. " SET `layers` = replace(`layers`, %s, %s)", array($rso, $rsn));
						 $wpdb->query($sql);
						 $sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES. " SET `params` = replace(`params`, %s, %s)", array($rso, $rsn));
						 $wpdb->query($sql);
						 $sql = $wpdb->prepare("UPDATE ".$wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES. " SET `layers` = replace(`layers`, %s, %s)", array($rso, $rsn));
						 $wpdb->query($sql);
						 $return = RevLoader::__("Domains successfully changed in all sliders",'domain-switch');
						 
						 }else{
							 $return = RevLoader::__("No Data Send",'domain-switch');
						 }
						 
					 }

					if ( $return ) {
						if ( is_array( $return ) ) {
							// if(isset($return['message'])) $this->ajax_response_success($return["message"]);
							if ( isset( $return['message'] ) ) {
								$this->ajax_response_data(
									array(
										'message' => $return['message'],
										'data'    => $return['data'],
									)
								);
							}
							$this->ajax_response_data( array( 'data' => $return['data'] ) );
						} else {
							$this->ajax_response_success( $return );
						}
					} else {
						$return = '';
					}
					break;
			}
		} catch ( Exception $e ) {
			$message = $e->getMessage();
			if ( in_array( $action, array( 'preview_slide', 'preview_slider' ) ) ) {
				echo $message;
				RevLoader::wp_die();
			}
			$this->ajax_response_error( $message );
		}

		// it's an ajax action, so exit
		$this->ajax_response_error( RevLoader::__( 'No response on action', 'revslider' ) );
		RevLoader::wp_die();
	}

	/**
	 * Ajax handling for frontend, no privileges here
	 */
	public function do_front_ajax_action() {
		$token = $this->get_post_var( 'token', false );

		// verify the token
		$is_verified = wp_verify_nonce( $token, 'RevSlider_Front' );

		$error = false;
		if ( $is_verified ) {
			$data = $this->get_post_var( 'data', false );
			switch ( $this->get_post_var( 'client_action', false ) ) {
				case 'get_slider_html':
					$alias  = $this->get_post_var( 'alias', '' );
					$usage  = $this->get_post_var( 'usage', '' );
					$modal  = $this->get_post_var( 'modal', '' );
					$layout = $this->get_post_var( 'layout', '' );
					$offset = $this->get_post_var( 'offset', '' );
					$id     = intval( $this->get_post_var( 'id', 0 ) );

					// check if $alias exists in database, transform it to id
					if ( $alias !== '' ) {
						$sr = new RevSliderSlider();
						$id = intval( $sr->alias_exists( $alias, true ) );
					}

					if ( $id > 0 ) {
						  $html = '';
						  ob_start();
						  $slider = new RevSliderOutput();
						  $slider->set_ajax_loaded();

						  $slider_class = $slider->add_slider_to_stage( $id, $usage, $layout, $offset, $modal );
						  $html         = ob_get_contents();
						  ob_clean();
						  ob_end_clean();

						  $result = ( ! empty( $slider_class ) && $html !== '' ) ? true : false;

						if ( ! $result ) {
							$error = RevLoader::__( 'Slider not found', 'revslider' );
						} else {
							if ( $html !== false ) {
								$this->ajax_response_data( $html );
							} else {
								$error = RevLoader::__( 'Slider not found', 'revslider' );
							}
						}
					} else {
						   $error = RevLoader::__( 'No Data Received', 'revslider' );
					}
					break;
			}
		} else {
			$error = true;
		}

		if ( $error !== false ) {
			$show_error = ( $error !== true ) ? RevLoader::__( 'Loading Error', 'revslider' ) : RevLoader::__( 'Loading Error: ', 'revslider' ) . $error;

			$this->ajax_response_error( $show_error, false );
		}
		exit;
	}

	/**
	 * echo json ajax response as error
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseError();
	 */
	protected function ajax_response_error( $message, $data = null ) {
		$this->ajax_response( false, $message, $data, true );
	}

	/**
	 * echo ajax success response with redirect instructions
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseSuccessRedirect();
	 */
	protected function ajax_response_redirect( $message, $url ) {
		$data = array(
			'is_redirect'  => true,
			'redirect_url' => $url,
		);

		$this->ajax_response( true, $message, $data, true );
	}

	/**
	 * echo json ajax response, without message, only data
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseData()
	 */
	protected function ajax_response_data( $data ) {
		$data = ( gettype( $data ) == 'string' ) ? array( 'data' => $data ) : $data;

		$this->ajax_response( true, '', $data );
	}

	/**
	 * echo ajax success response
	 *
	 * @before: RevSliderBaseAdmin::ajaxResponseSuccess();
	 */
	protected function ajax_response_success( $message, $data = null ) {

		$this->ajax_response( true, $message, $data, true );
	}

	/**
	 * echo json ajax response
	 * before: RevSliderBaseAdmin::ajaxResponse
	 */
	private function ajax_response( $success, $message, $data = null ) {

		$response = array(
			'success' => $success,
			'message' => $message,
		);

		if ( ! empty( $data ) ) {
			if ( gettype( $data ) == 'string' ) {
				$data = array( 'data' => $data );
			}

			$response = array_merge( $response, $data );
		}

		echo json_encode( $response );

		RevLoader::wp_die();
	}


	/**
	 * set the page that should be shown
	 **/
	private function set_current_page() {
		$view       = $this->get_get_var( 'view' );
		$this->view = ( empty( $view ) ) ? 'sliders' : $this->get_get_var( 'view' );
	}

	/**
	 * include/display the previously set page
	 * only allow certain pages to be showed
	 **/
	public function display_admin_page() {
		try {
			if ( ! in_array( $this->view, $this->allowed_views ) ) {
				$this->throw_error( RevLoader::__( 'Bad Request', 'revslider' ) );
			}

			switch ( $this->view ) {
				// switch URLs to corresponding php files
				case 'slide':
					$view = 'builder';
					break;
				case 'sliders':
				default:
					$view = 'overview';
					break;
			}

			$this->validate_filepath( $this->path_views . $view . '.php', 'View' );

			include $this->path_views . 'header.php';
			include $this->path_views . $view . '.php';
			include $this->path_views . 'footer.php';

		} catch ( Exception $e ) {
			$this->show_error( $this->view, $e->getMessage() );
		}
	}


	/**
	 * show an nice designed error
	 **/
	public function show_error( $view, $message ) {
		echo '<div class="rs-error">';
		echo RevLoader::__( 'Slider Revolution encountered the following error: ', 'revslider' );
		echo RevLoader::esc_attr( $view );
		echo ' - Error: <span>';
		echo RevLoader::esc_attr( $message );
		echo '</span>';
		echo '</div>';
		exit;
	}


	/**
	 * validate that some file exists, if not - throw error
	 *
	 * @before: RevSliderFunctions::validateFilepath
	 */
	public function validate_filepath( $filepath, $prefix = null ) {
		if ( file_exists( $filepath ) == true ) {
			return true;
		}

		$prefix  = ( $prefix == null ) ? 'File' : $prefix;
		$message = $prefix . ' ' . RevLoader::esc_attr( $filepath ) . ' not exists!';

		$this->throw_error( $message );
	}


	/**
	 * esc attr recursive
	 *
	 * @since: 6.0
	 */
	public static function esc_js_deep( $value ) {
		$value = is_array( $value ) ? array_map( array( 'RevSliderAdmin', 'esc_js_deep' ), $value ) : RevLoader::esc_js( $value );

		return $value;
	}


	/**
	 * generate missing attachement metadata for images
	 *
	 * @since: 6.0
	 **/
	public function generate_attachment_metadata() {
		$rs_meta_create = RevLoader::get_option( 'rs_image_meta_todo', array() );

		if ( ! empty( $rs_meta_create ) ) {
			foreach ( $rs_meta_create as $attach_id => $save_dir ) {
				if ( $attach_data = @wp_generate_attachment_metadata( $attach_id, $save_dir ) ) {
					@wp_update_attachment_metadata( $attach_id, $attach_data );
				}
				unset( $rs_meta_create[ $attach_id ] );
				RevLoader::update_option( 'rs_image_meta_todo', $rs_meta_create );
			}
		}
	}

}