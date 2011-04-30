<?php
require "../includes/lib/lib.php";

$TITLE = "Szukaj";
require "header.php";
?>

<div id="searchbox_big">
	<table><tr>
	<td id="sbb_left">
		<div id="sbbl_top">&nbsp;</div>

		<div id="sbbl_middle">
			<img src="gfx/logo.png" width="300" height="49" />
			<div id="sbblm_name"><?= CFG_TITLE ?></div>
		</div>

		<div id="sbbl_bottom">
			<ul>
				<li class="first"><a href="#">Przeglądaj</a></li>
				<li><a href="#">Najlepsze</a></li>
				<li class="last"><a href="#">Najnowsze</a></li>
			</ul>
		</div>
	</td>

	<td id="sbb_middle">&nbsp;</td>

	<td id="sbb_right">
		<div id="sbbr_top">
			<ul>
				<li class="first"><a href="#">Książki</a></li>
				<li><a href="#">Muzyka</a></li>
				<li class="last"><a href="#">Film</a></li>
			</ul>
		</div>

		<div id="sbbr_middle">
			<span id="sbbrm_box">
				<form action="search.php">
					<input type="textbox" class="searchterm" name="q" />
					<span class="searchbutton">Szukaj</span>
				</form>
			</span>
		</div>

		<div id="sbbr_bottom">&nbsp;</div>
	</td>
	</tr></table>
</div>

<div id="index_links">
	<b>...lub przeglądaj ręcznie:</b>
	<ul>
		<li><a href="#">Najlepsze</a></li>
		<li><a href="#">Najnowsze</a></li>
		<li><a href="#">Losowe</a></li>
	</ul>
</div>

<?php require "footer.php" ?>
