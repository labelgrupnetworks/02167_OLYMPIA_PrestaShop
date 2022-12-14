<?php 

include_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/label_provis.php');

$module = Module::getInstanceByName('label_provis');

// Test endpoint ecommerce_list
// $params = [
//     'itemtype' => Tools::getValue('itemtype', null), // tipo de producto que queramos obtener, si no se especifica, obtenemos todos los tipos
//     'installationId' => $module->getIdInstallation(), // El id de la instalación
//     'clienttype' => Tools::getValue('clienttype', null), // El id de tipo de cliente si queremos obtener lo disponible para este tipo de cliente
// ];
// $response = $module->request('ecommerce_list', $params);
// dump($response);
try{
    $customer = new Customer(5);
    $customer->id = null;
    $customer->email = 'vcinca2@labelgrup.com';
    $customer->add();
} catch (Exception $ex){
    dump($ex);
}