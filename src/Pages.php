<?php

namespace raudius\phpdf;

const ORIENTATION_LANDSCAPE = 'L';
const ORIENTATION_PORTRAIT = 'P';

/**
 * Filters pages in a PDF according to the provided filter funciton.
 * Returns an array of (1-based-indexed) page numbers that were validly filtered.
 *
 * @return int[]
 */
function getPagesBy(Phpdf $pdf, callable $filterFunction): array {
    $data = getDocData($pdf);
    if (!$data) {
        return [];
    }
    $pages = [];
    foreach ($data['pages'] as $page) {
        $objectRef = $page['object'];
        $page['object'] = $data['objects'][$objectRef];
        if ($filterFunction($page)) {
            $pages[] = $page['pageposfrom1'];
        }
    }

    return $pages;
}

/**
 * Gets the page numbers that match the orientation.
 * For providing the orientation use the constants: ORIENTATION_LANDSCAPE or ORIENTATION_PORTRAIT.
 *
 * @return int[]
 */
function getPagesByOrientation(Phpdf $pdf, string $orientation): array {
    if (ORIENTATION_PORTRAIT !== $orientation && ORIENTATION_LANDSCAPE !== $orientation) {
        return [];
    }

    $expectPortrait = ORIENTATION_PORTRAIT === $orientation;

    return getPagesBy($pdf, function ($page) use ($expectPortrait) {
        $dimensions = $page['object']['/CropBox'] ?? $page['object']['/MediaBox'] ?? [0,0,0,0];

        [, , $width, $height] = $dimensions;
        $rotation = $page['object']['/Rotate'] ?? 0;

        $isPortrait = $height > $width;
        if (90 === $rotation || 270 === $rotation) {
            $isPortrait = !$isPortrait;
        }

        return $expectPortrait === $isPortrait;
    });
}
