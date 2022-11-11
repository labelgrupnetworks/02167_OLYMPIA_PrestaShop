{if isset($kb_product_room_form)}
    {$kb_product_room_form}
    {if isset($admin_check_room_type_url)} 
        <script>
        var admin_check_room_type_url = "{$admin_check_room_type_url nofilter}";{* Variable contains HTML/CSS/JSON, escape not required *}
        </script>
    {/if}
{/if}

<script>
    {if isset($kb_room_type_field_value)}
    var kb_room_type_field_value = '{$kb_room_type_field_value}';
    {/if}
    var check_for_all = "{l s='Kindly check for all available languages' mod='kbbookingcalendar'}";
    var empty_field = "{l s='Field cannot be empty' mod='kbbookingcalendar'}";
    var please_check_kb_fields = "{l s='Fields cannot be blank.Please check for all the languages in the field.' mod='kbbookingcalendar'}";
    var select_placeholder = "{l s='Select' mod='kbbookingcalendar'}";
    var no_match_err = "{l s='No Matches Found' mod='kbbookingcalendar'}";
    var kb_category_empty = "{l s='Select Category' mod='kbbookingcalendar'}";
    var select_empty = "{l s='Please select' mod='kbbookingcalendar'}";
    var empty_field = "{l s='Field cannot be empty' mod='kbbookingcalendar'}";
    var currentText = '{l s='Now'  mod='kbbookingcalendar' js=1}';
    var closeText = '{l s='Done'  mod='kbbookingcalendar' js=1}';
    var timeonlytext = '{l s='Choose Time'  mod='kbbookingcalendar' js=1}';
    var upload_image_empty = "{l s='Please upload image' mod='kbbookingcalendar'}";
    var end_date_error = "{l s='To date cannot be previous/same to From date.' mod='kbbookingcalendar'}";
    var end_time_error = "{l s='To Time cannot be previous/same to From Time.' mod='kbbookingcalendar'}";
    var end_start_error = "{l s='Check-out Time cannot be previous to Check-In Time.' mod='kbbookingcalendar'}";
    var store_category_mand = "{l s='Please select category' mod='kbbookingcalendar'}";
    var star_rating_empty = "{l s='Please select star rating' mod='kbbookingcalendar'}";
    var min_max_days_valid = "{l s='Maximum days cannot be previous/same to Minimum days.' mod='kbbookingcalendar'}";
    var min_max_hrs_valid = "{l s='Maximum Hours cannot be previous/same to Minimum Hours.' mod='kbbookingcalendar'}";
    var kb_image_valid = "{l s='Uploaded file is not an image' mod='kbbookingcalendar'}";
    var kb_image_size_valid = "{l s='Uploaded file size must be less than 2Mb' mod='kbbookingcalendar'}";
    var select_product_type = "{l s='Please select any product type' mod='kbbookingcalendar'}";
    var kb_date_override_string = "{l s='The dates are override with ' mod='kbbookingcalendar'}";
    var kb_to_string = "{l s='to' mod='kbbookingcalendar'}";
    var kb_and_string = "{l s='&' mod='kbbookingcalendar'}";
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
        image_size: "{l s='Uploaded file size must be less than #.' mod='kbbookingcalendar'}",
        html_tags: "{l s='Field should not contain HTML tags.' mod='kbbookingcalendar'}",
        number_pos: "{l s='You can enter only positive numbers.' mod='kbbookingcalendar'}",
        empty_email: "{l s='Please enter Email.' mod='kbbookingcalendar'}",
        validate_email: "{l s='Please enter a valid Email.' mod='kbbookingcalendar'}",
        max_email: "{l s='Email cannot be greater than # characters.' mod='kbbookingcalendar'}",
        not_image: "{l s='Uploaded file is not an image' mod='kbbookingcalendar'}",
        validate_range: "{l s='Number is not in the valid range. It should be betwen # and @@' mod='kbbookingcalendar'}",
        valid_percentage: "{l s='Percentage should be in number.' mod='kbbookingcalendar'}",
        between_percentage: "{l s='Percentage should be between 0 and 100.' mod='kbbookingcalendar'}",
    });
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