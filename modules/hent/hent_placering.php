<?php
require_once __DIR__ . "/../../includes/config.php";

// Hent alle placeringer
$plStmt = $pdo->query("SELECT id, plads FROM plads ORDER BY plads");
$placeringer = $plStmt->fetchAll();

$pl_id = $_GET['pl_id'] ?? '';
$varer = [];

if ($pl_id !== '') {
    $stmt = $pdo->prepare("
        SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
        p.plads AS pladsnavn
        FROM lagerbeholdning l
        LEFT JOIN plads p ON l.pla_id = p.id
        WHERE l.pla_id = :pl
        ORDER BY l.varenavn

    ");
    $stmt->execute([':pl' => $pl_id]);
    $varer = $stmt->fetchAll();
}

ob_start();
?>

<div class="container mt-4">
    <h3>Hent varer efter placering</h3>

    <form method="GET" class="mb-3" style="max-width:300px;">
        <label for="pl_id">Vælg placering:</label>
        <select name="pl_id" id="pl_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- vælg --</option>

            <?php foreach ($placeringer as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($p['id'] == $pl_id ? 'selected' : '') ?>>
                    <?= $p['plads'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($pl_id !== ''): ?>
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>ID</th>
            <th>Varenavn</th>
            <th>Købt</th>
            <th>Sidste</th>
            <th>Mængde</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($varer as $v): ?>
        <tr>
            <td data-label="ID">
                <a class="btn btn-primary btn-sm" href="hent_vare.php?id=<?= $v['id'] ?>">
                    <?= $v['id'] ?>
                </a>
            </td>

            <td data-label="Varenavn"><?= $v['varenavn'] ?></td>
            <td data-label="Købt"><?= $v['kobsdato'] ?></td>
            <td data-label="Sidste"><?= $v['sidstedato'] ?></td>
            <td data-label="Mængde"><?= $v['maengde'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
