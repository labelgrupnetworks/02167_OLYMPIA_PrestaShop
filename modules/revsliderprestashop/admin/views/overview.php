<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$rs_slider     = new RevSliderFunctionsAdmin();
$overview_data = $rs_slider->get_slider_overview();

$system_config = $rs_slider->get_system_requirements();
// track
// $current_user    = wp_get_current_user();
$current_user   = 'shahin';
$latest_version = RevLoader::get_option( 'revslider-latest-version', RS_REVISION );
$stable_version = RevLoader::get_option( 'revslider-stable-version', '4.2' );
$code           = RevLoader::get_option( 'revslider-code', '' );
$time           = date( 'H' );
$timezone       = date( 'e' );/* Set the $timezone variable to become the current timezone */
$hi             = RevLoader::__( 'Good Evening ', 'revslider' );
$selling        = $rsaf->get_addition( 'selling' );
if ( $time < '12' ) {
	$hi = RevLoader::__( 'Good Morning ', 'revslider' );
} elseif ( $time >= '12' && $time < '17' ) {
	$hi = RevLoader::__( 'Good Afternoon ', 'revslider' );
}
$rs_languages = $rs_slider->get_available_languages();


?>
<div id="rb_tlw">
    <?php
	// INCLUDE NEEDED CONTAINERS
	require_once RS_PLUGIN_PATH . 'admin/views/modals-general.php';

	require_once RS_PLUGIN_PATH . 'admin/views/modals-overview.php';

	require_once RS_PLUGIN_PATH . 'admin/views/modals-copyright.php';
	?>
</div>


<div id="rs_overview_menu" class="_TPRB_">
    <div class="rso_scrollmenuitem" data-ref="#rs_overview"><i
            class="material-icons">view_module</i><?php RevLoader::_e( 'Modules', 'revslider' ); ?></div>
    <div class="rso_scrollmenuitem" data-ref="#plugin_update_row"><i
            class="material-icons">update</i><?php RevLoader::_e( 'Updates', 'revslider' ); ?></div>
    <div class="rso_scrollmenuitem" data-ref="#plugin_activation_row"><i
            class="material-icons">vpn_key</i><?php RevLoader::_e( 'Activation', 'revslider' ); ?></div>
    <div class="rso_scrollmenuitem" data-ref="#plugin_news_row"><i class="material-icons">library_books</i><?php RevLoader::_e('Our Products', 'revslider'); ?>
    </div>
    <div id="globalsettings" class="rso_scrollmenuitem"><i
            class="material-icons">settings</i><?php RevLoader::_e( 'Globals', 'revslider' ); ?></div>
    <div id="linktodocumentation" class="rso_scrollmenuitem"><i
            class="material-icons">chrome_reader_mode</i><?php RevLoader::_e( 'FAQ\'s', 'revslider' ); ?></div>
    <div id="contactsupport" class="rso_scrollmenuitem"><i
            class="material-icons">contact_support</i><?php RevLoader::_e( 'Support', 'revslider' ); ?></div>
    <div class="rso_scrollmenuitem" id="rso_menu_notices">
        <div id="rs_notice_bell" class="notice_level_2"><i id="rs_notice_the_bell"
                class="material-icons">notifications_active</i></div>
        <div class="notice_level_2" id="rs_notice_counter">0</div>
        <ul id="rs_notices_wrapper"></ul>
    </div>
