<?php
require "../includes/lib/lib.php";
lib_jsuse("lib/rpc.js");
lib_jsuse("add.js");/* TODO: make compatibile with lib_jsonload */
$TITLE = "Dodaj nową rzecz do wypożyczenia";
require "header.php";
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
		</div>
	</td>
	</tr></table>
</div>

<div id="add">
    <form method="POST">
        <table>
            <tr>
                <td>Tytuł:</td><td><input type="text" name="title"/></td>
            </tr><tr>
                <td>Autor:</td><td><input type="text" name="author"/><button id="btn_choose_author">Wybierz autora z dostępnych</button></td>
            </tr><tr>
                <td></td>
                <td>
                    <input type="text" name="author_id"/><!-- in production - change this input's type to hidden -->
                    <input type="submit" value="Dodaj" />
                </td>
            </tr>
        <table>
    </form>
    <div id="search_box">
        <input type="text" name="query"/><span style="background-color: green;" name="error"></span>
        <div name="result"/>
    </div>
</div>

<?php require "footer.php" ?>
