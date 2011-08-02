<?php
require "../includes/lib/lib.php";

$TITLE = "Szukaj";
require "header.php";
?>

<div id="searchutils_big">
	<table><tr>
	<td id="sub_left">
		<div id="subl_top">&nbsp;</div>

		<div id="subl_middle">
			<img src="gfx/logo.png" width="300" height="49" />
			<div id="sublm_name"><?= CFG_TITLE ?></div>
		</div>

		<div id="subl_bottom">
			<ul>
				<li class="first"><a href="#">Przeglądaj</a></li>
				<li><a href="#">Najlepsze</a></li>
				<li class="last"><a href="#">Najnowsze</a></li>
			</ul>
		</div>
	</td>

	<td id="sub_middle">&nbsp;</td>

	<td id="sub_right">
		<div id="subr_top">
			<ul>
				<li class="first"><a href="#">Książki</a></li>
				<li><a href="#">Muzyka</a></li>
				<li class="last"><a href="#">Film</a></li>
			</ul>
		</div>

		<div id="subr_middle">
			<span id="subrm_box" class="searchbox formbox">
				<form action="search.php">
					<input type="textbox" id="searchterm" name="q" />
					<span id="searchbutton">Szukaj</span>
				</form>
			</span>
		</div>

		<div id="subr_bottom">&nbsp;</div>
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
