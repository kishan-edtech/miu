<?php
session_start();
require '../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php';

$header[] = ['UTR No', 'Amount', 'Date', 'Student ID'];

$xlsx = SimpleXLSXGen::fromArray($header)->downloadAs('University Payouts.xlsx');
