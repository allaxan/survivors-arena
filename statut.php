<?php
// Classe Statut : représente un effet temporaire sur un personnage
class Statut {
    public string $type; // 'empoisonne', 'infecte', etc.
    public int $duree;   // nombre de tours

    public function __construct($type, $duree) {
        $this->type = $type;
        $this->duree = $duree;
    }
}
