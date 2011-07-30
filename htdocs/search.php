<?php

require "../includes/lib/lib.php";
require "../includes/functions.php";

$TITLE = "Szukaj";
require "header.php";

lib_jsuse("lib/rpc.js");
lib_jsuse("search.js");
lib_jsuse("bookview.js");

?>
<script type="text/javascript">
var search = function()
{
	BS.search($("#searchterm").val());
};

/* main function */
var main = function()
{
	/*
	 * initialize elements
	 */
	BS.init('#sw-results');
	BV.init('#sw-bookview');

	/* pass click on results to the book view */
	$(document).bind("BS/TitleSelected", function(e, title_id) { BV.view(title_id); });

	/* use query as page title, update #searchcount */
	$(document).bind("BS/SearchResult", function(e, search)
	{
		if (!$(document).data("orig_title"))
			$(document).data("orig_title", document.title);

		document.title = $(document).data("orig_title") + ": " + search.query;
		$("#searchcount").text(search.titles.length);
	});

	/* pass submits in query field to search */
	$("#searchbutton").click(search);
	$("#searchterm").keydown(function(e) { if (e.keyCode == 13) search(); });

	/*
	 * do search from this GET query
	 */
	$("#searchterm").val(B.getparam("q"));
	search();
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
			<span id="susrm_box" class="searchbox">
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
