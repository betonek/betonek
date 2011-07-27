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
	$.rpc("search", {
		query: query,
		engine: "simple"
	}, function(d) {
		/* announce search results */
		$(document).trigger("BS/SearchResult", d);

		/* make new list */
		BS.$root.empty();
		$("<ul>").appendTo(BS.$root);

		/* TODO: template */
		$.each(d.titles, function(k, v)
		{
			var e = $("<li>");
			e.data(v);
			e.text(v.author + ": " + v.title);
			e.appendTo(BS.$root);
		});

		/* announce title selections */
		BS.$root.find("li").click(function(e) {
			$(document).trigger("BS/TitleSelected", $(e.target).data("title_id"));
		});
	});
}
};
