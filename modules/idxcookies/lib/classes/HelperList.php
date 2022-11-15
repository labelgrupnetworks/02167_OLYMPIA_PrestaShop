<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2019 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

class IdxrHelperList_3_0_2 extends HelperList
{
    public $use_cookie = true;

    public $key_filtros;

    public function __construct()
    {
        parent::__construct();

        $this->shopLinkType = '';
        $this->simple_header = false;
        $this->actions = array('edit', 'delete');
        $this->show_toolbar = true;
        $this->_pagination = array(20, 50, 100);
    }

    /**
     * Display list header (filtering, pagination and column names)
     */
    public function displayListHeader()
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        $id_cat = (int)Tools::getValue('id_'.($this->is_cms ? 'cms_' : '').'category');

        if (!isset($token) || empty($token)) {
            $token = $this->token;
        }
        /* Determine total page number */
        $pagination = $this->_default_pagination;
        if (in_array((int)Tools::getValue($this->list_id.'_pagination'), $this->_pagination)) {
            $pagination = (int)Tools::getValue($this->list_id.'_pagination');
        } elseif (isset($this->context->cookie->{$this->list_id.'_pagination'}) && $this->context->cookie->{$this->list_id.'_pagination'}) {
            $pagination = $this->context->cookie->{$this->list_id.'_pagination'};
        }

        $total_pages = max(1, ceil($this->listTotal / $pagination));

        $identifier = Tools::getIsset($this->identifier) ? '&'.$this->identifier.'='.(int)Tools::getValue($this->identifier) : '';
        $order = '';
        if (Tools::getIsset($this->table.'Orderby')) {
            $order = '&'.$this->table.'Orderby='.urlencode($this->orderBy).'&'.$this->table.'Orderway='.urlencode(strtolower($this->orderWay));
        }

        $action = $this->currentIndex.$identifier.'&token='.$token.'#'.$this->list_id;

        /* Determine current page number */
        $page = (int)Tools::getValue('submitFilter'.$this->list_id);

        if (!$page) {
            $page = 1;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $this->page = (int)$page;

        /* Choose number of results per page */
        $selected_pagination = Tools::getValue(
            $this->list_id.'_pagination',
            isset($this->context->cookie->{$this->list_id.'_pagination'}) ? $this->context->cookie->{$this->list_id.'_pagination'} : $this->_default_pagination
        );

        if (!isset($this->table_id) && $this->position_identifier && (int)Tools::getValue($this->position_identifier, 1)) {
            $this->table_id = substr($this->identifier, 3, strlen($this->identifier));
        }

        if ($this->position_identifier && ($this->orderBy == 'position' && $this->orderWay != 'DESC')) {
            $table_dnd = true;
        }

        $prefix = isset($this->controller_name) ? str_replace(array('admin', 'controller'), '', Tools::strtolower($this->controller_name)) : '';
        $ajax = false;
        foreach ($this->fields_list as $key => $params) {
            if (!isset($params['type'])) {
                $params['type'] = 'text';
            }

            $value_key = $prefix.$this->list_id.'Filter_'.(array_key_exists('filter_key', $params) ? $params['filter_key'] : $key);
            if ($key == 'active' && strpos($key, '!') !== false) {
                $keys = explode('!', $params['filter_key']);
                $value_key = $keys[1];
            }
            $value = Context::getContext()->cookie->{$value_key};
            if (!$value && Tools::getIsset($value_key)) {
                $value = Tools::getValue($value_key);
            } elseif (isset($params['value'])) {
                $value = $params['value'];
            }
            switch ($params['type']) {
                case 'bool':
                    if (isset($params['ajax']) && $params['ajax']) {
                        $ajax = true;
                    }
                    break;

                case 'date':
                case 'datetime':
                    if (is_string($value)) {
                        $value = Tools::unSerialize($value);
                    }
                    if (!Validate::isCleanHtml($value[0]) || !Validate::isCleanHtml($value[1])) {
                        $value = '';
                    }
                    $name = $this->list_id.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key);
                    $name_id = str_replace('!', '__', $name);

                    $params['id_date'] = $name_id;
                    $params['name_date'] = $name;

                    $this->context->controller->addJqueryUI('ui.datepicker');
                    break;

                case 'select':
                    foreach ($params['list'] as $option_value => $option_display) {
                        if (isset(Context::getContext()->cookie->{$prefix.$this->list_id.'Filter_'.$params['filter_key']})
                            && Context::getContext()->cookie->{$prefix.$this->list_id.'Filter_'.$params['filter_key']} == $option_value
                            && Context::getContext()->cookie->{$prefix.$this->list_id.'Filter_'.$params['filter_key']} != '') {
                            $this->fields_list[$key]['select'][$option_value]['selected'] = 'selected';
                        }
                    }
                    break;

                case 'text':
                    if (!Validate::isCleanHtml($value)) {
                        $value = '';
                    }
            }

            $params['value'] = $value;
            $this->fields_list[$key] = $params;
        }

        $has_value = false;
        $has_search_field = false;

        foreach ($this->fields_list as $key => $field) {
            if (isset($field['value']) && $field['value'] !== false && $field['value'] !== '') {
                if (is_array($field['value']) && trim(implode('', $field['value'])) == '') {
                    continue;
                }

                $has_value = true;
                break;
            }
            if (!(isset($field['search']) && $field['search'] === false)) {
                $has_search_field = true;
            }
        }

