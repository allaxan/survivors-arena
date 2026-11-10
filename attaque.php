<?php
// Classe Attaque : représente une attaque/action spéciale
class Attaque {
    public string $nom;
    public int $degats;
    public ?string $effet; // ex: 'empoisonne', 'stun', null

    public function __construct($nom, $degats, $effet = null) {
        $this->nom = $nom;
        $this->degats = $degats;
        $this->effet = $effet;
    }
}
