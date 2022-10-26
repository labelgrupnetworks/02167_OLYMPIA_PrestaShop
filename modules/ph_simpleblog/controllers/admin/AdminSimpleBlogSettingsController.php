<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2020 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class AdminSimpleBlogSettingsController extends ModuleAdminController
{
    public $is_16;
    public $is_17;

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;

        $this->initOptions();

        $this->is_16 = (version_compare(_PS_VERSION_, '1.6.0', '>=') === true && version_compare(_PS_VERSION_, '1.7.0', '<') === true) ? true : false;
        $this->is_17 = (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) ? true : false;
    }

    public function initOptions()
    {
        $this->optionTitle = $this->l('Settings');

        $blogCategories = SimpleBlogCategory::getCategories($this->context->language->id);

        $simpleBlogCategories = [];

        $simpleBlogCategories[0] = $this->l('All categories');
        $simpleBlogCategories[9999] = $this->l('Featured only');

        foreach ($blogCategories as $key => $category) {
            $simpleBlogCategories[$category['id']] = $category['name'];
        }

        $relatedPosts = [];

        if (Module::isInstalled('ph_relatedposts')) {
            $relatedPosts = [
                'related_posts' => [
                    'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                    'title' => $this->l('Related Posts widget settings'),
                    'image' => '../img/t/AdminOrderPreferences.gif',
                    'fields' => [
                        'PH_RELATEDPOSTS_GRID_COLUMNS' => [
                            'title' => $this->l('Grid columns:'),
                            'cast' => 'intval',
                            'desc' => $this->l('Working only with "Recent Posts layout:" setup to "Grid"'),
                            'show' => true,
                            'required' => true,
                            'type' => 'radio',
                            'choices' => [
                                '2' => $this->l('2 columns'),
                                '3' => $this->l('3 columns'),
                                '4' => $this->l('4 columns'),
                            ],
                        ], // PH_RELATEDPOSTS_GRID_COLUMNS
                    ],
                ],
            ];
        }

        $timezones = [
            'Pacific/Midway' => '(GMT-11:00) Midway Island',
            'US/Samoa' => '(GMT-11:00) Samoa',
            'US/Hawaii' => '(GMT-10:00) Hawaii',
            'US/Alaska' => '(GMT-09:00) Alaska',
            'US/Pacific' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
            'America/Tijuana' => '(GMT-08:00) Tijuana',
            'US/Arizona' => '(GMT-07:00) Arizona',
            'US/Mountain' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua',
            'America/Mazatlan' => '(GMT-07:00) Mazatlan',
            'America/Mexico_City' => '(GMT-06:00) Mexico City',
            'America/Monterrey' => '(GMT-06:00) Monterrey',
            'Canada/Saskatchewan' => '(GMT-06:00) Saskatchewan',
            'US/Central' => '(GMT-06:00) Central Time (US &amp; Canada)',
            'US/Eastern' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
            'US/East-Indiana' => '(GMT-05:00) Indiana (East)',
            'America/Bogota' => '(GMT-05:00) Bogota',
            'America/Lima' => '(GMT-05:00) Lima',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'Canada/Atlantic' => '(GMT-04:00) Atlantic Time (Canada)',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'Canada/Newfoundland' => '(GMT-03:30) Newfoundland',
            'America/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
            'Greenland' => '(GMT-03:00) Greenland',
            'Atlantic/Stanley' => '(GMT-02:00) Stanley',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Africa/Casablanca' => '(GMT) Casablanca',
            'Europe/Dublin' => '(GMT) Dublin',
            'Europe/Lisbon' => '(GMT) Lisbon',
            'Europe/London' => '(GMT) London',
            'Africa/Monrovia' => '(GMT) Monrovia',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade',
            'Europe/Berlin' => '(GMT+01:00) Berlin',
            'Europe/Bratislava' => '(GMT+01:00) Bratislava',
            'Europe/Brussels' => '(GMT+01:00) Brussels',
            'Europe/Budapest' => '(GMT+01:00) Budapest',
            'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
            'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
            'Europe/Madrid' => '(GMT+01:00) Madrid',
            'Europe/Paris' => '(GMT+01:00) Paris',
            'Europe/Prague' => '(GMT+01:00) Prague',
            'Europe/Rome' => '(GMT+01:00) Rome',
            'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
            'Europe/Skopje' => '(GMT+01:00) Skopje',
            'Europe/Stockholm' => '(GMT+01:00) Stockholm',
            'Europe/Vienna' => '(GMT+01:00) Vienna',
            'Europe/Warsaw' => '(GMT+01:00) Warsaw',
            'Europe/Zagreb' => '(GMT+01:00) Zagreb',
            'Europe/Athens' => '(GMT+02:00) Athens',
            'Europe/Bucharest' => '(GMT+02:00) Bucharest',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Africa/Harare' => '(GMT+02:00) Harare',
            'Europe/Helsinki' => '(GMT+02:00) Helsinki',
            'Europe/Istanbul' => '(GMT+02:00) Istanbul',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Europe/Kiev' => '(GMT+02:00) Kyiv',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Europe/Riga' => '(GMT+02:00) Riga',
            'Europe/Sofia' => '(GMT+02:00) Sofia',
            'Europe/Tallinn' => '(GMT+02:00) Tallinn',
            'Europe/Vilnius' => '(GMT+02:00) Vilnius',
            'Asia/Baghdad' => '(GMT+03:00) Baghdad',
            'Asia/Kuwait' => '(GMT+03:00) Kuwait',
            'Africa/Nairobi' => '(GMT+03:00) Nairobi',
            'Asia/Riyadh' => '(GMT+03:00) Riyadh',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Europe/Moscow' => '(GMT+04:00) Moscow',
            'Asia/Baku' => '(GMT+04:00) Baku',
            'Europe/Volgograd' => '(GMT+04:00) Volgograd',
            'Asia/Muscat' => '(GMT+04:00) Muscat',
            'Asia/Tbilisi' => '(GMT+04:00) Tbilisi',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Kabul' => '(GMT+04:30) Kabul',
            'Asia/Karachi' => '(GMT+05:00) Karachi',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Kolkata',
            'Asia/Kathmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Yekaterinburg' => '(GMT+06:00) Ekaterinburg',
            'Asia/Almaty' => '(GMT+06:00) Almaty',
            'Asia/Dhaka' => '(GMT+06:00) Dhaka',
            'Asia/Novosibirsk' => '(GMT+07:00) Novosibirsk',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok',
            'Asia/Jakarta' => '(GMT+07:00) Jakarta',
            'Asia/Krasnoyarsk' => '(GMT+08:00) Krasnoyarsk',
            'Asia/Chongqing' => '(GMT+08:00) Chongqing',
            'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
            'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Asia/Singapore' => '(GMT+08:00) Singapore',
            'Asia/Taipei' => '(GMT+08:00) Taipei',
            'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
            'Asia/Urumqi' => '(GMT+08:00) Urumqi',
            'Asia/Irkutsk' => '(GMT+09:00) Irkutsk',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Tokyo' => '(GMT+09:00) Tokyo',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Asia/Yakutsk' => '(GMT+10:00) Yakutsk',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Canberra' => '(GMT+10:00) Canberra',
            'Pacific/Guam' => '(GMT+10:00) Guam',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Australia/Melbourne' => '(GMT+10:00) Melbourne',
            'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
            'Australia/Sydney' => '(GMT+10:00) Sydney',
            'Asia/Vladivostok' => '(GMT+11:00) Vladivostok',
            'Asia/Magadan' => '(GMT+12:00) Magadan',
            'Pacific/Auckland' => '(GMT+12:00) Auckland',
            'Pacific/Fiji' => '(GMT+12:00) Fiji',
        ];

        $timezones_select = [];

        foreach ($timezones as $value => $name) {
            $timezones_select[] = ['id' => $value, 'name' => $name];
        }

        $pre_settings_content = '<button type="submit" name="regenerateThumbnails" class="button btn btn-default"><i class="process-icon-cogs"></i>' . $this->l('Regenerate thumbnails') . '</button>&nbsp;';
        $pre_settings_content .= '<button type="submit" name="submitExportSettings" class="button btn btn-default"><i class="process-icon-export"></i>' . $this->l('Export settings') . '</button>&nbsp;';
        $pre_settings_content .= '<br /><br />';

        $standard_options = [
            'general' => [
                'title' => $this->l('Blog for PrestaShop - Settings'),
                'info' => $pre_settings_content,
                'fields' => [
                    'PH_BLOG_TIMEZONE' => [
                        'title' => $this->l('Timezone:'),
                        'desc' => $this->l('If you want to use future post publication date you need to setup your timezone'),
                        'type' => 'select',
                        'list' => $timezones_select,
                        'identifier' => 'id',
                        'required' => true,
                        'validation' => 'isGenericName',
                    ], // PH_BLOG_TIMEZONE

                    'PH_BLOG_POSTS_PER_PAGE' => [
                        'title' => $this->l('Posts per page:'),
                        'cast' => 'intval',
                        'desc' => $this->l('Number of blog posts displayed per page. Default is 10.'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_POSTS_PER_PAGE

                    'PH_BLOG_SLUG' => [
                        'title' => $this->l('Blog main URL (by default: blog)'),
                        'validation' => 'isGenericName',
                        'required' => true,
                        'type' => 'text',
                        'size' => 40,
                    ], // PH_BLOG_SLUG

                    'PH_BLOG_MAIN_TITLE' => [
                        'title' => $this->l('Blog title:'),
                        'validation' => 'isGenericName',
                        'type' => 'textLang',
                        'size' => 40,
                        'desc' => $this->l('Meta Title for blog homepage'),
                    ], // PH_BLOG_MAIN_TITLE

                    'PH_BLOG_MAIN_META_DESCRIPTION' => [
                        'title' => $this->l('Blog description:'),
                        'validation' => 'isGenericName',
                        'type' => 'textLang',
                        'size' => 75,
                        'desc' => $this->l('Meta Description for blog homepage'),
                    ], // PH_BLOG_MAIN_META_DESCRIPTION

                    'PH_BLOG_DATEFORMAT' => [
                        'title' => $this->l('Blog default date format:'),
                        'desc' => $this->l('More details: https://www.smarty.net/docsv2/en/language.modifier.date.format.tpl'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                        'size' => 40,
                    ], // PH_BLOG_DATEFORMAT

                    'PH_CATEGORY_SORTBY' => [
                        'title' => $this->l('Sort categories by:'),
                        'desc' => $this->l('Select which method use to sort categories in SimpleBlog Categories Block'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'position' => $this->l('Position (1-9)'),
                            'name' => $this->l('Name (A-Z)'),
                            'id' => $this->l('ID (1-9)'),
                        ],
                    ], // PH_CATEGORY_SORTBY

                    'PH_BLOG_FB_INIT' => [
                        'title' => $this->l('Init Facebook?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'desc' => $this->l('If you already use some Facebook widgets in your theme please select option to "No". If you select "Yes" then SimpleBlog will add facebook connect script on single post page.'),
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_FB_INIT

                    // @todo - 2.0.0
                    // 'PH_BLOG_LOAD_FA' => array(
                    //     'title' => $this->l('Load FontAwesome?'),
                    //     'validation' => 'isBool',
                    //     'cast' => 'intval',
                    //     'desc' => $this->l('If you already use FontAwesome in your theme please select option to "No".'),
                    //     'required' => true,
                    //     'type' => 'bool'
                    // ), // PH_BLOG_LOAD_FA
                ],
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
            ],

            'layout' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Appearance Settings - General'),
                'fields' => [
                    'PH_BLOG_DISPLAY_BREADCRUMBS' => [
                        'title' => $this->l('Display breadcrumbs in center-column?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'desc' => $this->l('Sometimes you want to remove breadcrumbs from center-column. Option for 1.6 only'),
                        'required' => true,
                        'type' => 'bool',
                        'class' => '',
                    ], // PH_BLOG_DISPLAY_BREADCRUMBS

                    'PH_BLOG_LIST_LAYOUT' => [
                        'title' => $this->l('Posts list layout:'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'full' => $this->l('Full width with large images'),
                            'grid' => $this->l('Grid'),
                        ],
                    ], // PH_BLOG_LIST_LAYOUT

                    'PH_BLOG_GRID_COLUMNS' => [
                        'title' => $this->l('Grid columns:'),
                        'cast' => 'intval',
                        'desc' => $this->l('Working only with "Posts list layout" setup to "Grid"'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            '2' => $this->l('2 columns'),
                            '3' => $this->l('3 columns'),
                            '4' => $this->l('4 columns'),
                        ],
                    ], // PH_BLOG_GRID_COLUMNS

                    'PH_BLOG_MASONRY_LAYOUT' => array(
                        'title' => $this->l('Use Masonry layout?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'desc' => $this->l('You can use masonry layout if you use Grid as a post list layout'),
                        'type' => 'bool',
                    ), // PH_BLOG_MASONRY_LAYOUT

                    'PH_BLOG_CSS' => [
                        'title' => $this->l('Custom CSS'),
                        'show' => true,
                        'required' => false,
                        'type' => 'textarea',
                        'cols' => '70',
                        'rows' => '10',
                    ], // PH_BLOG_CSS
                ],
            ],

            'single_post' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Appearance Settings - Single post'),
                'fields' => [
                    'PH_BLOG_DISPLAY_LIKES' => [
                        'title' => $this->l('Display "likes"?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_LIKES

                    'PH_BLOG_DISPLAY_SHARER' => [
                        'title' => $this->l('Use share icons on single post page?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_SHARER

                    'PH_BLOG_DISPLAY_AUTHOR' => [
                        'title' => $this->l('Display post author?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->l('This option also applies to the list of posts from the category'),
                    ], // PH_BLOG_DISPLAY_AUTHOR

                    'PH_BLOG_DISPLAY_VIEWS' => [
                        'title' => $this->l('Display "views"?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->l('This option also applies to the list of posts from the category'),
                    ], // PH_BLOG_DISPLAY_VIEWS

                    'PH_BLOG_DISPLAY_DATE' => [
                        'title' => $this->l('Display post creation date?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->l('This option also applies to the list of posts from the category'),
                    ], // PH_BLOG_DISPLAY_DATE

                    'PH_BLOG_DISPLAY_FEATURED' => [
                        'title' => $this->l('Display post featured image?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_FEATURED

                    'PH_BLOG_DISPLAY_CATEGORY' => [
                        'title' => $this->l('Display post category?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->l('This option also applies to the list of posts from the category'),
                    ], // PH_BLOG_DISPLAY_CATEGORY

                    'PH_BLOG_DISPLAY_TAGS' => [
                        'title' => $this->l('Display post tags?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_TAGS

                    'PH_BLOG_DISPLAY_RELATED' => [
                        'title' => $this->l('Display related products?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_RELATED
                ],
            ],

            'category_page' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Appearance Settings - Post lists'),
                'fields' => [
                    'PH_BLOG_DISPLAY_MORE' => [
                        'title' => $this->l('Display "Read more"?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_MORES

                    'PH_BLOG_DISPLAY_COMMENTS' => [
                        'title' => $this->l('Display number of comments?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_COMMENTS

                    'PH_BLOG_DISPLAY_THUMBNAIL' => [
                        'title' => $this->l('Display post thumbnails?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_THUMBNAILS

                    'PH_BLOG_DISPLAY_DESCRIPTION' => [
                        'title' => $this->l('Display post short description?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_DESCRIPTION

                    'PH_BLOG_DISPLAY_CAT_DESC' => [
                        'title' => $this->l('Display category description on category page?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_CAT_DESC

                    'PH_BLOG_DISPLAY_CATEGORY_IMAGE' => [
                        'title' => $this->l('Display category image?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_CATEGORY_IMAGE

                    'PH_BLOG_DISPLAY_CATEGORY_CHILDREN' => [
                        'title' => $this->l('Display subcategories?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_CATEGORY_CHILDREN

                    'PH_CATEGORY_IMAGE_X' => [
                        'title' => $this->l('Default category image width (px)'),
                        'cast' => 'intval',
                        'desc' => $this->l('Default: 535 (For PrestaShop 1.5), 870 (For PrestaShop 1.6), 1000 (For PrestaShop 1.7)'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_CATEGORY_IMAGE_X

                    'PH_CATEGORY_IMAGE_Y' => [
                        'title' => $this->l('Default category image height (px)'),
                        'cast' => 'intval',
                        'desc' => $this->l('Default: 150'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_CATEGORY_IMAGE_Y
                ],
            ],

            'comments' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Comments'),
                'fields' => [
                    'PH_BLOG_COMMENTS_SYSTEM' => [
                        'title' => $this->l('Comments system:'),
                        'desc' => $this->l('What type of comments system do you want to use?'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'native' => $this->l('Default native comments'),
                            'facebook' => $this->l('Facebook comments'),
                            'disqus' => $this->l('Disqus comments'),
                        ],
                    ], // PH_BLOG_GRID_COLUMNS

                    'PH_BLOG_COMMENT_AUTO_APPROVAL' => [
                        'title' => $this->l('Automatically approve new comments?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_AUTO_APPROVAL

                    'PH_BLOG_COMMENT_ALLOW' => [
                        'title' => $this->l('Allow comments?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_ALLOW

                    'PH_BLOG_COMMENT_ALLOW_GUEST' => [
                        'title' => $this->l('Allow comments for non logged in users?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_ALLOW_GUEST

                    'PH_BLOG_COMMENT_NOTIFICATIONS' => [
                        'title' => $this->l('Notify about new comments?'),
                        'validation' => 'isBool',
                        'desc' => $this->l('Only for native comment system'),
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_NOTIFICATIONS

                    'PH_BLOG_COMMENT_NOTIFY_EMAIL' => [
                        'title' => $this->l('E-mail for notifications'),
                        'type' => 'text',
                        'desc' => $this->l('Only for native comment system'),
                        'size' => 55,
                        'required' => false,
                    ], // PH_BLOG_COMMENT_NOTIFY_EMAIL

                    'PS_COMMENTS_MARK_EMAILS' => [
                        'title' => $this->l('E-mail\'s for highlighted comments'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                        'hint' => $this->l('Separated by comma.'),
                        'desc' => $this->l('Type e-mails of customer which comments will be highlighted on the comment list'),
                    ],
                ],
            ],

            'facebook_comments' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Facebook comments - settings'),
                'fields' => [
                    'PH_BLOG_FACEBOOK_MODERATOR' => [
                        'title' => $this->l('Facebook comments moderator User ID'),
                        'type' => 'text',
                        'size' => 55,
                    ], // PH_BLOG_FACEBOOK_MODERATOR

                    'PH_BLOG_FACEBOOK_APP_ID' => [
                        'title' => $this->l('Facebook application ID (may be required for comments moderation)'),
                        'type' => 'text',
                        'size' => 75,
                    ], // PH_BLOG_FACEBOOK_APP_ID

                    'PH_BLOG_FACEBOOK_COLOR_SCHEME' => [
                        'title' => $this->l('Faceboook comments color scheme'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'light' => $this->l('Light'),
                            'dark' => $this->l('Dark'),
                        ],
                    ], // PH_BLOG_FACEBOOK_COLOR_SCHEME
                ],
            ],

            'facebook_share' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Facebook sharing - settings'),
                'fields' => [
                    'PH_BLOG_IMAGE_FBSHARE' => [
                        'title' => $this->l('Which image use as a image shared on Facebook?'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'featured' => $this->l('Featured'),
                            'thumbnail' => $this->l('Thumbnail'),
                        ],
                    ], // PH_BLOG_IMAGE_FBSHARE
                ],
            ],

            'disqus_comments' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Disqus comments - settings'),
                'fields' => [
                    'PH_BLOG_DISQUS_SHORTNAME' => [
                        'title' => $this->l('Shortname'),
                        'type' => 'text',
                        'size' => 55,
                    ], // PH_BLOG_DISQUS_SHORTNAME
                ],
            ],

            'comments_spam_protection' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Comments - Spam Protection for native comments system') . ' - reCAPTCHA v2, checkbox version',
                'info' => '<div class="alert alert-info">' . $this->l('Spam protection is provided by Google reCAPTCHA service, to gain keys:') . '
                    <ol>
                        <li>' . $this->l('Login to your Google Account and go to this page:') . ' https://www.google.com/recaptcha/admin</li>
                        <li>' . $this->l('Register a new site') . '</li>
                        <li>' . $this->l('Get Site Key and Secret Key and provide these keys here in Settings') . '</li>
                        <li>' . $this->l('Remember: if you do not specify the correct keys, the captcha will not work') . '</li>
                    </ol>
                </div>',
                'fields' => [
                    'PH_BLOG_COMMENTS_RECAPTCHA' => [
                        'title' => $this->l('Enable spam protection?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENTS_RECAPTCHA

                    'PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY' => [
                        'title' => $this->l('Site key:'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => false,
                    ], // PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY

                    'PH_BLOG_COMMENTS_RECAPTCHA_SECRET_KEY' => [
                        'title' => $this->l('Secret key:'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => false,
                    ], // PH_BLOG_COMMENTS_RECAPTCHA_SECRET_KEY

                    'PH_BLOG_COMMENTS_RECAPTCHA_THEME' => [
                        'title' => $this->l('reCAPTCHA color scheme:'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'light' => $this->l('Light'),
                            'dark' => $this->l('Dark'),
                        ],
                    ], // PH_BLOG_COMMENTS_RECAPTCHA_THEME
                ],
            ],

            'thumbnails' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Thumbnails Settings'),
                'info' => '<div class="alert alert-info">' . $this->l('Remember to regenerate thumbnails after doing changes here') . '</div>',
                'fields' => [
                    'PH_BLOG_THUMB_METHOD' => [
                        'title' => $this->l('Resize method:'),
                        'cast' => 'intval',
                        'desc' => $this->l('Select wich method use to resize thumbnail. Adaptive resize: What it does is resize the image to get as close as possible to the desired dimensions, then crops the image down to the proper size from the center.'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            '1' => $this->l('Adaptive resize (recommended)'),
                            '2' => $this->l('Crop from center'),
                        ],
                    ], // PH_BLOG_THUMB_METHOD

                    'PH_BLOG_THUMB_X' => [
                        'title' => $this->l('Default thumbnail width (px)'),
                        'cast' => 'intval',
                        'desc' => $this->l('Default: 255 (For PrestaShop 1.5), 420 (For PrestaShop 1.6), 600 (For PrestaShop 1.7)'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_X

                    'PH_BLOG_THUMB_Y' => [
                        'title' => $this->l('Default thumbnail height (px)'),
                        'cast' => 'intval',
                        'desc' => $this->l('Default: 200 (For PrestaShop 1.5 and 1.6)'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_Y

                    'PH_BLOG_THUMB_X_WIDE' => [
                        'title' => $this->l('Default thumbnail width (wide version) (px)'),
                        'cast' => 'intval',
                        'desc' => $this->l('Default: 535 (For PrestaShop 1.5), 870 (For PrestaShop 1.6), 1000 (For PrestaShop 1.7)'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_X_WIDE

                    'PH_BLOG_THUMB_Y_WIDE' => [
                        'title' => $this->l('Default thumbnail height (wide version) (px)'),
                        'cast' => 'intval',
                        'desc' => $this->l('Default: 350 (For PrestaShop 1.5 and 1.6)'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_Y_WIDE
                ],
            ],

            'troubleshooting' => [
                'submit' => ['title' => $this->l('Update'), 'class' => 'button'],
                'title' => $this->l('Troubleshooting'),
                'fields' => [
                    'PH_BLOG_RELATED_PRODUCTS_USE_DEFAULT_LIST' => [
                        'title' => $this->l('Use product list from your theme for related products?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'desc' => $this->l('By default Blog for PrestaShop uses default-bootstrap product list markup for related products, you can switch this option to load your product-list.tpl instead. In PrestaShop 1.7 we always use theme products list.'),
                        'type' => 'bool',
                    ], // PH_BLOG_RELATED_PRODUCTS_USE_DEFAULT_LIST

                    'PH_BLOG_LOAD_FONT_AWESOME' => [
                        'title' => $this->l('Load FontAwesome from module? Only for PS 1.6.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'desc' => $this->l('Important: Blog for PrestaShop uses fa fa-iconname format instead of icon-iconname format used by default in PrestaShop.'),
                        'type' => 'bool',
                    ], // PH_BLOG_LOAD_FONT_AWESOME

                    'PH_BLOG_LOAD_BXSLIDER' => [
                        'title' => $this->l('Load BxSlider from module? Only for PS 1.6.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_LOAD_BXSLIDER

                    // 'PH_BLOG_LOAD_MASONRY' => array(
                    //     'title' => $this->l('Load Masonry from module?'),
                    //     'validation' => 'isBool',
                    //     'cast' => 'intval',
                    //     'required' => true,
                    //     'type' => 'bool',
                    // ), // PH_BLOG_LOAD_MASONRY

                    'PH_BLOG_LOAD_FITVIDS' => [
                        'title' => $this->l('Load FitVids from module?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_LOAD_FITVIDS

                    'PH_BLOG_WAREHOUSE_COMPAT' => [
                        'title' => $this->l('Force Warehouse theme compatibility?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_WAREHOUSE_COMPAT
                ],
            ],
        ];

        $widgets_options = [];
        $widgets_options = array_merge($relatedPosts, []);

        $import_settings = [
            'import_settings' => [
                'submit' => ['title' => $this->l('Import settings'), 'class' => 'button'],
                'title' => $this->l('Import settings'),
                'fields' => [
                    'PH_BLOG_IMPORT_SETTINGS' => [
                        'title' => $this->l('Paste here content of your settings file to import'),
                        'show' => false,
                        'required' => false,
                        'type' => 'textarea',
                        'cols' => '70',
                        'rows' => '10',
                    ], // PH_BLOG_IMPORT_SETTINGS
                ], ],
        ];

        //$this->hide_multishop_checkbox = true;
        $this->fields_options = array_merge($standard_options, $widgets_options, $import_settings);

        return parent::renderOptions();
    }

    public static function prepareValueForLangs($value)
    {
        $languages = Language::getLanguages(false);

        $output = [];

        foreach ($languages as $lang) {
            $output[$lang['id_lang']] = $value;
        }

        return $output;
    }

    public static function getValueForLangs($field)
    {
        $languages = Language::getLanguages(false);

        $output = [];

        foreach ($languages as $lang) {
            $output[$lang['id_lang']] = Configuration::get($field, $lang['id_lang']);
        }

        return $output;
    }

    public function beforeUpdateOptions()
    {
        $importSettings = Tools::getValue('PH_BLOG_IMPORT_SETTINGS', false);

        if (trim($importSettings) != '') {
            if (!is_array(unserialize($importSettings))) {
                die(Tools::displayError('File with settings is invalid'));
            }

            $settings = unserialize($importSettings);
            $simple_fields = [];

            foreach ($this->fields_options as $category_data) {
                if (!isset($category_data['fields'])) {
                    continue;
                }

                foreach ($category_data['fields'] as $name => $field) {
                    $simple_fields[$name] = $field;
                }
            }

            foreach ($settings as $conf_name => $conf_value) {
                Configuration::deleteByName($conf_name);

                // if($simple_fields[$conf_name]['type'] == 'textLang')
                //     Configuration::updateValue($conf_name, self::prepareValueForLangs($conf_value));
                // else
                //     Configuration::updateValue($conf_name, $conf_value);
                Configuration::updateValue($conf_name, $conf_value);
            }

            Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getValue('token') . '&conf=6');
        }

        $customCSS = '/** custom css for SimpleBlog **/' . PHP_EOL;
        $customCSS .= Tools::getValue('PH_BLOG_CSS', false);

        if ($customCSS) {
            $handle = _PS_MODULE_DIR_ . 'ph_simpleblog/css/custom.css';

            if (!file_put_contents($handle, $customCSS)) {
                die(Tools::displayError('Problem with saving custom CSS, contact with module author'));
            }
        }

        // delete routing from PREFIX_configuration
        Db::getInstance()->query(
            'DELETE FROM `'._DB_PREFIX_.'configuration`
            WHERE `name` LIKE \'PS_ROUTE_module-ph_simpleblog%\''
        );
    }

    public function initContent()
    {
        $this->multiple_fieldsets = true;

        if (Tools::isSubmit('regenerateThumbnails')) {
            SimpleBlogPost::regenerateThumbnails();
            Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getValue('token') . '&conf=9');
        }

        if (Tools::isSubmit('submitExportSettings')) {
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename=ph_simpleblog_configuration_' . date('d-m-Y') . '.txt');

            $configs = [];
            foreach ($this->fields_options as $category_data) {
                if (!isset($category_data['fields'])) {
                    continue;
                }

                $fields = $category_data['fields'];

                foreach ($fields as $field => $values) {
                    if ($values['type'] == 'textLang') {
                        $configs[$field] = self::getValueForLangs($field);
                    } else {
                        $configs[$field] = Configuration::get($field);
                    }
                }
            }

            echo serialize($configs);

            exit();
        }

        $this->context->smarty->assign([
            'content' => $this->content,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
        ]);

        parent::initContent();
    }

    public function processUpdateOptions()
    {
        parent::processUpdateOptions();
        if (empty($this->errors)) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminSimpleBlogSettings') . '&conf=6');
        }
    }
}
