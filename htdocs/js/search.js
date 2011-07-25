var BS = {
/** DOM root */
$root: undefined,

init: function(root, query)
{
	BS.$root = $(root);
	BS.search(query);
},

search: function(query)
{
	$.rpc("search", {
		query: query,
		engine: "simple"
	}, function(d) {
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

		/* announce title selection */
		BS.$root.find("li").click(function(e) {
			$(document).trigger("betTitleSelected", $(e.target).data());
		});
	});
}
};
