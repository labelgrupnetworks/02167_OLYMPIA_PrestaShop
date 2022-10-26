<?php
/**
 * Provide an admin area view for the Slider Modal Options
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();

?>

<!--WELCOME MODAL-->
<div class="_TPRB_ rb-modal-wrapper" data-modal="rbm_welcomeModal">
    <div class="rb-modal-inner">
        <div class="rb-modal-content">
            <div id="rbm_welcomeModal" class="rb_modal form_inner">
                <div class="rbm_header"><span
                        class="rbm_title"><?php printf(RevLoader::__('Welcome to Slider Revolution %s', 'revslider'), RS_REVISION);?></span><i
                        class="rbm_close material-icons">close</i></div>
                <div class="rbm_content">
                    <div style="padding:80px 100px 0px">
                        <div id="welcome_logo"></div>
                        <div class="mcg_option_third_wraps">
                            <div class="st_slider mcg_guide_optionwrap mcg_option_third">
                                <div class="mcg_o_title"><?php RevLoader::_e('What\'s new?');?></div>
                                <div class="mcg_o_descp">
                                    <?php printf(RevLoader::__( 'Check out our Change Log to learn about new Features and Bug Fixes in Version %s.', 'revslider'), RS_REVISION); ?>
                                </div>
                                <div class="div25"></div>
                                <a target="_blank" href="https://classydevs.com/slider-revolution-prestashop"
                                    class="basic_action_button autosize basic_action_lilabutton"><?php RevLoader::_e('More Info');?></a>
                            </div>
                            <div class="st_scene mcg_guide_optionwrap mcg_option_third">
                                <div class="mcg_o_title"><?php RevLoader::_e('Docs & FAQs');?></div>
                                <div class="mcg_o_descp">
                                    <?php printf(RevLoader::__('Checkout our all new Help Center<br>with updated %s Support Material.', 'revslider'), RS_REVISION); ?>
                                </div>
                                <div class="div25"></div>
                                <a target="_blank" href="https://www.classydevs.com/support"
                                    class="basic_action_button autosize basic_action_lilabutton"><?php RevLoader::_e('Help Center');?></a>
                            </div>

                            <div class="st_carousel mcg_guide_optionwrap mcg_option_third last">
                                <div class="mcg_o_title"><?php RevLoader::_e('Clear your Browser Cache');?></div>
                                <div class="mcg_o_descp">
                                    <?php RevLoader::_e('To make sure that all Slider Revolution files<br>are updated, please clear your cache.');?>
                                </div>
                                <div class="div25"></div>
                                <a target="_blank"
                                    href="https://classydevs.com/prestashop-1-7-clear-cache/"
                                    class="basic_action_button autosize basic_action_lilabutton"><?php RevLoader::_e('How to?');?></a>
                            </div>
                        </div>
                        <div class="div75"></div>
                    </div>

                    <?php
					if(RevLoader::get_option('revslider-valid', 'false') == 'true') { ?>
                    <div id="open_welcome_register_form" class="big_purple_linkbutton">
                        <?php RevLoader::_e('Lets get Started with ' );?> <b>
                            <?php printf(RevLoader::__('Slider Revolution %s', 'revslider'), RS_REVISION); ?></b></div>
                    <?php } else { ?>
                    <div id="open_welcome_register_form" class="big_purple_linkbutton">
                        <?php RevLoader::_e('Activate Slider Revolution to');?> <b> <i class="material-icons">lock</i>
                            <?php RevLoader::_e('Unlock all Features');?></b></div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>


<!--GLOBAL CUSTOM FONTS MODAL-->
<div class="_TPRB_ rb-modal-wrapper" data-modal="rbm_globalfontsettings" style="z-index:1000010 !important">
    <div class="rb-modal-inner">
        <div class="rb-modal-content">
            <div id="rbm_globalfontsettings" class="rb_modal form_inner">
                <div class="rbm_header"><i class="rbm_symbol material-icons">font_download</i><span
                        class="rbm_title"><?php RevLoader::_e('Global Custom Fonts', 'revslider');?></span><i
                        class="rbm_close material-icons">close</i></div>
                <div class="rbm_content">
                    <div class="modal_fields_title" style="width:200px;">
                        <?php RevLoader::_e('Font Family Name', 'revslider');?></div>
                    <!--
					-->
                    <div class="modal_fields_title" style="width:200px;">
                        <?php RevLoader::_e('Font CSS URL', 'revslider');?></div>
                    <!--
					-->
                    <div class="modal_fields_title" style="width:200px;">
                        <?php RevLoader::_e('Available Font Weights', 'revslider');?></div>
                    <!--
					-->
                    <div class="modal_fields_title" style="width:75px;margin-left:10px;">
                        <?php RevLoader::_e('Front End', 'revslider');?></div>
                    <!--
					-->
                    <div class="modal_fields_title" style="width:75px;"><?php RevLoader::_e('in Editor', 'revslider');?>
                    </div>
                    <div id="global_custom_fonts" style="margin-bottom:10px">
                    </div>
                    <div id="add_new_custom_font" class="basic_action_button autosize rightbutton"><i
                            class="material-icons">add</i><?php RevLoader::_e('Add Custom Font', 'revslider');?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--GLOBAL CUSTOM HOOK MODAL-->

<div class="_TPRB_ rb-modal-wrapper rev-add-custom-hook-modal-area" data-modal="rbm_addnewhook"
    style="z-index:1000010 !important">
    <div class="rb-modal-inner">
        <div class="rb-modal-content" style="text-align: center;">
            <div id="rbm_addnewhook" class="rb_modal form_inner">
                <div class="rbm_header"><span
                        class="rbm_title"><?php RevLoader::_e('Add Custom Hook', 'revslider');?></span><i
                        class="rbm_close material-icons">close</i></div>
                <div class="rbm_content">
                    <div class="rev-add-custom-hook-name-input">
                        <label_a class="rev-hook-lable"><?php echo 'Hook Name'; ?>:</label_a><input type="text"
                            id="add_cust_hook" name="eg-hook-name" value="" />
                    </div>
                    <div id="add_hook_bt" class="rbm_darkhalfbutton mr10">
                        <span id="add_hook_txt"><?php RevLoader::_e('Submit Now', 'revslider');?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--GLOBAL SETTINGS MODAL-->
<div class="_TPRB_ rb-modal-wrapper" data-modal="rbm_globalsettings">
    <div class="rb-modal-inner">
        <div class="rb-modal-content">
            <div id="rbm_globalsettings" class="rb_modal form_inner">
                <div class="rbm_header"><i class="rbm_symbol material-icons">settings</i><span
                        class="rbm_title"><?php RevLoader::_e('Global Settings', 'revslider');?></span><i
                        class="rbm_close material-icons">close</i></div>
                <div class="rbm_content">
                    <!--
					-->
                    <!--
					-->
                    <div class="rbm_general_half" style="padding-left:20px;">
                        <div class="ale_i_title"><?php RevLoader::_e('Default Layout Grid Breakpoints', 'revslider');?>
                        </div>
                        <hr class="general_hr">
                        <label_a><?php RevLoader::_e('Default desktop content width', 'revslider');?></label_a><input
                            type="text" class="easyinit globalinput" data-numeric="true" data-allowed="px" data-min="0"
                            data-max="2400" data-r="globals.size.desktop"><span class="linebreak"></span>
                        <label_a><?php RevLoader::_e('Default notebook content width', 'revslider');?></label_a><input
                            type="text" class="easyinit globalinput" data-numeric="true" data-allowed="px" data-min="0"
                            data-max="2400" data-r="globals.size.notebook"><span class="linebreak"></span>
                        <label_a><?php RevLoader::_e('Default tablet content width', 'revslider');?></label_a><input
                            type="text" class="easyinit globalinput" data-numeric="true" data-allowed="px" data-min="0"
                            data-max="2400" data-r="globals.size.tablet"><span class="linebreak"></span>
                        <label_a><?php RevLoader::_e('Default mobile content width', 'revslider');?></label_a><input
                            type="text" class="easyinit globalinput" data-numeric="true" data-allowed="px" data-min="0"
                            data-max="2400" data-r="globals.size.mobile"><span class="linebreak"></span>
                        <div class="div25"></div>
                        <div class="ale_i_title"><?php RevLoader::_e('Fonts', 'revslider');?></div>
                        <hr class="general_hr">
                        <label_a><?php RevLoader::_e('Enable custom font selection in editor', 'revslider');?></label_a>
                        <div id="rs_gl_custom_fonts" class="basic_action_button autosize"><i
                                class="material-icons">font_download</i><?php RevLoader::_e('Edit Custom Fonts', 'revslider');?>
                        </div>
                        <label_a><?php RevLoader::_e('Disable RS Font Awesome Library', 'revslider');?></label_a><input
                            type="checkbox" class="easyinit globalinput" data-r="globals.fontawesomedisable"><span
                            class="linebreak"></span>
                        <div class="div25"></div>
                        <label_a><?php RevLoader::_e('Enable Google Fonts download', 'revslider');?></label_a><select
                            id="fontdownload" name="fontdownload" data-theme="inmodal"
                            class="globalinput easyinit nosearchbox tos2" data-r="globals.fontdownload">
                            <option selected="selected" value="off">
                                <?php RevLoader::_e('Load from Google','revslider');?></option>
                            <option value="preload"><?php RevLoader::_e('Preload from Google', 'revslider');?></option>
                            <option value="disable"><?php RevLoader::_e('Disable, Load on your own', 'revslider');?>
                            </option>
                        </select><span class="linebreak"></span>
                        <label_a><?php RevLoader::_e('Optional Google Fonts loading URL', 'revslider');?></label_a>
                        <input type="text" class="easyinit globalinput" data-r="globals.fonturl"
                            placeholder="<?php RevLoader::_e('(ie. http://fonts.useso.com/css?family for chinese Environment)', 'revslider');?>"><span
                            class="linebreak"></span>
                        <label_a></label_a>
                        <div id="rs_trigger_font_deletion" class="basic_action_button autosize"><i
                                class="material-icons">build</i><?php RevLoader::_e('Update Preload Fonts', 'revslider'); ?>
                        </div>


                        <!--<input type="text" class="easyinit globalinput" data-r="globals.customfonts" placeholder="<?php RevLoader::_e('font-family, font-family, ...', 'revslider');?>"><span class="linebreak"></span>-->
                        <!--<div id="general_custom_fonts_list"></div>
						<label_a></label_a><div class="basic_action_button onlyicon" id="add_custom_global_fonts"><i class="material-icons">add</i></div>		-->
                        <div class="div25"></div>
                        <div class="ale_i_title"><?php RevLoader::_e('Miscellaneous', 'revslider');?></div>
                        <hr class="general_hr">

                        <label_a><?php RevLoader::_e('Editor High Contrast mode', 'revslider');?></label_a><input
                            type="checkbox" class="easyinit globalinput callEvent" data-evt="highContrast"
                            data-r="globals.highContrast"><span class="linebreak"></span>

                    </div>
                </div>

                <div id="rbm_globalsettings_savebtn"><i class="material-icons mr10">save</i><span
                        class="rbm_cp_save_text"><?php RevLoader::_e('Save Global Settings', 'revslider');?></span>
                </div>
            </div>
        </div>
    </div>
</div>