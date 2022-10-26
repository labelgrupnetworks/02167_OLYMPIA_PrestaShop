<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();

class RevSliderFunctionsAdmin extends RevSliderFunctions {
	
	/**
	 * get the full object of: 
	 * +- Slider Templates
	 * +- Created Slider
	 * +- Object Library Images
	 * - Object Library Videos
	 * +- SVG
	 * +- Font Icons
	 * - layers
	 **/
	public function get_full_library($include = array('all'), $tmp_slide_uid = array(), $refresh_from_server = false, $get_static_slide = false){
		$include	= (array)$include;
		$template	= new RevSliderTemplate();
		$library	= new RevSliderObjectLibrary();
		$slide		= new RevSliderSlide();
		$object		= array();
		$tmp_slide_uid = ($tmp_slide_uid !== false) ? (array)$tmp_slide_uid : array();
		
		if($refresh_from_server){
			if(in_array('all', $include) || in_array('moduletemplates', $include)){ //refresh template list from server
				$template->_get_template_list(true);
				if(!isset($object['moduletemplates'])) $object['moduletemplates'] = array();
				$object['moduletemplates']['tags'] = $template->get_template_categories();
				asort($object['moduletemplates']['tags']);
			}
			if(in_array('all', $include) || in_array('layers', $include) || in_array('videos', $include) || in_array('images', $include) || in_array('objects', $include)){ //refresh object list from server
				$library->_get_list(true);
			}
			if(in_array('all', $include) || in_array('layers', $include)){ //refresh object list from server
				if(!isset($object['layers'])) $object['layers'] = array();
				$object['layers']['tags'] = $library->get_objects_categories('4');
				asort($object['layers']['tags']);
			}
			if(in_array('all', $include) || in_array('videos', $include)){ //refresh object list from server
				if(!isset($object['videos'])) $object['videos'] = array();
				$object['videos']['tags'] = $library->get_objects_categories('3');
				asort($object['videos']['tags']);
			}
			if(in_array('all', $include) || in_array('images', $include)){ //refresh object list from server
				if(!isset($object['images'])) $object['images'] = array();
				$object['images']['tags'] = $library->get_objects_categories('2');
				asort($object['images']['tags']);
			}
			if(in_array('all', $include) || in_array('objects', $include)){ //refresh object list from server
				if(!isset($object['objects'])) $object['objects'] = array();
				$object['objects']['tags'] = $library->get_objects_categories('1');
				asort($object['objects']['tags']);
			}
			$object = RevLoader::apply_filters('revslider_get_full_library_refresh', $object, $include, $tmp_slide_uid, $refresh_from_server, $get_static_slide, $this);
		}
		
		if(in_array('moduletemplates', $include) || in_array('all', $include)){
			if(!isset($object['moduletemplates'])) $object['moduletemplates'] = array();
			$object['moduletemplates']['items']	= $template->get_tp_template_sliders_for_library($refresh_from_server);
		}
		if(in_array('moduletemplateslides', $include) || in_array('all', $include)){
			if(!isset($object['moduletemplateslides'])) $object['moduletemplateslides'] = array();
			$object['moduletemplateslides']['items'] = $template->get_tp_template_slides_for_library($tmp_slide_uid);
		}
		if(in_array('modules', $include) || in_array('all', $include)){
			if(!isset($object['modules'])) $object['modules'] = array();
			$object['modules']['items'] = $this->get_slider_overview();
		}
		if(in_array('moduleslides', $include) || in_array('all', $include)){
			if(!isset($object['moduleslides'])) $object['moduleslides'] = array();
			$object['moduleslides']['items'] = $slide->get_slides_for_library($tmp_slide_uid, $get_static_slide);
		}
		if(in_array('svgs', $include) || in_array('all', $include)){
			if(!isset($object['svgs'])) $object['svgs'] = array();
			$object['svgs']['items'] = $library->get_svg_sets_full();
		}
		if(in_array('fonticons', $include) || in_array('all', $include)){
			if(!isset($object['fonticons'])) $object['fonticons'] = array();
			$object['fonticons']['items'] = $library->get_font_icons();
		}
		if(in_array('layers', $include) || in_array('all', $include)){
			if(!isset($object['layers'])) $object['layers'] = array();
			$object['layers']['items'] = $library->load_objects('4');
		}
		if(in_array('videos', $include) || in_array('all', $include)){
			if(!isset($object['videos'])) $object['videos'] = array();
			$object['videos']['items'] = $library->load_objects('3');
		}
		if(in_array('images', $include) || in_array('all', $include)){
			if(!isset($object['images'])) $object['images'] = array();
			$object['images']['items'] = $library->load_objects('2');
		}
		if(in_array('objects', $include) || in_array('all', $include)){
			if(!isset($object['objects'])) $object['objects'] = array();
			$object['objects']['items'] = $library->load_objects('1');
		}
		/*if(in_array('wpimages', $include) || in_array('all', $include)){
			$data = $this->get_request_var('data');
			$after = $this->get_val($data, 'after', false);
			if(!isset($object['wpimages'])) $object['wpimages'] = array();
			$object['wpimages']['items'] = $library->load_wp_objects('image', $after);
		}
		if(in_array('wpvideos', $include) || in_array('all', $include)){
			$data = $this->get_request_var('data');
			$after = $this->get_val($data, 'after', false);
			if(!isset($object['wpvideos'])) $object['wpvideos'] = array();
			$object['wpvideos']['items'] = $library->load_wp_objects('video', $after);
		}*/
		$object = RevLoader::apply_filters('revslider_get_full_library', $object, $include, $tmp_slide_uid, $refresh_from_server, $get_static_slide, $this);
		
		return $object;
	}
	
	
	/**
	 * get the short library with categories and how many elements exist
	 **/
	public function get_short_library(){
		
		$template = new RevSliderTemplate();
		$library = new RevSliderObjectLibrary();
		$sliders = $this->get_slider_overview();
		
		
		$slider_cat = array();
		if(!empty($sliders)){
			foreach($sliders as $slider){
				$tags = $this->get_val($slider, 'tags', array());
				if(!empty($tags)){
					foreach($tags as $tag){
						if(trim($tag) !== '' && !isset($slider_cat[$tag])) $slider_cat[$tag] = ucwords($tag);
					}
				}
			}
		}
		
		$svg_cat = $library->get_svg_categories();
		$oc	= $library->get_objects_categories('1');
		$oc2 = $library->get_objects_categories('2');
		$oc3 = $library->get_objects_categories('3');
		$oc4 = $library->get_objects_categories('4');
		$t_cat = $template->get_template_categories();
		$font_cat = $library->get_font_tags();
		
		$wpi = array('jpg' => 'jpg', 'png' => 'png');
		$wpv = array('mpeg' => 'mpeg', 'mp4' => 'mp4', 'ogv' => 'ogv');
		
		asort($wpi);
		asort($wpv);
		asort($oc);
		asort($t_cat);
		asort($slider_cat);
		asort($svg_cat);
		asort($font_cat);
		
		$tags = array(
			'moduletemplates' => array('tags' => $t_cat),
			'modules'	=> array('tags' => $slider_cat),
			'svgs'		=> array('tags' => $svg_cat),
			'fonticons'	=> array('tags' => $font_cat),
			'layers'	=> array('tags' => $oc4),
			'videos'	=> array('tags' => $oc3),
			'images'	=> array('tags' => $oc2),
			'objects'	=> array('tags' => $oc)/*,
			'wpimages'	=> array('tags' => $wpi),
			'wpvideos'	=> array('tags' => $wpv)*/
		);
		return RevLoader::apply_filters('revslider_get_short_library', $tags, $library, $this);
	}
	
	
	/**
	 * Get Sliders data for the overview page
	 **/
	public function get_slider_overview(){
		$rs_slider	= new RevSliderSlider();
		$rs_slide	= new RevSliderSlide();
		$sliders	= $rs_slider->get_sliders(false);


		$rs_folder	= new RevSliderFolder();
		$folders	= $rs_folder->get_folders();
		
		$sliders 	= array_merge($sliders, $folders);
		$data		= array();

//        var_dump($sliders);
//        die();
		if(!empty($sliders)){
			$slider_list = array();
			foreach($sliders as $slider){
				$slider_list[] = $slider->get_id();
			}
			
			$slides_raw = $rs_slide->get_all_slides_raw($slider_list);
			
			foreach($sliders as $slider){
				$slides = array();
				$sid = $slider->get_id();
				foreach($slides_raw as $s => $r){
					if($r->get_slider_id() !== $sid) continue;
					
					$slides[] = $r;
					unset($slides_raw[$s]);
				}
				
				$slides = (empty($slides)) ? false : $slides;
				
				$slider->init_layer = false;
				$data[] = $slider->get_overview_data(false, $slides);
			}
		}
		
		return $data;
	}
	
	
	/**
	 * insert custom animations
	 * @before: RevSliderOperations::insertCustomAnim();
	 */
	public function insert_animation($animation, $type){
		$handle = $this->get_val($animation, 'name', false);
		$result = false;
		
		if($handle !== false && trim($handle) !== ''){
			global $wpdb;
			
			//check if handle exists
			$arr = array(
				'handle'	=> $this->get_val($animation, 'name'),
				'params'	=> json_encode($animation),
				'settings'	=> $type
			);
			
			$result = $wpdb->insert($wpdb->prefix . RevSliderFront::TABLE_LAYER_ANIMATIONS, $arr);
		}

		return ($result) ? $wpdb->insert_id : $result;
	}
	
	
	/**
	 * update custom animations
	 * @before: RevSliderOperations::updateCustomAnim();
	 */
	public function update_animation($animation_id, $animation, $type){
		global $wpdb;
		
		$arr = array(
			'handle'	=> $this->get_val($animation, 'name'),
			'params'	=> json_encode($animation),
			'settings'	=> $type
		);
		
		$result = $wpdb->update($wpdb->prefix . RevSliderFront::TABLE_LAYER_ANIMATIONS, $arr, array('id' => $animation_id));
		
		return ($result) ? $animation_id : $result;
	}
	
	
	/**
	 * delete custom animations
	 * @before: RevSliderOperations::deleteCustomAnim();
	 */
	public function delete_animation($animation_id){
		global $wpdb;
		
		$result = $wpdb->delete($wpdb->prefix . RevSliderFront::TABLE_LAYER_ANIMATIONS, array('id' => $animation_id));
		
		return $result;
	}
	
	
	/**
	 * @since: 5.3.0
	 * create a page with revslider shortcodes included
	 * @before: RevSliderOperations::create_slider_page();
	 **/
	public static function create_slider_page($added, $modals = array(), $additions = array()){
		global $wp_version;
		
		$new_page_id = 0;
		
		if(!is_array($added)) return RevLoader::apply_filters('revslider_create_slider_page', $new_page_id, $added);
		
		$content = '';
		$page_id = RevLoader::get_option('rs_import_page_id', 1);
		
		//get alias of all new Sliders that got created and add them as a shortcode onto a page
		if(!empty($added)){
			foreach($added as $sid){
				$slider = new RevSliderSlider();
				$slider->init_by_id($sid);
				$alias = $slider->get_alias();
				if($alias !== ''){
					$usage		= (in_array($sid, $modals, true)) ? ' usage="modal"' : '';
					$addition	= (isset($additions[$sid])) ? ' ' . $additions[$sid] : '';
					if(strpos($addition, 'usage=\"modal\"') !== false) $usage = ''; //remove as not needed two times
					
					if(version_compare($wp_version, '5.0', '>=')){ //add gutenberg code
						$ov_data = $slider->get_overview_data();
						$title	 = $slider->get_val($ov_data, 'title', '');
						$img	 = $slider->get_val($ov_data, array('bg', 'src'), '');
						$wrap_addition	= ($img !== '') ? ',"sliderImage":"'.$img.'"' : '';
						$div_addition	= ($title !== '') ? ' data-slidertitle="'.$title.'"' : '';
						
						$zindex_pos = strpos($addition, 'zindex=\"');
						if($zindex_pos !== false){
							$zindex = substr($addition, $zindex_pos + 9, strpos($addition, '\"', $zindex_pos + 9) - ($zindex_pos + 9));
							$div_addition .= ' style="z-index:'.$zindex.';"';
							$wrap_addition .= ',"zindex":"'.$zindex.'"';
						}
						
						$content .= '<!-- wp:themepunch/revslider {"checked":true'.$wrap_addition.'} -->'."\n";
						$content .= '<div class="wp-block-themepunch-revslider revslider" data-modal="false"'.$div_addition.'>';
					}
					
					$content .= '[rev_slider alias="'.$alias.'"'.$usage.$addition.']'; //this way we will reorder as last comes first
					
					if(version_compare($wp_version, '5.0', '>=')){ //add gutenberg code
						$content .= '</div>'."\n".'<!-- /wp:themepunch/revslider -->'."\n";
					}
				}
			}
		}
		
		if($content !== ''){
			$new_page_id = wp_insert_post(
				array(
					'post_title'    => wp_strip_all_tags('RevSlider Page '.$page_id), //$title
					'post_content'  => $content,
					'post_type'   	=> 'page',
					'post_status'   => 'draft',
					'page_template' => '../public/views/revslider-page-template.php'
				)
			);
			
			if(is_wp_error($new_page_id)) $new_page_id = 0; //fallback to 0
			
			$page_id++;
			RevLoader::update_option('rs_import_page_id', $page_id);
		}
		
		return RevLoader::apply_filters('revslider_create_slider_page', $new_page_id, $added);
	}
	