</div>
<div id="rs_overview" class="rs_overview _TPRB_">
    <div id="rsalienfakeplaceholder"></div>
    <!-- WELCOME TO SLIDER REVOLUTION -->
    <div id="rs_welcome_header_area">

        <h2 id="rs_welcome_h2" class="title"><?php echo $hi; // track echo $current_user->display_name; echo '!'; ?>
        </h2>
        <h3 id="rs_welcome_h3" class="subtitle">
            <?php
		RevLoader::_e( 'You are running Slider Revolution ', 'revslider' );
		echo RS_REVISION;
		?>
        </h3>
        <h3 id="rs_welcome_h3" class="crazy-advertisement">
            <?php
		RevLoader::_e( 'Show Slider Anywhere You Want With Crazyelements Module', 'revslider' );
		?>
            <a id="rs_crazyarea_registered"
                href="https://classydevs.com/crazy-elements/pricing/?utm_source=revsix_backoffice&utm_medium=revsix_backoffice&utm_campaign=revsix_backoffice&utm_id=revsix_backoffice&utm_term=revsix_backoffice&utm_content=revsix_backoffice"
                class="basic_action_button longbutton basic_action_lilabutton"
                target="_blank"><?php RevLoader::_e( 'GET FREE!!!', 'revslider' ); ?></a>
        </h3>
        <?php if ( $selling === true ) { ?>
        <a href="https://sliderrevolution.com/members-login/" target="_blank" id="rs_memarea_registered"
            class="basic_action_button longbutton basic_action_lilabutton"><i
                class="material-icons">person_outline</i><?php RevLoader::_e( 'Members Area', 'revslider' ); ?></a>
        <!-- <a href="https://sliderrevolution.com/members-login/" target="_blank" id="rs_memarea"></a>                      -->
        <?php } ?>
    </div>

    <!-- CREATE YOUR SLIDERS -->
    <div id="add_new_slider_wrap">
        <div id="new_blank_slider" class="new_slider_block"><i class="material-icons">movie_filter</i><span
                class="nsb_title"><?php RevLoader::_e( 'New Blank Module', 'revslider' ); ?></span></div>
        <!--
		-->
        <div id="new_slider_from_template" class="new_slider_block"><i class="material-icons">style</i><span
                class="nsb_title"><?php RevLoader::_e( 'New Module from Template', 'revslider' ); ?></span>
            <div id="new_templates_counter" class="new_elements_available">+ 13</div>
        </div>
        <!--
		-->
        <div id="new_slider_import" class="new_slider_block"><i class="material-icons">file_upload</i><span
                class="nsb_title"><?php RevLoader::_e( 'Manual Import', 'revslider' ); ?></span></div>
        <!--
		-->
        <div id="add_on_management" class="new_slider_block"><i class="material-icons">extension</i><span
                class="nsb_title"><?php RevLoader::_e( 'AddOns', 'revslider' ); ?></span>
            <div id="new_addons_counter" class="new_elements_available">+19</div>
        </div>
    </div>

    <!--LIST AND FILTER OF EXISTIN SLIDERS-->
    <div id="existing_sliders" class="overview_wrap">
        <div id="modulesoverviewheader" class="overview_header">
            <div class="rs_fh_left"><input class="flat_input" id="searchmodules" type="text"
                    placeholder="<?php RevLoader::_e( 'Search Modules...', 'revslider' ); ?>" /></div>
            <div class="rs_fh_right">
                <i class="material-icons reset_select" id="reset_sorting">replay</i><select id="sel_overview_sorting"
                    data-evt="updateSlidersOverview" data-evtparam="#reset_sorting"
                    class="overview_sortby tos2 nosearchbox callEvent" data-theme="autowidth">
                    <option value="datedesc"><?php RevLoader::_e( 'Sort by Creation', 'revslider' ); ?></option>
                    <option value="date"><?php RevLoader::_e( 'Creation Ascending', 'revslider' ); ?></option>
                    <option value="title"><?php RevLoader::_e( 'Sort by Title', 'revslider' ); ?></option>
                    <option value="titledesc"><?php RevLoader::_e( 'Title Descending', 'revslider' ); ?></option>
                </select>
                <i class="material-icons reset_select" id="reset_filtering">replay</i><select
                    id="sel_overview_filtering" data-evt="updateSlidersOverview" data-evtparam="#reset_filtering"
                    class="overview_filterby tos2 nosearchbox callEvent" data-theme="autowidth">
                    <option value="all"><?php RevLoader::_e( 'Show all Modules', 'revslider' ); ?></option>
                </select>
                <div data-evt="updateSlidersOverview" id="add_folder" class="action_button">
                    <?php RevLoader::_e( 'Add Folder', 'revslider' ); ?><i class="material-icons">add</i></div>
                <div style="margin-left:30px;" data-evt="updateSlidersOverview" id="add_hook" class="action_button">
                    <?php RevLoader::_e( 'Add Hook', 'revslider' ); ?><i class="material-icons">add</i></div>
            </div>
            <div class="tp-clearfix"></div>
        </div>
        <div class="div15"></div>
        <div class="overview_elements" style="z-index:2">
            <div class="overview_elements_overlay"></div>
        </div>
        <div class="overview_slide_elements" style="z-index:1">
            <div class="overview_slide_elements_overlay"></div>
            <div id="modulesoverviewfooter" class="overview_header_footer">
                <div class="rs_fh_right">
                    <div class="ov-pagination"></div>
                    <select id="pagination_select_2" data-evt="updateSlidersOverview"
                        class="overview_pagination tos2 nosearchbox callEvent" data-theme="nomargin">
                        <option id="page_per_page_0" value="4"></option>
                        <option id="page_per_page_1" selected="selected" value="8"></option>
                        <option id="page_per_page_2" value="16"></option>
                        <option id="page_per_page_3" value="32"></option>
                        <option id="page_per_page_4" value="64"></option>
                        <option value="all"><?php RevLoader::_e( 'Show All', 'revslider' ); ?></option>
                    </select>
                </div>
                <div class="tp-clearfix"></div>
            </div>
            <!-- FOLDER LIST -->
            <div id="slider_folders_wrap"></div>
            <div id="slider_folders_wrap_underlay"></div>
        </div>



        <div class="div150"></div>
        <!-- PLUGIN INFORMATIONS -->
        <div id="plugin_update_row" class="plugin_inforow">
            <!-- PLUGIN UPDATE -->
            <div class="pli_left">
                <h3 class="pli_title"><?php RevLoader::_e( 'Plugin Updates', 'revslider' ); ?></h3>
                <grayiconbox><i class="material-icons">flag</i></grayiconbox>
                <div class="pli_twoline">
                    <div class="pli_subtitle"><?php RevLoader::_e( 'Installed Version', 'revslider' ); ?></div>
                    <div class="dynamicval pli_subtitle"><?php echo RS_REVISION; ?></div>
                </div>
                <div class="div10"></div>
                <grayiconbox id="available_version_icon"><i class="material-icons">cloud_download</i></grayiconbox>
                <div id="available_version_content" class="pli_twoline">
                    <div class="pli_subtitle"><?php RevLoader::_e( 'Available Version', 'revslider' ); ?></div>
                    <div class="available_latest_version dynamicval pli_subtitle"><?php echo $latest_version; ?></div>
                </div>
                <darkiconbox id="check_for_updates" class="rfloated"><i class="material-icons">refresh</i></darkiconbox>
                <div class="div50"></div>
                <bluebutton id="updateplugin"><?php RevLoader::_e( 'Update Now', 'revslider' ); ?></bluebutton>
                <div class="div75"></div>
                <h3 class="pli_title"><?php RevLoader::_e( 'System Requirements', 'revslider' ); ?></h3>
                <div id="system_requirements">
                    <div id="syscheck_memory_limit" class="system_requirement"><i
                            class="material-icons done_icon">done</i><i
                            class="material-icons warning_icon">warning</i><?php RevLoader::_e( 'Memory Limit (256M)', 'revslider' ); ?>
                    </div>
                    <div id="syscheck_upload_max_filesize" class="system_requirement"><i
                            class="material-icons done_icon">done</i><i
                            class="material-icons warning_icon">warning</i><?php RevLoader::_e( 'Upload Max. Filesize (256M)', 'revslider' ); ?>
                    </div>
                    <div id="syscheck_post_max_size" class="system_requirement"><i
                            class="material-icons done_icon">done</i><i
                            class="material-icons warning_icon">warning</i><?php RevLoader::_e( 'Max. Post Size (256M)', 'revslider' ); ?>
                    </div>
                    <div id="syscheck_object_library_writable" class="system_requirement"><i
                            class="material-icons done_icon">done</i><i
                            class="material-icons warning_icon">warning</i><?php RevLoader::_e( 'Object Library', 'revslider' ); ?>
                    </div>

                </div>
            </div>
            <!-- PLUGIN HISTORY -->
            <div class="pli_right">
                <h3 class="pli_title"><?php RevLoader::_e( 'Update History', 'revslider' ); ?></h3>
                <div id="plugin_history" class="pli_update_history">
                    <?php echo file_get_contents( RS_PLUGIN_PATH . 'release_log.html' ); ?></div>
            </div>
        </div>

        <div class="div150"></div>
        <!-- PLUGIN INFORMATIONS -->
        <div id="plugin_activation_row" class="plugin_inforow">
            <!-- PLUGIN UPDATE -->
            <div id="activation_area" class="pli_left">
                <h3 id="activateplugintitle" class="pli_title">
                    <?php echo ( $selling === true ) ? RevLoader::__( 'Register License Key', 'revslider' ) : RevLoader::__( 'Register Purchase Code', 'revslider' ); ?>
                </h3>
                <row>
                    <onehalf style="padding-right:5px">
                        <div id="activated_ornot_box" class="box_with_icon"><i
                                class="material-icons">done</i><?php RevLoader::_e( 'Registered', 'revslider' ); ?>
                        </div>
                    </onehalf>
                    <onehalf style="padding-left:5px"><a target="_blank"
                            href="<?php echo ( $selling === true ) ? 'https://classydevs.com/slider-revolution-prestashop/pricing/?utm_source=revsix_backoffice&utm_medium=revsix_backoffice&utm_campaign=revsix_backoffice&utm_id=revsix_backoffice&utm_term=revsix_backoffice&utm_content=revsix_backoffice' : 'https://classydevs.com/slider-revolution-prestashop/pricing/?utm_source=revsix_backoffice&utm_medium=revsix_backoffice&utm_campaign=revsix_backoffice&utm_id=revsix_backoffice&utm_term=revsix_backoffice&utm_content=revsix_backoffice'; ?>"
                            class="box_with_icon"><i
                                class="material-icons">vpn_key</i><?php echo ( $selling === true ) ? RevLoader::__( 'Find My Key', 'revslider' ) : RevLoader::__( 'Find My Code', 'revslider' ); ?></a>
                    </onehalf>
                </row>
                <div class="div10"></div>
                <div id="purchasekey_wrap" class="activated">
                    <div id="hide_purchasekey"><?php RevLoader::_e( 'xxxx xxxx xxxx xxxx', 'revslider' ); ?></div>
                    <input class="codeinput" id="purchasekey"
                        placeholder="<?php echo ( $selling === true ) ? RevLoader::__( 'Enter License Key', 'revslider' ) : RevLoader::__( 'Enter Purchase Code', 'revslider' ); ?>" />
                </div>
                <div class="div25"></div>
                <bluebutton id="activateplugin">
                    <?php echo ( $selling === true ) ? RevLoader::__( 'Deregister this Key', 'revslider' ) : RevLoader::__( 'Deregister this Code', 'revslider' ); ?>
                </bluebutton>
                <div class="div25"></div>
                <div class="infobox">
                    <div class="bluetitle">
                        <?php echo ( $selling === true ) ? RevLoader::__( '1 License Key per Website', 'revslider' ) : RevLoader::__( '1 Purchase Code per Website', 'revslider' ); ?>
                    </div>
                    <?php if ( $selling === true ) { ?>
                    <div class="simpletext">
                        <?php RevLoader::_e( 'If you want to use Slider Revolution on another domain, please deregister it in the <a href="https://sliderrevolution.com/members-login/" target="_blank">members area</a> or get <a href="https://classydevs.com/slider-revolution-prestashop/pricing/?utm_source=revsix_backoffice&utm_medium=revsix_backoffice&utm_campaign=revsix_backoffice&utm_id=revsix_backoffice&utm_term=revsix_backoffice&utm_content=revsix_backoffice" target="_blank">more license keys</a>', 'revslider' ); ?>
                    </div>
                    <?php } else { ?>
                    <div class="simpletext">
                        <?php RevLoader::_e( 'If you want to use Slider Revolution on another domain, please <a href="https://classydevs.com/slider-revolution-prestashop?utm_source=revsix_boff_new_domain&utm_medium=revsix_boff_new_domain&utm_campaign=revsix_boff_new_domain&utm_id=revsix_boff_new_domain&utm_term=revsix_boff_new_domain&utm_content=revsix_boff_new_domain" target="_blank">purchase another license</a>', 'revslider' ); ?>
                    </div>
                    <?php } ?>

                </div>
            </div>
            <!-- PLUGIN FEATURES -->
            <div class="pli_right">
                <h3 class="pli_title" id="rs_register_to_unlock">
                    <?php RevLoader::_e( 'Register to unlock all Premium Features', 'revslider' ); ?></h3>
                <div class="features_wrapper">
                    <!-- TEMPLATE LIBRARY -->
                    <div class="featurebox">
                        <div class="box_with_icon not_activated activate_to_unlock"><i
                                class="material-icons">do_not_disturb</i><?php RevLoader::_e( 'Register to Unlock', 'revslider' ); ?>
                        </div>
                        <?php require_once RS_PLUGIN_PATH . 'admin/views/features/premade_template.php'; ?>
                    </div>
                    <!--                
				-->
                    <div class="featurebox">
                        <div class="box_with_icon not_activated activate_to_unlock"><i
                                class="material-icons">do_not_disturb</i><?php RevLoader::_e( 'Register to Unlock', 'revslider' ); ?>
                        </div>
                        <?php require_once RS_PLUGIN_PATH . 'admin/views/features/object_library.php'; ?>
                    </div>
                    <!--
				-->
                    <div class="featurebox">
                        <div class="box_with_icon not_activated activate_to_unlock"><i
                                class="material-icons">do_not_disturb</i><?php RevLoader::_e( 'Register to Unlock', 'revslider' ); ?>
                        </div>
                        <?php require_once RS_PLUGIN_PATH . 'admin/views/features/layer_animations.php'; ?>
                    </div>
                    <!--
				-->
                    <div class="featurebox">
                        <div class="box_with_icon not_activated activate_to_unlock"><i
                                class="material-icons">do_not_disturb</i><?php RevLoader::_e( 'Register to Unlock', 'revslider' ); ?>
                        </div>
                        <?php require_once RS_PLUGIN_PATH . 'admin/views/features/add_ons.php'; ?>
                    </div>
                    <!--                            
				-->
                    <div class="featurebox">
                        <div class="box_with_icon not_activated activate_to_unlock"><i
                                class="material-icons">do_not_disturb</i><?php RevLoader::_e( 'Register to Unlock', 'revslider' ); ?>
                        </div>
                        <?php require_once RS_PLUGIN_PATH . 'admin/views/features/support.php'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="div150"></div>


        <div id="plugin_news_row" class="plugin_inforow">

            <div id="cwt_socials" class="pli_left">
            <h3 class="pli_title"><?php RevLoader::_e( 'Connect with ClassyDevs', 'revslider' ); ?></h3>
                <a class="cwt_link" target="_blank" href="https://www.youtube.com/c/ClassyDevs">
                    <grayiconbox class="cwt_youtube"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'YouTube', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">youtube.com/c/ClassyDevs</div>
                    </div>
                </a>
                <div class="div10"></div>
                <a class="cwt_link" target="_blank" href="https://twitter.com/ClassyDevs">
                    <grayiconbox class="cwt_twitter"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'Twitter', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">twitter.com/ClassyDevs</div>
                    </div>
                </a>
                <div class="div10"></div>
                <a class="cwt_link" target="_blank" href="https://www.facebook.com/classydevs">
                    <grayiconbox class="cwt_facebook"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'Facebook', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">facebook.com/classydevs</div>
                    </div>
                </a>
                <div class="div10"></div>
                <a class="cwt_link" target="_blank" href="https://www.instagram.com/classydevs/">
                    <grayiconbox class="cwt_instagram"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'Instagram', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">instagram.com/classydevs/</div>
                    </div>
                </a>
                <div class="div30"></div>
                <h3 class="pli_title"><?php RevLoader::_e( 'Connect with Slider Revolution', 'revslider' ); ?></h3>
                <a class="cwt_link" target="_blank" href="https://youtube.com/c/sliderrevolution">
                    <grayiconbox class="cwt_youtube"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'YouTube', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">youtube.com/c/sliderrevolution</div>
                    </div>
                </a>
                <div class="div10"></div>
                <a class="cwt_link" target="_blank" href="https://twitter.com/revslider">
                    <grayiconbox class="cwt_twitter"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'Twitter', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">twitter.com/revslider</div>
                    </div>
                </a>
                <div class="div10"></div>
                <a class="cwt_link" target="_blank" href="https://www.facebook.com/wordpress.slider.revolution">
                    <grayiconbox class="cwt_facebook"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'Facebook', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">facebook.com/wordpress.slider.revolution</div>
                    </div>
                </a>
                <div class="div10"></div>
                <a class="cwt_link" target="_blank" href="https://instagram.com/sliderrevolution">
                    <grayiconbox class="cwt_instagram"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'Instagram', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">instagram.com/sliderrevolution</div>
                    </div>
                </a>
                <div class="div10"></div>
                <a class="cwt_link" target="_blank" href="https://dribbble.com/themepunch">
                    <grayiconbox class="cwt_dribbble"></grayiconbox>
                    <div class="pli_twoline">
                        <div class="pli_subtitle"><?php RevLoader::_e( 'Dribbble', 'revslider' ); ?></div>
                        <div class="dynamicval pli_subtitle">dribbble.com/themepunch</div>
                    </div>
                </a>
                <div class="div100"></div>
                <h3 class="pli_title"><?php RevLoader::_e( 'Signup to our Newsletter', 'revslider' ); ?></h3>

                <div class="div25"></div>
                <a href="https://newsletter.classydevs.com/" target="_blank">
                    <bluebutton><?php RevLoader::_e( 'Sign Up', 'revslider' ); ?></bluebutton>
                </a>
                <div class="div25"></div>
                <div class="infobox">
                    <div class="bluetitle"><?php RevLoader::_e( 'Updates, New Products, Spotlights', 'revslider' ); ?>
                    </div>
                    <div class="simpletext">
                        <?php RevLoader::_e( 'Get access to the latest News from Slider Revolution. We promise to never send you Spam!', 'revslider' ); ?>
                    </div>
                </div>
            </div>

            <!-- PLUGIN HISTORY -->
            <div id="twitter_wrapper" class="pli_right" style="width:100%">
                <h3 class="pli_title rs_integation_title_empty">&nbsp;</h3>
                <div class="rs_sp_integration_area">
                    <div class="rs_sp_integration_container">
                        <div class="rs_sp_integration_container_area">
                            <div class="rs_sp_integration_header">
                                <div class="rs_sp_integration_header_title">
                                    <h2>Our Other Products</h2>
                                </div>
                                <div class="rs_sp_integration_header_title rs_sp_header_title_themes">
                                    <!-- <h2>Recommended Shopify Themes</h2> -->
                                </div>
                            </div>
                            <div class="rs_sp_integration_body">
                                <div class="rs_sp_integration_body_inner">

                                    <div class="rs_sp_integration_body_content rs_sp_inte_apps">
                                        <div class="rs_sp_integration_item_single_body">
                                            <div class="rs_sp_integration_item_single_content_top">
                                                <div class="rs_sp_integration_item_single_content">
                                                    <div class="rs_sp_inte_logo">
                                                        <a href="https://classydevs.com/prestashop-page-builder/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd" target="_blank"><img src="<?php echo RS_PLUGIN_URL.'admin/assets/images/Crazy-elements.svg'; ?>" alt=""></a>
                                                    </div>
                                                    <div class="rs_sp_inte_inner_content">
                                                        <a href="https://classydevs.com/prestashop-page-builder/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd"  target="_blank">Crazyelements</a>
                                                        <p>The Most Powerful Drag and Drop Page Builder for PrestaShop to Create a High Quality Website in the Fastest Time Possible </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rs_sp_integration_item_single_body">
                                            <div class="rs_sp_integration_item_single_content_top">
                                                <div class="rs_sp_integration_item_single_content">
                                                    <div class="rs_sp_inte_logo">
                                                        <a href="https://classydevs.com/free-modules/smartblog/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd" target="_blank"><img src="<?php echo RS_PLUGIN_URL.'admin/assets/images/smartblog.svg'; ?>" alt=""></a>
                                                    </div>
                                                    <div class="rs_sp_inte_inner_content">
                                                        <a href="https://classydevs.com/free-modules/smartblog/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd" target="_blank">SmartBlog</a>
                                                        <p>The Best Blog Module Available for PrestaShop with Many Awesome Child Modules to Make Your Site SEO Friendly.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rs_sp_integration_body_content rs_sp_inte_apps">
                                        <div class="rs_sp_integration_item_single_body">
                                            <div class="rs_sp_integration_item_single_content_top">
                                                <div class="rs_sp_integration_item_single_content">
                                                    <div class="rs_sp_inte_logo">
                                                        <a href="https://classydevs.com/myshop-multipurpose-prestashop-theme/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd" target="_blank"><img src="<?php echo RS_PLUGIN_URL.'admin/assets/images/myshop-logo-nly.svg'; ?>" alt=""></a>
                                                    </div>
                                                    <div class="rs_sp_inte_inner_content">
                                                        <a href="https://classydevs.com/myshop-multipurpose-prestashop-theme/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd" target="_blank">MyShop</a>
                                                        <p>The Best Multipurpose PrestaShop Theme to Meet All the Needs of Your PrestaShop eCommerce.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rs_sp_integration_item_single_body">
                                            <div class="rs_sp_integration_item_single_content_top">
                                                <div class="rs_sp_integration_item_single_content">
                                                    <div class="rs_sp_inte_logo">
                                                        <a href="https://classydevs.com/free-modules/classy-product-extra-tab/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd" target="_blank"><img src="<?php echo RS_PLUGIN_URL.'admin/assets/images/Classy-product-extra-tab.svg'; ?>" alt=""></a>
                                                    </div>
                                                    <div class="rs_sp_inte_inner_content">
                                                        <a href="https://classydevs.com/free-modules/classy-product-extra-tab/?utm_source=revsix_ourprd&utm_medium=revsix_ourprd&utm_campaign=revsix_ourprd&utm_id=revsix_ourprd&utm_term=revsix_ourprd&utm_content=revsix_ourprd" target="_blank">Classy Product Extra Tab</a>
                                                        <p>Add Extra Tab to Your Products and Show More Organinzed Content to Attract Your Customers.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>	

                                <div class="rs_sp_view_more_button">
                                    <a href="https://classydevs.com/prestashop-addons/?utm_source=revsix_ourprd_more&utm_medium=revsix_ourprd_more&utm_campaign=revsix_ourprd_more&utm_id=revsix_ourprd_more&utm_term=revsix_ourprd_more&utm_content=revsix_ourprd_more" target="_blank">View More</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script type="text/javascript">
    window.sliderLibrary = JSON.parse(
        <?php echo $rs_slider->json_encode_client_side( array( 'sliders' => $overview_data ) ); ?>);
    window.rs_system = JSON.parse(<?php echo $rs_slider->json_encode_client_side( $system_config ); ?>);
    if (document.readyState === "loading")
        window.addEventListener('DOMContentLoaded', function() {
            RVS.ENV.code = "<?php echo $code; ?>";
            RVS.F.initOverView();
        });
    else {
        RVS.ENV.code = "<?php echo $code; ?>";
        RVS.F.initOverView();
    }
    </script>