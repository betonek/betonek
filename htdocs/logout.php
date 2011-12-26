<?php
require "include/lib/lib.php";

Session::kill();
header("Location: index.php");
