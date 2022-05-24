<?php

namespace raudius\phpdf;

use MrRio\ShellWrap as sh;
use Throwable;

/**
 * @param string ...$options
 * @return string
 * @throws PhpdfException
 */
function qpdf(...$options): string {
    $previousEoE = sh::$exceptionOnError;
    sh::$exceptionOnError = true;

    try {
        $result = sh::qpdf(...$options);
    } catch (Throwable $e) {
        throw new PhpdfException($e->getMessage(), $e->getCode(), $e);
    } finally {
        sh::$exceptionOnError = $previousEoE;
    }

    return trim($result);
}

function pdfStub(): string {
    try {
        return qpdf('--empty', '--', '-');
    } catch (PhpdfException $exception) {
        return "%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000052 00000 n
0000000101 00000 n
trailer<</Size 4/Root 1 0 R>>
startxref
178
%%EOF";
    }
}

/**
 * Returns the number of pages in the document.
 * If there was an error while reading the document output is -1.
 *
 * @param Phpdf $pdf
 * @param string|null $password
 * @return int
 */
function getPageCount(Phpdf $pdf, ?string $password = null): int {
    $options = ['--show-npages', $pdf->getPath()];
    if ($password) {
        $options[] = "--password=$password";
    }
    try {
        $n = qpdf(...$options);
        if (is_numeric($n)) {
            return (int) $n;
        }
    } catch (PhpdfException $e) {
    }
    return -1;
}

function getDocData(Phpdf $pdf): ?array {
    try {
        $json = qpdf($pdf->getPath(), '--json=1');
        return json_decode($json, true);
    } catch (PhpdfException $e) {
        return null;
    }
}

function checkQpdfDependency(): bool {
    try {
        qpdf('--version');
    } catch (PhpdfException $e) {
        return false;
    }
    return true;
}

function isEncrypted(Phpdf $phpdf): bool {
    try {
        qpdf('--is-encrypted', $phpdf->getPath());
    } catch (PhpdfException $e) {
        return false;
    }
    return true;
}
