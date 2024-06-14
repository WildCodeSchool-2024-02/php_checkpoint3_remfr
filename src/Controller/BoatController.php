<?php

namespace App\Controller;

use App\Service\MapManager;
use App\Repository\BoatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/move/{direction}', name: 'moveDirection')]
    public function moveDirection(
        string $direction,
        BoatRepository $boatRepository,
        EntityManagerInterface $entityManager,
        MapManager $mapManager
    ): Response {
        $boat = $boatRepository->findOneBy([]);
        $boatXCoord = $boat->getCoordX();
        $boatYCoord = $boat->getCoordY();

        switch ($direction) {
            case 'N':
                $boatYCoord -= 1;
                break;
            case 'E':
                $boatXCoord += 1;
                break;
            case 'W':
                $boatXCoord -= 1;
                break;
            case 'S':
                $boatYCoord += 1;
                break;
        }

        if ($mapManager->validateCoordinates($boatXCoord, $boatYCoord)) {
            $boat->setCoordX($boatXCoord);
            $boat->setCoordY($boatYCoord);
            $entityManager->flush();
            if ($mapManager->checkTreasure($boat) == true) {
                $this->addFlash('victorymsg', 'You found the Treasure !');
            } else {
                return $this->redirectToRoute('map');
            } 
        } else {
            $this->addFlash('messages', 'Boat is out of bounds');
        }
       return $this->redirectToRoute('map'); 
        
    }
}
