(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
var iqitElementorButton;

document.addEventListener("DOMContentLoaded", function (event) {

    $(document).ready(function () {


        iqitElementorButton = (function () {

            var $wrapperCms = $('form[name="cms_page"]').first().find('.card-block').first().find('.card-text').first(),
                $wrapperProduct = $('#features'),
                $wrapperBlog = $('#elementor-button-blog-wrapper'),
                $wrapperCategory = $('form[name="category"]').first().find('.card-block').first().find('.card-text').first(),
                $wrapperBrand = $('form[name="manufacturer"]').first().find('#manufacturer_description'),
                $btnTemplate = $('#tmpl-btn-edit-with-elementor'),
                $btnTemplateProduct = $('#tmpl-btn-edit-with-elementor-product'),
                $btnTemplateBlog = $('#tmpl-btn-edit-with-elementor-blog'),
                $btnTemplateCategory = $('#tmpl-btn-edit-with-elementor-category'),
                $btnTemplateBrand = $('#tmpl-btn-edit-with-elementor-brand');

            function init() {
                $wrapperCms.prepend($btnTemplate.html());
                $wrapperProduct.prepend($btnTemplateProduct.html());
                $wrapperBlog.prepend($btnTemplateBlog.html());
                $wrapperCategory.prepend($btnTemplateCategory.html());
                $wrapperBrand.append($btnTemplateBrand.html());

                if (typeof elementorPageType !== 'undefined') {

                    if (elementorPageType == 'cms') {
                        var hideEditor = false;
                        jQuery.each(onlyElementor, function (i, val) {
                            if (val) {
                                hideEditor = true;
                            }
                        });
                        if (hideEditor) {
                            let $cmsPageContent =  $("#cms_page_content");
                            $cmsPageContent.first().parents('.form-group').last().hide();
                            $cmsPageContent.find('.autoload_rte').removeClass('autoload_rte');
                        }
                    }

                    if (elementorPageType == 'blog') {
                        var  hideEditor = false;
                        jQuery.each(onlyElementor, function(i, val) {
                            if(val){
                                hideEditor = true;
                            }
                        });
                        if (hideEditor){
                            $("[id^=content_]").first().parents('.form-group').last().remove();
                        }
                    }

                    if (elementorPageType == 'category') {
                        var $form = $('form[name="category"]').first();
                        $form.submit(function (event) {
                            $.ajax({
                                type: 'POST',
                                url: elementorAjaxUrl,
                                data: {
                                    action: 'categoryLayout',
                                    categoryId: $form.find("input[name='idPageElementor']").val(),
                                    justElementor: $form.find("input[name='justElementor']:checked").val()
                                },
                                success: function (resp) {
                                },
                                error: function () {
                                    console.log("error");
                                }
                            });

                        });

                    }
                }

            }

            return {init: init};

        })();

        iqitElementorButton.init();


    });

});

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJ2aWV3cy9fZGV2L2pzL2JhY2tvZmZpY2UvYmFja29mZmljZS5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQ0FBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24oKXtmdW5jdGlvbiByKGUsbix0KXtmdW5jdGlvbiBvKGksZil7aWYoIW5baV0pe2lmKCFlW2ldKXt2YXIgYz1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlO2lmKCFmJiZjKXJldHVybiBjKGksITApO2lmKHUpcmV0dXJuIHUoaSwhMCk7dmFyIGE9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitpK1wiJ1wiKTt0aHJvdyBhLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsYX12YXIgcD1uW2ldPXtleHBvcnRzOnt9fTtlW2ldWzBdLmNhbGwocC5leHBvcnRzLGZ1bmN0aW9uKHIpe3ZhciBuPWVbaV1bMV1bcl07cmV0dXJuIG8obnx8cil9LHAscC5leHBvcnRzLHIsZSxuLHQpfXJldHVybiBuW2ldLmV4cG9ydHN9Zm9yKHZhciB1PVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmUsaT0wO2k8dC5sZW5ndGg7aSsrKW8odFtpXSk7cmV0dXJuIG99cmV0dXJuIHJ9KSgpIiwidmFyIGlxaXRFbGVtZW50b3JCdXR0b247XG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJET01Db250ZW50TG9hZGVkXCIsIGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xuXG5cbiAgICAgICAgaXFpdEVsZW1lbnRvckJ1dHRvbiA9IChmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgIHZhciAkd3JhcHBlckNtcyA9ICQoJ2Zvcm1bbmFtZT1cImNtc19wYWdlXCJdJykuZmlyc3QoKS5maW5kKCcuY2FyZC1ibG9jaycpLmZpcnN0KCkuZmluZCgnLmNhcmQtdGV4dCcpLmZpcnN0KCksXG4gICAgICAgICAgICAgICAgJHdyYXBwZXJQcm9kdWN0ID0gJCgnI2ZlYXR1cmVzJyksXG4gICAgICAgICAgICAgICAgJHdyYXBwZXJCbG9nID0gJCgnI2VsZW1lbnRvci1idXR0b24tYmxvZy13cmFwcGVyJyksXG4gICAgICAgICAgICAgICAgJHdyYXBwZXJDYXRlZ29yeSA9ICQoJ2Zvcm1bbmFtZT1cImNhdGVnb3J5XCJdJykuZmlyc3QoKS5maW5kKCcuY2FyZC1ibG9jaycpLmZpcnN0KCkuZmluZCgnLmNhcmQtdGV4dCcpLmZpcnN0KCksXG4gICAgICAgICAgICAgICAgJHdyYXBwZXJCcmFuZCA9ICQoJ2Zvcm1bbmFtZT1cIm1hbnVmYWN0dXJlclwiXScpLmZpcnN0KCkuZmluZCgnI21hbnVmYWN0dXJlcl9kZXNjcmlwdGlvbicpLFxuICAgICAgICAgICAgICAgICRidG5UZW1wbGF0ZSA9ICQoJyN0bXBsLWJ0bi1lZGl0LXdpdGgtZWxlbWVudG9yJyksXG4gICAgICAgICAgICAgICAgJGJ0blRlbXBsYXRlUHJvZHVjdCA9ICQoJyN0bXBsLWJ0bi1lZGl0LXdpdGgtZWxlbWVudG9yLXByb2R1Y3QnKSxcbiAgICAgICAgICAgICAgICAkYnRuVGVtcGxhdGVCbG9nID0gJCgnI3RtcGwtYnRuLWVkaXQtd2l0aC1lbGVtZW50b3ItYmxvZycpLFxuICAgICAgICAgICAgICAgICRidG5UZW1wbGF0ZUNhdGVnb3J5ID0gJCgnI3RtcGwtYnRuLWVkaXQtd2l0aC1lbGVtZW50b3ItY2F0ZWdvcnknKSxcbiAgICAgICAgICAgICAgICAkYnRuVGVtcGxhdGVCcmFuZCA9ICQoJyN0bXBsLWJ0bi1lZGl0LXdpdGgtZWxlbWVudG9yLWJyYW5kJyk7XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIGluaXQoKSB7XG4gICAgICAgICAgICAgICAgJHdyYXBwZXJDbXMucHJlcGVuZCgkYnRuVGVtcGxhdGUuaHRtbCgpKTtcbiAgICAgICAgICAgICAgICAkd3JhcHBlclByb2R1Y3QucHJlcGVuZCgkYnRuVGVtcGxhdGVQcm9kdWN0Lmh0bWwoKSk7XG4gICAgICAgICAgICAgICAgJHdyYXBwZXJCbG9nLnByZXBlbmQoJGJ0blRlbXBsYXRlQmxvZy5odG1sKCkpO1xuICAgICAgICAgICAgICAgICR3cmFwcGVyQ2F0ZWdvcnkucHJlcGVuZCgkYnRuVGVtcGxhdGVDYXRlZ29yeS5odG1sKCkpO1xuICAgICAgICAgICAgICAgICR3cmFwcGVyQnJhbmQuYXBwZW5kKCRidG5UZW1wbGF0ZUJyYW5kLmh0bWwoKSk7XG5cbiAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGVsZW1lbnRvclBhZ2VUeXBlICE9PSAndW5kZWZpbmVkJykge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChlbGVtZW50b3JQYWdlVHlwZSA9PSAnY21zJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGhpZGVFZGl0b3IgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGpRdWVyeS5lYWNoKG9ubHlFbGVtZW50b3IsIGZ1bmN0aW9uIChpLCB2YWwpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAodmFsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGhpZGVFZGl0b3IgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGhpZGVFZGl0b3IpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsZXQgJGNtc1BhZ2VDb250ZW50ID0gICQoXCIjY21zX3BhZ2VfY29udGVudFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkY21zUGFnZUNvbnRlbnQuZmlyc3QoKS5wYXJlbnRzKCcuZm9ybS1ncm91cCcpLmxhc3QoKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGNtc1BhZ2VDb250ZW50LmZpbmQoJy5hdXRvbG9hZF9ydGUnKS5yZW1vdmVDbGFzcygnYXV0b2xvYWRfcnRlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBpZiAoZWxlbWVudG9yUGFnZVR5cGUgPT0gJ2Jsb2cnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgIGhpZGVFZGl0b3IgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGpRdWVyeS5lYWNoKG9ubHlFbGVtZW50b3IsIGZ1bmN0aW9uKGksIHZhbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKHZhbCl7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGhpZGVFZGl0b3IgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGhpZGVFZGl0b3Ipe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoXCJbaWRePWNvbnRlbnRfXVwiKS5maXJzdCgpLnBhcmVudHMoJy5mb3JtLWdyb3VwJykubGFzdCgpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGVsZW1lbnRvclBhZ2VUeXBlID09ICdjYXRlZ29yeScpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkZm9ybSA9ICQoJ2Zvcm1bbmFtZT1cImNhdGVnb3J5XCJdJykuZmlyc3QoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRmb3JtLnN1Ym1pdChmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybDogZWxlbWVudG9yQWpheFVybCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uOiAnY2F0ZWdvcnlMYXlvdXQnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY2F0ZWdvcnlJZDogJGZvcm0uZmluZChcImlucHV0W25hbWU9J2lkUGFnZUVsZW1lbnRvciddXCIpLnZhbCgpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAganVzdEVsZW1lbnRvcjogJGZvcm0uZmluZChcImlucHV0W25hbWU9J2p1c3RFbGVtZW50b3InXTpjaGVja2VkXCIpLnZhbCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChyZXNwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhcImVycm9yXCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHtpbml0OiBpbml0fTtcblxuICAgICAgICB9KSgpO1xuXG4gICAgICAgIGlxaXRFbGVtZW50b3JCdXR0b24uaW5pdCgpO1xuXG5cbiAgICB9KTtcblxufSk7XG4iXX0=
