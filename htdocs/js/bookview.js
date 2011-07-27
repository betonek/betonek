var BV = {
/** DOM root */
$root: undefined,

/** Initialize Book View
 * @param root        DOM node to draw in
 */
init: function(root, title_id, event_name)
{
	BV.$root = $(root);
},

/** Load and show given title */
view: function(title_id)
{
	BV.$root.empty();

	$.rpc("title_view", { title_id: title_id }, function(d)
	{
		B.tpl("tpl_bookview", d).appendTo(BV.$root);
	});
}
};