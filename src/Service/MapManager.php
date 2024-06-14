<?php

namespace App\Service;

use App\Repository\TileRepository;
use App\Entity\Boat;
use App\Entity\Tile;

class MapManager
{
    private TileRepository $tileRepository;

    public function __construct(TileRepository $tileRepository)
    {
        $this->tileRepository = $tileRepository;
    } 

    public function validateCoordinates(int $x, int $y): bool
    {
        if ($x > 11) {
            return false;
        } elseif ($y > 11) {
            return false;
        } elseif ($x < 0) {
            return false;
        } elseif ($y < 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getRandomIsland(array $tiles): Tile
    {
        $islands = [];

        foreach ($tiles as $tile){
            if ($tile->getType() === 'island'){
            $islands[] = $tile;
            }
        }
       
        $randomKey = array_rand($islands); 
        return $islands[$randomKey];
    }

    public function checkTreasure(object $boat): bool
    {
        $boatX = $boat->getCoordX();
        $boatY = $boat->getCoordY();

        $tile = $this->tileRepository->findOneBy(['coordX' => $boatX, 'coordY' => $boatY]);
        if ($tile && $tile->hasTreasure()) {
            return true;
        } else {
            return false;
        }
    }
}