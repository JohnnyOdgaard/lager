<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM lagerbeholdning WHERE id = :id");
$stmt->execute([':id' => $id]);
$vare = $stmt->fetch();

if (!$vare) {
    die("Varen findes ikke.");
}

ob_start();
?>

<div class="container mt-4">
    <h3>Hent vare</h3>

    <table class="table table-bordered table-sm">
        <tr><th>Varenavn</th><td><?= $vare['varenavn'] ?></td></tr>
        <tr><th>Købt</th><td><?= $vare['kobsdato'] ?></td></tr>
        <tr><th>Sidste</th><td><?= $vare['sidstedato'] ?></td></tr>
        <tr><th>Placering</th><td><?= $vare['pla_id'] ?></td></tr>
        <tr><th>Mængde</th><td><?= $vare['maengde'] ?></td></tr>
    </table>

    <form method="POST" action="hent_opdater.php">
        <input type="hidden" name="id" value="<?= $vare['id'] ?>">

        <label for="antal">Hvor meget vil du hente?</label>
       <input type="number" name="antal" class="form-control" value="<?= $vare['maengde'] ?>" autofocus>
        <button class="btn btn-primary mt-3">Hent</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
