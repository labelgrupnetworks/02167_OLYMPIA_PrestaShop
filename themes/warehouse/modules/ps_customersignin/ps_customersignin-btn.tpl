<div id="header-user-btn" class="col col-auto header-btn-w header-user-btn-w">
    {if $logged}
        {if isset($iqitTheme.h_user_dropdown) && $iqitTheme.h_user_dropdown}
            <div class="dropdown">
            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
               class="header-btn header-user-btn">
                <i class="fa fa-user fa-fw icon" aria-hidden="true"></i>
                <span class="title">{$customer.firstname|truncate:15:'...'}</span>
            </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{$urls.pages.identity}">
                        <i class="fa fa-user fa-fw" aria-hidden="true"></i>
                        {l s='Information' d='Shop.Theme.Customeraccount'}
                    </a>
                    {if $customer.addresses|count}
                        <a class="dropdown-item" href="{$urls.pages.addresses}">
                            <i class="fa fa-map-marker fa-fw" aria-hidden="true"></i>
                            {l s='Addresses' d='Shop.Theme.Customeraccount'}
                        </a>
                    {else}
                        <a class="dropdown-item" href="{$urls.pages.address}">
                            <i class="fa fa-map-marker fa-fw" aria-hidden="true"></i>
                            {l s='Add first address' d='Shop.Theme.Customeraccount'}
                        </a>
                    {/if}
                    {if !$configuration.is_catalog}
                        <a class="dropdown-item" href="{$urls.pages.history}">
                            <i class="fa fa-history fa-fw" aria-hidden="true"></i>
                            {l s='Order history and details' d='Shop.Theme.Customeraccount'}
                        </a>
                    {/if}
                    {if !$configuration.is_catalog}
                        <a class="dropdown-item" href="{$urls.pages.order_slip}">
                            <i class="fa fa-file-o fa-fw" aria-hidden="true"></i>
                            {l s='Credit slips' d='Shop.Theme.Customeraccount'}
                        </a>
                    {/if}

                    {if $configuration.voucher_enabled && !$configuration.is_catalog}
                        <a class="dropdown-item" href="{$urls.pages.discount}">
                            <i class="fa fa-tags fa-fw" aria-hidden="true"></i>
                            {l s='Vouchers' d='Shop.Theme.Customeraccount'}
                        </a>
                    {/if}

                    {if $configuration.return_enabled && !$configuration.is_catalog}
                        <a class="dropdown-item" href="{$urls.pages.order_follow}">
                            <i class="fa fa-undo fa-fw"" aria-hidden="true"></i>
                            {l s='Merchandise returns' d='Shop.Theme.Customeraccount'}
                        </a>
                    {/if}
                    <a class="dropdown-item" href="{$urls.actions.logout}">
                        <i class="fa fa-sign-out fa-fw" aria-hidden="true"></i>
                        {l s='Sign out' d='Shop.Theme.Actions'}
                    </a>
                </div>
            </div>
        {else}
            <a href="{$urls.pages.my_account}"
               title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}"
               rel="nofollow" class="header-btn header-user-btn">
                <i class="fa fa-user fa-fw icon" aria-hidden="true"></i>
                <span class="title">{$customer.firstname|truncate:15:'...'}</span>
            </a>
        {/if}
    {else}
        <a href="{$urls.pages.my_account}"
           title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}"
           rel="nofollow" class="header-btn header-user-btn">
            <i class="fa fa-user fa-fw icon" aria-hidden="true"></i>
            <span class="title">{l s='Sign in' d='Shop.Theme.Actions'}</span>
        </a>
    {/if}
</div>