	/**
	 * add notices from ThemePunch
	 * @since: 4.6.8
	 */
	public function add_notices(){
		$_n = array();
		//track
		//$notices = (array)RevLoader::get_option('revslider-notices', false);
		$notices = RevLoader::get_option('revslider-notices', false);
		if(!empty($notices) && is_array($notices)){
			$n_discarted = RevLoader::get_option('revslider-notices-dc', array());
			
			foreach($notices as $notice){
				//check if global or just on plugin related pages
				if($notice->version === true || !in_array($notice->code, $n_discarted) && version_compare($notice->version, RS_REVISION, '>=')){
					$_n[] = $notice;
				}
			}
		}
		
		//push whatever notices we might need
		return $_n;
	}
	
	/**
	 * get basic v5 Slider data
	 **/
	public function get_v5_slider_data(){
		global $wpdb;
		
		$sliders	= array();
		$do_order	= 'id';
		$direction	= 'ASC';
		
		$slider_data = $wpdb->get_results($wpdb->prepare("SELECT `id`, `title`, `alias`, `type` FROM ".$wpdb->prefix . RevSliderFront::TABLE_SLIDER."_bkp ORDER BY %s %s", array($do_order, $direction)), ARRAY_A);
		
		if(!empty($slider_data)){
			foreach($slider_data as $data){
				if($this->get_val($data, 'type') == 'template') continue;
				
				$sliders[] = $data;
			}
		}
		
		return $sliders;
	}
	
