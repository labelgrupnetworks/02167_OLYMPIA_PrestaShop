{if !isset($renderAjax)}
    {if isset($hotel_rooms) && !empty($hotel_rooms)} 
        {if !isset($searchroomcat)}<div class="tabs kb-hotel-room-block">{/if}
            <h3 class="h3">{l s='Available Rooms' mod='kbbookingcalendar'}</h3>
            <table class="table table-bordered table-labeled">
                <thead>
                    <tr>
                        <th>{l s='Rooms' mod='kbbookingcalendar'}</th>
                        <th>{l s='Category' mod='kbbookingcalendar'}</th>
                        <th>{l s='Type' mod='kbbookingcalendar'}</th>
                        <th>{l s='Price' mod='kbbookingcalendar'}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $hotel_rooms as $rooms}
                        <tr>
                            <td>
                                {if !empty($rooms['upload_images'])}
                                    {assign var=kb_room_images value=$rooms['upload_images']|json_decode:1}
                                    {if isset($kb_room_images[0]['link'])} 
                                        <img src="{$kb_room_images[0]['link']}" width="100" height="auto">
                                    {else}
                                        <img src="{$no_img}" width="100" height="auto">
                                    {/if}
                                {/if}
                            </td>
                            <td>
                            {if isset($rooms['room_category_name'])}{$rooms['room_category_name']}{/if}
                        </td>
                        <td>
                        {if isset($rooms['room_type']['room_name'])}{$rooms['room_type']['room_name']}{/if}
                    </td>
                    <td>
                        {$rooms['price']}
                    </td>
                    <td>
                        <button type="button" class="btn btn-success" onclick="renderHotelRoomPopup({$rooms['id_booking_product']},{$rooms['id_booking_room_facilities_map']})">{l s='Book Now' mod='kbbookingcalendar'}</button>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    {if !isset($searchroomcat)}</div>{/if}
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="kbrenderHotelRoomBlockModel" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">{l s='Booking Room' mod='kbbookingcalendar'}</h4>
            </div>
            <form id="request_manf_form" action="#" method="post" class="defaultForm form-horizontal">
                <div class="modal-body">

                </div>

            </form>
        </div>
    </div>
</div>
<script>
    kb_hotel_url = "{$hotel_url}";
