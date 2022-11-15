<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

class LRPAjaxResponse
{
    private $error = false;

    private $response_array = array();

    public function hasErrors()
    {
        return $this->error;
    }

    public function addMessage($message, $dom_element_id, $error = true)
    {
        $this->response_array[] = array(
            'message' => $message,
            'dom_element' => $dom_element_id,
            'error' => $error
        );
        if ($error) $this->error = true;
    }

    public function ajaxDie()
    {
        $response = new stdClass();
        $response->meta['error'] = $this->hasErrors();
        $response->content = $this->response_array;
        die (Tools::jsonEncode($response));
    }

    /**
     * return an empty array to calling ajax function to satisfy the success call
     */
    public function ajaxOk()
    {
        die (Tools::jsonEncode(array()));
    }
}
