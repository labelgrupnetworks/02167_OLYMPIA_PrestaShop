<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();

class RevSliderWpml extends RevSliderFunctions {
	
	private $cur_lang;
	
	/**
	 * load the wpml filters ect.
	 **/
	public function __construct(){
		//RevLoader::add_filter('revslider_get_posts_by_category', array($this, 'translate_category_lang'), 10, 2);
	//	RevLoader::add_filter('revslider_get_parent_slides_pre', array($this, 'change_lang'), 10, 4);
	//	RevLoader::add_filter('revslider_get_parent_slides_post', array($this, 'change_lang_to_orig'), 10, 4);
		RevLoader::add_action('revslider_header_content', array($this, 'add_javascript_language'));
	}
	
	/**
	 * true / false if the wpml plugin exists
	 */
//	public function wpml_exists(){
//		return did_action('wpml_loaded');
//	}
//
//
//	/**
//	 * valdiate that wpml exists
//	 */
//	public function validateWpmlExists(){
//		if(!$this->wpml_exists()){
//			$this->throw_error(RevLoader::__('The WPML plugin is not activated', 'revslider'));
//		}
//	}

    public static function wpml_exists()
    {
        return true;

        if (class_exists("SitePress")) {
            return(true);
        } else {
            return(false);
        }
    }

    private static function validateWpmlExists()
    {
        if (!self::isWpmlExists()) {
            UniteFunctionsRev::throwError("The wpml plugin don't exists");
        }
    }

    /**
	 * get languages array
	 */
//	public function getArrLanguages($get_all = true){
//		$this->validateWpmlExists();
//
//		$langs		= RevLoader::apply_filters('wpml_active_languages', array());
//		$response	= array();
//
//		if($get_all == true){
//			$response['all'] = RevLoader::__('All Languages', 'revslider');
//		}
//
//		foreach($langs as $code => $lang){
//			$name			 = $lang['native_name'];
//			$response[$code] = $name;
//		}
//
//		return $response;
//	}

    public static function getArrLanguages($getAllCode = true)
    {
        $arrLangs = Language::getLanguages();

        $response = array();



        if ($getAllCode == true) {
            $response["all"] = RevLoader::__("All Languages", REVSLIDER_TEXTDOMAIN);
        }



        foreach ($arrLangs as $code => $arrLang) {
            $ind = $arrLang['iso_code'];
            $response[$ind] = $arrLang['name'];
        }



        return($response);
    }
	/**
	 * get assoc array of lang codes
	 */
//	public function getArrLangCodes($get_all = true){
//		$codes = array();
//
//		if($get_all == true){
//			$codes['all'] = 'all';
//		}
//
//		$this->validateWpmlExists();
//		$langs = RevLoader::apply_filters('wpml_active_languages', array());
//
//		foreach($langs as $code => $arr){
//			$codes[$code] = $code;
//		}
//
//		return $codes;
//	}
    public static function getArrLangCodes($getAllCode = true)
    {
        $arrCodes = array();

        if ($getAllCode == true) {
            $arrCodes["all"] = "all";
        }

        $arrLangs = Language::getLanguages();

        foreach ($arrLangs as $code => $arr) {
            $ind = $arr['iso_code'];

            $arrCodes[$ind] = $ind;
        }

        return($arrCodes);
    }
	
	/**
	 * check if all languages exists in the given langs array
	 */
//	public function isAllLangsInArray($codes){
//		$all_codes	= $this->getArrLangCodes();
//		$diff		= array_diff($all_codes, $codes);
//		return empty($diff);
//	}
    public static function isAllLangsInArray($arrCodes)
    {
        $arrAllCodes = self::getArrLangCodes();

        $diff = array_diff($arrAllCodes, $arrCodes);

        return(empty($diff));
    }
	
	/**
	 * get flag url
	 */
//	public function getFlagUrl($code){
//
//		$this->validateWpmlExists();
//
//		if(empty($code) || $code == 'all'){
//            //$url = RS_PLUGIN_URL.'admin/assets/images/icon-all.png'; // NEW: ICL_PLUGIN_URL . '/res/img/icon16.png';
//            $url = ICL_PLUGIN_URL . '/res/img/icon16.png';
//        }else{
//            $active_languages = RevLoader::apply_filters('wpml_active_languages', array());
//            $url = isset($active_languages[$code]['country_flag_url']) ? $active_languages[$code]['country_flag_url'] : null;
//        }
//
//		//default: show all
//		if(empty($url)){
//			//$url = RS_PLUGIN_URL.'admin/assets/images/icon-all.png';
//			$url = ICL_PLUGIN_URL . '/res/img/icon16.png';
//		}
//
//		return $url;
//	}
    public static function getFlagUrl($code)
    {
        $arrLangs = Language::getLanguages();

        if ($code == 'all') {
            $url = RevLoader::get_module_url() . '/views/img/icon16.png';
        } else {
            $url = '';
            foreach ($arrLangs as $lang) {
                if ($lang['iso_code'] == $code) {
                    $url = _THEME_LANG_DIR_ . $lang['id_lang'] . '.jpg';
                }
            }
        }


        return($url);
    }
	