	/**
	 * get basic v5 Slider data
	 **/
	public function reimport_v5_slider($id){
		global $wpdb;
		
		$done = false;
		
		$slider_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . RevSliderFront::TABLE_SLIDER."_bkp WHERE `id` = %s", $id), ARRAY_A);
		
		if(!empty($slider_data)){
			$slides_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . RevSliderFront::TABLE_SLIDES."_bkp WHERE `slider_id` = %s", $id), ARRAY_A);
			$static_slide_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES."_bkp WHERE `slider_id` = %s", $id), ARRAY_A);
			
			if(!empty($slides_data)){
				//check if the ID's exist in the new tables, if yes overwrite, if not create
				$slider_v6 = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . RevSliderFront::TABLE_SLIDER." WHERE `id` = %s", $id), ARRAY_A);
				unset($slider_data['id']);
				if(!empty($slider_v6)){
					/**
					 * push the old data to the already imported Slider
					 **/
					$result = $wpdb->update($wpdb->prefix . RevSliderFront::TABLE_SLIDER, $slider_data, array('id' => $id));
				}else{
					$result	= $wpdb->insert($wpdb->prefix . RevSliderFront::TABLE_SLIDER, $slider_data);
					$id		= ($result) ? $wpdb->insert_id : false;
				}
				if($id !== false){
					foreach($slides_data as $k => $slide_data){
						$slide_data['slider_id'] = $id;
						$slide_v6 = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . RevSliderFront::TABLE_SLIDES." WHERE `id` = %s", $slide_data['id']), ARRAY_A);
						$slide_id = $slide_data['id'];
						unset($slide_data['id']);
						if(!empty($slide_v6)){
							$result = $wpdb->update($wpdb->prefix . RevSliderFront::TABLE_SLIDES, $slide_data, array('id' => $slide_id));
						}else{
							$result	= $wpdb->insert($wpdb->prefix . RevSliderFront::TABLE_SLIDES, $slide_data);
						}
					}
					if(!empty($static_slide_data)){
						$static_slide_data['slider_id'] = $id;
						$slide_v6 = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES." WHERE `id` = %s", $static_slide_data['id']), ARRAY_A);
						$slide_id = $static_slide_data['id'];
						unset($static_slide_data['id']);
						if(!empty($slide_v6)){
							$result = $wpdb->update($wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES, $static_slide_data, array('id' => $slide_id));
						}else{
							$result	= $wpdb->insert($wpdb->prefix . RevSliderFront::TABLE_STATIC_SLIDES, $static_slide_data);
						}
					}
					
					$slider = new RevSliderSlider();
					$slider->init_by_id($id);
					
					$upd = new RevSliderPluginUpdate();
					
					$upd->upgrade_slider_to_latest($slider);
					$done = true;
				}
			}
		}
		
		return $done;
	}
	
	
	/**
	 * returns an object of current system values
	 **/
	public function get_system_requirements(){
		$dir	= RevLoader::wp_upload_dir();
		$basedir = $this->get_val($dir, 'basedir').'/';
		$ml		= ini_get('memory_limit');
		$mlb	= RevLoader::wp_convert_hr_to_bytes($ml);
		$umf	= ini_get('upload_max_filesize');
		$umfb	= RevLoader::wp_convert_hr_to_bytes($umf);
		$pms	= ini_get('post_max_size');
		$pmsb	= RevLoader::wp_convert_hr_to_bytes($pms);
		
		
		$mlg  = ($mlb >= 268435456) ? true : false;
		$umfg = ($umfb >= 33554432) ? true : false;
		$pmsg = ($pmsb >= 33554432) ? true : false;
		
		return array(
			'memory_limit' => array(
				'has' => RevLoader::size_format($mlb),
				'min' => RevLoader::size_format(268435456),
				'good'=> $mlg
			),
			'upload_max_filesize' => array(
				'has' => RevLoader::size_format($umfb),
				'min' => RevLoader::size_format(33554432),
				'good'=> $umfg
			),
			'post_max_size' => array(
				'has' => RevLoader::size_format($pmsb),
				'min' => RevLoader::size_format(33554432),
				'good'=> $pmsg
			),
			'upload_folder_writable'	=> RevLoader::wp_is_writable($basedir),
			'object_library_writable'	=> RevLoader::wp_image_editor_supports(array('methods' => array('resize', 'save'))),
			'server_connect'			=> RevLoader::get_option('revslider-connection', false),
		);
	}
	
	/**
	 * import a media file uploaded through the browser to the media library
	 **/
	public function import_upload_media(){
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		
		global $wp_filesystem;
		WP_Filesystem();
		
		$import_file = $this->get_val($_FILES, 'import_file');
		$error		 = $this->get_val($import_file, 'error');
		$return		 = array('error' => RevLoader::__('File not found', 'revslider'));
		
		switch($error){
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				return array('error' => RevLoader::__('No file sent', 'revslider'));
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return array('error' => RevLoader::__('Exceeded filesize limit', 'revslider'));
			default:
			break;
		}
		
		$path = $this->get_val($import_file, 'tmp_name');
		if(isset($path['error'])) return array('error' => $path['error']);
		
		if(file_exists($path) == false) return array('error' => RevLoader::__('File not found', 'revslider'));
		if($this->get_val($import_file, 'size') > wp_max_upload_size()) return array('error' => RevLoader::__('Exceeded filesize limit', 'revslider'));
		
		$file_mime = mime_content_type($path);
		$allow = array(
			'jpg|jpeg|jpe'	=> 'image/jpeg',
			'gif'			=> 'image/gif',
			'png'			=> 'image/png',
			'bmp'			=> 'image/bmp',
			'mpeg|mpg|mpe'	=> 'video/mpeg',
			'mp4|m4v'		=> 'video/mp4',
			'ogv'			=> 'video/ogg',
			'webm'			=> 'video/webm'
		);
		
		if(!in_array($file_mime, $allow)) return array('error' => RevLoader::__('WordPress doesn\'t allow this filetype', 'revslider'));
		
		$upload_dir = RevLoader::wp_upload_dir();
		
		$new_path = $path;
		$file_name = $this->get_val($import_file, 'name');
		$i = 0;
		while(file_exists($new_path)){
			$i++;
			$new_path = $upload_dir['path']. '/' .$i. '-' .$file_name;
		}
		
		if(move_uploaded_file($path, $new_path)){
			$upload_id = wp_insert_attachment(
				array(
					'guid'			 => $new_path, 
					'post_mime_type' => $file_mime,
					'post_title'	 => preg_replace( '/\.[^.]+$/', '', $file_name),
					'post_name'		 => sanitize_title_with_dashes(str_replace('_', '-', $file_name)),
					'post_content'	 => '',
					'post_status'	 => 'inherit'
				),
				$new_path
			);
			
			require_once(ABSPATH . 'wp-admin/includes/image.php');
		 
			@wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_path));
			
			//$meta = wp_get_attachment_metadata( $attachment->ID );
			
			$img_dim = @wp_get_attachment_image_src($upload_id, 'full');
			$width	= ($img_dim !== false) ? $this->get_val($img_dim, 1, '') : '';
			$height	= ($img_dim !== false) ? $this->get_val($img_dim, 2, '') : '';
			
			$return = array('error' => false, 'id' => $upload_id, 'path' => wp_get_attachment_url($upload_id), 'width' => $width, 'height' => $height); //$new_path
		}
		
		return $return;
	}
	
	public function sort_by_slide_order($a, $b) {
		return $a['slide_order'] - $b['slide_order'];
	}
	
	
	/**
	 * Create Multilanguage for JavaScript
	 */
	public function get_javascript_multilanguage(){
		$lang = array(
			'previewnotworking' => RevLoader::__('The preview could not be loaded due to some conflict with another WordPress theme or plugin', 'revslider'),
			'checksystemnotworking' => RevLoader::__('Server connection issues, contact your hosting provider for further assistance', 'revslider'),
			'editskins' => RevLoader::__('Edit Skin List', 'revslider'),
			'globalcoloractive' => RevLoader::__('Color Skin Active', 'revslider'),
			'corejs' => RevLoader::__('Core JavaScript', 'revslider'),
			'corecss' => RevLoader::__('Core CSS', 'revslider'),
			'coretools' => RevLoader::__('Core Tools (GreenSock & Co)', 'revslider'),
			'enablecompression' => RevLoader::__('Enable Server Compression', 'revslider'),
			'noservercompression' => RevLoader::__('Not Available, read FAQ', 'revslider'),
			'servercompression' => RevLoader::__('Serverside Compression', 'revslider'),
			'sizeafteroptim' => RevLoader::__('Size after Optimization', 'revslider'),
			'chgimgsizesrc' => RevLoader::__('Change Image Size or Src', 'revslider'),
			'pickandim' => RevLoader::__('Pick another Dimension', 'revslider'),
			'optimize' => RevLoader::__('Optimize', 'revslider'),
			'savechanges' => RevLoader::__('Save Changes', 'revslider'),
			'applychanges' => RevLoader::__('Apply Changes', 'revslider'),
			'suggestion' => RevLoader::__('Suggestion', 'revslider'),
			'toosmall' => RevLoader::__('Too Small', 'revslider'),
			'standard1x' => RevLoader::__('Standard (1x)', 'revslider'),
			'retina2x' => RevLoader::__('Retina (2x)', 'revslider'),
			'oversized' => RevLoader::__('Oversized', 'revslider'),
			'quality' => RevLoader::__('Quality', 'revslider'),
			'file' => RevLoader::__('File', 'revslider'),
			'resize' => RevLoader::__('Resize', 'revslider'),
			'lowquality' => RevLoader::__('Optimized (Low Quality)', 'revslider'),
			'notretinaready' => RevLoader::__('Not Retina Ready', 'revslider'),
			'element' => RevLoader::__('Element', 'revslider'),
			'calculating' => RevLoader::__('Calculating...', 'revslider'),
			'filesize' => RevLoader::__('File Size', 'revslider'),
			'dimension' => RevLoader::__('Dimension', 'revslider'),
			'dimensions' => RevLoader::__('Dimensions', 'revslider'),
			'optimization' => RevLoader::__('Optimization', 'revslider'),
			'optimized' => RevLoader::__('Optimized', 'revslider'),
			'smartresize' => RevLoader::__('Smart Resize', 'revslider'),
			'optimal' => RevLoader::__('Optimal', 'revslider'),
			'recommended' => RevLoader::__('Recommended', 'revslider'),
			'hrecommended' => RevLoader::__('Highly Recommended', 'revslider'),
			'optimizertitel' => RevLoader::__('File Size Optimizer', 'revslider'),
			'loadedmediafiles' => RevLoader::__('Loaded Media Files', 'revslider'),
			'loadedmediainfo' => RevLoader::__('Optimize to save up to ', 'revslider'),
			'optselection' => RevLoader::__('Optimize Selection', 'revslider'),
			'visibility' => RevLoader::__('Visibility', 'revslider'),
			'layers' => RevLoader::__('Layers', 'revslider'),
			'videoid' => RevLoader::__('Video ID', 'revslider'),
			'youtubeid' => RevLoader::__('YouTube ID', 'revslider'),
			'vimeoid' => RevLoader::__('Vimeo ID', 'revslider'),
			'poster' => RevLoader::__('Poster', 'revslider'),
			'youtubeposter' => RevLoader::__('YouTube Poster', 'revslider'),
			'vimeoposter' => RevLoader::__('Vimeo Poster', 'revslider'),
			'postersource' => RevLoader::__('Poster Image', 'revslider'),
			'medialibrary' => RevLoader::__('Media Library', 'revslider'),
			'objectlibrary' => RevLoader::__('Object Library', 'revslider'),
			'videosource' => RevLoader::__('Video Source', 'revslider'),
			'imagesource' => RevLoader::__('Image Source', 'revslider'),
			'extimagesource' => RevLoader::__('External Image Source', 'revslider'),
			'mediasrcimage' => RevLoader::__('Image Based', 'revslider'),
			'mediasrcext' => RevLoader::__('External Image', 'revslider'),				
			'mediasrcsolid' => RevLoader::__('Background Color', 'revslider'),
			'mediasrctrans' => RevLoader::__('Transparent', 'revslider'),
			'please_wait_a_moment' => RevLoader::__('Please Wait a Moment', 'revslider'),
			'backgrounds' => RevLoader::__('Backgrounds', 'revslider'),
			'name' => RevLoader::__('Name', 'revslider'),
			'colorpicker' => RevLoader::__('Color Picker', 'revslider'),
			'savecontent' => RevLoader::__('Save Content', 'revslider'),
			'modulbackground' => RevLoader::__('Module Background', 'revslider'),
			'wrappingtag' => RevLoader::__('Wrapping Tag', 'revslider'),
			'tag' => RevLoader::__('Tag', 'revslider'),
			'content' => RevLoader::__('Content', 'revslider'),
			'nolayerstoedit' => RevLoader::__('No Layers to Edit', 'revslider'),
			'layermedia' => RevLoader::__('Layer Media', 'revslider'),
			'oppps' => RevLoader::__('Ooppps....', 'revslider'),
			'no_nav_changes_done' => RevLoader::__('None of the Settings changed. There is Nothing to Save', 'revslider'),
			'no_preset_name' => RevLoader::__('Enter Preset Name to Save or Delete', 'revslider'),
			'customlayergrid_size_title' => RevLoader::__('Custom Size is currently Disabled', 'revslider'),
			'customlayergrid_size_content' => RevLoader::__('The Current Size is set to calculate the Layer grid sizes Automatically.<br>Do you want to continue with Custom Sizes or do you want to keep the Automatically generated sizes ?', 'revslider'),
			'customlayergrid_answer_a' => RevLoader::__('Keep Auto Sizes', 'revslider'),
			'customlayergrid_answer_b' => RevLoader::__('Use Custom Sizes', 'revslider'),
			'removinglayer_title' => RevLoader::__('What should happen Next?', 'revslider'),
			'removinglayer_attention' => RevLoader::__('Need Attention by removing', 'revslider'),
			'removinglayer_content' => RevLoader::__('Where do you want to move the Inherited Layers?', 'revslider'),
			'dragAndDropFile' => RevLoader::__('Drag & Drop Import File', 'revslider'),
			'or' => RevLoader::__('or', 'revslider'),
			'clickToChoose' => RevLoader::__('Click to Choose', 'revslider'),
			'embed' => RevLoader::__('Embed', 'revslider'),
			'export' => RevLoader::__('Export', 'revslider'),
			'delete' => RevLoader::__('Delete', 'revslider'),
			'duplicate' => RevLoader::__('Duplicate', 'revslider'),
			'preview' => RevLoader::__('Preview', 'revslider'),
			'tags' => RevLoader::__('Tags', 'revslider'),
			'folders' => RevLoader::__('Folder', 'revslider'),
			'rename' => RevLoader::__('Rename', 'revslider'),
			'root' => RevLoader::__('Root Level', 'revslider'),
			'simproot' => RevLoader::__('Root', 'revslider'),
			'show' => RevLoader::__('Show', 'revslider'),
			'perpage' => RevLoader::__('Per Page', 'revslider'),
			'convertedlayer' => RevLoader::__('Layer converted Successfully', 'revslider'),
			'layerloopdisabledduetimeline' => RevLoader::__('Layer Loop Effect disabled', 'revslider'),
			'layerbleedsout' => RevLoader::__('<b>Layer width bleeds out of Grid:</b><br>-Auto Layer width has been removed<br>-Line Break set to Content Based', 'revslider'),
			'noMultipleSelectionOfLayers' => RevLoader::__('Multiple Layerselection not Supported<br>in Animation Mode', 'revslider'),
			'closeNews' => RevLoader::__('Close News', 'revslider'),
			'copyrightandlicenseinfo' => RevLoader::__('&copy; Copyright & License Info', 'revslider'),
			'registered' => RevLoader::__('Registered', 'revslider'),
			'notRegisteredNow' => RevLoader::__('Unregistered', 'revslider'),
			'dismissmessages' => RevLoader::__('Dismiss Messages', 'revslider'),
			'someAddonnewVersionAvailable' => RevLoader::__('Some AddOns have new versions available', 'revslider'),
			'newVersionAvailable' => RevLoader::__('New Version Available. Please Update', 'revslider'),
			'addonsmustbeupdated' => RevLoader::__('AddOns Outdated. Please Update', 'revslider'),
			'notRegistered' => RevLoader::__('Plugin is not Registered', 'revslider'),
			'notRegNoPremium' => RevLoader::__('Register to unlock Premium Features', 'revslider'),
			'notRegNoAll' => RevLoader::__('Register to Unlock all Features', 'revslider'),
			'notRegNoAddOns' => RevLoader::__('Register to unlock AddOns', 'revslider'),
			'notRegNoSupport' => RevLoader::__('Register to unlock Support', 'revslider'),
			'notRegNoLibrary' => RevLoader::__('Register to unlock Library', 'revslider'),
			'notRegNoUpdates' => RevLoader::__('Register to unlock Updates', 'revslider'),
			'notRegNoTemplates' => RevLoader::__('Register to unlock Templates', 'revslider'),
			'areyousureupdateplugin' => RevLoader::__('Do you want to start the Update process?', 'revslider'),
			'updatenow' => RevLoader::__('Update Now', 'revslider'),
			'toplevels' => RevLoader::__('Higher Level', 'revslider'),
			'siblings' => RevLoader::__('Current Level', 'revslider'),
			'otherfolders' => RevLoader::__('Other Folders', 'revslider'),
			'parent' => RevLoader::__('Parent Level', 'revslider'),
			'from' => RevLoader::__('from', 'revslider'),
			'to' => RevLoader::__('to', 'revslider'),
			'actionneeded' => RevLoader::__('Action Needed', 'revslider'),
			'updatedoneexist' => RevLoader::__('Done', 'revslider'),
			'updateallnow' => RevLoader::__('Update All', 'revslider'),
			'updatelater' => RevLoader::__('Update Later', 'revslider'),
			'addonsupdatemain' => RevLoader::__('The following AddOns require an update:', 'revslider'),
			'addonsupdatetitle' => RevLoader::__('AddOns need attention', 'revslider'),
			'updatepluginfailed' => RevLoader::__('Updating Plugin Failed', 'revslider'),
			'updatingplugin' => RevLoader::__('Updating Plugin...', 'revslider'),
			'licenseissue' => RevLoader::__('License validation issue Occured. Please contact our Support.', 'revslider'),
			'leave' => RevLoader::__('Back to Overview', 'revslider'),
			'reLoading' => RevLoader::__('Page is reloading...', 'revslider'),
			'updateplugin' => RevLoader::__('Update Plugin', 'revslider'),
			'updatepluginsuccess' => RevLoader::__('Slider Revolution Plugin updated Successfully.', 'revslider'),
			'updatepluginfailure' => RevLoader::__('Slider Revolution Plugin updated Failure:', 'revslider'),
			'updatepluginsuccesssubtext' => RevLoader::__('Slider Revolution Plugin updated Successfully to', 'revslider'),
			'reloadpage' => RevLoader::__('Reload Page', 'revslider'),
			'loading' => RevLoader::__('Loading', 'revslider'),
			'globalcolors' => RevLoader::__('Global Colors', 'revslider'),
			'elements' => RevLoader::__('Elements', 'revslider'),
			'loadingthumbs' => RevLoader::__('Loading Thumbnails...', 'revslider'),
			'jquerytriggered' => RevLoader::__('jQuery Triggered', 'revslider'),
			'atriggered' => RevLoader::__('&lt;a&gt; Tag Link', 'revslider'),
			'firstslide' => RevLoader::__('First Slide', 'revslider'),
			'lastslide' => RevLoader::__('Last Slide', 'revslider'),
			'nextslide' => RevLoader::__('Next Slide', 'revslider'),
			'previousslide' => RevLoader::__('Previous Slide', 'revslider'),
			'somesourceisnotcorrect' => RevLoader::__('Some Settings in Slider <strong>Source may not complete</strong>.<br>Please Complete All Settings in Slider Sources.', 'revslider'),
			'somelayerslocked' => RevLoader::__('Some Layers are <strong>Locked</strong> and/or <strong>Invisible</strong>.<br>Change Status in Timeline.', 'revslider'),
			'editorisLoading' => RevLoader::__('Editor is Loading...', 'revslider'),
			'addingnewblankmodule' => RevLoader::__('Adding new Blank Module...', 'revslider'),
			'opening' => RevLoader::__('Opening', 'revslider'),
			'featuredimages' => RevLoader::__('Featured Images', 'revslider'),
			'images' => RevLoader::__('Images', 'revslider'),
			'none' => RevLoader::__('None', 'revslider'),
			'select' => RevLoader::__('Select', 'revslider'),
			'reset' => RevLoader::__('Reset', 'revslider'),
			'custom' => RevLoader::__('Custom', 'revslider'),
			'out' => RevLoader::__('OUT', 'revslider'),
			'in' => RevLoader::__('IN', 'revslider'),
			'sticky_navigation' => RevLoader::__('Navigation Options', 'revslider'),
			'sticky_slider' => RevLoader::__('Module General Options', 'revslider'),
			'sticky_slide' => RevLoader::__('Slide Options', 'revslider'),
			'sticky_layer' => RevLoader::__('Layer Options', 'revslider'),
			'imageCouldNotBeLoaded' => RevLoader::__('Set a Slide Background Image to use this feature', 'revslider'),
			'oppps' => RevLoader::__('Ooppps....', 'revslider'),
			'no_nav_changes_done' => RevLoader::__('None of the Settings changed. There is Nothing to Save', 'revslider'),
			'no_preset_name' => RevLoader::__('Enter Preset Name to Save or Delete', 'revslider'),
			'customlayergrid_size_title' => RevLoader::__('Custom Size is currently Disabled', 'revslider'),
			'customlayergrid_size_content' => RevLoader::__('The Current Size is set to calculate the Layer grid sizes Automatically.<br>Do you want to continue with Custom Sizes or do you want to keep the Automatically generated sizes ?', 'revslider'),
			'customlayergrid_answer_a' => RevLoader::__('Keep Auto Sizes', 'revslider'),
			'customlayergrid_answer_b' => RevLoader::__('Use Custom Sizes', 'revslider'),
			'removinglayer_title' => RevLoader::__('What should happen Next?', 'revslider'),
			'removinglayer_attention' => RevLoader::__('Need Attention by removing', 'revslider'),
			'removinglayer_content' => RevLoader::__('Where do you want to move the Inherited Layers?', 'revslider'),
			'dragAndDropFile' => RevLoader::__('Drag & Drop Import File', 'revslider'),
			'or' => RevLoader::__('or', 'revslider'),
			'clickToChoose' => RevLoader::__('Click to Choose', 'revslider'),
			'embed' => RevLoader::__('Embed', 'revslider'),
			'export' => RevLoader::__('Export', 'revslider'),
			'exporthtml' => RevLoader::__('HTML', 'revslider'),
			'delete' => RevLoader::__('Delete', 'revslider'),
			'duplicate' => RevLoader::__('Duplicate', 'revslider'),
			'preview' => RevLoader::__('Preview', 'revslider'),
			'tags' => RevLoader::__('Tags', 'revslider'),
			'folders' => RevLoader::__('Folder', 'revslider'),
			'rename' => RevLoader::__('Rename', 'revslider'),
			'root' => RevLoader::__('Root Level', 'revslider'),
			'simproot' => RevLoader::__('Root', 'revslider'),
			'show' => RevLoader::__('Show', 'revslider'),
			'perpage' => RevLoader::__('Per Page', 'revslider'),
			'releaseToAddLayer' => RevLoader::__('Release to Add Layer', 'revslider'),
			'releaseToUpload' => RevLoader::__('Release to Upload file', 'revslider'),
			'moduleZipFile' => RevLoader::__('Module .zip', 'revslider'),
			'importing' => RevLoader::__('Processing Import of', 'revslider'),
			'importfailure' => RevLoader::__('An Error Occured while importing', 'revslider'),
			'successImportFile' => RevLoader::__('File Succesfully Imported', 'revslider'),
			'importReport' => RevLoader::__('Import Report', 'revslider'),
			'updateNow' => RevLoader::__('Update Now', 'revslider'),
			'activateToUpdate' => RevLoader::__('Activate To Update', 'revslider'),
			'activated' => RevLoader::__('Activated', 'revslider'),
			'notActivated' => RevLoader::__('Not Activated', 'revslider'),			
			'embedingLine1' => RevLoader::__('Standard Module Embedding', 'revslider'),
			'embedingLine2' => RevLoader::__('For the <b>pages and posts</b> editor insert the Shortcode:', 'revslider'),
			'embedingLine2a' => RevLoader::__('To Use it as <b>Modal</b> on <b>pages and posts</b> editor insert the Shortcode:', 'revslider'),
			'embedingLine3' => RevLoader::__('From the <b>widgets panel</b> drag the "Revolution Module" widget to the desired sidebar.', 'revslider'),
			'embedingLine4' => RevLoader::__('Advanced Module Embedding', 'revslider'),
			'embedingLine5' => RevLoader::__('For the <b>theme html</b> use:', 'revslider'),
			'embedingLine6' => RevLoader::__('To add the slider only to the homepage, use:', 'revslider'),
			'embedingLine7' => RevLoader::__('To add the slider only to single Pages, use:', 'revslider'),
			'noLayersSelected' => RevLoader::__('Select a Layer', 'revslider'),
			'layeraction_group_link' => RevLoader::__('Link Actions', 'revslider'),
			'layeraction_group_slide' => RevLoader::__('Slide Actions', 'revslider'),
			'layeraction_group_layer' => RevLoader::__('Layer Actions', 'revslider'),
			'layeraction_group_media' => RevLoader::__('Media Actions', 'revslider'),
			'layeraction_group_fullscreen' => RevLoader::__('Fullscreen Actions', 'revslider'),
			'layeraction_group_advanced' => RevLoader::__('Advanced Actions', 'revslider'),
			'layeraction_menu' => RevLoader::__('Menu Link & Scroll', 'revslider'),
			'layeraction_link' => RevLoader::__('Simple Link', 'revslider'),
			'layeraction_callback' => RevLoader::__('Call Back', 'revslider'),
			'layeraction_modal' => RevLoader::__('Open Slider Modal', 'revslider'),
			'layeraction_scroll_under' => RevLoader::__('Scroll below Slider', 'revslider'),
			'layeraction_scrollto' => RevLoader::__('Scroll To ID', 'revslider'),
			'layeraction_jumpto' => RevLoader::__('Jump to Slide', 'revslider'),
			'layeraction_next' => RevLoader::__('Next Slide', 'revslider'),
			'layeraction_prev' => RevLoader::__('Previous Slide', 'revslider'),
			'layeraction_next_frame' => RevLoader::__('Next Frame', 'revslider'),
			'layeraction_prev_frame' => RevLoader::__('Previous Frame', 'revslider'),
			'layeraction_pause' => RevLoader::__('Pause Slider', 'revslider'),
			'layeraction_resume' => RevLoader::__('Play Slide', 'revslider'),
			'layeraction_close_modal' => RevLoader::__('Close Slider Modal', 'revslider'),
			'layeraction_open_modal' => RevLoader::__('Open Slider Modal', 'revslider'),
			'layeraction_toggle_slider' => RevLoader::__('Toggle Slider', 'revslider'),
			'layeraction_start_in' => RevLoader::__('Go to 1st Frame ', 'revslider'),
			'layeraction_start_out' => RevLoader::__('Go to Last Frame', 'revslider'),
			'layeraction_start_frame' => RevLoader::__('Go to Frame "N"', 'revslider'),
			'layeraction_toggle_layer' => RevLoader::__('Toggle 1st / Last Frame', 'revslider'),
			'layeraction_toggle_frames' => RevLoader::__('Toggle "N/M" Frames', 'revslider'),
			'layeraction_start_video' => RevLoader::__('Start Media', 'revslider'),
			'layeraction_stop_video' => RevLoader::__('Stop Media', 'revslider'),
			'layeraction_toggle_video' => RevLoader::__('Toggle Media', 'revslider'),
			'layeraction_mute_video' => RevLoader::__('Mute Media', 'revslider'),
			'layeraction_unmute_video' => RevLoader::__('Unmute Media', 'revslider'),
			'layeraction_toggle_mute_video' => RevLoader::__('Toggle Mute Media', 'revslider'),
			'layeraction_toggle_global_mute_video' => RevLoader::__('Toggle Mute All Media', 'revslider'),
			'layeraction_togglefullscreen' => RevLoader::__('Toggle Fullscreen', 'revslider'),
			'layeraction_gofullscreen' => RevLoader::__('Enter Fullscreen', 'revslider'),
			'layeraction_exitfullscreen' => RevLoader::__('Exit Fullscreen', 'revslider'),
			'layeraction_simulate_click' => RevLoader::__('Simulate Click', 'revslider'),
			'layeraction_toggle_class' => RevLoader::__('Toggle Class', 'revslider'),
			'layeraction_none' => RevLoader::__('Disabled', 'revslider'),
			'backgroundvideo' => RevLoader::__('Background Video', 'revslider'),
			'videoactiveslide' => RevLoader::__('Video in Active Slide', 'revslider'),
			'firstvideo' => RevLoader::__('Video in Active Slide', 'revslider'),
			'triggeredby' => RevLoader::__('Behavior', 'revslider'),
			'addaction' => RevLoader::__('Add Action to ', 'revslider'),
			'ol_images' => RevLoader::__('Images', 'revslider'),
			'ol_layers' => RevLoader::__('Layer Objects', 'revslider'),
			'ol_objects' => RevLoader::__('Objects', 'revslider'),
			'ol_modules' => RevLoader::__('Own Modules', 'revslider'),
			'ol_fonticons' => RevLoader::__('Font Icons', 'revslider'),
			'ol_moduletemplates' => RevLoader::__('Module Templates', 'revslider'),
			'ol_videos' => RevLoader::__('Videos', 'revslider'),
			'ol_svgs' => RevLoader::__('SVG\'s', 'revslider'),
			'ol_favorite' => RevLoader::__('Favorites', 'revslider'),
			'installed' => RevLoader::__('Installed', 'revslider'),
			'notinstalled' => RevLoader::__('Not Installed', 'revslider'),
			'setupnotes' => RevLoader::__('Setup Notes', 'revslider'),
			'requirements' => RevLoader::__('Requirements', 'revslider'),
			'installedversion' => RevLoader::__('Installed Version', 'revslider'),
			'cantpulllinebreakoutside' => RevLoader::__('Use LineBreaks only in Columns', 'revslider'),
			'availableversion' => RevLoader::__('Available Version', 'revslider'),
			'installpackage' => RevLoader::__('Installing Template Package', 'revslider'),
			'installtemplate' => RevLoader::__('Install Template', 'revslider'),
			'installingtemplate' => RevLoader::__('Installing Template', 'revslider'),
			'search' => RevLoader::__('Search', 'revslider'),
			'publish' => RevLoader::__('Publish', 'revslider'),
			'unpublish' => RevLoader::__('Unpublish', 'revslider'),
			'slidepublished' => RevLoader::__('Slide Published', 'revslider'),
			'slideunpublished' => RevLoader::__('Slide Unpublished', 'revslider'),
			'layerpublished' => RevLoader::__('Layer Published', 'revslider'),
			'layerunpublished' => RevLoader::__('Layer Unpublished', 'revslider'),
			'folderBIG' => RevLoader::__('FOLDER', 'revslider'),
			'moduleBIG' => RevLoader::__('MODULE', 'revslider'),
			'objectBIG' => RevLoader::__('OBJECT', 'revslider'),
			'packageBIG' => RevLoader::__('PACKAGE', 'revslider'),
			'thumbnail' => RevLoader::__('Thumbnail', 'revslider'),
			'imageBIG' => RevLoader::__('IMAGE', 'revslider'),
			'videoBIG' => RevLoader::__('VIDEO', 'revslider'),
			'iconBIG' => RevLoader::__('ICON', 'revslider'),
			'svgBIG' => RevLoader::__('SVG', 'revslider'),
			'fontBIG' => RevLoader::__('FONT', 'revslider'),
			'redownloadTemplate' => RevLoader::__('Re-Download Online', 'revslider'),
			'createBlankPage' => RevLoader::__('Create Blank Page', 'revslider'),
			'please_wait_a_moment' => RevLoader::__('Please Wait a Moment', 'revslider'),
			'changingscreensize' => RevLoader::__('Changing Screen Size', 'revslider'),
			'qs_headlines' => RevLoader::__('Headlines', 'revslider'),
			'qs_content' => RevLoader::__('Content', 'revslider'),
			'qs_buttons' => RevLoader::__('Buttons', 'revslider'),
			'qs_bgspace' => RevLoader::__('BG & Space', 'revslider'),
			'qs_shadow' => RevLoader::__('Shadow', 'revslider'),
			'qs_shadows' => RevLoader::__('Shadow', 'revslider'),
			'saveslide' => RevLoader::__('Saving Slide', 'revslider'),
			'loadconfig' => RevLoader::__('Loading Configuration', 'revslider'),
			'updateselects' => RevLoader::__('Updating Lists', 'revslider'),
			'lastslide' => RevLoader::__('Last Slide', 'revslider'),
			'textlayers' => RevLoader::__('Text Layers', 'revslider'),
			'globalLayers' => RevLoader::__('Global Layers', 'revslider'),
			'slidersettings' => RevLoader::__('Slider Settings', 'revslider'),
			'animatefrom' => RevLoader::__('Animate From', 'revslider'),
			'animateto' => RevLoader::__('Keyframe #', 'revslider'),
			'transformidle' => RevLoader::__('Transform Idle', 'revslider'),
			'enterstage' => RevLoader::__('Anim From', 'revslider'),
			'leavestage' => RevLoader::__('Anim To', 'revslider'),
			'onstage' => RevLoader::__('Anim To', 'revslider'),	
			'keyframe' => RevLoader::__('Keyframe', 'revslider'),
			'notenoughspaceontimeline' => RevLoader::__('Not Enough space between Frames.', 'revslider'),
			'framesizecannotbeextended' => RevLoader::__('Frame Size can not be Extended. Not enough Space.', 'revslider'),
			'backupTemplateLoop' => RevLoader::__('Loop Template', 'revslider'),
			'backupTemplateLayerAnim' => RevLoader::__('Animation Template', 'revslider'),
			'choose_image' => RevLoader::__('Choose Image', 'revslider'),
			'choose_video' => RevLoader::__('Choose Video', 'revslider'),
			'slider_revolution_shortcode_creator' => RevLoader::__('Slider Revolution Shortcode Creator', 'revslider'),
			'shortcode_generator' => RevLoader::__('Shortcode Generator', 'revslider'),
			'please_add_at_least_one_layer' => RevLoader::__('Please add at least one Layer.', 'revslider'),
			'shortcode_parsing_successfull' => RevLoader::__('Shortcode parsing successfull. Items can be found in step 3', 'revslider'),
			'shortcode_could_not_be_correctly_parsed' => RevLoader::__('Shortcode could not be parsed.', 'revslider'),
			'addonrequired' => RevLoader::__('Addon Required', 'revslider'),
			'licencerequired' => RevLoader::__('Activate License', 'revslider'),
			'searcforicon' => RevLoader::__('Search Icons...', 'revslider'),
			'savecurrenttemplate' => RevLoader::__('Save Current Template', 'revslider'),
			'overwritetemplate' => RevLoader::__('Overwrite Template ?', 'revslider'),
			'deletetemplate' => RevLoader::__('Delete Template ?', 'revslider'),
			'credits' => RevLoader::__('Credits', 'revslider'),
			'notinstalled' => RevLoader::__('Not Installed', 'revslider'),
			'enabled' => RevLoader::__('Enabled', 'revslider'),
			'global' => RevLoader::__('Global', 'revslider'),
			'install_and_activate' => RevLoader::__('Install Add-On', 'revslider'),
			'install' => RevLoader::__('Install', 'revslider'),
			'enableaddon' => RevLoader::__('Enable Add-On', 'revslider'),
			'disableaddon' => RevLoader::__('Disable Add-On', 'revslider'),
			'enableglobaladdon' => RevLoader::__('Enable Global Add-On', 'revslider'),
			'disableglobaladdon' => RevLoader::__('Disable Global Add-On', 'revslider'),
			'sliderrevversion' => RevLoader::__('Slider Revolution Version', 'revslider'),
			'checkforrequirements' => RevLoader::__('Check Requirements', 'revslider'),
			'activateglobaladdon' => RevLoader::__('Activate Global Add-On', 'revslider'),
			'activateaddon' => RevLoader::__('Activate Add-On', 'revslider'),
			'activatingaddon' => RevLoader::__('Activating Add-On', 'revslider'),
			'enablingaddon' => RevLoader::__('Enabling Add-On', 'revslider'),
			'addon' => RevLoader::__('Add-On', 'revslider'),
			'installingaddon' => RevLoader::__('Installing Add-On', 'revslider'),
			'disablingaddon' => RevLoader::__('Disabling Add-On', 'revslider'),
			'buildingSelects' => RevLoader::__('Building Select Boxes', 'revslider'),
			'warning' => RevLoader::__('Warning', 'revslider'),
			'blank_page_added' => RevLoader::__('Blank Page Created', 'revslider'),
			'blank_page_created' => RevLoader::__('Blank page has been created:', 'revslider'),
			'visit_page' => RevLoader::__('Visit Page', 'revslider'),
			'edit_page' => RevLoader::__('Edit Page', 'revslider'),
			'closeandstay' => RevLoader::__('Close', 'revslider'),
			'changesneedreload' => RevLoader::__('The changes you made require a page reload!', 'revslider'),
			'saveprojectornot ' => RevLoader::__('Save your project & reload the page or cancel', 'revslider'),
			'saveandreload' => RevLoader::__('Save & Reload', 'revslider'),
			'canceldontreload' => RevLoader::__('Cancel & Reload Later', 'revslider'),
			'saveconfig' => RevLoader::__('Save Configuration', 'revslider'),
			'updatingaddon' => RevLoader::__('Updating', 'revslider'),
			'addonOnlyInSlider' => RevLoader::__('Enable/Disable Add-On on Module', 'revslider'),
			'openQuickEditor' => RevLoader::__('Open Quick Content Editor', 'revslider'),
			'openQuickStyleEditor' => RevLoader::__('Open Quick Style Editor', 'revslider'),
			'sortbycreation' => RevLoader::__('Sort by Creation', 'revslider'),
			'creationascending' => RevLoader::__('Creation Ascending', 'revslider'),
			'sortbytitle' => RevLoader::__('Sort by Title', 'revslider'),
			'titledescending' => RevLoader::__('Title Descending', 'revslider'),
			'updatefromserver' => RevLoader::__('Update List', 'revslider'),
			'audiolibraryloading' => RevLoader::__('Audio Wave Library is Loading...', 'revslider'),
			'editModule' => RevLoader::__('Edit Module', 'revslider'),
			'editSlide' => RevLoader::__('Edit Slide', 'revslider'),
			'showSlides' => RevLoader::__('Show Slides', 'revslider'),
			'openInEditor' => RevLoader::__('Open in Editor', 'revslider'),
			'openFolder' => RevLoader::__('Open Folder', 'revslider'),
			'moveToFolder' => RevLoader::__('Move to Folder', 'revslider'),
			'loadingcodemirror' => RevLoader::__('Loading CodeMirror Library...', 'revslider'),
			'lockunlocklayer' => RevLoader::__('Lock / Unlock Selected', 'revslider'),
			'nrlayersimporting' => RevLoader::__('Layers Importing', 'revslider'),
			'nothingselected' => RevLoader::__('Nothing Selected', 'revslider'),
			'layerwithaction' => RevLoader::__('Layer with Action', 'revslider'),
			'imageisloading' => RevLoader::__('Image is Loading...', 'revslider'),
			'importinglayers' => RevLoader::__('Importing Layers...', 'revslider'),
			'triggeredby' => RevLoader::__('Triggered By', 'revslider'),
			'import' => RevLoader::__('Imported', 'revslider'),
			'layersBIG' => RevLoader::__('LAYERS', 'revslider'),
			'intinheriting' => RevLoader::__('Responsivity', 'revslider'),
			'changesdone_exit' => RevLoader::__('The changes you made will be lost!', 'revslider'),
			'exitwihoutchangesornot' => RevLoader::__('Are you sure you want to continue?', 'revslider'),
			'areyousuretoexport' => RevLoader::__('Are you sure you want to export ', 'revslider'),
			'areyousuretodelete' => RevLoader::__('Are you sure you want to delete ', 'revslider'),
			'areyousuretodeleteeverything' => RevLoader::__('Delete All Sliders and Folders included in ', 'revslider'),
			'leavewithoutsave' => RevLoader::__('Leave without Save', 'revslider'), 
			'updatingtakes' => RevLoader::__('Updating the Plugin may take a few moments.', 'revslider'),
			'exportslidertxt' => RevLoader::__('Downloading the Zip File may take a few moments.', 'revslider'),
			'exportslider' => RevLoader::__('Export Slider', 'revslider'),
			'yesexport' => RevLoader::__('Yes, Export Slider', 'revslider'),
			'yesdelete' => RevLoader::__('Yes, Delete Slider', 'revslider'),
			'yesdeleteslide' => RevLoader::__('Yes, Delete Slide', 'revslider'),
			'yesdeleteall' => RevLoader::__('Yes, Delete All Slider(s)', 'revslider'),
			'stayineditor' => RevLoader::__('Stay in Edior', 'revslider'),
			'redirectingtooverview' => RevLoader::__('Redirecting to Overview Page', 'revslider'),
			'leavingpage' => RevLoader::__('Leaving current Page', 'revslider'),
			'ashtmlexport' => RevLoader::__('as HTML Document', 'revslider'),
			'preparingdatas' => RevLoader::__('Preparing Data...', 'revslider'),
			'loadingcontent' => RevLoader::__('Loading Content...', 'revslider'),
			'copy' => RevLoader::__('Copy', 'revslider'),
			'paste' => RevLoader::__('Paste', 'revslider'),
			'framewait' => RevLoader::__('WAIT', 'revslider'),
			'frstframe' => RevLoader::__('1st Frame', 'revslider'),
			'lastframe' => RevLoader::__('Last Frame', 'revslider'),
			'onlyonaction' => RevLoader::__('on Action', 'revslider'),
			'cannotbeundone' => RevLoader::__('This action can not be undone !!', 'revslider'),
			'deleteslider' => RevLoader::__('Delete Slider', 'revslider'),
			'deleteslide' => RevLoader::__('Delete Slide', 'revslider'),
			'deletingslide' => RevLoader::__('This can be Undone only within the Current session.', 'revslider'),
			'deleteselectedslide' => RevLoader::__('Are you sure you want to delete the selected Slide:', 'revslider'),
			'cancel' => RevLoader::__('Cancel', 'revslider'),
			'addons' => RevLoader::__('Add-Ons', 'revslider'),
			'deletingsingleslide' => RevLoader::__('Deleting Slide', 'revslider'),
			'lastslidenodelete' => RevLoader::__('"Last Slide in Module. Can not be deleted"', 'revslider'),
			'deletingslider' => RevLoader::__('Deleting Slider', 'revslider'),
			'active_sr_tmp_obl' => RevLoader::__('Template & Object Library', 'revslider'),
			'active_sr_inst_upd' => RevLoader::__('Instant Updates', 'revslider'),
			'active_sr_one_on_one' => RevLoader::__('1on1 Support', 'revslider'),			
			'parallaxsettoenabled' => RevLoader::__('Parallax is now generally Enabled', 'revslider'),
			'timelinescrollsettoenabled' => RevLoader::__('Scroll Based Timeline is now generally Enabled', 'revslider'),
			'feffectscrollsettoenabled' => RevLoader::__('Filter Effect Scroll is now generally Enabled', 'revslider'),
			'nolayersinslide' => RevLoader::__('Slide has no Layers', 'revslider'),
			'leaving' => RevLoader::__('Changes that you made may not be saved.', 'revslider'),
			'sliderasmodal' => RevLoader::__('Add Slider as Modal', 'revslider'),
			'register_to_unlock' => RevLoader::__('Register to unlock all Premium Features', 'revslider'),
			'premium_features_unlocked' => RevLoader::__('All Premium Features unlocked', 'revslider'),
			'tryagainlater' => RevLoader::__('Please try again later', 'revslider'),
			'quickcontenteditor' => RevLoader::__('Quick Content Editor', 'revslider'),
			'module' => RevLoader::__('Module', 'revslider'),
			'quickstyleeditor' => RevLoader::__('Quick Style Editor', 'revslider'),
			'all' => RevLoader::__('All', 'revslider'),
			'active_sr_to_access' => RevLoader::__('Register Slider Revolution<br>to Unlock Premium Features', 'revslider'),
			'membersarea' => RevLoader::__('Members Area', 'revslider'),
			'onelicensekey' => RevLoader::__('1 License Key per Website!', 'revslider'),
			'onepurchasekey' => RevLoader::__('1 Purchase Code per Website!', 'revslider'),
			'onelicensekey_info' => RevLoader::__('If you want to use your license key on another domain, please<br> deregister it in the members area or use a different key.', 'revslider'),
			'onepurchasekey_info' => RevLoader::__('If you want to use your purchase code on<br>another domain, please deregister it first or', 'revslider'),
			'registeredlicensekey' => RevLoader::__('Registered License Key', 'revslider'),
			'registeredpurchasecode' => RevLoader::__('Registered Purchase Code', 'revslider'),
			'registerlicensekey' => RevLoader::__('Register License Key', 'revslider'),
			'registerpurchasecode' => RevLoader::__('Register Purchase Code', 'revslider'),
			'registerCode' => RevLoader::__('Register this Code', 'revslider'),
			'registerKey' => RevLoader::__('Register this License Key', 'revslider'),
			'deregisterCode' => RevLoader::__('Deregister this Code', 'revslider'),
			'deregisterKey' => RevLoader::__('Deregister this License Key', 'revslider'),
			'active_sr_plg_activ' => RevLoader::__('Register Purchase Code', 'revslider'),
			'active_sr_plg_activ_key' => RevLoader::__('Register License Key', 'revslider'),
			'getpurchasecode' => RevLoader::__('Get a Purchase Code', 'revslider'),
			'getlicensekey' => RevLoader::__('Licensing Options', 'revslider'),
			'ihavepurchasecode' => RevLoader::__('I have a Purchase Code', 'revslider'),
			'ihavelicensekey' => RevLoader::__('I have a License Key', 'revslider'),
			'enterlicensekey' => RevLoader::__('Enter License Key', 'revslider'),
			'enterpurchasecode' => RevLoader::__('Enter Purchase Code', 'revslider'),
			'colrskinhas' => RevLoader::__('This Skin use', 'revslider'),
			'deleteskin' => RevLoader::__('Delete Skin', 'revslider'),
			'references' => RevLoader::__('References', 'revslider'),
			'colorwillkept' => RevLoader::__('The References will keep their colors after deleting Skin.', 'revslider'),
			'areyousuredeleteskin' => RevLoader::__('Are you sure to delete Color Skin?', 'revslider'),

			
		);

		return RevLoader::apply_filters('revslider_get_javascript_multilanguage', $lang);
	}

	
	/**
	 * returns all image sizes that have the same aspect ratio, rounded on the second
	 * @since: 6.1.4
	 **/
	public function get_same_aspect_ratio_images($images){
		$return = array();
		$images = (array)$images;
		
		if(!empty($images)){
			$objlib = new RevSliderObjectLibrary();
			$upload_dir = RevLoader::wp_upload_dir();
			
			foreach($images as $key => $image){
				//check if we are from object library
				if($objlib->_is_object($image)){
					$_img = $image;
					$image = $objlib->get_correct_size_url($image, 100, true);
					$objlib->_check_object_exist($image); //check to redownload if not downloaded yet
					
					$sizes = $objlib->get_sizes();
					$return[$key] = array();
					
					if(!empty($sizes)){
						foreach($sizes as $size){
							$url = $objlib->get_correct_size_url($image, $size);
							$file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);
							$_size = getimagesize($file);
							$return[$key][$size] = array(
								'url'	=> $url,
								'width'	=> $this->get_val($_size, 0),
								'height'=> $this->get_val($_size, 1),
								'size'	=> filesize($file)
							);
							
							if($_img === $url) $return[$key][$size]['default'] = true;
						}
						
						//$image = $objlib->get_correct_size_url($image, 100, true);
						$file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image);
						$_size = getimagesize($file);
						$return[$key][100] = array(
							'url'	=> $image,
							'width'	=> $this->get_val($_size, 0),
							'height'=> $this->get_val($_size, 1),
							'size'	=> filesize($file)
						);
						if($_img === $return[$key][100]['url']) $return[$key][100]['default'] = true;
					}
				}else{
					$_img = (intval($image) === 0) ? $this->get_image_id_by_url($image) : $image;
					//track
					//$img_data = wp_get_attachment_metadata($_img);
					$img_data = '';
					
					if(!empty($img_data)){
						$return[$key] = array();
						$ratio = round($this->get_val($img_data, 'width', 1) / $this->get_val($img_data, 'height', 1), 2);
						$sizes = $this->get_val($img_data, 'sizes', array());
						$file = $upload_dir['basedir'] .'/'. $this->get_val($img_data, 'file');
						$return[$key]['orig'] = array(
							'url'	=> $upload_dir['baseurl'] .'/'. $this->get_val($img_data, 'file'),
							'width'	=> $this->get_val($img_data, 'width'),
							'height'=> $this->get_val($img_data, 'height'),
							'size'	=> filesize($file)
						);
						if($image === $return[$key]['orig']['url']) $return[$key]['orig']['default'] = true;
						
						if(!empty($sizes)){
							foreach($sizes as $sn => $sv){
								$_ratio = round($this->get_val($sv, 'width', 1) / $this->get_val($sv, 'height', 1), 2);
								if($_ratio === $ratio){
									$i = wp_get_attachment_image_src($_img, $sn);
									if($i === false) continue;
									
									$file = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $this->get_val($i, 0));
									$return[$key][$sn] = array(
										'url'	=> $this->get_val($i, 0),
										'width'	=> $this->get_val($sv, 'width'),
										'height'=> $this->get_val($sv, 'height'),
										'size'	=> filesize($file)
									);
									if($image === $return[$key][$sn]['url']) $return[$key][$sn]['default'] = true;
								}
							}
						}
					}else{
						//either external URL or not available anymore in the media library
					}
				}
			}
		}
		
		return $return;
	}
	
	/** 
	 * returns all files plus sizes of JavaScript and css files used by the AddOns
	 * @since. 6.1.4
	 **/
	public function get_addon_sizes($addons){
		$sizes = array();
		
		if(empty($addons) || !is_array($addons)) return $sizes;
		
		$_css = '/public/assets/css/';
		$_js = '/public/assets/js/';
		//these are the sizes before the AddOns where updated
		$_a = array(
			'revslider-404-addon' => array(),
			'revslider-backup-addon' => array(),
			'revslider-beforeafter-addon' => array(
				$_css .'revolution.addon.beforeafter.css' => 3512,
				$_js .'revolution.addon.beforeafter.min.js' => 21144
			),
			'revslider-bubblemorph-addon' => array(
				$_css .'revolution.addon.bubblemorph.css' => 341,
				$_js .'revolution.addon.bubblemorph.min.js' => 11377
			),
			'revslider-domain-switch-addon' => array(),
			'revslider-duotonefilters-addon' => array(
				$_css .'revolution.addon.duotone.css' => 11298,
				$_js .'revolution.addon.duotone.min.js' => 1232
			),
			'revslider-explodinglayers-addon' => array(
				$_css .'revolution.addon.explodinglayers.css' => 704,
				$_js .'revolution.addon.explodinglayers.min.js' => 19012
			),
			'revslider-featured-addon' => array(),
			'revslider-filmstrip-addon' => array(
				$_css .'revolution.addon.filmstrip.css' => 843,
				$_js .'revolution.addon.filmstrip.min.js' => 5409
			),
			'revslider-gallery-addon' => array(),
			'revslider-liquideffect-addon' => array(
				$_css .'revolution.addon.liquideffect.css' => 606,
				$_js .'pixi.min.js' => 514062,
				$_js .'revolution.addon.liquideffect.min.js' => 11899
			),
			'revslider-login-addon' => array(),
			'revslider-maintenance-addon' => array(),
			'revslider-paintbrush-addon' => array(
				$_css .'revolution.addon.paintbrush.css' => 676,
				$_js .'revolution.addon.paintbrush.min.js' => 6841
			),
			'revslider-panorama-addon' => array(
				$_css .'revolution.addon.panorama.css' => 1823,
				$_js .'three.min.js' => 504432,
				$_js .'revolution.addon.panorama.min.js' => 12909
			),
			'revslider-particles-addon' => array(
				$_css .'revolution.addon.particles.css' => 668,
				$_js .'revolution.addon.particles.min.js' => 33963
			),
			'revslider-polyfold-addon' => array(
				$_css .'revolution.addon.polyfold.css' => 900,
				$_js .'revolution.addon.polyfold.min.js' => 5125
			),
			'revslider-prevnext-posts-addon' => array(),
			'revslider-refresh-addon' => array(
				$_js .'revolution.addon.refresh.min.js' => 920
			),
			'revslider-rel-posts-addon' => array(),
			'revslider-revealer-addon' => array(
				$_css .'revolution.addon.revealer.css' => 792,
				$_css .'revolution.addon.revealer.preloaders.css' => 14792,
				$_js .'revolution.addon.revealer.min.js' => 7533
			),
			'revslider-sharing-addon' => array(
				$_js .'revslider-sharing-addon-public.js' => 6232
			),
			'revslider-slicey-addon' => array(
				$_js .'revolution.addon.slicey.min.js' => 4772
			),
			'revslider-snow-addon' => array(
				$_js .'revolution.addon.snow.min.js' => 4823
			),
			'revslider-template-addon' => array(),
			'revslider-typewriter-addon' => array(
				$_css .'typewriter.css' => 233,
				$_js .'revolution.addon.typewriter.min.js' => 8038
			),
			'revslider-weather-addon' => array(
				$_css .'revslider-weather-addon-icon.css' => 3699,
				$_css .'revslider-weather-addon-public.css' => 483,
				$_css .'weather-icons.css' => 31082,
				$_js .'revslider-weather-addon-public.js' => 5335
			),
			'revslider-whiteboard-addon' => array(
				$_js .'revolution.addon.whiteboard.min.js' => 10649
			)
		);
		
		//AddOns can apply/modify the default data here
		$_a = RevLoader::apply_filters('revslider_create_slider_page', $_a, $_css, $_js, $this);
		
		foreach($addons as $addon){
			if(!isset($_a[$addon])) continue;
			$sizes[$addon] = 0;
			if(!empty($_a[$addon])){
				foreach($_a[$addon] as $size){
					$sizes[$addon] += $size;
				}
			}
			//$sizes[$addon] = $_a[$addon];
		}
		
		return $sizes;
	}
	
	/** 
	 * returns a list of found compressions
	 * @since. 6.1.4
	 **/
	public function compression_settings(){
		$match	= array();
		$com	= array('gzip', 'compress', 'deflate', 'br'); //'identity' -> means no compression prefered
		$enc	= $this->get_val($_SERVER, 'HTTP_ACCEPT_ENCODING');
		
		if(empty($enc)) return $match;
		
		foreach($com as $c){
			if(strpos($enc, $c) !== false) $match[] = $c;
		}
		
		return $match;
	}
	
	/**
	 * get all available languages from Slider Revolution
	 **/
	public function get_available_languages(){

		//track
		return RS_PLUGIN_PATH.'languages/';

		$lang_codes = array(
			'de_DE' => RevLoader::__('German', 'revslider'),
			'en_US' => RevLoader::__('English', 'revslider'),
			'fr_FR' => RevLoader::__('French', 'revslider'),
			'zh_CN' => RevLoader::__('Chinese', 'revslider')
		);
		
		$lang = get_available_languages(RS_PLUGIN_PATH.'languages/');
		$_lang = array();
		if(!empty($lang)){
			foreach($lang as $k => $v){
				if(strpos($v, 'revsliderhelp-') !== false) continue;
				
				$_lc = str_replace('revslider-', '', $v);
				$_lang[$_lc] = (isset($lang_codes[$_lc])) ? $lang_codes[$_lc] : $_lc;
			}
		}
		
		return $_lang;
	}
}
?>