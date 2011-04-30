<?php
require "../includes/lib/lib.php";

Session::kill();
header("Location: index.php");
