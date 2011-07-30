/** Main Betonek file - mostly libraries and shared code */
var B = {

/* delme? */
init: function()
{
	/* select first input box */
	$("form:first input:first").focus();
},

/** Make a JSON-RPC call */
rpc: function(method, args, cb, errcb)
{
	return $.rpc(method, args, cb, errcb ? errcb : function(err)
	{
		alert("Błąd rpc " + method + " nr " + err.code + ": " + err.message);
	});
},

/** Returns a GET parameter value */
getparam: function(name)
{
	return decodeURI(
		(RegExp('[?|&]' + name + '=' + '(.+?)(&|$)').exec(window.location.search) || [,null])[1]
	);
}
};
