/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 */
	$(document).ready(function() {    
            
            $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
    }).datepicker('update', new Date());
    
    $("#datepicker1").datepicker({
        autoclose: true,
        todayHighlight: true,
        startDate: '+1M'
    }).datepicker('update', new Date());
    
     $('input[name="kb_start_date"').val(kb_start_date);
     $('input[name="kb_end_date"').val(kb_end_date);
		$('#calendar').fullCalendar({
//			dayRightclick: function(date, jsEvent, view) {
//				alert(date.format());
//				// Prevent browser context menu:
//				return false;
//			},
//			eventRightclick: function(event, jsEvent, view) {
//				alert(event.title);
//                                //'rightclicked on event ' + 
//				// Prevent browser context menu:
//				return false;
//			},
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay,agendaWeek,agendaDay'
			},
			buttonText: {
				basicWeek: kb_basicWeek,
				basicDay: kb_basicDay,
				agendaWeek: kb_agendaWeek,
				agendaDay: kb_agendaDay
			},
			defaultDate: kb_current_date,
                        locale: initialLocaleCode,
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			events: kb_calender_data
		});
		$(".fc-event-container a").attr('target','_blank');
                
                    
	});
        
        
        function calenderFilterReset()
        {
            $('select[name="kb_product_type"]').val(0);
            $('select[name="kb_product_id"]').val(0);
            $('input[name="kb_start_date"').val('');
            $('input[name="kb_end_date"').val('');
        }