<?php

namespace App\Service;

use App\Entity\Station;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\String\UnicodeString;

class StationsImporter
{
    public const PATH = '/data/import/';
    public const JSON_URL = 'https://raw.githubusercontent.com/openeventdatabase/datasources/master/fr.prix-carburants/stations.json';

    public function __construct(private XmlDownloader $downloader, private KernelInterface $kernel, private EntityManagerInterface $em, private StationRepository $stationRepository)
    {
    }


    public function import(?SymfonyStyle $io = null): bool
    {
        $this->stationRepository->clearTable();

        $xmlPath = $this->downloader->download($io);
        if (!$xmlPath) {
            return false;
        }

        if (!$this->extractFromXml($xmlPath, $io)) {
            $io?->error('An error occurred during XML extraction');
            return false;
        }

        $io?->info('Downloading JSON ...');
        $jsonPath = $this->getJsonFilePath();
        if (!$jsonPath) {
            $io?->error('An error occurred during the download of the JSON file');
            return false;
        }
        $io?->info('Download complete');
        if (!$this->extractFromJson($jsonPath, $io)) {
            $io?->error('An error occurred during JSON extraction');
            return false;
        }

        return true;
    }


    private function extractFromXml(string $path, ?SymfonyStyle $io = null): bool
    {
        $xml = file_get_contents($path);
        if (!$xml) {
            return false;
        }

        $encoder = new XmlEncoder();
        $list = $encoder->decode($xml,'xml')['pdv'];

        $io?->progressStart();
        foreach ($list as $row) {
            $station = $this->hydrate($row);
            $this->em->persist($station);

            $io?->progressAdvance();
        }

        $this->em->flush();
        $io?->progressFinish();

        return true;
    }


    private function extractFromJson(string $path, ?SymfonyStyle $io = null): bool
    {
        $json = file_get_contents($path);
        if (!$json) {
            return false;
        }
        $json = json_decode($json);
        if (!$json) {
            return false;
        }

        $encoder = new JsonEncoder();

        $io?->progressStart();
        foreach ($json as $row) {
            $row = $encoder->decode($row, 'json');
            $station = $this->stationRepository->findOneBy(['code' => $row['id']]);

            if ($station) {
                $station = $this->hydrate($row, $station);
                $this->em->persist($station);
            }
            $io?->progressAdvance();
        }

        $this->em->flush();
        $io?->progressFinish();

        return true;
    }


    /**
     * @param array<string|int|float> $row
     */
    public function hydrate(array $row, ?Station $station = null): Station
    {
        if (!$station) {
            $station = new Station();
        }

        if (array_key_exists('@id', $row)) {
            $station->setCode($row['@id']);
        }
        if (array_key_exists('nom', $row)) {
            $station->setName($row['nom']);
        }
        if (array_key_exists('adresse', $row)) {
            $station->setAddress($row['adresse']);
        }
        if (array_key_exists('@cp', $row)) {
            $station->setPostcode($row['@cp']);
        }
        if (array_key_exists('ville', $row)) {
            $city = (new UnicodeString($row['ville']))
                ->ascii()
                ->upper();

            $station->setCity($city);
        }
        if (array_key_exists('@latitude', $row)) {
            $station->setLatitude($row['@latitude'] / 100000);
        }
        if (array_key_exists('@longitude', $row)) {
            $station->setLongitude($row['@longitude'] / 100000);
        }

        return $station;
    }


    /**
     * Download and return path of JSON file (null if an error occurred)
     */
    private function getJsonFilePath(): ?string
    {
        $path = $this->kernel->getProjectDir() . self::PATH . 'stations.json';
        if (file_exists($path)) {
            return $path;
        }

        $content = file_get_contents(self::JSON_URL);
        if (!$content) {
            return null;
        }

        $data = explode("\n", $content);
        array_pop($data);
        if (!file_put_contents($path, json_encode($data))) {
            return null;
        }

        return $path;
    }
}