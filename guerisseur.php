<?php

class Guerisseur extends Personnage
{
    public function regenerer(?int $x = null): void
    {
        if (is_null($x)) {
            $this->setPv(self::MAXLIFE);
        } else {
            $this->setPv($this->pv + ($x * 2));
        }
    }
}
