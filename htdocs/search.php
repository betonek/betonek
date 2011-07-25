<?php
/* TODO: send keypress in below <form> via RPC-JSON */

require "../includes/lib/lib.php";
require "../includes/functions.php";

$TITLE = "Szukaj: $search[query]";
require "header.php";

lib_jsuse("lib/rpc.js");
lib_jsuse("search.js");
lib_jsuse("bookview.js");

lib_jsonload("BS.init('#sw-results', '$_GET[q]')"); /* TODO: get the query on JavaScript-side */
lib_jsonload("BV.init('#sw-bookview')");

?>
<div id="searchbox">
	<table><tr>
	<td id="sb_left">
		<div id="sbl_middle">
			<img src="gfx/logo2.png" width="184" height="30" />
			<div id="sblm_name"><?= CFG_TITLE ?></div>
		</div>
	</td>

	<td id="sb_middle">&nbsp;</td>

	<td id="sb_right">
		<div id="sbr_middle">
			<span id="sbrm_box">
				<form>
					<input type="textbox" class="searchterm" name="q" value="<?= $_GET["q"] ?>"/>
					<span class="searchbutton">Szukaj</span>
				</form>
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
