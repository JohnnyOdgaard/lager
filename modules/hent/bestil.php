<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_GET['id'] ?? null;
$vare = null;

// Hvis der kommer et ID fra hent-modulet
if ($id) {
    $stmt = $pdo->prepare("SELECT varenavn FROM lagerbeholdning WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $vare = $stmt->fetch();
}

ob_start();
?>

<div class="container mt-4">
    <h3>Opret bestilling</h3>

    <?php if ($vare): ?>
        <p>Du er ved at bestille følgende vare:</p>

        <table class="table table-bordered table-sm">
            <tr><th>Varenavn</th><td><?= $vare['varenavn'] ?></td></tr>
            <tr><th>Beholdnings-ID</th><td><?= $id ?></td></tr>
        </table>

        <form method="POST" action="opretbestilling.php">
            <input type="hidden" name="beholdID" value="<?= $id ?>">
            <input type="hidden" name="vare" value="<?= $vare['varenavn'] ?>">

            <button class="btn btn-primary">Opret bestilling</button>
        </form>

    <?php else: ?>
        <p>Opret manuel bestilling:</p>

        <form method="POST" action="opretbestilling.php">
            <label>Varenavn</label>
            <input type="text" name="vare" class="form-control" required>

            <button class="btn btn-primary mt-3">Opret bestilling</button>
        </form>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
