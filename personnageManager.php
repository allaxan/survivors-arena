<?php
require_once 'config.php';

/**
 * PersonnageManager - Gestion des personnages en base de données
 * 
 * À TOI DE JOUER ! Tu vas implémenter les méthodes CRUD ci-dessous.
 */
class PersonnageManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    /**
     * TODO 1 : Récupère tous les personnages de la base
     * @return array Tableau d'objets Personnage
     * 
     * INDICES :
     * - Utilise $this->pdo->query("SELECT * FROM personnages")
     * - Parcours les résultats avec fetchAll()
     * - Pour chaque ligne, crée un Personnage avec hydrate()
     */
    public function getAll(): array {
        $personnages = []; 
        $stmt = $this->pdo->query("SELECT * FROM personnages"); 
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            $perso = new Personnage(); 
            $perso->hydrate($row); 
            $personnages[] = $perso; 
        }
        return $personnages; 
    }
    
    /**
     * TODO 2 : Récupère un personnage par son ID
     * @param int $id
     * @return Personnage|null
     * 
     * INDICES :
     * - Utilise une requête préparée : prepare("SELECT ... WHERE id = :id")
     * - Execute avec ['id' => $id]
     * - Récupère avec fetch()
     */
    public function getById(int $id): ?Personnage {
        $stmt = $this->pdo->prepare("SELECT * FROM personnages WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(); 
        if ($row) {
            $perso = new Personnage();
            $perso->hydrate($row);
            return $perso;
        }
        return null;
    }
    
    /**
     * TODO 3 : Ajoute un nouveau personnage en base
     * @param Personnage $personnage
     * @return bool
     * 
     * INDICES :
     * - INSERT INTO personnages (nom, atk, pv) VALUES (:nom, :atk, :pv)
     * - Utilise les getters du personnage
     * - Retourne true si réussi
     */
    public function add(Personnage $personnage): bool {
        $stmt = $this->pdo->prepare("INSERT INTO personnages (nom, type, atk, defense, pv, energie, rarete, faction, description, image) VALUES (:nom, :type, :atk, :defense, :pv, :energie, :rarete, :faction, :description, :image)");
        $result = $stmt->execute([
            'nom' => $personnage->getName(),
            'type' => $personnage->getType(),
            'atk' => $personnage->getAtk(),
            'defense' => $personnage->getDefense(),
            'pv' => $personnage->getPv(),
            'energie' => $personnage->getEnergie(),
            'rarete' => $personnage->getRarete(),
            'faction' => $personnage->getFaction(),
            'description' => $personnage->getDescription(),
            'image' => $personnage->getImage()
        ]);
        if ($result) {
            $personnage->setId($this->pdo->lastInsertId());
        }
        return $result;
    }
    
    /**
     * TODO 4 : Met à jour un personnage existant
     * @param Personnage $personnage
     * @return bool
     * 
     * INDICES :
     * - UPDATE personnages SET nom=:nom, atk=:atk, pv=:pv WHERE id=:id
     * - N'oublie pas l'ID dans le WHERE !
     */
    public function update(Personnage $personnage): bool {
        $stmt = $this->pdo->prepare("UPDATE personnages SET nom=:nom, type=:type, atk=:atk, defense=:defense, pv=:pv, energie=:energie, rarete=:rarete, faction=:faction, description=:description, image=:image WHERE id=:id");
        $result = $stmt->execute([
            'nom' => $personnage->getName(),
            'type' => $personnage->getType(),
            'atk' => $personnage->getAtk(),
            'defense' => $personnage->getDefense(),
            'pv' => $personnage->getPv(),
            'energie' => $personnage->getEnergie(),
            'rarete' => $personnage->getRarete(),
            'faction' => $personnage->getFaction(),
            'description' => $personnage->getDescription(),
            'image' => $personnage->getImage(),
            'id' => $personnage->getId()
        ]);
        return $result; 
    }
    
    /**
     * TODO 5 : Supprime un personnage
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM personnages WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);
        return $result;
    }
}
