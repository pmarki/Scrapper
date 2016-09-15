<?php

require_once 'Scrapper.php';

//create scrapper object passing webpage url and requested category
$scrapper = new Scrapper('http://www.black-ink.org', 'Digitalia');

//if you want to change category or url after initialization you can use
//$scrapper->init('http://www.black-ink.org', 'Digitalia');

//get json data from main site and those linked to it.
echo $scrapper->json();  
