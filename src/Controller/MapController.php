<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Entity\Tile;
use App\Service\MapManager;
use App\Repository\BoatRepository;
use App\Repository\TileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    #[Route('/map', name: 'map')]
    public function displayMap(BoatRepository $boatRepository, TileRepository $tileRepository): Response
    {
        $tiles = $tileRepository->findAll();

        foreach ($tiles as $tile) {
            $map[$tile->getCoordX()][$tile->getCoordY()] = $tile;
        }

        $boat = $boatRepository->findOneBy([]);

        return $this->render('map/index.html.twig', [
            'map'  => $map ?? [],
            'boat' => $boat,
            'tile' => $tile,
        ]);
    }

    #[Route('/start', name: 'start')]
    public function start(BoatRepository $boatRepository, TileRepository $tileRepository, MapManager $mapManager, EntityManagerInterface $entityManager): Response
    {
        $boat = $boatRepository->findOneBy([]);
        $boat->setCoordX(0);
        $boat->setCoordY(0);
        
        $oldTreasure = $tileRepository->findOneBy(['hasTreasure' => true]);
        if ($oldTreasure) {
            $oldTreasure->setHasTreasure(false);
        }

        $treasureIsland = $mapManager->getRandomIsland($tileRepository->findAll());
        $treasureIsland->setHasTreasure(true);
        $entityManager->flush();
        return $this->redirectToRoute('map');
    }
}
