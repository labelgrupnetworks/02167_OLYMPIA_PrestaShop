<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class SimpleBlogComment extends ObjectModel
{
    private static $commentHierarchy = [];

    public $id_simpleblog_comment;
    public $id_simpleblog_post;
    public $id_parent = 0;
    public $id_customer;
    public $id_guest;
    public $name;
    public $email;
    public $comment;
    public $active = 0;
    public $ip;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'simpleblog_comment',
        'primary' => 'id_simpleblog_comment',
        'multilang' => false,
        'fields' => [
            'id_simpleblog_comment' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_simpleblog_post' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_parent' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_customer' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'id_guest' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'size' => 255,
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'size' => 140,
            ],
            'comment' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
            ],
            'active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'ip' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'size' => 255,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
        ],
    ];

    public function __construct($id_simpleblog_comment = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_simpleblog_comment, $id_lang, $id_shop);
    }

    public static function getComments($id_simpleblog_post, $withHierarchy = true)
    {
        $comments = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT id_simpleblog_comment, id_parent
            FROM ' . _DB_PREFIX_ . 'simpleblog_comment
            WHERE id_simpleblog_post = ' . (int) $id_simpleblog_post . '
            AND active = 1 AND id_parent = 0'
        );

        if (!$comments) {
            return [];
        }

        foreach ($comments as &$comment) {
            $comment = self::presentComment(0, new SimpleBlogComment($comment['id_simpleblog_comment']));
            $comment['replies'] = self::getReplies($comment['id']);
        }

        return $comments;
    }

    public static function getReplies($id_simpleblog_comment)
    {
        $comments = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT id_simpleblog_comment, id_parent
            FROM ' . _DB_PREFIX_ . 'simpleblog_comment
            WHERE id_parent = ' . (int) $id_simpleblog_comment . '
            AND active = 1'
        );

        if (!$comments) {
            return [];
        }

        foreach ($comments as &$comment) {
            $comment = self::presentComment(1, new SimpleBlogComment($comment['id_simpleblog_comment']));
        }

        return $comments;
    }

    public static function presentComment($depth = 1, SimpleBlogComment $comment)
    {
        $output['depth'] = $depth;
        $output['id'] = (int) $comment->id;
        $output['name'] = $comment->name;
        $output['email'] = $comment->email;
        $output['comment'] = $comment->comment;
        $output['date_add'] = $comment->date_add;

        $highlightedEmails = trim(preg_replace('/\s/', '', Configuration::get('PS_COMMENTS_MARK_EMAILS')), ',');

        if ($output['email'] && in_array($output['email'], explode(',', $highlightedEmails))) {
            $output['is_highlighted'] = 1;
        } else {
            $output['is_highlighted'] = 0;
        }

        return $output;
    }

    public static function getCommentsCount($id_simpleblog_post)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT COUNT(id_simpleblog_comment)
            FROM ' . _DB_PREFIX_ . 'simpleblog_comment
            WHERE id_simpleblog_post = ' . (int) $id_simpleblog_post . '
            AND active = 1'
        );
    }
}
