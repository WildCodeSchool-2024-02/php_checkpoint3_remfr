<?php

namespace App\Service;


use App\Entity\Boat;
use App\Entity\Tile;
use App\Repository\BoatRepository;
use App\Repository\TileRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Response;

class MapManager
{
    private $tileRepository;
    private $boatRepository;

    public function __construct(TileRepository $tileRepository, BoatRepository $boatRepository)
    {
        $this->tileRepository = $tileRepository;
        $this->boatRepository = $boatRepository;
    }
    public function tileExists($x, $y):bool
    {
        if (!$this->tileRepository->findOneBy(['coordX' => $x, 'coordY' => $y])) {
            return false;
        }
        return true;
    }

    public function getRandomIsland():Tile
    {
        $tiles = $this->tileRepository->findBy(['type' => 'island']);
        $treasureTile = array_rand($tiles, 1);


        return $tiles[$treasureTile];
    }

    public function checkTreasure(Boat $boat):bool
    {
        $boat = $this->boatRepository->findOneBy([]);
        $isle = $this->tileRepository->findOneBy(['coordX' => $boat->getCoordX(), 'coordY' => $boat->getCoordY()]);

        if ($isle->hasTreasure()) {
            return true;
        }
        return false;
    }
}