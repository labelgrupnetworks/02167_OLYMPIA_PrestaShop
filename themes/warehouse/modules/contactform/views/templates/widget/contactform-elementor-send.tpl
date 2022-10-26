
    <div class="contact-form">
        <form  class="js-elementor-contact-form">

            <div class="js-elementor-contact-norifcation-wrapper">
            {if $notifications}
                <div class="col-xs-12 alert {if  isset($notifications.nw_error) && $notifications.nw_error}alert-danger{else}alert-success{/if}">
                    <ul>
                        {foreach $notifications.messages as $notif}
                            <li>{$notif}</li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            </div>

        </form>
    </div>
