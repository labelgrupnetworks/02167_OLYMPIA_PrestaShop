<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class SimpleBlogPostAuthor extends ObjectModel
{
    public $id_simpleblog_author;
    public $firstname;
    public $lastname;
    public $photo;
    public $email;
    public $facebook;
    public $google;
    public $linkedin;
    public $twitter;
    public $instagram;
    public $phone;
    public $www;
    public $active = 1;

    public $bio;
    public $additional_info;

    public $link_rewrite;

    public static $definition = [
        'table' => 'simpleblog_author',
        'primary' => 'id_simpleblog_author',
        'multilang' => true,
        'fields' => [
            'firstname' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 255,
            ],
            'lastname' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 255,
            ],
            'photo' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'size' => 9999,
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 140,
            ],
            'facebook' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
            ],
            'google' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
            ],
            'linkedin' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
            ],
            'twitter' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
            ],
            'instagram' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
            ],
            'phone' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isPhoneNumber',
            ],
            'www' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isUrl',
            ],
            'active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'bio' => [
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang' => true,
            ],
            'additional_info' => [
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang' => true,
            ],
            'link_rewrite' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isLinkRewrite',
                'required' => true,
                'size' => 128,
            ],
        ],
    ];

    public function __construct($id_simpleblog_author = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_simpleblog_author, $id_lang, $id_shop);
    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getPosts()
    {
        $finder = new BlogPostsFinder();
        $finder->setAuthor($this->id_simpleblog_author);

        return $finder->findPosts();
    }

    public function getUrl()
    {
        return Context::getContext()->link->getModuleLink('ph_simpleblog', 'author', ['rewrite' => $this->link_rewrite]);
    }

    public static function getAuthors($idLang = null)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $collection = new PrestaShopCollection('SimpleBlogPostAuthor', $idLang);
        $collection->where('active', '=', '1');

        return $collection->getResults();
    }

    public static function getByRewrite($rewrite)
    {
        if (!Validate::isLinkRewrite($rewrite)) {
            throw new Exception('Cannot get author information, invalid data');
        }

        $sql = new DbQuery();
        $sql->select('id_simpleblog_author');
        $sql->from('simpleblog_author', 'a');
        $sql->where('a.active = 1');
        $sql->where('a.link_rewrite = "' . pSQL($rewrite) . '"');

        $idAuthor = Db::getInstance()->getValue($sql);

        if ($idAuthor) {
            return new self((int) $idAuthor, Context::getContext()->language->id);
        }

        return false;
    }

    public static function getAll()
    {
        $sql = new DbQuery();
        $sql->select('CONCAT (sba.`firstname`, " ", sba.`lastname`) as name, sba.*, sbal.bio, sbal. additional_info');
        $sql->from('simpleblog_author', 'sba');
        $sql->leftJoin('simpleblog_author_lang', 'sbal', 'sba.`id_simpleblog_author` = sbal.`id_simpleblog_author` AND sbal.`id_lang` = ' . Context::getContext()->language->id);
        $sql->where('sba.active = 1');
        $sql->orderBy('id_simpleblog_author ASC');

        return Db::getInstance()->executeS($sql);
    }
}
