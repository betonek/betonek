<?php

require "../includes/lib/lib.php";

lib_jsuse("search.js");
lib_jsuse("bookview.js");

$TITLE = "Szukaj";
require "header.php";

?>
<script type="text/javascript">
/** If true, load first search result */
var load_first_title = false;

/** Run when user hits search */
var search = function()
{
	var query = $("#searchterm").val();

	/* update hash param */
	B.setparam("q", query);

	/* update title */
	document.title = $(document).data("orig_title");

	/* send the query */
	BS.search(query, load_first_title);
};

/** Run when search query comes back */
var searchresults = function(e, search)
{
	/* use query as the page title */
	document.title = $(document).data("orig_title") + ": " + search.query;

	var result_count = search.titles.length;

	/* update #searchcount */
	$("#searchcount").text(result_count);

	/* enable load_first_title after first success */
	load_first_title = true;
};

/** Run when user clicks on book in search results */
var titleselected = function(e, title_id)
{
	/* notify bookview.js */
	BV.view(title_id);

	/* update param */
	B.setparam("t", title_id);
};

/** Main function */
var main = function()
{
	/*
	 * initialize elements
	 */
	BS.init('#sw-results');
	BV.init('#sw-bookview');
	$(document).data("orig_title", document.title);

	/* pass submit of the query field to search */
	$("#searchbutton").click(search);
	$("#searchterm").keydown(function(e) { if (e.keyCode == 13) search(); });

	/* when search comes back, update some elements */
	$(document).bind("BS/SearchResult", searchresults);

	/* pass click on search results to the book view */
	$(document).bind("BS/TitleSelected", titleselected);

	/*
	 * finally, do the work from this page load
	 */
	if (B.getparam("t") > 0)
		BV.view(B.getparam("t"));
	else
		load_first_title = true;

	$("#searchterm").val(B.getparam("q"));
	$("#searchbutton").click();
};
</script>

<div id="searchutils_small">
	<table><tr>
	<td id="sus_left">
		<div id="susl_middle" class="logobox">
			<a href="<?= CFG_URL ?>">
				<img src="gfx/logo2.png" width="184" height="30" />
				<div id="suslm_name"><?= CFG_TITLE ?></div>
			</a>
		</div>
	</td>

	<td id="sus_middle">&nbsp;</td>

	<td id="sus_right">
		<div id="susr_middle">
			<span id="susrm_box" class="searchbox formbox">
				<input type="textbox" id="searchterm" name="q" />
				<span id="searchbutton">Szukaj</span>
			</span>
			<span id="susrm_count">
				Znaleziono: <span id="searchcount">0</span>
			</span>
		</div>
	</td>
	</tr></table>
</div>

<div id="searchwindow">
	<div id="sw-results"></div>
	<div id="sw-bookview"></div>
</div>

<?php require "footer.php" ?>
