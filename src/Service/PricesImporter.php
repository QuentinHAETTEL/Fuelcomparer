<?php

namespace App\Service;

use App\Entity\Fuel;
use App\Entity\Price;
use App\Entity\Station;
use App\Repository\FuelRepository;
use App\Repository\PriceRepository;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class PricesImporter
{
    public function __construct(private XmlDownloader $downloader, private EntityManagerInterface $em, private PriceRepository $priceRepository, private StationRepository $stationRepository, private FuelRepository $fuelRepository)
    {
    }


    public function import(?SymfonyStyle $io = null): bool
    {
        $this->priceRepository->clearTable();

        $path = $this->downloader->download($io);
        if (!$path) {
            return false;
        }

        $xml = file_get_contents($path);
        if (!$xml) {
            $io?->error('An error occurred during XML reading');
            return false;
        }


        if (!$this->extractFromXml($xml, $io)) {
            $io?->error('An error occurred during XML extraction');
            return false;
        }

        return true;
    }


    private function extractFromXml(string $xml, ?SymfonyStyle $io = null): bool
    {
        $encoder = new XmlEncoder();
        $list = $encoder->decode($xml,'xml')['pdv'];

        $io?->progressStart();
        foreach ($list as $row) {
            $station = $this->stationRepository->findOneBy(['code' => $row['@id']]);

            if (array_key_exists('prix', $row) && $station !== null) {
                // If there is only one price for a station
                if (array_key_exists('@nom', $row['prix'])) {
                    $row['prix'] = [$row['prix']];
                }

                foreach ($row['prix'] as $item) {
                    $fuel = $this->fuelRepository->findOneBy(['code' => $item['@nom']]);

                    if ($fuel !== null) {
                        $price = $this->hydrate($item, $station, $fuel);
                        $this->em->persist($price);

                        $station->addPrice($price);
                        $this->em->persist($station);

                        $io?->progressAdvance();
                    }
                }
            }
        }

        $this->em->flush();
        $io?->progressFinish();

        return true;
    }


    /**
     * @param array<string|int|float> $row
     */
    public function hydrate(array $row, Station $station, Fuel $fuel): Price
    {
        $price = new Price();
        $price
            ->setStation($station)
            ->setFuel($fuel)
            ->setAmount($row['@valeur']);

        return $price;
    }
}
