/** Book view
 * Loads given title and draws it on screen
 */
var BV = {
$root: undefined,      /** DOM root */
title_id: undefined,   /** currently viewed title */

/** Initialize Book View
 * @param root        DOM node to draw in
 */
init: function(root)
{
	BV.$root = $(root);
},

/** Load and show given title */
view: function(title_id)
{
	BV.title_id = title_id;
	BV.$root.empty(); // @1

	if (!title_id)
		return;

	$.rpc("title_view", { title_id: title_id }, function(d)
	{
		/* dont draw if not empty (see @1) */
		if (!BV.$root.is(":empty"))
			return;

		$("#tpl_bookview").tmpl(d).appendTo(BV.$root);

		/* attach user rating */
		$("#bv_raty").raty(
		{
			path: 'gfx/',
			width: "100%",
			cancel: true,
			cancelHint: 'anuluj swoją ocenę',
			cancelPlace: 'right',
			hintList: ['1/5', '2/5', '3/5', '4/5', '5/5'],
			noRatedMsg: '(jeszcze nie ocenione)',

			start: d.mark,
			targetKeep: true,
			click: BV.rate
		});

		/* attach average rating */
		$("#bv_raty_avg").raty(
		{
			path: 'gfx/',
			width: "100%",
			hintList: ['1/5', '2/5', '3/5', '4/5', '5/5'],
			noRatedMsg: '(jeszcze nie ocenione)',

			half: true,
			start: d.average_mark,
			readOnly: true
		});

		/* make comment submission */
		$("#bv_write_comment_button").click(BV.comment);

		/* add/del */
		$("#bvo_add").click(BV.add);
		$("#bvo_del").click(BV.del);
	});
},

rate: function(mark)
{
	$.rpc("title_rate", { title_id: BV.title_id, mark: mark }, function(d)
	{
		$.fn.raty.start(d.average_mark, '#bv_raty_avg');
	});
},

comment: function()
{
	$.rpc("title_comment", { title_id: BV.title_id, comment: $("#bv_write_comment").val() },
	function(d)
	{
		BV.reload();
	});
},

reload: function()
{
	BV.view(BV.title_id);
},

add: function()
{
	$.rpc("item_add", { title_id: BV.title_id }, function(d)
	{
		BV.reload();
	});
},

del: function()
{
	$.rpc("item_del", { title_id: BV.title_id }, function(d)
	{
		BV.reload();
	});
}
};
