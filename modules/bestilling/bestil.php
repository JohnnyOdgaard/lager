<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_GET['id'] ?? null;
$vare = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT varenavn FROM lagerbeholdning WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $vare = $stmt->fetch();
}

ob_start();
?>

<div class="container mt-4">
    <h3>Bestilling</h3>

    <?php if ($vare): ?>
        <p>Varen er nu tom på lager. Hvad vil du gøre?</p>

        <table class="table table-bordered table-sm">
            <tr><th>Varenavn</th><td><?= $vare['varenavn'] ?></td></tr>
            <tr><th>Beholdnings-ID</th><td><?= $id ?></td></tr>
        </table>

        <form method="POST" action="opretbestilling.php" style="display:inline-block;">
            <input type="hidden" name="beholdID" value="<?= $id ?>">
            <input type="hidden" name="vare" value="<?= $vare['varenavn'] ?>">
            <button class="btn btn-primary">Opret bestilling</button>
        </form>

        <form method="POST" action="slet_beholdning.php" style="display:inline-block; margin-left:10px;">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button class="btn btn-danger">Opret ikke bestilling</button>
        </form>

    <?php else: ?>
        <p>Opret manuel bestilling:</p>

        <form method="POST" action="opretbestilling.php">
            <label>Varenavn</label>
            <input type="text" name="vare" class="form-control" required autofocus>
            <button class="btn btn-primary mt-3">Opret bestilling</button>
        </form>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
