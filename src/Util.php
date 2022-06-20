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

    if (Phpdf::getSuppressWarnings()) {
        $options[] = '--no-warn';
    }

    try {
        $result = sh::qpdf(...$options);
    } catch (Throwable $e) {
        if (Phpdf::getSuppressWarnings() === true && $e->getCode() === 3) {
            return $e->getMessage();
        }
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

/**
 * Returns the QPDF version as an integer.
 * Each versioning number gets inflated by 100, this format comfortably fits all versions up to 999.999.999 within
 * a 32-bit int.
 * Version 9.1.1 -> 009 001 001 -> 009001001
 *
 * If qpdf is not installed: -1 is returned.
 * If qpdf appears to be installed but the version could not be determined: 0 is returned.
 *
 * @return int
 */
function getQpdfVersion(): int {
    // Can't suppress warning on --version command.
    $previous = Phpdf::setSuppressWarnings(false);

    try {
        $output = qpdf('--version');
    } catch (PhpdfException $e) {
        return -1;
    }
    Phpdf::setSuppressWarnings($previous);
    preg_match('/version (\d+)\.(\d+)\.(\d+)/', $output, $matches);

    if (count($matches) < 4) {
        return 0;
    }

    return (int) ($matches[3])
        + (int) ($matches[2]) * 1000
        + (int) ($matches[1]) * 1000 * 1000;
}

function checkQpdfDependency(): bool {
    return getQpdfVersion() > 0;
}

function isEncrypted(Phpdf $phpdf): bool {
    try {
        qpdf('--is-encrypted', $phpdf->getPath());
    } catch (PhpdfException $e) {
        return false;
    }
    return true;
}
