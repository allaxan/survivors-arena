<?php

class Guerrier extends Personnage
{
    const BONUS = 20;

    public function attaque(Personnage $cible): void
    {
        $this->executerAttaque($cible, self::BONUS);
    }
}
