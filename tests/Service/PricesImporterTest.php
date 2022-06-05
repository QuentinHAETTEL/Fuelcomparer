<?php

namespace App\Tests\Service;

use App\Entity\Fuel;
use App\Entity\Station;
use App\Repository\FuelRepository;
use App\Repository\PriceRepository;
use App\Repository\StationRepository;
use App\Service\PricesImporter;
use App\Service\XmlDownloader;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PricesImporterTest extends TestCase
{
    private PricesImporter $pricesImporter;
    private array $data = [
        [
            '@nom' => 'Gazole',
            '@id' => 1,
            '@maj' => '2022-01-01 00:00:00',
            '@valeur' => '1.750'
        ],
        [
            '@nom' => 'E10',
            '@id' => 5,
            '@maj' => '2022-01-01 00:00:00',
            '@valeur' => '1.855'
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();
        $downloader = $this->createMock(XmlDownloader::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $priceRepository = $this->createMock(PriceRepository::class);
        $stationRepository = $this->createMock(StationRepository::class);
        $fuelRepository = $this->createMock(FuelRepository::class);
        $this->pricesImporter = new PricesImporter($downloader, $em, $priceRepository, $stationRepository, $fuelRepository);
    }


    public function testHydrate(): void
    {
        $station = new Station();
        $fuel = new Fuel();

        foreach ($this->data as $index => $row) {
            $price = $this->pricesImporter->hydrate($row, $station, $fuel);
            $this->assertEquals($this->data[$index]['@valeur'], $price->getAmount());
        }
    }
}