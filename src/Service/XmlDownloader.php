<?php

namespace App\Service;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use ZipArchive;

class XmlDownloader
{
    public const PATH = '/data/import/';
    public const URL = 'https://donnees.roulez-eco.fr/opendata/instantane';


    public function __construct(private KernelInterface $kernel)
    {
    }


    /**
     * Download and return path of XML file (null if an error occurred)
     */
    public function download(?SymfonyStyle $io = null): ?string
    {
        $io?->info('Downloading XML ...');
        $xmlPath = $this->getXmlFilePath();
        if (!$xmlPath) {
            $io?->error('An error occurred during the download of the XML file');
            return null;
        }
        $io?->info('Download complete');

        return $xmlPath;
    }


    private function getXmlFilePath(): ?string
    {
        $filename = date('Y-m-d') . '.xml';
        $directory = $this->kernel->getProjectDir() . self::PATH;
        $path = $directory . $filename;

        if (file_exists($path)) {
            return $path;
        }

        $tmp = tmpfile();
        $tmpPath = stream_get_meta_data($tmp)['uri'];
        if (!copy(self::URL, $tmpPath)) {
            return null;
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            return null;
        }
        $zip->renameName($zip->getNameIndex(0), $filename);
        $zip->extractTo($directory, $filename);
        $zip->close();

        return $path;
    }
}