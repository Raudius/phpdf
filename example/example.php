<?php

require_once '../vendor/autoload.php';

use raudius\phpdf\Operations\Decrypt;
use raudius\phpdf\Operations\Merge;
use raudius\phpdf\Operations\Overlay;
use raudius\phpdf\Phpdf;

$protected_pdf = Phpdf::fopen('../tests/test_files/password.pdf');
(new Decrypt('12345'))->execute($protected_pdf)->saveAs('out_decrypted.pdf');

$files_to_merge = [
    '../tests/test_files/normal.pdf',
    '../tests/test_files/2pages.pdf',
    '../tests/test_files/10pages.pdf',
];
$merge_pdfs = array_map([Phpdf::class, 'fopen'], $files_to_merge);
$merged = (new Merge($merge_pdfs))->execute();
$merged->saveAs('out_merged.pdf');

(new Overlay($protected_pdf))->execute($merged);
$merged->saveAs('out_overlay.pdf');
