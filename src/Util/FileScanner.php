<?php

namespace Tusk\Util;

/**
 * Scans directories and their subdirectories for files with the specified
 * ending, e.g. '.php' or 'Spec.php'
 *
 * @author James Lippitt
 */
class FileScanner
{
    private $proxy;

    public function __construct(GlobalFunctionProxy $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Iterates through a list of directories, scanning them and their
     * subdirectories for any files whose names end with the specified string.
     * Returns the paths of all resulting files as an array.
     *
     * @param array $dirs
     * @param string $ending
     * @return array
     */
    public function find(array $dirs, $ending)
    {
        $ending = '/' . preg_quote($ending) . '$/';

        $files = [];

        foreach ($dirs as $dir) {
            $files = array_merge($files, $this->scan($dir, $ending));
        }

        return $files;
    }

    private function scan($dir, $ending)
    {
        $files = [];

        if ($this->proxy->is_dir($dir)) {
            foreach ($this->proxy->scandir($dir) as $file) {
                if ($file[0] !== '.') {
                    $files = array_merge(
                        $files,
                        $this->scan($dir . '/' . $file, $ending)
                    );
                }
            }

        } elseif (preg_match($ending, $dir)) {
            $files[] = $this->proxy->realpath($dir);
        }

        return $files;
    }
}
