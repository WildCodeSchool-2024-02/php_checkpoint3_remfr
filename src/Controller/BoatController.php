<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/boat')]
class BoatController extends AbstractController
{
    #[Route('/move/{x<\d+>}/{y<\d+>}', name: 'moveBoat', requirements: ['x'=>'\d+','y'=>'\d+'])]
    public function moveBoat(int $x,int $y,BoatRepository $boatRepository,EntityManagerInterface $entityManager): Response
    {
        $boat = $boatRepository->findOneBy([]);
        $boat->setCoordX($x);
        $boat->setCoordY($y);

        $entityManager->flush();
        
        return $this->redirectToRoute('map');
    }

    #[Route('/direction/{direction}', name: 'moveDirection', requirements: ['direction'=>'[NSEW]'])]
    public function moveDirection($direction, BoatRepository $boatRepository, EntityManagerInterface $entityManager, ): Response
    {
        $boat = $boatRepository->findOneBy([]);
        $x = $boat->getCoordX();
        $y = $boat->getCoordY();

        switch ($direction) {
            case 'N':
                $boat->setCoordY($y - 1);
                break;
            case 'S':
                $boat->setCoordY($y + 1);
                break;
            case 'E':
                $boat->setCoordX($x - 1);
                break;
            case 'W':
                $boat->setCoordX($x + 1);
                break;
        }
        $entityManager->flush();
        return $this->redirectToRoute('map');
    }
}
