<?php

namespace App\Controller;

use App\Service\MapManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tile;
use App\Repository\BoatRepository;
use App\Repository\TileRepository;

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

        $position = $tileRepository->findOneBy(['coordX' => $boat->getCoordX(),'coordY' => $boat->getCoordY()]);

        return $this->render('map/index.html.twig', [
            'map'  => $map ?? [],
            'boat' => $boat,
            'position' => $position,
        ]);
    }
    #[Route('/start', name: 'game_start')]
    public function start(BoatRepository $boatRepository, TileRepository $tileRepository, MapManager $mapManager, EntityManagerInterface $entityManager): Response
    {
        $boat = $boatRepository->findOneBy([]);

        $boat->setCoordX(0);
        $boat->setCoordY(0);

        $isles = $tileRepository->findAll([]);
        foreach ($isles as $tile) {
            $tile->setHasTreasure(false);
        }

        $treasureIsle = $mapManager->getRandomIsland();
        $treasureIsle->setHasTreasure(true);

        //dd($boat);
        $entityManager->flush();

        return $this->redirectToRoute('map');
    }
}
