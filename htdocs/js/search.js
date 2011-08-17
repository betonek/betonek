/** Book Search
 * Performs search queries and displays results
 *
 * Events(params)
 * @li BS/TitleSelected(title_id) - user clicked on new title
 * @li BS/SearchResult(result)   - rpc "search" performed, returned data in "result"
 */
var BS = {

/** DOM root */
$root: undefined,

/** Initialize Book Search
 * @param root      DOM node to draw in
 */
init: function(root)
{
	BS.$root = $(root);
},

/** Make search query and display the results
 * @param click_first      simulate click on first result
 */
search: function(query, click_first)
{
	var typeaggr = {};

	/* make new list */
	BS.$root.empty();

	$.rpc("search", {
		query: query,
		engine: "simple"
	}, function(d) {
		/* announce search results */
		$(document).trigger("BS/SearchResult", d);

		/* attach template */
		$("#tpl_search_results").tmpl(d).appendTo(BS.$root);

		/* draw results */
		$.each(d.titles, function(k, v)
		{
			/* draw */
			$("#tpl_search_title")
				.tmpl(v)
				.data(v)
				.appendTo("#sr_" + v.type);

			/* per-type data aggregation */
			if (typeaggr[v.type] == undefined)
				typeaggr[v.type] = { counter: 1 };
			else
				typeaggr[v.type].counter++;
		});

		/* accordion part to open on init */
		var acc_start = -1;

		/* count number of titles in each type category */
		$("#sr_acc ul").each(function(i)
		{
			/* number of titles */
			var count = $(this).find("li").length;

			/* candidate for acc_start */
			if (acc_start < 0 && count > 0)
				acc_start = i;

			/* set header counter */
			$("#" + $(this).attr("id") + "_ctr")
				.text($(this).find("li").length);
		});

		/* make accordion */
		$("#sr_acc").accordion({
			active: acc_start < 0 ? 0 : acc_start,
			fillSpace: true
		});

		/* monitor for title selections */
		BS.$root.find("li").click(BS.selected);

		if (click_first)
			BS.$root.find("li").first().click();
	});
},

/** Handles click on a title */
selected: function(e)
{
	$(document).trigger("BS/TitleSelected", $(e.target).data("title_id"));
}
};
