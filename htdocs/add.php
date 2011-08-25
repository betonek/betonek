<?php

require "../includes/lib/lib.php";

lib_jsuse("add.js");

$TITLE = "Dodaj tytuł";
require "header.php";

?>
<script type="text/javascript">
var main = function()
{
	BA.init('#addbox');
}
</script>

<div id="addbox" class="loginbox formbox">
</div>
<?php require "footer.php" ?>
