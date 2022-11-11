{*
* Do not edit the file if you want to upgrade the module in future.
*
* @author    Globo Jsc <contact@globosoftware.net>
* @copyright 2020 Globo., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
{if $combination_html == 'check_box'}
    <div class="gform_variant-list"><ul class="nav">{foreach from=$combinations item=combination}<li><div class="checkbox"><label class="col-lg-12" for="gform_attribute_check_{$combination['id_product_attribute']|escape:'html':'UTF-8'}"><input type="checkbox" name="gform_attribute[{$id_product|escape:'html':'UTF-8'}][]" id="gform_attribute_check_{$combination['id_product_attribute']|escape:'html':'UTF-8'}" value="{$combination['id_product_attribute']|escape:'html':'UTF-8'}" {$combination['checked']|escape:'html':'UTF-8'}> {$combination['attributes']|escape:'html':'UTF-8'} - {$combination['combination_price']|escape:'html':'UTF-8'} </label> </div> </li>{/foreach} </ul></div>
{/if}