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
	/* RPC call arguments */
	var args = {
		query: query
	};

	/* parse the query */
	var extract = function(name)
	{
		var m = RegExp('(.*)' + name + "(|:'[^']*'|:[^ ]*)( .*|$)").exec(args.query);

		if (m == null)
			return undefined;

		args.query = m[1] + m[3];
		return m[2] ? m[2].substr(1) : true;
	};

	args.owner  = extract("/moje");
	args.author = extract("/autor");

	if (extract("/książka"))
		args.type = "book";
	if (extract("/audiobook"))
		args.type = "audiobook";
	if (extract("/muzyka"))
		args.type = "audio";
	if (extract("/film"))
		args.type = "film";

	/* make new list @1 */
	BS.$root.empty();

	$.rpc("search", args, function(d) {
		/* dont draw if not empty (see @1) */
		if (!BS.$root.is(":empty"))
			return;

		/* announce search results */
		$(document).trigger("BS/SearchResult", d);

		/* nothing found? */
		if (d.titles.length == 0) {
			$("#tpl_search_not_found").tmpl(d).appendTo(BS.$root);
			return;
		}

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
		});

		/* accordion part to open on init */
		var acc_start = -1;

		/* number of empty cells */
		var empty = 0;

		/* count number of titles in each type category */
		$("#sr_acc ul").each(function(i)
		{
			/* number of titles */
			var count = $(this).find("li").length;

			if (count == 0) {
				empty++;
				$(this).prev().remove();
				$(this).remove();
				return;
			}

			/* candidate for acc_start */
			if (acc_start < 0 && count > 0)
				acc_start = i - empty;

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
		$("#sr_acc li").click(BS.selected);

		if (click_first)
			$("#sr_acc li").first().click();
	});
},

/** Handles click on a title */
selected: function(e)
{
	$("#sr_acc li").removeClass("sr_selected");
	$(this).addClass("sr_selected");

	$(document).trigger("BS/TitleSelected", $(this).data("title_id"));
}
};
