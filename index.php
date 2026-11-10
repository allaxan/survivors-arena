<?php
spl_autoload_register(function ($class) {
    $classMap = [
        'Personnage' => 'personnage.php',
        'PersonnageManager' => 'personnageManager.php',
        'Guerrier' => 'guerrier.php',
        'Guerisseur' => 'guerisseur.php',
        'Combat' => 'combat.php',
        'Attaque' => 'attaque.php',
        'Statut' => 'statut.php',
    ];

    if (isset($classMap[$class])) {
        require_once $classMap[$class];
        return;
    }

    require_once $class . '.php';
});
require_once 'personnageManager.php';
$manager = new PersonnageManager();
$personnages = $manager->getAll();

function normalizeRareteClass(string $rarete): string
{
    $rarete = trim($rarete);
    $rarete = strtr($rarete, [
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'À' => 'A', 'Â' => 'A', 'Ä' => 'A',
        'à' => 'a', 'â' => 'a', 'ä' => 'a',
        'Î' => 'I', 'Ï' => 'I', 'î' => 'i', 'ï' => 'i',
        'Ô' => 'O', 'Ö' => 'O', 'ô' => 'o', 'ö' => 'o',
        'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ç' => 'C', 'ç' => 'c',
    ]);
    $rarete = strtolower($rarete);
    $rarete = preg_replace('/[^a-z0-9]+/', '-', $rarete);
    $rarete = trim($rarete, '-');

    return $rarete !== '' ? $rarete : 'commun';
}

function buildCombatIntro(Personnage $attaquant, Personnage $cible): string
{
    $sameFaction = $attaquant->getFaction() === $cible->getFaction();
    if ($sameFaction) {
        return $attaquant->getName() . ' defie un allie : ' . $cible->getName() . ' !';
    }
    return $attaquant->getName() . ' (' . $attaquant->getFaction() . ') defie ' . $cible->getName() . ' (' . $cible->getFaction() . ').';
}

function buildAttackNarration(Personnage $attaquant, Personnage $cible): string
{
    $info = $attaquant->getDernierCoup();
    $parts = [];
    $parts[] = $attaquant->getName() . ' attaque ' . $cible->getName() . '.';

    if (!empty($info['esquive'])) {
        $parts[] = $cible->getName() . ' esquive le coup !';
    } else {
        $detail = 'Degats: ' . $info['degats'] . '.';
        if (!empty($info['mitigation'])) {
            $detail .= ' DEF absorbe ' . $info['mitigation'] . '.';
        }
        $parts[] = $detail;
        if (!empty($info['crit'])) {
            $parts[] = 'Coup critique !';
        }
        if (!empty($info['puissante'])) {
            $parts[] = 'Attaque puissante !';
        }
    }

    $parts[] = 'PV restants de ' . $cible->getName() . ' : ' . $cible->getPv() . '.';
    if (!$cible->isAlive()) {
        $parts[] = $cible->getName() . ' est KO !';
    }
    $parts[] = $attaquant->getName() . ' Energie: ' . $info['energieAvant'] . ' -> ' . $info['energieApres'] . '.';

    return implode("\n", $parts);
}

$selectedAttaquant = $_POST['attaquant'] ?? '';
$selectedCible = $_POST['cible'] ?? '';

// Traitement du combat
$resultat = '';
$resultatType = 'info';
$combatEffectue = false;
$resetEffectue = false;
if (isset($_POST['reset'])) {
    $nbReset = 0;
    foreach ($personnages as $p) {
        if (!$p->isAlive()) {
            $p->setPv(Personnage::MAXLIFE);
            $manager->update($p);
            $nbReset++;
        }
    }
    $resetEffectue = true;
    if ($nbReset > 0) {
        $resultatType = 'success';
        $resultat = 'Reanimation terminee : ' . $nbReset . ' personnage(s) remis sur pied.';
    } else {
        $resultatType = 'info';
        $resultat = 'Aucun personnage KO a reanimer.';
    }
} elseif (isset($_POST['attaquant'], $_POST['cible'])) {
    $idA = (int)$_POST['attaquant'];
    $idC = (int)$_POST['cible'];
    if ($idA !== $idC) {
        $attaquant = $manager->getById($idA);
        $cible = $manager->getById($idC);
        if ($attaquant && $cible && $attaquant->isAlive() && $cible->isAlive()) {
            $attaquant->attaque($cible);
            $manager->update($attaquant);
            $manager->update($cible);
            $combatEffectue = true;
            $resultatType = 'success';
            $resultat = buildCombatIntro($attaquant, $cible) . "\n";
            $resultat .= buildAttackNarration($attaquant, $cible);
            if ($cible->isAlive()) {
                $cible->attaque($attaquant);
                $manager->update($cible);
                $manager->update($attaquant);
                $resultat .= "\n" . $cible->getName() . ' contre-attaque !' . "\n";
                $resultat .= buildAttackNarration($cible, $attaquant);
            }
        } else {
            $resultatType = 'error';
            $resultat = 'Combat impossible (personnage mort ou inexistant).';
        }
    } else {
        $resultatType = 'error';
        $resultat = 'On ne peut pas se battre contre soi-même !';
    }
}

