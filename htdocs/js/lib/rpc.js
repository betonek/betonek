(function($){
/** Make a JSON-RPC call
 * @param method {String}   method name
 * @param params {Object}   parameters
 * @param cb {Function}     success callback (gets data.result and method)
 * @param errcb {Function}  error callback (gets data.error and method)
 * @param options {Object}  additional JQuery AJAX options (see docs) */
$.rpc = $.rpc || function(method, params, cb, errcb, options)
{
	var $body = $("body");

	$body.addClass("busy");
	$body.data("rpc_waiting", 1 + ($body.data("rpc_waiting") || 0));

	var handle_return = function()
	{
		var waiting = $body.data("rpc_waiting") - 1;

		if (waiting <= 0) {
			$body.data("rpc_waiting", 0);
			$("body").removeClass("busy");
		} else {
			$body.data("rpc_waiting", waiting);
		}
	};

	return $.ajax($.extend({
		contentType: 'application/json',
		type: 'POST',
		url: "rpc.php",
		cache: false,
		success: function(data) {
			handle_return();
			if (cb && data.result)
				cb(data.result, method);
			else if (errcb && data.error)
				errcb(data.error, method);
		},
		error: function(jqXHR, status, exception)
		{
			handle_return();

			if (status == "error") {
				alert("RPC error: " + exception);
			} else if (status == "timeout") {
				/* repeat the query */
				$.rpc(method, params, cb, errcb, options);
			} else {
				alert("RPC " + status + " (" + exception + ")");
			}
		},
		timeout: 10000,
		data: JSON.stringify({ method: method, params: params })
	}, options));
}})(jQuery);
