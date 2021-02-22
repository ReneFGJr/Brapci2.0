<?php
use voku\helper;
use voku\helper\UTF8;

require("voku/UTF8.php");
require("voku/ASCII.php");
require("voku/Bootup.php");

$bn = new Bootup;
$bn->initAll();
$ut = new UTF8;
$ut->checkForSupport();
//Bootup::initAll(); // Enables UTF-8 for PHP
//UTF8::checkForSupport(); // Check UTF-8 support for PHP
