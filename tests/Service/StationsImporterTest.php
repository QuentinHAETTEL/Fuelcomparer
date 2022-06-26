<?php

namespace App\Tests\Service;

use App\Entity\Station;
use App\Repository\StationRepository;
use App\Service\StationsImporter;
use App\Service\XmlDownloader;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class StationsImporterTest extends TestCase
{
    private StationsImporter $stationsImporter;


    public function setUp(): void
    {
        parent::setUp();
        $downloader = $this->createMock(XmlDownloader::class);
        $kernel = $this->createMock(KernelInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $stationRepository = $this->createMock(StationRepository::class);
        $this->stationsImporter = new StationsImporter($downloader, $kernel, $em, $stationRepository);
    }


    public function testHydrateXML(): void
    {
        $data = [
            '@id' => 1234567,
            '@latitude' => 5012345.678,
            '@longitude' => 3212244.567,
            '@cp' => 75000,
            '@pop' => 'R',
            'adresse' => 'Rue de la forêt',
            'ville' => 'Saint-Étienne'
        ];

        $station = $this->stationsImporter->hydrate($data);

        $this->assertEquals('1234567', $station->getCode());
        $this->assertEquals('Rue de la forêt', $station->getAddress());
        $this->assertEquals('75000', $station->getPostcode());
        $this->assertEquals('SAINT-ETIENNE', $station->getCity());
        $this->assertEquals('50.12345678', $station->getLatitude());
        $this->assertEquals('32.12244567', $station->getLongitude());
    }


    public function testHydrateJSON(): void
    {
        $data = [
            'commune' => 'PARIS',
            'id' => '1234567',
            'marque' => 'TOTAL',
            'nom' => 'CARREFOUR'
        ];

        $station = new Station();
        $station
            ->setCode('1234567')
            ->setAddress('Rue de la forêt')
            ->setPostcode(75000)
            ->setCity('PARIS');
        $station = $this->stationsImporter->hydrate($data, $station);

        $this->assertEquals('1234567', $station->getCode());
        $this->assertEquals('PARIS', $station->getCity());
        $this->assertEquals('CARREFOUR', $station->getName());
    }
}