</script>
{/if}
{elseif isset($renderAjax)}
    <style>
        .images-container .slider.kbroom_img_slider .slick-track {
                width: auto;
                min-width: 200px;
        }
    </style>
    <div>
        <h2 class="h2" style="text-align: center;"></h2>
        <div class="col-md-5">
            <div class="images-container">
                {if !empty($hotel_rooms) && isset($hotel_rooms[0]['upload_images'])}
                    {assign var=kb_room_images value=$hotel_rooms[0]['upload_images']|json_decode:1}
                    {if !empty($kb_room_images)}
                        <div class="slider kbroom_img_slider" style="width: 200px">
                            {foreach from=$kb_room_images item=image}
                                <div class="image">
                                    <img src="{$image.link}" width="200" itemprop="image">
                                </div>
                            {/foreach}
                        </div>
                    {else}
                        <img src="{$no_img}" width="200" itemprop="image">
                    {/if}
                {else}
                    <img src="{$no_img}" width="200" itemprop="image">
                {/if}
            </div>
        </div>
        <div class="col-md-7">
            <div class="product-form-wrapper kb-hotel-room-popup-date-block">
                <div class="kb-booking-product-price-block">
                    <span class="kb-booking-product-price">{$hotel_rooms[0]['price']}</span>
                </div>
                <input type="hidden" name="kb_room_selected_hotel" value="{$hotel_rooms[0]['id_booking_room_facilities_map']}">
                <div class="form-group col-lg-6" style="padding: 0;    padding-right: 10px;">
                    <label class="control-label" style="text-align: left;">
                        {l s='Check-In' mod='kbbookingcalendar'}
                    </label>
                    <input class="form-control" name="kb_room_check_in" autocomplete="off">
                </div>
                <div class="form-group col-lg-6" style="padding: 0;    padding-right: 10px;">
                    <label class="control-label" style="text-align: left;">{l s='Check-Out' mod='kbbookingcalendar'}</label>
                    <input class="form-control" name="kb_room_check_out" autocomplete="off">
                </div>
            </div>
            <div class="product-add-to-cart">
                <div class="form-group">
                    <label class="control-label">{l s='Quantity' mod='kbbookingcalendar' }</label>
                    <div class="qty">
                        <input type="number" value="1" class="kb_hotel_room_qty" min="1" max="100" autocomplete="off">
                    </div>
                </div>
                <div class="add">
                    <button class="btn btn-primary kb-add-to-cart" type="button" onclick="submitRoomHotelCart();">
                        <i class="material-icons shopping-cart">shopping_cart</i>&nbsp;{l s='Book Now' mod='kbbookingcalendar'}
                    </button>

                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
        <div class="">
            <div style="padding: 1.25rem 1.5rem;">
                {if isset($hotel_rooms[0]['room_type']) && !empty($hotel_rooms[0]['room_type'])}
                    <h6 class="h6" style="    border-bottom: 1px dashed #d3d3d3;
                        padding-bottom: 10px;">{l s='Allowed Guest' mod='kbbookingcalendar'}</h6>
                    <p>
                        <span style="color: #000;">{l s='Maximum Allowed Adult' mod='kbbookingcalendar'}</span> : {$hotel_rooms[0]['room_type']['max_allowed_adult']}<br/>
                        <span style="color: #000;">{l s='Maximum Allowed Child' mod='kbbookingcalendar'}</span> : {$hotel_rooms[0]['room_type']['max_allowed_child']}
                    </p>
                {/if}
            </div>
            <div class="hotel-room-addition-info-block">
                <h6 class="h6">{l s='Other Information' mod='kbbookingcalendar'}</h6>
                <p>
                    {if isset($hotel_rooms[0]['room_type']) && !empty($hotel_rooms[0]['room_type'])}
                        <span>{l s='Room Type' mod='kbbookingcalendar'}</span> : {$hotel_rooms[0]['room_type']['room_name']}<br/>
                    {/if}
                    {if isset($hotel_rooms[0]['room_category_name']) && !empty($hotel_rooms[0]['room_category_name'])}
                        <span>{l s='Room Category' mod='kbbookingcalendar'}</span> : {$hotel_rooms[0]['room_category_name']}<br/>
                    {/if}
                    {if isset($hotel_rooms[0]['start_time']) && !empty($hotel_rooms[0]['start_time'])}
                        <span>{l s='Check-In' mod='kbbookingcalendar'}</span> : {$hotel_rooms[0]['start_time']}<br/>
                    {/if}
                    {if isset($hotel_rooms[0]['end_time']) && !empty($hotel_rooms[0]['end_time'])}
                        <span>{l s='Check-Out' mod='kbbookingcalendar'}</span> : {$hotel_rooms[0]['end_time']}<br/>
                    {/if}
                </p>
            </div>
            {if isset($hotel_rooms[0]['room_facilities']) && !empty($hotel_rooms[0]['room_facilities'])}
                <div class="kb-room-facilities-block">
                    <h6 class="h6">{l s='Room Facilities' mod='kbbookingcalendar'}</h6>
                    <div class="kb_room_slider kb-center slider-nav" style="display: block">
                        {foreach $hotel_rooms[0]['room_facilities'] as $facilities}
                            <div class="kb-slick-block col-lg-3" style="">
                                {if $facilities['image_type'] == 'font'}
                                    <i class="fa fa-3x {$facilities['font_awesome_icon']}"></i>
                                {else}
                                    <img src="{$facilities['upload_image']}" height="62" width="62">
                                {/if}
                                <div>
                                    <label>
                                        <span>{$facilities['name']}</span>
                                    </label>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                    <script>
                        var kb_slider_item_count = '{$hotel_rooms[0]['room_facilities']|count}';
                    </script>
                </div>
            {/if}
            <div style="clear:both;"></div>
        </div>
    </div>
{/if}

<script>
    var kb_no_room_available = "{l s='No Rooms available for the selected category' mod='kbbookingcalendar' }";
    var room_available = "{l s='Available Rooms' mod='kbbookingcalendar' }";
    {if isset($render_dates) && !empty($render_dates)}
    var kb_render_dates = '{$render_dates nofilter}';{* Variable contains HTML/CSS/JSON, escape not required *}
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