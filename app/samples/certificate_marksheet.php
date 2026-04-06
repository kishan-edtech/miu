<?php
session_start();
require '../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php';

$header[] = ['Student ID', 'Enrollment No', 'Certificate', 'Marksheet'];

$xlsx = SimpleXLSXGen::fromArray($header)->downloadAs('Certificate Marksheet Sample.xlsx');
