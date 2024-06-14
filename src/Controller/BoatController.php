<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use App\Repository\TileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MapManager;

#[Route('/boat')]
class BoatController extends AbstractController
{
    private $mapManager;

    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

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

    #[Route('/direction/{direction<[NEWS]>}', name: 'moveDirection')]
    public function moveDirection(
        string $direction,
        BoatRepository $boatRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $boat = $boatRepository->findOneBy([]);

        $currentX = $boat->getCoordX();
        $currentY = $boat->getCoordY();

        switch ($direction) {
            case 'N':
                $newX = $currentX;
                $newY = $currentY - 1;
                break;
            case 'E':
                $newX = $currentX + 1;
                $newY = $currentY;
                break;
            case 'W':
                $newX = $currentX - 1;
                $newY = $currentY;
                break;
            case 'S':
                $newX = $currentX;
                $newY = $currentY + 1;
                break;
        }

        if (!$this->mapManager->tileExists($newX, $newY)) {
            $this->addFlash('danger', 'Jack ne doit pas sortir de la map !');
        } else {

        $boat->setCoordX($newX);
        $boat->setCoordY($newY);

        $entityManager->flush();
    }

        return $this->redirectToRoute('map');
    }
}
