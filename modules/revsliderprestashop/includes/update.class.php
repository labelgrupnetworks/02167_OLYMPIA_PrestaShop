<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class RevSliderUpdate {

	private $plugin_url      = 'https://www.themepunch.com/links/slider_revolution_wordpress';
	private $crazy_store_url = 'https://classydevs.com/';
	private $remote_url      = 'check_for_updates.php';
	private $remote_url_info = 'revslider/revslider.php';
	private $plugin_slug     = 'revslider';
	private $version;
	private $plugins;
	private $option;
	public $force = false;


	public function __construct( $version ) {
		$this->option = $this->plugin_slug . '_update_info';
		$this->_retrieve_version_info();
		$this->version = $version;
	}


	public function add_update_checks() {
		if ( $this->force === true ) {
			ini_set( 'max_execution_time', 300 ); // an update can follow, so set the execution time high for the runtime
			$transient = get_site_transient( 'update_plugins' );
			$rs_t      = $this->set_update_transient( $transient );

			if ( ! empty( $rs_t ) ) {
				set_site_transient( 'update_plugins', $rs_t );
			}
		}

		RevLoader::add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'set_update_transient' ) );
		RevLoader::add_filter( 'plugins_api', array( &$this, 'set_updates_api_results' ), 10, 3 );
	}


	public function set_update_transient( $transient ) {
		$this->_check_updates();

		if ( isset( $transient ) && ! isset( $transient->response ) ) {
			$transient->response = array();
		}

		if ( ! empty( $this->data->basic ) && is_object( $this->data->basic ) ) {
			if ( version_compare( $this->version, $this->data->basic->version, '<' ) ) {
				$this->data->basic->new_version             = $this->data->basic->version;
				$transient->response[ RS_PLUGIN_SLUG_PATH ] = $this->data->basic;
			}
		}

		return $transient;
	}


	public function set_updates_api_results( $result, $action, $args ) {
		$this->_check_updates();

		if ( isset( $args->slug ) && $args->slug == $this->plugin_slug && $action == 'plugin_information' ) {
			if ( is_object( $this->data->full ) && ! empty( $this->data->full ) ) {
				$result = $this->data->full;
			}
		}

		return $result;
	}


	public function _check_updates() {
		// Get data
		if ( empty( $this->data ) ) {
			$data = RevLoader::get_option( $this->option, false );
			$data = $data ? $data : new stdClass();

			$this->data = is_object( $data ) ? $data : maybe_unserialize( $data );
		}

		$last_check = RevLoader::get_option( 'revslider-update-check' );
		if ( $last_check == false ) { // first time called
			$last_check = time() - 172802;
			RevLoader::update_option( 'revslider-update-check', $last_check );
		}

		// Check for updates
		if ( time() - $last_check > 172800 || $this->force == true ) {
			$data = $this->_retrieve_update_info();

			if ( isset( $data->basic ) ) {
				update_option( 'revslider-update-check', time() );

				$this->data->checked = time();
				$this->data->basic   = $data->basic;
				$this->data->full    = $data->full;

				RevLoader::update_option( 'revslider-stable-version', $data->full->stable );
				RevLoader::update_option( 'revslider-latest-version', $data->full->version );
			}
		}

		// Save results
		RevLoader::update_option( $this->option, $this->data );
	}


	public function _retrieve_update_info() {
		$rslb = new RevSliderLoadBalancer();
		$data = new stdClass();

		// Build request
		$rattr = array(
			'code'    => urlencode( RevLoader::get_option( 'revslider-code', '' ) ),
			'version' => urlencode( RS_REVISION ),
		);

		if ( RevLoader::get_option( 'revslider-valid', 'false' ) !== 'true' && version_compare( RS_REVISION, RevLoader::get_option( 'revslider-stable-version', '4.2' ), '<' ) ) { // We'll get the last stable only now!
			$rattr['get_stable'] = 'true';
		}

		$request = $rslb->call_url( $this->remote_url_info, $rattr, 'updates' );

		if ( ! RevLoader::is_wp_error( $request ) ) {
			if ( $response = RevLoader::maybe_unserialize( $request['body'] ) ) {
				if ( is_object( $response ) ) {
					$data                 = $response;
					$data->basic->url     = $this->plugin_url;
					$data->full->url      = $this->plugin_url;
					$data->full->external = 1;
				}
			}
		}

		return $data;
	}


	public function _retrieve_version_info() {
		$rslb       = new RevSliderLoadBalancer();
		$last_check = RevLoader::get_option( 'revslider-update-check-short' );
		// Check for updates
		if ( $last_check == false || time() - $last_check > 172800 || $this->force == true ) {
			RevLoader::update_option( 'revslider-update-check-short', time() );

			$purchase = ( RevLoader::get_option( 'revslider-valid', 'false' ) == 'true' ) ? RevLoader::get_option( 'revslider-code', '' ) : '';

			// classydevs way

			$api_params = array(
				'edd_action' => 'get_version',
				'license'    => $purchase,
				'item_id'    => '13738',
				'version'    => RS_REVISION,
				'url'        => _PS_BASE_URL_SSL_,
			);
			$url        = $this->crazy_store_url . '?' . http_build_query( $api_params );
			$response   = RevSliderLicense::wp_remote_get(
				$url,
				array(
					'timeout' => 20,
					'headers' => '',
					'header'  => false,
					'json'    => true,
				)
			);

			$responsearray = Tools::jsonDecode( $response, true );

			if ( version_compare( $api_params['version'], $responsearray['new_version'], '<' ) ) {
				if ( isset( $responsearray['new_version'] ) ) {
					RevLoader::update_option( 'revslider-latest-version', $responsearray['new_version'] );

				}

				if ( isset( $responsearray['stable_version'] ) ) {
					RevLoader::update_option( 'revslider-stable-version', $responsearray['stable_version'] );
				}

				if ( isset( $responsearray['package'] ) ) {
					RevLoader::update_option( 'revslider-down-package', $responsearray['package'] );
				}
			} else {
				RevLoader::update_option( 'revslider-down-package', '' );
			}
			// *******************************************themepunch way to check update version *******************************************
		}

		// force that the update will be directly searched
		if ( $this->force == true ) {
			RevLoader::update_option( 'revslider-update-check', '' );
		}

		return RevLoader::get_option( 'revslider-latest-version', RS_REVISION );
	}
}


/**
 * old classname extends new one (old classnames will be obsolete soon)
 *
 * @since: 5.0
 **/
class UniteUpdateClassRev extends RevSliderUpdate {}

