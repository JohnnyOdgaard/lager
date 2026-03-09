<?php
require_once __DIR__ . "/../../includes/config.php";

// Hent alle kategorier
$katStmt = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori");
$kategorier = $katStmt->fetchAll();

$kat_id = $_GET['kat_id'] ?? '';
$varer = [];

if ($kat_id !== '') {
    $stmt = $pdo->prepare("
        SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
        p.plads AS pladsnavn
        FROM lagerbeholdning l
        LEFT JOIN plads p ON l.pla_id = p.id
        WHERE l.typ_id = :kat
        ORDER BY l.varenavn

    ");
    $stmt->execute([':kat' => $kat_id]);
    $varer = $stmt->fetchAll();
}

ob_start();
?>

<div class="container mt-4">
    <h3>Hent varer efter kategori</h3>

    <form method="GET" class="mb-3" style="max-width:300px;">
        <label for="kat_id">Vælg kategori:</label>
        <select name="kat_id" id="kat_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- vælg --</option>

            <?php foreach ($kategorier as $k): ?>
                <option value="<?= $k['id'] ?>" <?= ($k['id'] == $kat_id ? 'selected' : '') ?>>
                    <?= $k['kategori'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($kat_id !== ''): ?>
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>ID</th>
            <th>Varenavn</th>
            <th>Købt</th>
            <th>Sidste</th>
            <th>Placering</th>
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
            <td data-label="Placering"><?= $v['pladsnavn'] ?></td>
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
