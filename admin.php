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
$editPerso = null;
if (isset($_GET['edit'])) {
    $editPerso = $manager->getById((int)$_GET['edit']);
}
if ($editPerso && isset($_POST['update'])) {
    $nom = $_POST['nom'];
    $atk = (int)$_POST['atk'];
    $pv = (int)$_POST['pv'];
    $editPerso->setName($nom);
    $editPerso->setAtk($atk);
    $editPerso->setPv($pv);
    $manager->update($editPerso);
    header("Location: admin.php");
    exit();
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $manager->delete($id);
    header("Location: admin.php");
    exit();
}
if (isset($_POST['add'])) {
    $nom = $_POST['nom'];
    $atk = (int)$_POST['atk'];
    $pv = (int)$_POST['pv'];
    $nouveauPersonnage = new Personnage();
    $nouveauPersonnage->setName($nom);
    $nouveauPersonnage->setAtk($atk);
    $nouveauPersonnage->setPv($pv);
    $manager->add($nouveauPersonnage);
    header("Location: admin.php");
    exit();
}
$personnages = $manager->getAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion des personnages</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <h1 style="text-align:center">Administration des personnages</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>ATK</th>
            <th>PV</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($personnages as $p): ?>
            <tr>
                <td><?= $p->getId() ?></td>
                <td><?= htmlspecialchars($p->getName()) ?></td>
                <td><?= $p->getAtk() ?></td>
                <td><?= $p->getPv() ?></td>
                <td>
                    <a href="?edit=<?= $p->getId() ?>">Modifier</a> |
                    <a href="?delete=<?= $p->getId() ?>" onclick="return confirm('Supprimer ce personnage ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <form method="post">
    <h2><?= $editPerso ? 'Modifier' : 'Ajouter' ?> un personnage</h2>
    <?php if ($editPerso): ?>
        <input type="hidden" name="id" value="<?= $editPerso->getId() ?>">
    <?php endif; ?>
    <label>Nom : <input type="text" name="nom" required value="<?= $editPerso ? htmlspecialchars($editPerso->getName()) : '' ?>"></label>
    <label>ATK : <input type="number" name="atk" min="1" max="100" required value="<?= $editPerso ? $editPerso->getAtk() : '' ?>"></label>
    <label>PV : <input type="number" name="pv" min="1" max="1000" required value="<?= $editPerso ? $editPerso->getPv() : '' ?>"></label>
    <input type="submit" name="<?= $editPerso ? 'update' : 'add' ?>" value="<?= $editPerso ? 'Mettre à jour' : 'Ajouter' ?>">
</form>
</body>

</html>