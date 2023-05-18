<?php
$veza = new mysqli('localhost', '', '', 'BP');

if ($veza->connect_errno) {
    die("Greska pri povezivanju na bazu: " . $veza->connect_error);
}

if (!$veza->set_charset("utf8")) {
    die("Error loading character set utf8: " . $veza->error);
}


?>