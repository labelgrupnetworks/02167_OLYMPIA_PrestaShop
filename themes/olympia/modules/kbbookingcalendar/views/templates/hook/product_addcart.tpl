<div class="kb-booking-product-block" style="display: none;">
    <h5 class="h5">
        {l s='Reserva ahora' mod='kbbookingcalendar'}
    </h5>
    <div class="col-lg-12 kb-booking-product-price-block">
        <div class="booking-product-basic-info"> 
            {if $product_type =='appointment'}
                {if !empty($booking_product_details['service_type'])}
                    <p>
                        {l s='Service Type' mod='kbbookingcalendar'} : {if $booking_product_details['service_type'] =='home_service'}{l s='Home' mod='kbbookingcalendar'}{else}{l s='Branch' mod='kbbookingcalendar'}{/if}
                    </p>
                {/if}
            {/if}
            {if $product_type == 'hotel_booking'}
                {if isset($booking_product_details['star_rating']) && !empty($booking_product_details['star_rating'])}
                    <div class="velsof_star_ratings">
                        <div class="">
                            <div class="star_content clearfix">
                                {section name="i" start=0 loop=5 step=1}
                                    {if $booking_product_details['star_rating'] le $smarty.section.i.index}
                                        <div class="kb-star"></div>
                                    {else}
                                        <div class="kb-star star_on"></div>
                                    {/if}
                                {/section}
                            </div>
                        </div>
                    </div>
                {/if}
            {/if}
            {if $product_type != 'hotel_booking'}
                <div class="form-group">
                    <div class="qty">
                        {l s='Quantity:' mod='kbbookingcalendar'}&nbsp; <input autocomplete="off" type="number" value="1" class="kb_product_qty" min="1" max="100">
                    </div>
                </div>
            {/if}
        </div>
    </div>
    <div class="col-lg-12 booking-product-checkin-block">
        {if $product_type =='appointment'}
            <div class="form-group col-lg-5">
                <label class="control-label" style="text-align: left;">
                    {l s='Appointment Date' mod='kbbookingcalendar'}
                </label>
                <input class="form-control" name="kb_check_in" autocomplete="off">
            </div>
        {else}
            {if $product_type !='hotel_booking'}
                {if $booking_product_details['period_type'] == 'date_time'}
                    <div class="form-group col-lg-5">
                        <label class="control-label" style="text-align: left;">
                            {l s='Check-In' mod='kbbookingcalendar'}
                        </label>
                        <input class="form-control" name="kb_check_in" autocomplete="off">
                    </div>
                {else}
                    <div class="form-group col-lg-6">
                        <label class="control-label" style="text-align: left;">
                            {l s='Check-In' mod='kbbookingcalendar'}
                        </label>
                        <input class="form-control" name="kb_check_in" autocomplete="off">
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" style="text-align: left;">{l s='Check-Out' mod='kbbookingcalendar'}</label>
                        <input class="form-control" name="kb_check_out" autocomplete="off">
                    </div>
                {/if}
            {/if}
        {/if}

    </div>

    {if $booking_product_details['enable_product_map']}
        <div class="kb-booking-map-block">
            <div id="map"></div>
            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key={$map_api_key|escape:'htmlall':'UTF-8'}&callback=initialize">
            </script>
            <script>
                //code by rishabh to fix incorrect location   start here 
                var locations = '';
                    {if !empty($booking_product_details['address'])}
                    locations = [
                        ["<div class='velo-popup'><div class='gm_address'>{$booking_product_details['address']}</div>", '{$booking_product_details['latitude']|escape:'htmlall':'UTF-8'}', '{$booking_product_details['longitude']|escape:'htmlall':'UTF-8'}'],
                    ];{*escape not required*}
                    {/if}
                    function initialize() {
                        var myLatlng = new google.maps.LatLng('{$booking_product_details['latitude']|escape:'htmlall':'UTF-8'}', '{$booking_product_details['longitude']|escape:'htmlall':'UTF-8'}');
                        var myOptions = {
                            zoom: 8,
                            center: myLatlng,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }
                        var map = new google.maps.Map(document.getElementById("map"), myOptions);
                        setMarkers(map, locations);
                    }
                    //code by rishabh to fix incorrect location  end here
                    function setMarkers(map, locations) {  
                        if (locations.length > 0) {
                            var marker, i

                            for (i = 0; i < locations.length; i++)
                            {

                                var loan = locations[i][0]
                                var lat = locations[i][1]
                                var long = locations[i][2]

                                latlngset = new google.maps.LatLng(lat, long);
                                var marker = new google.maps.Marker({
                                    map: map, title: loan, position: latlngset, animation: google.maps.Animation.DROP
                                });
                                map.setCenter(marker.getPosition());


                                var content = loan;

                                var infowindow = new google.maps.InfoWindow()

                                google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                                    return function () {
                                        infowindow.setContent(content);
                                        infowindow.open(map, marker);
                                    };
                                })(marker, content, infowindow));
                            }
                        }
                        google.maps.event.addDomListener(window, "load", initialize);
                    }
            </script>
        </div>
    {/if}



    <input type="hidden" name="kb_booking_product" value="{$booking_product_details['id_booking_product']}">
    <input type="hidden" name="kb_id_product" value="{$booking_product_details['id_product']}">
    <input type="hidden" name="kb_product_type" value="{$booking_product_details['product_type']}">
    <input type="hidden" name="kb_period_type" value="{$booking_product_details['period_type']}">
    <input type="hidden" name="kb_product_price" value="{$product_price}">
    <input type="hidden" name="kb_product_date_valid" value="0">
    {if $product_type =="appointment"}
        <input type="hidden" name="kb_service_type" value="{$booking_product_details['service_type']}">
    {/if}
    {if isset($kbdisable_days)}
        <input type="hidden" name="kb_disable_days" value="{$kbdisable_days}">
    {/if}
    {if $product_type != 'hotel_booking'}
        <div>
            <button type="submit" class="btn btn-primary" id="kb-submit-booking-product-cart">{l s='Book Now' mod='kbbookingcalendar'}</button>
        </div>
    {elseif $product_type == 'hotel_booking'}
        {if !empty($room_category)}
            <div>
                <div class="form-group row">
                    <label class="col-md-3 form-control-label">{l s='Category' mod='kbbookingcalendar'}</label>
                    <div class="col-md-6">
                        <select name="room_category" id="kb-product-room-category-search" class="form-control form-control-select">
                            {foreach $room_category as $cat}
                                <option value="{$cat['id_booking_category']}">{$cat['name']}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
        {/if}
        <div>
            <script>
                var kb_hotel_url = "{$hotel_url nofilter}";
            </script>
        </div>
    {/if}

    {if isset($kb_setting) && !empty($kb_setting) && $kb_setting['display_price_rule']}
        {if isset($price_rule_applicable) && !empty($price_rule_applicable)}
            <div class="kb-product-price-rule-block">
                <h6>{l s='Price Rule is applicable:' mod='kbbookingcalendar'}</h6>
                {foreach $price_rule_applicable as $key => $rule}
                    <p>{$key+1}.&nbsp; <span class="kb-rule-label">{$rule['name']|capitalize} :</span>
                        {l s='Discount of' mod='kbbookingcalendar'} 
                        {if $rule['reduction_type']=='percentage'}
                            {$rule['reduction']|intval}%
                        {else}
                            {Tools::convertPrice($rule['reduction'])|string_format:"%.2f"}&nbsp;{$currency_sign}
                        {/if}
                        {if $rule['date_selection'] == 'date_range'}
                            {l s='from' mod='kbbookingcalendar'} {$rule['start_date']|date_format} {l s='to' mod='kbbookingcalendar' } {$rule['end_date']|date_format}
                        {else}
                            {l s='on' mod='kbbookingcalendar'} {$rule['particular_date']|date_format}
                        {/if}
                    </p>
                {/foreach}
            </div>
        {/if}
    {/if}

    <script type="text/javascript">
        var kb_is_booking_product = 1;
        var current_date = "{$current_date}";
        {if !empty($booking_product_details['date_details'])}
        var kb_booking_date_details = '{$booking_product_details['date_details'] nofilter}';{* Variable contains HTML/CSS/JSON, escape not required *}
        {/if}
        var kb_booking_disable_days = '{$booking_product_details['disable_days'] nofilter}';{* Variable contains HTML/CSS/JSON, escape not required *}
        var kb_cart_url = "{$cart_url nofilter}";
        var actual_cart_url = "{$actual_cart_url nofilter}";
        var kb_checkin_checkout_valid = "{l s='Checkout date cannot be previous to checkin date' mod='kbbookingcalendar'}";
        var kb_checkin_checkout_empty = "{l s='Please select the valid dates.' mod='kbbookingcalendar'}";
        {if isset($render_dates) && !empty($render_dates)}
        var kb_render_dates = '{$render_dates nofilter}';{* Variable contains HTML/CSS/JSON, escape not required *}
        {/if}
    </script>

</div>