<?php

require "../includes/lib/lib.php";
require "../includes/functions.php";

lib_jsuse("search.js");
lib_jsuse("bookview.js");

$TITLE = "Szukaj";
require "header.php";

?>
<script type="text/javascript">
/** Run when user hits search */
var search = function()
{
	var query = $("#searchterm").val();

	/* block UI */
	//$.blockUI({message: null});

	/* update hash param */
	B.setparam("q", query);

	/* update title */
	document.title = $(document).data("orig_title");

	/* send the query */
	BS.search(query);
};

/** Run when search query comes back */
var searchresults = function(e, search)
{
	//$.unblockUI();

	/* use query as the page title */
	document.title = $(document).data("orig_title") + ": " + search.query;

	/* update #searchcount */
	$("#searchcount").text(search.titles.length);
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
	$("#searchterm").val(B.getparam("q"));
	$("#searchbutton").click();
	BV.view(B.getparam("t"));
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
