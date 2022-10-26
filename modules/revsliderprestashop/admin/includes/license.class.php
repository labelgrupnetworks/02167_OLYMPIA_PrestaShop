<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2020 ThemePunch
 * @since     6.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class RevSliderLicense extends RevSliderFunctions {




	private static $licence_url    = 'https://classydevs.com/';
	private static $item_id        = '13738';
	private static $current        = '';
	private static $licence_status = 'rs6_licence_status';
	private static $licence_data   = 'rs6_licence_dataset';
	private static $licence_ex     = 'rs6_licence_expires';

	/**
	 * Activate the Plugin through the ThemePunch Servers
	 *
	 * @before 6.0.0: RevSliderOperations::checkPurchaseVerification();
	 * @before 6.2.0: RevSliderAdmin::activate_plugin();
	 **/
	public function activate_plugin( $code, $auto = false ) {

		$rs6_licence_date = RevLoader::get_option( 'rs6_licence_date', '' );
		$first_install    = false;
		if ( $rs6_licence_date == '' ) {
			$today            = date( 'Y-m-d' );
			$rs6_licence_date = RevLoader::update_option( 'rs6_licence_date', $today );
			$first_install    = true;
		}
		if ( $rs6_licence_date != '' ) {
			$today = date( 'Y-m-d' );
			if ( $auto == true ) {
				if ( ( strtotime( $today ) == strtotime( $rs6_licence_date ) ) && ! $first_install ) {
					return false;
				}
			}
		}
		$array = array(
			'edd_action' => 'activate_license',
			'license'    => $code,
			'item_id'    => self::$item_id, // The ID of the item in EDD
			'url'        => _PS_BASE_URL_SSL_,
		);
		$url   = self::$licence_url . '?' . http_build_query( $array );
		if ( $code ) {
			$response = self::wp_remote_get(
				$url,
				array(
					'timeout' => 15,
					'headers' => '',
					'header'  => false,
					'json'    => true,
				)
			);
			
			$responsearray = Tools::jsonDecode( $response, true );

			

			if ( $responsearray['success'] == 'true' && $responsearray['license'] == 'valid' ) {

				RevLoader::update_option( 'revslider-valid', 'true' );
				RevLoader::update_option( 'revslider-code', $code );
				RevLoader::update_option( self::$licence_data, $response );
				RevLoader::update_option( self::$licence_ex, $responsearray['expires'] );
				$rs6_licence_date = RevLoader::update_option( 'rs6_licence_date', $today );

				return true;
			} else {
				RevLoader::update_option( 'revslider-valid', $responsearray['license'] );
				RevLoader::update_option( 'revslider-code', '' );
				RevLoader::update_option( self::$licence_data, $response );
				RevLoader::update_option( self::$licence_ex, $responsearray['license'] );
				$rs6_licence_date = RevLoader::update_option( 'rs6_licence_date', $today );
				return false;
			}
		} else {
			RevLoader::update_option( 'revslider-code', '' );
			RevLoader::update_option( 'revslider-valid', 'false' );
			RevLoader::update_option( self::$licence_data, 'false' );
			RevLoader::update_option( self::$licence_ex, 'false' );
			return false;
		}
	}

	/**
	 * Deactivate the Plugin through the ThemePunch Servers
	 *
	 * @before 6.0.0: RevSliderOperations::doPurchaseDeactivation();
	 * @before 6.2.0: RevSliderAdmin::deactivate_plugin();
	 **/
	public function deactivate_plugin() {

		$code = RevLoader::get_option( 'revslider-code', '' );

		$array = array(
			'edd_action' => 'deactivate_license',
			'license'    => $code,
			'item_id'    => self::$item_id,
			'url'        => _PS_BASE_URL_SSL_,
		);
		$url   = self::$licence_url . '?' . http_build_query( $array );

		if ( $code ) {
			$response      = self::wp_remote_get(
				$url,
				array(
					'timeout' => 15,
					'headers' => '',
					'header'  => false,
					'json'    => true,
				)
			);
			$responsearray = Tools::jsonDecode( $response, true );
			if ( $responsearray['success'] == true ) {
				RevLoader::update_option( self::$licence_data, $response );
				RevLoader::update_option( self::$licence_ex, $responsearray['expires'] );
				RevLoader::update_option( 'revslider-valid', 'false' );
				RevLoader::update_option( 'revslider-code', '' );
				$today           = date( 'Y-m-d' );
				$ce_licence_date = RevLoader::update_option( 'ce_licence_date', $today );
				return true;
			}
		}

		return false;
	}

	public static function wp_remote_get( $url, $args = array() ) {
		return self::getHttpCurl( $url, $args );
	}

	public static function getHttpCurl( $url, $args ) {
		global $wp_version;
		if ( function_exists( 'curl_init' ) ) {
			$defaults = array(
				'method'      => 'GET',
				'timeout'     => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(
					'Authorization'   => 'Basic ',
					'Content-Type'    => 'application/x-www-form-urlencoded;charset=UTF-8',
					'Accept-Encoding' => 'x-gzip,gzip,deflate',
				),
				'body'        => array(),
				'cookies'     => array(),
				'user-agent'  => 'Prestashop' . $wp_version,
				'header'      => true,
				'sslverify'   => false,
				'json'        => false,
			);

			$args         = array_merge( $defaults, $args );
			$curl_timeout = ceil( $args['timeout'] );
			$curl         = curl_init();
			if ( $args['httpversion'] == '1.0' ) {
				curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
			} else {
				curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
			}
			curl_setopt( $curl, CURLOPT_USERAGENT, $args['user-agent'] );
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, $curl_timeout );
			curl_setopt( $curl, CURLOPT_TIMEOUT, $curl_timeout );
			curl_setopt( $curl, CURLOPT_POST, 1 );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, 'api=true' );
			$ssl_verify = $args['sslverify'];
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, $ssl_verify );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, ( $ssl_verify === true ) ? 2 : false );
			$http_headers = array();
			if ( $args['header'] ) {
				curl_setopt( $curl, CURLOPT_HEADER, $args['header'] );
				foreach ( $args['headers'] as $key => $value ) {
					$http_headers[] = "{$key}: {$value}";
				}
			}
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false );
			if ( defined( 'CURLOPT_PROTOCOLS' ) ) { // PHP 5.2.10 / cURL 7.19.4
				curl_setopt( $curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS );
			}
			if ( is_array( $args['body'] ) || is_object( $args['body'] ) ) {
				$args['body'] = http_build_query( $args['body'] );
			}
			$http_headers[] = 'Content-Length: ' . strlen( $args['body'] );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			$response = curl_exec( $curl );
			if ( $args['json'] ) {
				return $response;
			}
			$header_size    = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
			$responseHeader = substr( $response, 0, $header_size );
			$responseBody   = substr( $response, $header_size );
			$error          = curl_error( $curl );
			$errorcode      = curl_errno( $curl );
			$info           = curl_getinfo( $curl );
			curl_close( $curl );
			$info_as_response            = $info;
			$info_as_response['code']    = $info['http_code'];
			$info_as_response['message'] = 'OK';
			$response                    = array(
				'body'     => $responseBody,
				'headers'  => $responseHeader,
				'info'     => $info,
				'response' => $info_as_response,
				'error'    => $error,
				'errno'    => $errorcode,
			);
			return $response;
		}
		return false;
	}
}