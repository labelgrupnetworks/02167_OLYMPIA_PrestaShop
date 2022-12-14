<?php
class Label_provisCronjobModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->module->defineSettings();
        $token = Tools::getValue('token');
        if ($token == $this->module->getCronToken()) {
            $id_shop = (int)Tools::getValue('id_shop');
            $action = pSQL(Tools::getValue('action'));
            // $time = pSQL(Tools::getValue('time', microtime(true)));
            if (Tools::getValue('complete')) {
                echo 'Complete';
            } elseif ($this->isAvailableAction($action)) {
                $this->indexProducts($action, $id_shop, $time);
            }
        }
        exit();
    }

    /**
     * Función que comprueba si la acción parametrizada está contemplada
     */
    private function isAvailableAction($action)
    {
        $actionList = [
            'test' => 1, 
            'login' => 1, 
            // ...
        ];
        return !empty($actionList[$action]);
    }

    /**
     * Función que ejecuta las acciones de cada endpoint
     */
    private function indexProducts($action, $id_shop, $total_indexed, $time)
    {
        $products_per_request = (int)Tools::getValue('products_per_request', 1000);
        $params = array(
            'id_shop' => $id_shop,
            'total_indexed' => (int)$total_indexed,
            'time' => $time,
            'products_per_request' => $products_per_request,
            'action' => $action,
        );
        if ($action == 'index-selected') {
            $ids = $this->module->formatIDs(explode('-', Tools::getValue('ids')));
            $indexed = $this->module->indexProduct($ids, false, array($id_shop));
            $params['total_indexed'] = count($this->module->formatIDs($indexed));
            $params['complete'] = 1;
        } elseif ($action == 'index-all') {
            $params['total_indexed'] += $this->module->reIndexProducts($time, $products_per_request, array($id_shop));
            $indexation_data = $this->module->getIndexationProcessData($time, true);
            if (empty($indexation_data[$id_shop]['missing'])) {
                $params['complete'] = 1;
            }
        } else {
            $params['total_indexed'] += $this->module->indexMissingProducts($products_per_request, array($id_shop));
            $indexation_data = $this->module->indexationInfo('count', array($id_shop));
            if (empty($indexation_data[$id_shop]['missing'])) {
                $params['complete'] = 1;
            }
        }
        $url = $this->module->getCronURL($id_shop, $params);
        Tools::redirect($url);
    }
}