	/**
	 * get language title by code
	 */
//	public function getLangTitle($code){
//		if($code == 'all'){
//			return(RevLoader::__('All Languages', 'revslider'));
//		}else{
//			$def = RevLoader::apply_filters('wpml_default_language', null);
//			return RevLoader::apply_filters('wpml_translated_language_name', '', $code, $def);
//		}
//	}
    public static function getLangTitle($code)
    {
        $langs = self::getArrLanguages();



        if ($code == "all") {
            return(RevLoader::__("All Languages", REVSLIDER_TEXTDOMAIN));
        }



        if (array_key_exists($code, $langs)) {
            return($langs[$code]);
        }



        $details = self::getLangDetails($code);

        if (!empty($details)) {
            return($details["english_name"]);
        }



        return("");
    }
	
	/**
	 * get current language
	 */
//	public function getCurrentLang(){
//		$this->validateWpmlExists();
//
//		return (RevLoader::is_admin()) ? RevLoader::apply_filters('wpml_default_language', null) : RevLoader::apply_filters('wpml_current_language', null);
//	}

    public static function getCurrentLang()
    {
        $language = Context::getContext()->language;

        $lang = $language->iso_code;

        return($lang);
    }


	/**
	 * was before in RevSliderFunctions::get_posts_by_category();
	 **/
	public function translate_category_lang($data, $type){
		$cat_id = $this->get_val($data, 'cat_id');
		$cat_id	= (strpos($cat_id, ',') !== false) ? explode(',', $cat_id) : array($cat_id);
		
		if($this->wpml_exists()){ //translate categories to languages
			$newcat = array();
			foreach($cat_id as $id){
				$newcat[] = RevLoader::apply_filters('wpml_object_id', $id, 'category', true);
			}
			$data['cat_id'] = implode(',', $newcat);
		}
		
		return $data;
	}
	
	
	/**
	 * switch the language if WPML is used in Slider
	 **/
	public function change_lang($lang, $published, $gal_ids, $slider){
		if($this->wpml_exists() && $slider->get_param('use_wpml', 'off') == 'on'){
			$this->cur_lang = RevLoader::apply_filters('wpml_current_language', null);
			RevLoader::do_action('wpml_switch_language', $lang);
		}
	}
	
	
	/**
	 * switch the language back to original, if WPML is used in Slider
	 **/
	public function change_lang_to_orig($lang, $published, $gal_ids, $slider){
		if($this->wpml_exists() && $slider->get_param(array('general', 'useWPML'), false) == true){ //switch language back
			RevLoader::do_action('wpml_switch_language', $this->cur_lang);
		}
	}
	
	
	/**
	 * modify slider language
	 * @before: RevSliderOutput::setLang()
	 */
	public function get_language($use_wpml, $slider){
//		$lang = ($this->wpml_exists() && $use_wpml == true) ? ICL_LANGUAGE_CODE : 'all';
		$lang = self::getCurrentLang();

		return $lang;
	}
	
	
	public function get_slider_language($slider){
		$use_wmpl = $slider->get_param(array('general', 'useWPML'), false);
		
		return $this->get_language($use_wmpl, $slider);
	}
	
	/**
	 * add languages as javascript object to the RevSlider BackEnd Header
	 **/
	public function add_javascript_language($rsad){
		if(!$this->wpml_exists()) return '';
		
		$langs = $this->getArrLanguages();
		
		$use_langs = array();
		foreach($langs as $code => $lang){
			$use_langs[$code] = array(
				'title' => $lang,
				'image'	=> $this->getFlagUrl($code)
			);
		}
		echo '<script type="text/javascript">';
		echo 'var RS_WPML_LANGS = JSON.parse(\''.json_encode($use_langs).'\');';
		echo '</script>';
	}
}

$rs_wmpl = new RevSliderWpml();

/**
 * old classname extends new one (old classnames will be obsolete soon)
 * @since: 5.0
 **/
class UniteWpmlRev extends RevSliderWpml {}
?>