if ($combatEffectue || $resetEffectue) {
    $personnages = $manager->getAll();
}

$stats = [
    'total' => count($personnages),
    'vivants' => 0,
    'ko' => 0,
];
foreach ($personnages as $p) {
    if ($p->isAlive()) {
        $stats['vivants']++;
    } else {
        $stats['ko']++;
    }
}
$personnagesById = [];
foreach ($personnages as $p) {
    $personnagesById[$p->getId()] = $p;
}

$selectedAttaquantObj = $selectedAttaquant !== '' ? ($personnagesById[(int)$selectedAttaquant] ?? null) : null;
$selectedCibleObj = $selectedCible !== '' ? ($personnagesById[(int)$selectedCible] ?? null) : null;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combat</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="<?= $combatEffectue ? 'combat-active' : '' ?>">
    <section class="arena">
        <header class="arena-header">
            <h1 class="arena-title">Arène de combat</h1>
            <p class="subtitle">Choisis un attaquant et une cible pour lancer un duel.</p>
            <div class="page-summary">
                Total : <strong><?= $stats['total'] ?></strong> |
                Vivants : <strong><?= $stats['vivants'] ?></strong> |
                KO : <strong><?= $stats['ko'] ?></strong>
            </div>
            <form method="post" class="reset-form" onsubmit="return confirm('Reanimer tous les personnages KO ?');">
                <input type="hidden" name="reset" value="1">
                <button type="submit">Reanimer tous les KO</button>
            </form>
        </header>
        <?php if ($resultat): ?>
            <div class="resultat resultat--<?= $resultatType ?>" role="status" aria-live="polite">
                <?= nl2br($resultat) ?>
            </div>
        <?php endif; ?>
        <div class="duel-grid">
            <div class="fighter-card fighter-card--attacker">
                <div class="fighter-role">Attaquant</div>
                <?php if ($selectedAttaquantObj): ?>
                    <?php
                    $aPv = $selectedAttaquantObj->getPv();
                    $aPvPercent = max(0, min(100, $aPv));
                    $aEnergie = $selectedAttaquantObj->getEnergie();
                    $aEnergiePercent = max(0, min(100, $aEnergie));
                    $attaqueImage = $selectedAttaquantObj->getImage();
                    ?>
                    <?php if (!empty($attaqueImage)): ?>
                        <div class="fighter-image">
                            <img src="<?= $attaqueImage ?>" alt="<?= $selectedAttaquantObj->getName() ?>" style="display: block;">
                        </div>
                    <?php endif; ?>
                    <div class="fighter-head">
                        <div>
                            <div class="fighter-name"><?= $selectedAttaquantObj->getName() ?></div>
                            <div class="fighter-meta">#<?= $selectedAttaquantObj->getId() ?> · <?= $selectedAttaquantObj->getType() ?></div>
                        </div>
                        <?php if (!$selectedAttaquantObj->isAlive()): ?>
                            <span class="status-badge">KO</span>
                        <?php endif; ?>
                    </div>
                    <div class="fighter-meta">Rareté : <?= $selectedAttaquantObj->getRarete() ?></div>
                    <div class="stat-line">
                        <span>PV</span>
                        <strong><?= $aPv ?>/<?= Personnage::MAXLIFE ?></strong>
                    </div>
                    <div class="stat-bar pv" aria-label="PV attaquant">
                        <span style="width: <?= $aPvPercent ?>%"></span>
                    </div>
                    <div class="stat-line">
                        <span>Énergie</span>
                        <strong><?= $aEnergie ?>/100</strong>
                    </div>
                    <div class="stat-bar energie" aria-label="Énergie attaquant">
                        <span style="width: <?= $aEnergiePercent ?>%"></span>
                    </div>
                <?php else: ?>
                    <div class="fighter-placeholder">Choisis un attaquant pour afficher ses stats.</div>
                <?php endif; ?>
            </div>
            <div class="vs-column">
                <div class="vs-badge">VS</div>
                <div class="vs-text">Sélectionne deux personnages puis lance l'attaque.</div>
            </div>
            <div class="fighter-card fighter-card--defender">
                <div class="fighter-role">Cible</div>
                <?php if ($selectedCibleObj): ?>
                    <?php
                    $dPv = $selectedCibleObj->getPv();
                    $dPvPercent = max(0, min(100, $dPv));
                    $dEnergie = $selectedCibleObj->getEnergie();
                    $dEnergiePercent = max(0, min(100, $dEnergie));
                    ?>
                    <?php if (!empty($selectedCibleObj->getImage())): ?>
                        <div class="fighter-image">
                            <img src="<?= $selectedCibleObj->getImage() ?>" alt="<?= $selectedCibleObj->getName() ?>" style="display: block;">
                        </div>
                    <?php endif; ?>
                    <div class="fighter-head">
                        <div>
                            <div class="fighter-name"><?= $selectedCibleObj->getName() ?></div>
                            <div class="fighter-meta">#<?= $selectedCibleObj->getId() ?> · <?= $selectedCibleObj->getType() ?></div>
                        </div>
                        <?php if (!$selectedCibleObj->isAlive()): ?>
                            <span class="status-badge">KO</span>
                        <?php endif; ?>
                    </div>
                    <div class="fighter-meta">Rareté : <?= $selectedCibleObj->getRarete() ?></div>
                    <div class="stat-line">
                        <span>PV</span>
                        <strong><?= $dPv ?>/<?= Personnage::MAXLIFE ?></strong>
                    </div>
                    <div class="stat-bar pv" aria-label="PV cible">
                        <span style="width: <?= $dPvPercent ?>%"></span>
                    </div>
                    <div class="stat-line">
                        <span>Énergie</span>
                        <strong><?= $dEnergie ?>/100</strong>
                    </div>
                    <div class="stat-bar energie" aria-label="Énergie cible">
                        <span style="width: <?= $dEnergiePercent ?>%"></span>
                    </div>
                <?php else: ?>
                    <div class="fighter-placeholder">Choisis une cible pour afficher ses stats.</div>
                <?php endif; ?>
            </div>
        </div>
        <form method="post" class="combat-form">
            <label>Attaquant :
                <select name="attaquant" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($personnages as $p): ?>
                        <?php if ($p->isAlive()): ?>
                            <option value="<?= $p->getId() ?>" <?= ((string)$p->getId() === (string)$selectedAttaquant) ? 'selected' : '' ?>>
                                #<?= $p->getId() ?> <?= $p->getName() ?> (PV: <?= $p->getPv() ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Cible :
                <select name="cible" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($personnages as $p): ?>
                        <?php if ($p->isAlive()): ?>
                            <option value="<?= $p->getId() ?>" <?= ((string)$p->getId() === (string)$selectedCible) ? 'selected' : '' ?>>
                                #<?= $p->getId() ?> <?= $p->getName() ?> (PV: <?= $p->getPv() ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </label>
            <input type="submit" value="Attaquer !">
        </form>
    </section>
    <div class="card-container">
        <?php foreach ($personnages as $p): ?>
            <?php
            $isAlive = $p->isAlive();
            $pv = $p->getPv();
            $pvPercent = max(0, min(100, $pv));
            $energie = $p->getEnergie();
            $energiePercent = max(0, min(100, $energie));
            $rareteRaw = $p->getRarete();
            $rareteClass = normalizeRareteClass($rareteRaw);
            ?>
            <div class="card<?= $isAlive ? '' : ' dead' ?>">
                <div class="card-image">
                    <?php if (!empty($p->getImage())): ?>
                        <img src="<?= $p->getImage() ?>" alt="<?= $p->getName() ?>">
                    <?php else: ?>
                        <div class="no-image">?</div>
                    <?php endif; ?>
                </div>
                <div class="card-content">
                    <div class="card-id">
                        <strong>#<?= $p->getId() ?></strong>
                        <?php if (!$isAlive): ?>
                            <span class="status-badge">KO</span>
                        <?php endif; ?>
                    </div>
                    <h2><?= $p->getName() ?> <span class="rarete <?= $rareteClass ?>">[<?= $rareteRaw ?>]</span></h2>
                    <p><strong>Nom:</strong> <?= $p->getName() ?></p>
                    <p><strong>Type:</strong> <?= $p->getType() ?></p>
                    <p><strong>ATK:</strong> <?= $p->getAtk() ?></p>
                    <p><strong>DEF:</strong> <?= $p->getDefense() ?></p>
                    <p><strong>PV:</strong> <?= $pv ?></p>
                    <div class="stat-bar pv" aria-label="PV">
                        <span style="width: <?= $pvPercent ?>%"></span>
                    </div>
                    <p><strong>Énergie:</strong> <?= $energie ?></p>
                    <div class="stat-bar energie" aria-label="Énergie">
                        <span style="width: <?= $energiePercent ?>%"></span>
                    </div>
                    <p><strong>Rareté:</strong> <?= $rareteRaw ?></p>
                    <p><strong>Faction:</strong> <?= $p->getFaction() ?></p>
                    <p><strong>Description:</strong> <span class="desc"><?= $p->getDescription() ?></span></p>
                   
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin:2em;">
        <a href="admin.php">Retour à l'administration</a>
    </div>
    <script>
        (function () {
            const attaquant = document.querySelector('select[name="attaquant"]');
            const cible = document.querySelector('select[name="cible"]');
            if (!attaquant || !cible) {
                return;
            }

            function syncSelects(source, other) {
                const selected = source.value;
                Array.from(other.options).forEach((option) => {
                    if (!option.value) {
                        return;
                    }
                    option.disabled = selected && option.value === selected;
                });
            }

            function syncAll() {
                syncSelects(attaquant, cible);
                syncSelects(cible, attaquant);
            }

            attaquant.addEventListener('change', syncAll);
            cible.addEventListener('change', syncAll);
            syncAll();
        })();
    </script>
</body>

</html>
