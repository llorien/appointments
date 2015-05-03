jQuery(function($) {

    var ns = birchpress.namespace('birchschedule.fb');
    
    postboxes.pbhide = function(id) {
        $('#' + id).toggleClass('closed');
    }
    postboxes.save_state = function(){};
    
    $('#client_email_required').attr('disabled', true);
    $('#client_password_required').attr('disabled', true);
    $('input[name="birchschedule_fields_options[client_email][visibility]"]').attr('disabled', true);
    
    var showConfirmationSettings = function(type) {
        $('.birs_confirmation_settings').hide();
        $('#birchschedule_fields_options_submit_confirmation_settings_' + type).show();
    };
    $('input[name="birchschedule_fields_options[submit][confirmation][type]"]').change(function(){
        showConfirmationSettings($(this).val());
    });
    showConfirmationSettings($('input[name="birchschedule_fields_options[submit][confirmation][type]"]:checked').val());
});