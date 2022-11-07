<div class="elementor-section elementor-element elementor-element-4yej532 elementor-top-section elementor-section-full_width elementor-section-height-default elementor-section-height-default elementor-section-content-middle" data-element_type="section">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-row">
            {foreach $categories as $category}
                <div class="elementor-column elementor-element elementor-element-ht0p3k3 elementor-col-11 elementor-top-column" 
                    data-element_type="column">
                    <div class="elementor-column-wrap elementor-element-populated">
                        <div class="elementor-widget-wrap">
                            <div class="elementor-widget elementor-element elementor-element-c54elor elementor-widget-icon-box elementor-view-default elementor-position-top elementor-vertical-align-top" data-animation="fadeInLeft" data-element_type="icon-box">
                                <div class="elementor-widget-container">
                                    <div class="elementor-icon-box-wrapper">
                                        <div class="elementor-icon-box-icon">
                                            <img src="{$category.image_url}">
                                        </div>
                                        <div class="elementor-icon-box-content">
                                            <h3 class="elementor-icon-box-title">
                                                <a href="{$category.url}">{$category.name}</a>
                                            </h3>
                                            <div class="elementor-icon-box-description"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            {/foreach}
		</div>
    </div>
</div>