(function($){
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;

    var ns = namespace('birchschedule.aportal.reschedule');

    defineFunction(ns, 'getAvaliableTimeOptions', function(fn, successFn) {
        var ajaxUrl = birchschedule.model.getAjaxUrl();
        var i18nMessages = birchschedule.view.getI18nMessages();

        var queryData = {
            action: 'birchschedule_aportal_get_avaliable_time_options',
            apt_key: $('input[name=birs_appointment_key]').val(),
            birs_appointment_staff: $('#birs_appointment_staff').val(),
            birs_appointment_location: $('#birs_appointment_location').val(),
            birs_appointment_service: $('#birs_appointment_service').val(),
            birs_appointment_date: $('#birs_appointment_date').val(),
            birs_appointment_time: $('#birs_appointment_time').val()
        };
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: queryData,
            success: successFn
        });
        fn();
    });

    defineFunction(ns, 'changeTimeOptions', function(doc){
        $('#birs_appointment_time').html(doc);
        ns.createTimepicker();
        $('#birs_time_waiting').hide();
    });

    defineFunction(ns, 'clearTimeOptions', function(doc){
        $('#birs_appointment_time').html('');
        ns.createTimepicker();
        $('#birs_time_waiting').show();
    });

    defineFunction(ns, 'createDatepicker', function(){
        var datepickerI18nOptions = birchschedule.view.getDatepickerI18nOptions();
        var dateFormat = datepickerI18nOptions['dateFormat'];
        var options = $.extend(datepickerI18nOptions, {
            changeMonth: false,
            changeYear: false,
            beforeShowDay: function(date){
                var locationId = $('#birs_appointment_location').val();
                var staffId = $('#birs_appointment_staff').val();
                var serviceId = $('#birs_appointment_service').val();
                return birchschedule.view.ifShowDayForDatepicker(date, staffId, locationId, serviceId);
            },
            onSelect: function(dateText) {
                var date = $.datepicker.parseDate(dateFormat, dateText);
                var stdDateText = $.datepicker.formatDate('mm/dd/yy', date);
                $('#birs_appointment_date').val(stdDateText);
                ns.getAvaliableTimeOptions(ns.clearTimeOptions, ns.changeTimeOptions);
            }
        });
        $('#birs_appointment_datepicker').datepicker(options);
    });

    defineFunction(ns, 'createTimepicker', function(){
    });

    defineFunction(ns, 'rescheduleAppointment', function(fn, successFn){
        var ajaxUrl = birchschedule.model.getAjaxUrl();
        var i18nMessages = birchschedule.view.getI18nMessages();

        var queryData = {
            action: 'birchschedule_aportal_reschedule_appointment',
            apt_key: $('input[name=birs_appointment_key]').val(),
            birs_appointment_date: $('#birs_appointment_date').val(),
            birs_appointment_time: $('#birs_appointment_time').val()
        };
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: queryData,
            success: successFn
        });
        fn();
    });

    defineFunction(ns, 'handleReschedulingResponse', function(doc){
        var response = birchschedule.model.parseAjaxResponse(doc);
        var i18nMessages = birchschedule.view.getI18nMessages();
        var message = '';
        $('#birs_reschedule_appointment_submit').val(i18nMessages['Reschedule']);
        if(response.success) {
            message = response.success['message'];
            $('#birs_reschedule_appointment').html(message);
            return;
        }
        if(response.errors) {
            if(_.has(response.errors, 'appointment_nonexist')) {
                message = response.errors['appointment_nonexist'];
                $('#birs_reschedule_appointment').html(message);
                return;
            }
            _.each(response.errors, function(message, code) {
                var code = code.split(':');
                var field_id = code[1];
                var errorEl = $('#' + field_id + '_error');
                errorEl.html(message);
                errorEl.show();
            });
        }
    });

    defineFunction(ns, 'waitRescheduling', function(){
        var i18nMessages = birchschedule.view.getI18nMessages();
        $('#birs_reschedule_appointment .birs_error').hide();
        $('#birs_reschedule_appointment_submit').val(i18nMessages['Please wait...']);
    });

    defineFunction(ns, 'onRescheduleClick', function(){
        ns.rescheduleAppointment(ns.waitRescheduling, ns.handleReschedulingResponse);
    });

    defineFunction(ns, 'init', function() {
        ns.createDatepicker();
        ns.createTimepicker();
        var dateText = $('#birs_appointment_date').val();
        var date = $.datepicker.parseDate('mm/dd/yy', dateText);
        $('#birs_appointment_datepicker').datepicker('setDate', date);
        $('#birs_reschedule_appointment_submit').click(ns.onRescheduleClick);
    });

    addAction('birchschedule.initAfter', ns.init);
    
})(jQuery);