<?php

namespace App\Controller;

use App\Repository\LocodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LocodeController extends AbstractController
{

    const MESSAGE_ERROR_EMPTY_RESPONSE = ['message' => 'Location not found'];

    private LocodeRepository $locodeRepository;

    public function __construct(LocodeRepository $locodeRepository)
    {
        $this->locodeRepository = $locodeRepository;
    }


    #[Route('/api/locode', name: 'get_location_by_code', methods: ['GET'])]
    public function getLocationByCode(Request $request): Response
    {
        $location = $this->locodeRepository->findBy(['locode' => $request->get('code')]);

        if (empty($location)) {
            return new Response(
                json_encode(self::MESSAGE_ERROR_EMPTY_RESPONSE),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        return new Response(json_encode($location[0]->toArray()), 200, ['Content-Type' => 'application/json']);
    }


    #[Route('/api/byName', name: 'get_location_by_name', methods: ['GET'])]
    public function getLocationsByName(Request $request): Response
    {
        $locations = $this->locodeRepository->findBy(['nameWoDiacritics' => $request->get('name')]);
        foreach ($locations as $location) {
            $locationSelectByName[] = $location->toArray();
        }
        if (empty($locationSelectByName)) {
            return new Response(
                json_encode(self::MESSAGE_ERROR_EMPTY_RESPONSE),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        return new Response(json_encode($locationSelectByName), 200, ['Content-Type' => 'application/json']);
    }


}