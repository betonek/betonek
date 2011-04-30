<?php
/* TODO: send keypress in below <form> via RPC-JSON */

require "../includes/lib/lib.php";
lib_jsuse("lib/rpc.js");

/* delete unneeded characters */
$query = str_replace(array("\"", "'", "<", ">"), "", $_GET["q"]);

$TITLE = "Szukaj: $query";
require "header.php";

lib_jsonload("B.init_search('$query')"); /* TODO: get the query on JavaScript-side */

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
					<input type="textbox" class="searchterm" name="q" value="<?= $search["query"] ?>"/>
					<span class="searchbutton">Szukaj</span>
				</form>
			</span>
		</div>
	</td>
	</tr></table>
</div>

<div id="searchwindow">
	<table><tr><td>
		<div id="sw_book">
		</div>
	</td><td>
		<div id="sw_menu">
		</div>
	</td></tr></table>
</div>

<?php require "footer.php" ?>
