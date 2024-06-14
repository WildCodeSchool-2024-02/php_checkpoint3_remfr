<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MapManager;

#[Route('/boat')]
class BoatController extends AbstractController
{
    #[Route('/move/{x<\d+>}/{y<\d+>}', name: 'moveBoat')]
    public function moveBoat(
        int $x,
        int $y,
        BoatRepository $boatRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $boat = $boatRepository->findOneBy([]);
        
        $boat->setCoordX($x);
        $boat->setCoordY($y);

        $entityManager->flush();
        
        return $this->redirectToRoute('map');
    }
    #[Route('/direction/{direction<^[NSWE]$>}', name: 'direction')]
    public function moveDirection($direction, BoatRepository $boatRepository, EntityManagerInterface $entityManager, MapManager $mapManager): Response {
        $boat = $boatRepository->findOneBy([]);

        if ($direction === "E") {
            $boat->setCoordX($boat->getCoordX() + 1);
        } elseif ($direction === "W") {
            $boat->setCoordX($boat->getCoordX() - 1);
        } elseif ($direction === "N") {
            $boat->setCoordY($boat->getCoordY() - 1);
        } elseif ($direction === "S") {
            $boat->setCoordY($boat->getCoordY() + 1);
        }

        if (!$mapManager->tileExists($boat->getCoordX(), $boat->getCoordY())) {
            $this->addFlash('danger', 'Yer can\'t go there, sailor !');
            return $this->redirectToRoute('map');
        }

        //dd($mapManager->getRandomIsland());
        $entityManager->flush();

        if ($mapManager->checkTreasure($boat)) {
            $this->addFlash('success', 'Yer found the treasure, matey !');
        }

        return $this->redirectToRoute('map');
    }

}
