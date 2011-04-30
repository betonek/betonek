/*
 * Biblioteka Login
 */

var BL = {
	init: function(salt)
	{
		$("#loginform").submit(function()
		{
			var $pass = $("#loginform input[name='pass']");
			var crypted = $.sha256("" + salt + $pass.val());

			$pass.val("");
			$("#loginform input[name='crypted']").val(crypted);

			return true;
		});

		$("#loginform input[name='email']").focus();
	}
};
