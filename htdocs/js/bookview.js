var BV = {
/** DOM root */
$root: undefined,

init: function(root)
{
	BV.$root = $(root);

	/* TODO: not here? */
	$(document).bind("betTitleSelected", function(e, data)
	{
		BV.$root.empty();
		BV.$root.text("Selected " + data.title);
	});
}
};
