<?php
chdir('..');
require 'vendor/autoload.php';

$parsed = Mf2\fetch('https://brandonrozek.com/2015/12/creating-vcards-from-h-cards/');
print_r($parsed);

