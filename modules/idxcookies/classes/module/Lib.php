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

trait IdxrcookiesLib
{
    public function display($file, $template, $cache_id = null, $compile_id = null)
    {
        return parent::display($this->getModuleFile(), $template, $cache_id, $compile_id);
    }

    protected function execute($method, $params)
    {
        if (!is_callable(array($this, $method))) {
            return false;
        }
        return call_user_func_array(array($this, $method), $params);
    }

    protected function fetchTemplate($template, array $variables = array())
    {
        $tpl = $this->context->smarty->createTemplate($this->getTemplatePath('views/templates/'.$template), $this->context->smarty);
        $tpl->assign($variables);
        return $tpl->fetch();
    }

    protected function displayTemplate($template, $variables = array())
    {
        $this->context->smarty->assign($variables);
        return $this->display($this->getModuleFile(), 'views/templates/'.$template);
    }

    protected function displayHtml($html)
    {
        return $html;
    }
}
