<?php
require "include/lib/lib.php";

$TITLE = "Szukaj";
require "header.php";
?>
<script type="text/javascript">
var main = function()
{
	$("#searchbutton").click(function() { $("#searchform").submit(); });
};
</script>

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
				<li class="first"><a href="search.php?q=/moje">Moje zbiory</a></li>
				<li class="last"><a href="search.php?q=">Przeglądaj</a></li>
			</ul>
		</div>
	</td>

	<td id="sub_middle">&nbsp;</td>

	<td id="sub_right">
		<div id="subr_top">
			<ul>
				<li class="first"><a href="search.php?q=/książka">Książka</a></li>
				<li><a href="search.php?q=/audiobook">Audiobook</a></li>
				<li><a href="search.php?q=/muzyka">Muzyka</a></li>
				<li class="last"><a href="search.php?q=/film">Film</a></li>
			</ul>
		</div>

		<div id="subr_middle">
			<span id="subrm_box" class="searchbox formbox">
				<form action="search.php" id="searchform">
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
	<b>Wpisz szukane hasło i wciśnij Enter lub kliknij w czarne pole, aby przeglądać Bibliotekę. Nowe tytuły możesz
	dodać klikając w lewym górnym rogu.</b><br /><br />

	Biblioteka jest w fazie "Beta" ;-). Proszę o opinie, sugestie, itp. na maila: <a
	href="mailto:<?= CFG_ADMIN ?>"><?= CFG_ADMIN ?></a>.

</div>

<?php require "footer.php" ?>
