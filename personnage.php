<?php

class Personnage
{
    public function setId($id): void
    {
        $this->id = (int)$id;
    }
    /* =========================
       CONSTANTES & STATIQUES
    ========================= */
    const MAXLIFE = 100;
    protected static int $compteur = 0;

    /* =========================
       PROPRIÉTÉS
    ========================= */
    protected int $id = 0;
    protected string $name;
    protected string $type = 'inconnu';
    protected int $atk;
    protected int $defense = 0;
    protected int $pv;
    protected int $energie = 0;
    protected string $rarete = 'commun';
    protected string $faction = 'indépendant';
    protected string $description = '';
    protected string $image = '';
    protected array $dernierCoup = [
        'degats' => 0,
        'mitigation' => 0,
        'crit' => false,
        'puissante' => false,
        'esquive' => false,
        'energieAvant' => 0,
        'energieApres' => 0,
        'ciblePvAvant' => 0,
        'ciblePvApres' => 0,
    ];

    /* =========================
       CONSTRUCTEUR
    ========================= */
    public function __construct(?string $name = null, array $data = [])
    {
        if (!is_null($name)) {
            $this->setName($name);
            $this->setPv(self::MAXLIFE);
            $this->setAtk(10);
            self::$compteur++;
        }

        if (!empty($data)) {
            $this->hydrate($data);
            self::$compteur++;
        }
    }

    /* =========================
       MÉTHODES
    ========================= */
    public function crier(): string
    {
        return "AAAHHHHHHH !!!";
    }

    public function regenerer(?int $x = null): void
    {
        if (is_null($x)) {
            $this->setPv(self::MAXLIFE);
        } else {
            $this->setPv($this->pv + $x);
        }
    }

    public function isAlive(): bool
    {
        return $this->pv > 0;
    }

    public function attaque(Personnage $cible): void
    {
        $this->executerAttaque($cible, 0);
    }

    public function reinitPV(): void
    {
        $this->setPv(self::MAXLIFE);
    }

    /* =========================
       GETTERS / SETTERS
    ========================= */
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function getPv(): int
    {
        return $this->pv;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getDefense(): int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): void
    {
        $this->defense = $defense;
    }

    public function getEnergie(): int
    {
        return $this->energie;
    }

    public function setEnergie(int $energie): void
    {
        if ($energie < 0) {
            $this->energie = 0;
        } elseif ($energie > 100) {
            $this->energie = 100;
        } else {
            $this->energie = $energie;
        }
    }

    public function getDernierCoup(): array
    {
        return $this->dernierCoup;
    }

    public function getRarete(): string
    {
        return $this->rarete;
    }

    public function setRarete(string $rarete): void
    {
        $this->rarete = $rarete;
    }

    public function getFaction(): string
    {
        return $this->faction;
    }

    public function setFaction(string $faction): void
    {
        $this->faction = $faction;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function setPv(int $pv): void
    {
        if ($pv < 0) {
            $this->pv = 0;
        } elseif ($pv > self::MAXLIFE) {
            $this->pv = self::MAXLIFE;
        } else {
            $this->pv = $pv;
        }
    }

    public function getAtk(): int
    {
        return $this->atk;
    }

    public function setAtk(int $atk): void
    {
        $this->atk = $atk;
    }

    protected function executerAttaque(Personnage $cible, int $bonusAtk = 0): void
    {
        if (!$this->isAlive()) {
            return;
        }

        $energieAvant = $this->energie;
        $ciblePvAvant = $cible->getPv();

        $puissante = false;
        $energieCost = 0;
        $bonusMultiplier = 1.0;

        if ($this->energie >= 60) {
            $puissante = true;
            $energieCost = 25;
            $bonusMultiplier = 1.35;
        }

        $mitigation = (int)round($cible->getDefense() * 0.4);
        $baseDamage = max(1, ($this->atk + $bonusAtk) - $mitigation);

        $variancePercent = random_int(-10, 10);
        $baseDamage = (int)max(1, round($baseDamage * (1 + ($variancePercent / 100))));

        $esquive = false;
        $dodgeChance = min(18, intdiv($cible->getDefense(), 6));
        if ($dodgeChance > 0 && random_int(1, 100) <= $dodgeChance) {
            $esquive = true;
        }

        $crit = false;
        $critChance = min(25, 8 + (intdiv($this->energie, 20) * 4));
        if (!$esquive && random_int(1, 100) <= $critChance) {
            $crit = true;
            $baseDamage = (int)round($baseDamage * 1.5);
        }

        if (!$esquive && $puissante) {
            $baseDamage = (int)round($baseDamage * $bonusMultiplier);
        }

        $degats = $esquive ? 0 : $baseDamage;
        $cible->setPv($cible->getPv() - $degats);

        $gain = $esquive ? 4 : 8;
        $this->setEnergie($this->energie - $energieCost + $gain);

        $this->dernierCoup = [
            'degats' => $degats,
            'mitigation' => $mitigation,
            'crit' => $crit,
            'puissante' => $puissante,
            'esquive' => $esquive,
            'energieAvant' => $energieAvant,
            'energieApres' => $this->energie,
            'ciblePvAvant' => $ciblePvAvant,
            'ciblePvApres' => $cible->getPv(),
        ];
    }

    /* =========================
       STATIQUES
    ========================= */
    public static function afficheCompteur(): int
    {
        return self::$compteur;
    }

    /* =========================
       HYDRATATION
    ========================= */
    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'nom':
                    $this->setName($value);
                    break;
                case 'id':
                    $this->setId($value);
                    break;
                case 'atk':
                    $this->setAtk($value);
                    break;
                case 'pv':
                    $this->setPv($value);
                    break;
                case 'type':
                    $this->setType($value);
                    break;
                case 'defense':
                    $this->setDefense($value);
                    break;
                case 'energie':
                    $this->setEnergie($value);
                    break;
                case 'rarete':
                    $this->setRarete($value);
                    break;
                case 'faction':
                    $this->setFaction($value);
                    break;
                case 'description':
                    $this->setDescription($value);
                    break;
                case 'image':
                    $this->setImage($value);
                    break;
                default:
                    $method = 'set' . ucfirst($key);
                    if (method_exists($this, $method)) {
                        $this->$method($value);
                    }
            }
        }
    }
}