        Context::getContext()->smarty->assign(array(
            'page' => $page,
            'simple_header' => $this->simple_header,
            'total_pages' => $total_pages,
            'selected_pagination' => $selected_pagination,
            'pagination' => $this->_pagination,
            'list_total' => $this->listTotal,
            'sql' => isset($this->sql) && $this->sql ? str_replace('\n', ' ', str_replace('\r', '', $this->sql)) : false,
            'table' => $this->table,
            'bulk_actions' => $this->bulk_actions,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_scroll' => $this->toolbar_scroll,
            'toolbar_btn' => $this->toolbar_btn,
            'has_bulk_actions' => $this->hasBulkActions($has_value),
            'filters_has_value' => (bool)$has_value
        ));
        $isMultiShopActive = Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
        $this->header_tpl->assign(array_merge(array(
            'ajax' => $ajax,
            'multishop_active' => $isMultiShopActive,
            'title' => array_key_exists('title', $this->tpl_vars) ? $this->tpl_vars['title'] : $this->title,
            'show_filters' => ((count($this->_list) > 1 && $has_search_field) || $has_value),
            'currentIndex' => $this->currentIndex,
            'action' => $action,
            'is_order_position' => $this->position_identifier && $this->orderBy == 'position',
            'order_way' => $this->orderWay,
            'order_by' => $this->orderBy,
            'fields_display' => $this->fields_list,
            'delete' => in_array('delete', $this->actions),
            'identifier' => $this->identifier,
            'id_cat' => $id_cat,
            'shop_link_type' => $this->shopLinkType,
            'has_actions' => !empty($this->actions),
            'table_id' => isset($this->table_id) ? $this->table_id : null,
            'table_dnd' => isset($table_dnd) ? $table_dnd : null,
            'name' => isset($name) ? $name : null,
            'name_id' => isset($name_id) ? $name_id : null,
            'row_hover' => $this->row_hover,
            'list_id' => isset($this->list_id) ? $this->list_id : $this->table,
            'token' => $this->token,
        ), $this->tpl_vars));

        return $this->header_tpl->fetch();
    }

    public function resetFiltros()
    {
        Context::getContext()->cookie->__unset($this->key_filtros);
    }

    public function getFiltros($numResultados = 20)
    {
        if (!$this->key_filtros) {
            $this->key_filtros = $this->table;
        }
        $context = Context::getContext();
        if (Tools::isSubmit('submitReset'.$this->table)) {
            $pagina = 1;
            $filtros = array(
                'pagina' => $pagina,
                'numResultados' => $numResultados,
            );
            if ($this->use_cookie) {
                $context->cookie->__set($this->key_filtros, serialize($filtros));
            }
            Tools::redirectAdmin($this->currentIndex.'&token='.$this->token);
            die();
        } elseif (Tools::isSubmit('submitFilter'.$this->table)) {
            if (Tools::getValue('submitFilter'.$this->table) == 0) {
                $pagina = 1;
            } else {
                $pagina = (int)Tools::getValue('submitFilter'.$this->table);
            }
            if (Tools::isSubmit($this->key_filtros.'_pagination')) {
                $numResultados = (int)Tools::getValue($this->table.'_pagination');
            } elseif (Tools::isSubmit('selected_pagination')) {
                $numResultados = (int)Tools::getValue('selected_pagination');
            } else {
                $numResultados = 20;
            }
            $filtrosCookies = unserialize($context->cookie->__get($this->key_filtros));
            $filtros = array();
            $filtros['pagina'] = $pagina;
            $filtros['numResultados'] = $numResultados;
            $filter = $this->table."Filter_";
            foreach ($this->getAllValues() as $clave => $valor) {
                if (Tools::substr($clave, 0, Tools::strlen($filter)) === $filter) {
                    $key = str_replace($filter, "", $clave);
                    if (Tools::isSubmit($clave)) {
                        $value = Tools::getValue($clave);
                    } elseif (isset($filtrosCookies[$clave])) {
                        $value = $filtrosCookies[$clave];
                    }
                    if (is_array($value)) {
                        $empty = false;
                        foreach ($value as $valor) {
                            if (empty($valor)) {
                                $empty = true;
                                break;
                            }
                        }
                        if (!$empty) {
                            $filtros[$key] = $value;
                        }
                    } elseif (isset($value) && $value != "") {
                        $filtros[$key] = $value;
                    }
                }
            }
        } elseif ($this->use_cookie && $context->cookie->__get($this->key_filtros)) {
            $filtros = unserialize($context->cookie->__get($this->key_filtros));
            $pagina = $filtros['pagina'];
            if (isset($filtros['numResultados']) && is_numeric($filtros['numResultados']) && in_array($numResultados, $this->_pagination)) {
                $numResultados = $filtros['numResultados'];
            }
        } else {
            $pagina = 1;
            $filtros = array();
        }
        if (!is_numeric($pagina)) {
            $pagina = 1;
        }
        if (!is_numeric($numResultados) || $numResultados <= 0) {
            $numResultados = 20;
        }
        $filtros['pagina'] = $pagina;
        $filtros['numResultados'] = $numResultados;
        $this->_default_pagination = $numResultados;
        if (Tools::isSubmit($this->table.'Orderby')) {
            $filtros['orderBy'] = Tools::getValue($this->table.'Orderby');
            $this->orderBy = Tools::strtoupper($filtros['orderBy']);
        }
        if (Tools::isSubmit($this->table.'Orderway')) {
            $filtros['orderWay'] = Tools::getValue($this->table.'Orderway');
            $this->orderWay = Tools::strtoupper($filtros['orderWay']);
        }
        if ($this->use_cookie) {
            $context->cookie->__set($this->key_filtros, serialize($filtros));
        }
        return $filtros;
    }

    public function getAllValues()
    {
        return $_POST + $_GET;
    }
}
