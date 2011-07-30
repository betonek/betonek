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

search: function(query)
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

		/* make root accordion */
		$("#tpl_search_results").tmpl(d).appendTo(BS.$root);
		BS.$root.accordion();

		/* draw results */
		$.each(d.titles, function(k, v)
		{
			/* draw */
			$("#tpl_search_title")
				.tmpl(v)
				.data(v)
				.appendTo("#search_" + v.type);

			/* per-type data aggregation */
			if (typeaggr[v.type] == undefined)
				typeaggr[v.type] = { counter: 0 };

			typeaggr[v.type].counter++;
		});

		/* update counters in headers */
		$.each(typeaggr, function(k, v)
		{
			$("#search_" + k + "_counter").text(v.counter);
		});

		/* monitor for title selections */
		BS.$root.find("li").click(BS.selected);
	});
},

selected: function(e)
{
	$(document).trigger("BS/TitleSelected", $(e.target).data("title_id"));
}
};
