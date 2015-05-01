(function($){
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;
    var addFilter = birchpress.addFilter;

    var ns = namespace('birchschedule.fdadmin');

    defineFunction(ns, 'getFormQueryData', function(){
        var postData = $('form').serialize();
        return postData;
    });

    defineFunction(ns, 'ifOnlyShowAvailable', function() {
        var result = !$('#birs_show_all_time').is(':checked');
        return result;
    });

    defineFunction(ns, 'reloadTimeOptions', function(action){
        var time = $('#birs_appointment_time').val();
        $('#birs_appointment_time').html('');

    	var ajaxUrl = birchschedule.model.getAjaxUrl();
        var postData = ns.getFormQueryData();
        postData += '&' + $.param({
            action: action
        });
        $.post(ajaxUrl, postData, function(data, status, xhr){
            $('#birs_appointment_time').html(data);
            var options = $('#birs_appointment_time option').toArray();
            var timeValues = _.map(options, function(option){
            	return $(option).val();
            });
            if(time && _.contains(timeValues, time)) {
                $('#birs_appointment_time').val(time);
            }
        }, 'html');
    });

    defineFunction(ns, 'redefineFunctionsNew', function(){
    	defineFunction(birchschedule.view.appointments.new, 'reloadTimeOptions', function() {
            ns.reloadTimeOptions('birchschedule_fdadmin_get_available_time_options');
        });
        defineFunction(birchschedule.view.appointments.new, 'ifOnlyShowAvailable', ns.ifOnlyShowAvailable);
    });

    defineFunction(ns, 'redefineFunctionsEdit', function(){
    	defineFunction(birchschedule.view.appointments.edit.clientlist.reschedule, 'reloadTimeOptions', function() {
            ns.reloadTimeOptions('birchschedule_fdadmin_get_available_reschedule_time_options');
        });

        var selectedStaffId = $('#birs_appointment_staff').attr('data-value');
        var selectedDate = $('#birs_appointment_date').val();

    	defineFunction(birchschedule.view.appointments.edit.clientlist.reschedule, 'initDatepicker', function() {
            var config = {
                ifOnlyShowAvailable: ns.ifOnlyShowAvailable,
                ifShowDayForDatepicker: function(date, staffId, locationId, serviceId) {
                    if($.datepicker.formatDate('mm/dd/yy', date) === selectedDate &&
                        staffId === selectedStaffId) {
                        return [true, ''];
                    } else {
                        return birchschedule.view.ifShowDayForDatepicker(date, staffId, locationId, serviceId);
                    }
                }
            };
            return birchschedule.view.initDatepicker(config);
        });
    });

    addAction('birchschedule.view.appointments.new.initBefore', function(){
        ns.redefineFunctionsNew();
        $('#birs_show_all_time').change(function(){
            birchschedule.view.appointments.new.refreshDatepicker();
            birchschedule.view.appointments.new.reloadTimeOptions();
        });        
    });

    addAction('birchschedule.view.appointments.edit.clientlist.reschedule.initFormBefore', function(){
        ns.redefineFunctionsEdit();
        $('#birs_show_all_time').change(function(){
            birchschedule.view.appointments.edit.clientlist.reschedule.refreshDatepicker();
            birchschedule.view.appointments.edit.clientlist.reschedule.reloadTimeOptions();
        });
    });

    defineFunction(ns, 'init', function() {
    });

    addAction('birchschedule.initAfter', ns.init);
})(jQuery);
