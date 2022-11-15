
{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Jsc <contact@globosoftware.net>
* @copyright 2017 Globo., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<table  style="border-collapse:collapse;border-spacing:0px;width:100%;float: left;">
    <thead>
        <tr style="color: #777777; text-align:left; font-size: 14px;">
            <th> </th>
            <th> {l s='Product' mod='gformbuilderpro'}</th>
            <th> {l s='Price' mod='gformbuilderpro'}</th>
            <th> {l s='Quanity' mod='gformbuilderpro'}</th>
            <th> {l s='Total' mod='gformbuilderpro'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $gproducts key=keyproduct item=product}
            {assign var="linkpr" value=$links->getProductLink($product['id_product'], $product['link_rewrite'], $product['category'], null, $id_lang, $product['id_shop'], $product['id_product_attribute'], false, false, true)}
            <tr  style="width:100%;border-top: 1px solid #E5E7EA;">
                <td>
                    <a href="{$linkpr|escape:'htmlall':'UTF-8'}" target="_blank">
                        <img src="{$links->getImageLink($product['link_rewrite']|escape:'htmlall':'UTF-8', $product['id_image']|escape:'htmlall':'UTF-8', 'home_default')}" align="left" width="60" height="60" style="border:1px solid rgb(229,229,229);border-top-left-radius:8px;border-top-right-radius:8px;border-bottom-right-radius:8px;border-bottom-left-radius:8px;margin-right:15px"/>
                    </a>
                </td>
                <td>
                    <a href="{$linkpr|escape:'htmlall':'UTF-8'}" target="_blank" style="text-decoration: none;">
                        <span style="{if isset($name)}color:rgb(85,85,85);{/if}font-size:14px;font-weight:600;line-height:1.4">
                            {$product['name']|escape:'htmlall':'UTF-8'}
                        </span>
                    </a>
                </td>
                <td>
                    {$product['price']|escape:'htmlall':'UTF-8'}
                </td>
                <td>
                    {$product['cart_quantity']|escape:'htmlall':'UTF-8'}
                </td>
                <td>
                    {if isset($product['total_wt'])}{$product['total_wt']|escape:'htmlall':'UTF-8'}{else}{$product['total']|escape:'htmlall':'UTF-8'}{/if}
                </td>
            </tr>
        {/foreach}
        <tr  style="width:100%;border-top: 1px solid #E5E7EA;">
            <td colspan="5" style="text-align: right">
                {l s='Total' mod='gformbuilderpro'} : {$total|escape:'htmlall':'UTF-8'}
            </td>
        </tr>
    </tbody>
</table>
<div style="clear:both;"></div>