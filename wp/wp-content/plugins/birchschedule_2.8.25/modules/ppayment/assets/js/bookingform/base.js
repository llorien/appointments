(function($){
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;
    var addFilter = birchpress.addFilter;

    var ns = namespace('birchschedule.ppayment.bookingform');

    defineFunction(ns, 'getPlaceOrderFuncs', function() {
    	return {};
    });

    addFilter('birchschedule.view.bookingform.bookSucceed', function(fns, message) {
        fns['prepayment'] = function(message) {
            $('.birs_error').hide("");
            $('#birs_booking_box').hide();
            $('#birs_booking_success').html(message);
            $('#birs_booking_success').show("slow", function() {
                birchpress.util.scrollTo(
                    $("#birs_booking_success"),
                    600, -40);
            });
        }
        return fns;
    });

    defineFunction(ns, 'init', function() {
    	$('#birs_place_order').click(function() {
    		var funcs = ns.getPlaceOrderFuncs();
    		var method = $('input:radio[name=birs_payment_method]:checked').val();
    		if(_.has(funcs, method)) {
    			var placeOrder = funcs[method];
    			placeOrder();
    		}
    	});
    });

})(jQuery);