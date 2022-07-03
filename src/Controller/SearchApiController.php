<?php

namespace App\Controller;

use App\Repository\StationRepository;
use App\Service\GetCoordsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class SearchApiController extends AbstractController
{
    public function __construct(private GetCoordsService $coordsService, private StationRepository $stationRepository)
    {
    }


    #[Route('/search', name: 'api_search', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $request = json_decode($request->getContent(), true);

        $coords = $this->coordsService->getCenterOfCity($request['data']['selectedCity']['name']);
        $stations = $this->stationRepository->findStationsInRadius($coords[0], $coords[1], $request['data']['distance']);

        return new JsonResponse($stations);
    }
}