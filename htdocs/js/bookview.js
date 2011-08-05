/** Book view
 * Loads given title and draws it on screen
 */
var BV = {
$root: undefined,      /** DOM root */
title_id: undefined,   /** currently viewed title */

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
	BV.title_id = title_id;
	BV.$root.empty();

	if (!title_id)
		return;

	$.rpc("title_view", { title_id: title_id }, function(d)
	{
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
			click: function(score)
			{
				alert('wybrałeś ' + score);
			}
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

	});
}
};
