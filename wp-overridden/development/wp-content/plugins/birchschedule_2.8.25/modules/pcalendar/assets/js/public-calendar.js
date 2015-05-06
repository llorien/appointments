(function($){
    var params = birchschedule_pcalendar;
    var sc_attrs = birchschedule_pcalendar_sc_attrs;

    var ajaxUrl = params.ajax_url;
    var gmtOffset = params.gmt_offset;
    var locationMap = params.location_map;
    var locationStaffMap = params.location_staff_map;
    var staffOrder = params.staff_order;
    var locationOrder = params.location_order;
    var defaultView = sc_attrs['default_view'];
    
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;

    var ns = namespace('birchschedule.pcalendar');

    defineFunction(ns, 'getFullcalendarI18nOptions', function() {
        var fcI18nOptions = birchschedule.view.getFullcalendarI18nOptions();
        var fcOptions = $.extend(fcI18nOptions, {
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            ignoreTimezone: true,
            gmtOffset: gmtOffset,
            weekMode: 'liquid',
            editable: true,
            disableDragging: true,
            disableResizing: true,
            selectable: false,
            allDaySlot: true,
            slotMinutes: 15,
            firstHour: 9,
            defaultView: defaultView,
            dayClick: function(date, allDay, jsEvent, view){
                if(view.name === 'month') {
                    calendar.fullCalendar('changeView', 'agendaDay');
                    calendar.fullCalendar('gotoDate', date);
                }
            },
            events: function(start, end, callback){
                var locationId = $('#birs_calendar_location').val();
                var staffId = $('#birs_calendar_staff').val();
                var titleTemplate = $('#birs_appointment_title_template').val();
                start = moment(start).format('YYYY-MM-DD HH:mm:ss');
                end = moment(end).format('YYYY-MM-DD HH:mm:ss');
                $.ajax({
                    url: ajaxUrl,
                    dataType: 'html',
                    data: {
                        action: 'birchschedule_pcalendar_query_appointments',
                        birs_time_start: start,
                        birs_time_end: end,
                        birs_location_id: locationId,
                        birs_staff_id: staffId,
                        title_template: titleTemplate
                    },
                    success: function(doc){
                        doc = '<div>' + doc + '</div>';
                        var events = $.parseJSON($(doc).find('#birs_response').text());
                        callback(events);
                    }
                });
            }
        });
        return fcOptions;
    });

    defineFunction(ns, 'init', function(){
    
        function changeLocationOptions() {
            var html = '';
            $.each(locationOrder, function(index, key) {
                if(_(locationMap).has(key)) {
                    html += '<option value="' + key + '">' + 
                        locationMap[key].post_title + '</option>';  
                }
            });
            $('#birs_calendar_location').html(html);
        }

        function changeStaffOptions() {
            var locationId = $('#birs_calendar_location').val();
            var assignedStaff = locationStaffMap[locationId];
            var html = '';
            if(!assignedStaff){
                assignedStaff = {};
            }
            $.each(staffOrder, function(index, key){
                if(_(assignedStaff).has(key)) {
                    var value = assignedStaff[key];
                    html += '<option value="' + key + '">' + value + '</option>';                            
                }
            });
            var selectedStaff = $('#birs_calendar_staff').val();
            $('#birs_calendar_staff').html(html);
            if(selectedStaff) {
                $('#birs_calendar_staff').val(selectedStaff);
            }
        };

        changeLocationOptions();
        changeStaffOptions();
        $('#birs_calendar_location').change(function(){
            changeStaffOptions();
        });
        
        var fcOptions = ns.getFullcalendarI18nOptions();
        var calendar = $('#birs_calendar').fullCalendar(fcOptions);
        $('#birs_calendar_location').change(function(){
            calendar.fullCalendar('refetchEvents');
        });
        $('#birs_calendar_staff').change(function(){
            calendar.fullCalendar('refetchEvents');
        });
    });

    addAction('birchschedule.initAfter', ns.init);

})(jQuery);