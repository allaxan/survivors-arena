<?php
// Classe Combat : gère le déroulement d'un combat
class Combat {
    public Deck $deck1;
    public Deck $deck2;
    public int $tour = 1;

    public function __construct(Deck $deck1, Deck $deck2) {
        $this->deck1 = $deck1;
        $this->deck2 = $deck2;
    }

    // Ajoute ici les méthodes pour gérer le tour, les actions, la victoire...
}
