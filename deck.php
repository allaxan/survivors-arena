<?php
// Classe Deck : représente la main/équipe d'un joueur
class Deck {
    public array $cartes = []; // array de Personnage

    public function __construct(array $personnages) {
        $this->cartes = $personnages;
    }

    public function piocher() {
        return array_shift($this->cartes);
    }
}
