
    <div class="contact-form">
        <form  class="js-elementor-contact-form"  action="{url entity='module' name='iqitelementor' controller='Actions' params=['process' => 'handleWidget', 'ajax' => 1, 'form_recipient' => $form_recipient]}" method="post"
              {if $contact.allow_file_upload}enctype="multipart/form-data"{/if}>

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
            <section class="form-fields">

                {if $form_recipient == 'selection'}
                <div class="form-group">
                    <label class="form-control-label">{l s='Subject' d='Shop.Forms.Labels'}</label>
                    <div class="custom-select2">
                        <select name="id_contact" class="form-control form-control-select">
                            {foreach from=$contact.contacts item=contact_elt}
                                <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                    {else}
                    <input type="hidden" name="id_contact" value="{$form_recipient}" />
                {/if}

                <div class="form-group">
                    <label class="form-control-label">{l s='Email address' d='Shop.Forms.Labels'}</label>
                        <input
                                class="form-control"
                                name="from"
                                type="email"
                                value="{if isset($from)}{$from}{/if}"
                                placeholder="{l s='your@email.com' d='Shop.Forms.Help'}"
                        >
                </div>

                {if $contact.allow_file_upload}
                    <div class="form-group elementor-attachment-field">
                        <label class="form-control-label">{l s='Attachment' d='Shop.Forms.Labels'}</label>
                            <input type="file" name="fileUpload" class="filestyle" data-buttonText="{l s='Choose file' d='Shop.Theme.Actions'}">
                    </div>
                {/if}

                <div class="form-group">
                    <label class="form-control-label">{l s='Message' d='Shop.Forms.Labels'}</label>

          <textarea
                  class="form-control"
                  name="message"
                  placeholder="{l s='How can we help?' d='Shop.Forms.Help'}"
                  rows="3"
          >{if $contact.message}{$contact.message}{/if}</textarea>

                </div>
                {if isset($id_module)}
                <div class="form-group ">
                    {hook h='displayGDPRConsent' id_module=$id_module}
                </div>
                {/if}
            </section>

            <footer class="form-footer {if $btn_align == 'center'}text-center {/if} {if $btn_align == 'right'}text-right {/if}">
                <style>
                    input[name=url] {
                        display: none !important;
                    }
                </style>
                <input type="text" name="url" value=""/>

                <div class="js-csfr-token">
                    <input type="hidden" name="token" value="{$token}" />
                </div>

                <input type="hidden" name="submitMessage" value="1" />


                <input class="btn btn-primary btn-elementor-send {$btn_size} {if $btn_align == 'justify'} btn-block{/if}" type="submit"
                       value="{l s='Send' d='Shop.Theme.Actions'}">
            </footer>

        </form>
    </div>
