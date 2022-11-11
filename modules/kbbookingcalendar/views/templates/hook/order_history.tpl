<script>
    {if isset($history_reorder)}
    var history_reorder = "{$history_reorder}";
    var kb_history_orders = '{$kb_history_orders nofilter}';{* Variable contains HTML/CSS/JSON, escape not required *}
    {/if}
    {if isset($kb_cart_validate)}
    var kb_cart_validate = "{$kb_cart_validate}";
    var kb_cart_prod = '{$kb_cart_prod nofilter}';{* Variable contains HTML/CSS/JSON, escape not required *}
    {/if}
</script>
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2019 Knowband
* @license   see file: LICENSE.txt
*
*}