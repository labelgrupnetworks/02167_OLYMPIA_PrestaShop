{extends file='helpers/form/form.tpl'}

{block name='defaultForm'}

    
    {$form} {*Variable contains html content, escape not required*}
    <script type="text/javascript">
        var validate_css_length = "{l s='Length of CSS should be less than 10000' mod='kbbookingcalendar'}";
        var kb_numeric = "{l s='Field should be numeric.' mod='kbbookingcalendar'}";
        var kb_positive = "{l s='Field should be positive.' mod='kbbookingcalendar'}";
            velovalidation.setErrorLanguage({
            alphanumeric: "{l s='Field should be alphanumeric.' mod='kbbookingcalendar'}",
            digit_pass: "{l s='Password should contain atleast 1 digit.' mod='kbbookingcalendar'}",
            empty_field: "{l s='Field cannot be empty.' mod='kbbookingcalendar'}",
            number_field: "{l s='You can enter only numbers.' mod='kbbookingcalendar'}",            
            positive_number: "{l s='Number should be greater than 0.' mod='kbbookingcalendar'}",
            maxchar_field: "{l s='Field cannot be greater than # characters.' mod='kbbookingcalendar'}",
            minchar_field: "{l s='Field cannot be less than # character(s).' mod='kbbookingcalendar'}",
            invalid_date: "{l s='Invalid date format.' mod='kbbookingcalendar'}",
            valid_amount: "{l s='Field should be numeric.' mod='kbbookingcalendar'}",
            valid_decimal: "{l s='Field can have only upto two decimal values.' mod='kbbookingcalendar'}",
            maxchar_size: "{l s='Size cannot be greater than # characters.' mod='kbbookingcalendar'}",
            specialchar_size: "{l s='Size should not have special characters.' mod='kbbookingcalendar'}",
            maxchar_bar: "{l s='Barcode cannot be greater than # characters.' mod='kbbookingcalendar'}",
            positive_amount: "{l s='Field should be positive.' mod='kbbookingcalendar'}",
            maxchar_color: "{l s='Color could not be greater than # characters.' mod='kbbookingcalendar'}",
            invalid_color: "{l s='Color is not valid.' mod='kbbookingcalendar'}",
            specialchar: "{l s='Special characters are not allowed.' mod='kbbookingcalendar'}",
            script: "{l s='Script tags are not allowed.' mod='kbbookingcalendar'}",
            style: "{l s='Style tags are not allowed.' mod='kbbookingcalendar'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='kbbookingcalendar'}",
              not_image: "{l s='Uploaded file is not an image' mod='kbbookingcalendar'}",
            image_size: "{l s='Uploaded file size must be less than #.' mod='kbbookingcalendar'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='kbbookingcalendar'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='kbbookingcalendar'}",
});
    </script>
{/block}
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

