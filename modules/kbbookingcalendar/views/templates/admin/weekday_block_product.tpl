{if $id_booking_product}
    <div class="kb-weekday-block">
        <div class="weekday-title">
            <span>{l s='Select price by week day' mod='kbbookingcalendar'}</span>
        </div>
        <div class="form-group weekdays">
            <div class="input-group">
                <label for="weekday_price_sunday">{l s='Sunday' mod='kbbookingcalendar'}</label>
                <input class="form-control kb_weekday" id="weekday_price_details_0" type="number" name="weekday_price_details_0" value="{$weekday_data['weekday_price_details_0']}">
            </div>
            <div class="input-group">
                <label for="weekday_price_monday">{l s='Monday' mod='kbbookingcalendar'}</label>
                <input class="form-control kb_weekday" id="weekday_price_details_1" type="number" name="weekday_price_details_1" value="{$weekday_data['weekday_price_details_1']}">
            </div>
            <div class="input-group">
                <label for="weekday_price_tuesday">{l s='Tuesday' mod='kbbookingcalendar'}</label>
                <input class="form-control kb_weekday" id="weekday_price_details_2" type="number" name="weekday_price_details_2" value="{$weekday_data['weekday_price_details_2']}">
            </div>
            <div class="input-group">
                <label for="weekday_price_wednesday">{l s='Wednesday' mod='kbbookingcalendar'}</label>
                <input class="form-control kb_weekday" id="weekday_price_details_3" type="number" name="weekday_price_details_3" value="{$weekday_data['weekday_price_details_3']}">
            </div>
            <div class="input-group">
                <label for="weekday_price_thursday">{l s='Thursday' mod='kbbookingcalendar'}</label>
                <input class="form-control kb_weekday" id="weekday_price_details_4" type="number" name="weekday_price_details_4" value="{$weekday_data['weekday_price_details_4']}">
            </div>
            <div class="input-group">
                <label for="weekday_price_friday">{l s='Friday' mod='kbbookingcalendar'}</label>
                <input class="form-control kb_weekday" id="weekday_price_details_5" type="number" name="weekday_price_details_5" value="{$weekday_data['weekday_price_details_5']}">
            </div>
            <div class="input-group">
                <label for="weekday_price_saturday">{l s='Saturday' mod='kbbookingcalendar'}</label>
                <input class="form-control kb_weekday" id="weekday_price_details_6" type="number" name="weekday_price_details_6" value="{$weekday_data['weekday_price_details_6']}">
            </div>
        </div>
    </div>
{else}
    <div class="alert alert-warning kb-weekday-warning-alert">
        {l s='You must save this product before adding weekday price.' mod='kbbookingcalendar'}
    </div>
{/if}