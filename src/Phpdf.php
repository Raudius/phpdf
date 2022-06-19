<?php

namespace raudius\phpdf;

class Phpdf {
    /** @var resource */
    private $file;
    /** @var string|null */
    private static $tempDirectory;

    /**
     * @param resource $file
     */
    private function __construct($file) {
        $this->file = $file;
    }

    /**
     * Creates an empty PDF file.
     * Optionally a string representation of a PDF can be passed to populate the file.
     *
     * @param string|null $data
     * @return Phpdf
     */
    public static function create(string $data=null): Phpdf {
        if (!$data) {
            $data = pdfStub();
        }
        $file = static::createTempFile();
        fwrite($file, $data);
        return new self($file);
    }

    /**
     * Create a Phpdf object from an existing file. You can pass a fopen-handler or a path ot the file.
     * Do note that passing your own handler is of no particular benefit. The given handler will not be used for file
     * operations (these are handled externally through QPDF).
     *
     * @param resource|string $file - A resource object or path string pointing to the PDF file.
     * @param bool $overwrite - Whether we should make any changes directly to the file.
     * @return Phpdf
     * @throws PhpdfException
     */
    public static function fopen($file, bool $overwrite=false): Phpdf {
        if (!is_resource($file)) {
            $file = @fopen($file, 'rb');
            if (!$file) {
                throw new PhpdfException('Could not open file in path');
            }
        }

        if (!$overwrite) {
            $tmp = static::createTempFile();
            if (!$tmp) {
                throw new PhpdfException('Could not create temporary file.');
            }

            while (!feof($file)) {
                fwrite($tmp, fread($file, 2048));
            }
            fclose($file);
            $file = $tmp;
        }

        return new self($file);
    }

    /**
     * Creates a copy of the PDF in the provided path.
     *
     * @param string $path
     * @return bool
     */
    public function saveAs(string $path): bool {
        return copy($this->getPath(), $path);
    }

    /**
     * Closes the file handler.
     * This will render this object useless.
     * @return void
     */
    public function fclose(): void {
        fclose($this->file);
    }

    /**
     * Returns the path where the PDF is stored.
     * Unless you instantiated the Phpdf object with `::fopen` and specified the `overwrite=true` parameter, this should
     * return the path to a temporary file.
     *
     * @return string
     */
    public function getPath(): string {
        $meta_data = stream_get_meta_data($this->file);
        return $meta_data["uri"];
    }

    /**
     * @return resource
     */
    private static function createTempFile() {
        $temp_dir = static::$tempDirectory ?? sys_get_temp_dir();
        $filename = tempnam($temp_dir, 'phpdf_');

        if (!$filename) {
            throw new PhpdfException("Could not create a temporary file.");
        }

        return fopen($filename, 'wb');
    }

    /**
     * @param string $path
     * @return void
     */
    public static function setTempDirectory(string $path): void {
        static::$tempDirectory = $path;
    }
}
