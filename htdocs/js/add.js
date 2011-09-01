/** Book Add */
var BA = {
$root: undefined,      /** DOM root */

authors: [],           /** array of authors: { .author, .author_id } */
authors_src: [],       /** authors in form acceptable by jquery autocomplete plugin */
titles: [],            /** array of author titles: { .title, .title_id } */
titles_src: [],        /** see authors_src */

title: {
	type: "book",          /** title type */
	typename: "książkę",   /** type friendly name */
	author: "",            /** title author */
	author_id: 0,          /** optional author_id */
	title: "",             /** title title :) */
},

/** Initialize
 * @param root        DOM node to draw in
 */
init: function(root)
{
	BA.$root = $(root);
	BA.step1();
},

step1: function()
{
	B.tmpl(BA.$root, $("#tpl_add_step1"), BA.title);

	/* title type */
	BA.step1_typesync();
	$("#ba1_type").change(BA.step1_typechanged);

	/* provide author and title autocompletion */
	$("#ba1_author").autocomplete({
		autofocus: true,
		delay: 0,
		source: BA.authors_src,
		change: BA.step1_authorchanged
	});
	$("#ba1_title").autocomplete({
		delay: 0,
		source: BA.titles_src
	});

	/* fill author autocompletion list */
	$.rpc("author_search", {}, function(d) {
		BA.authors = d.authors;
		$.each(d.authors, function(k, v) { BA.authors_src.push(v.author); });
		$("#ba1_author").autocomplete("option", "source", BA.authors_src);
	});

	/* submit hooks */
	$("#ba1_next").click(function()
	{
		BA.step1_titlechanged();

		if (!BA.title.author || !BA.title.title) {
			alert("Pola 'autor' i 'tytuł' są wymagane");
			return;
		}

		BA.step2();
	});
},

/* sync GUI to object data */
step1_typesync: function()
{
	if (BA.title.type != $("#ba1_type option:selected").val()) {
		$("#ba1_type option:selected").attr("selected", false);

		$("#ba1_type option").each(function(i, e)
		{
			if ($(e).val() == BA.title.type) {
				$(e).attr("selected", true);
				return false;
			}
		});

		BA.step1_typechanged();
	}
},

/* update some labels */
step1_typechanged: function()
{
	var s = $("#ba1_type option:selected").val();
	var al, tn;

	if (s == "book") {
		al = "Autor";
		tn = "książkę";
	} else if (s == "audiobook") {
		al = "Autor";
		tn = "audiobooka";
	} else if (s == "audio") {
		al = "Zespół";
		tn = "muzykę";
	} else if (s == "film") {
		al = "Reżyser";
		tn = "film";
	}

	BA.title.type = s;
	BA.title.typename = tn;

	$("#ba1_typename").text(tn);
	$("#ba1_author_label").text(al);
},

/* check if an already existing author has been typed in */
step1_authorchanged: function()
{
	/* fetch author data */
	BA.title.author = $("#ba1_author").val();
	BA.title.author_id = 0;

	/* try to find author_id */
	$.each(BA.authors, function(k, v) {
		if (v.author == BA.title.author) {
			BA.title.author_id = v.author_id;
			return false;
		}
	});

	/* try to fetch author books and update title autocompletions */
	BA.step1_updatetitles();
},

/* update title autocompletions */
step1_updatetitles: function()
{
	BA.titles = [];
	BA.titles_src = [];

	if (BA.title.author_id) {
		$("#ba1_title").attr("disabled", true);

		$.rpc("author_titles", {author_id: BA.title.author_id}, function(d) {
			BA.titles = d.titles;
			$.each(d.titles, function(k, v) { BA.titles_src.push(v.title); });
			$("#ba1_title").autocomplete("option", "source", BA.titles_src);

		$("#ba1_title").attr("disabled", false);
		});
	} else {
		$("#ba1_title").autocomplete("option", "source", []);
	}
},

step1_titlechanged: function()
{
	BA.title.title = $("#ba1_title").val();
	BA.title.title_id = 0;

	/* try to find title_id */
	$.each(BA.titles, function(k, v) {
		if (v.title == BA.title.title && v.type == BA.title.type) {
			BA.title.title_id = v.title_id;
			return false;
		}
	});
},

step2: function()
{
	B.tmpl(BA.$root, $("#tpl_add_step2"), BA.title);

	$("#ba2_back").click(BA.step1);
	$("#ba2_next").click(function()
	{
		$("#ba_ctl").hide();
		$("#ba_ctl2").show();

		$.rpc("item_add", BA.title, function(d) {
			BA.step3();
		});
	});
},

step3: function()
{
	B.tmpl(BA.$root, $("#tpl_add_step3"), BA.title);
	$("#ba3_next").click(function()
	{
		BA.step1_updatetitles();
		BA.title.title = "";
		BA.step1();
	});
}
};
