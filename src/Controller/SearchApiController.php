<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class SearchApiController extends AbstractController
{
    #[Route('/search', name: 'api_search', methods: ['POST'])]
    public function index(): JsonResponse
    {
        return new JsonResponse('OK');
    }
}