/*
 * main Betonek file - mostly libraries and shared code
 */

var B = {

init: function()
{
	/* select first input box */
	$("input:first").focus();
},

/** Make a JSON-RPC call */
rpc: function(method, args, cb, errcb)
{
	return $.rpc(method, args, cb, errcb ? errcb : function(err)
	{
		alert("Błąd rpc " + method + " nr " + err.code + ": " + err.message);
	});
},

/** Regular expression for matching hash/GET parameter */
_pre: function(name)
{
	/* FIXME: handle empty p, eg ...&p=&a=blah */
	return RegExp('(^|&)' + name + '=' + '(.+?)(&.*|$)');
},

/** Returns a parameter value */
getparam: function(name)
{
	var val;

	/* extracts parameter from given string */
	var extract = function(src)
	{
		var dst = B._pre(name).exec(src.substr(1));
		if (dst)
			return decodeURIComponent(dst[2].replace(/\+/g, ' '));
		else
			return undefined;
	};

	/* first try URL hash */
	val = extract(window.location.hash);

	/* otherwise URL GET param */
	if (!val)
		val = extract(window.location.search);

	return val;
},

/** Set hash parameter value */
setparam: function(name, value)
{
	var hash = window.location.hash.substr(1);

	/* delete old value */
	hash = hash.replace(B._pre(name), '$1$3').replace(/&$/, '');

	/* set new value */
	hash += '&' + name + '=' + encodeURIComponent(value);

	/* store */
	window.location.hash = '#' + hash.replace(/^&/, '');
},

tmpl: function($dest, $tpl, data)
{
	$dest.empty();
	$tpl.tmpl(data).appendTo($dest);
}
};
