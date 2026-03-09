<?php
require_once __DIR__ . "/../../includes/config.php";

// Hent kategorier
$katStmt = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori");
$kategorier = $katStmt->fetchAll();

// Hent placeringer
$plStmt = $pdo->query("SELECT id, plads FROM plads ORDER BY plads");
$placeringer = $plStmt->fetchAll();

$kat_id = $_GET['kat_id'] ?? '';
$pl_id  = $_GET['pl_id'] ?? '';
$navn   = $_GET['navn'] ?? '';

$sql = "
    SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
           p.plads AS pladsnavn
    FROM lagerbeholdning l
    LEFT JOIN plads p ON l.pla_id = p.id
    WHERE 1=1
";

$params = [];

if ($kat_id !== '') {
    $sql .= " AND l.typ_id = :kat";
    $params[':kat'] = $kat_id;
}

if ($pl_id !== '') {
    $sql .= " AND l.pla_id = :pl";
    $params[':pl'] = $pl_id;
}

if ($navn !== '') {
    $sql .= " AND l.varenavn LIKE :navn";
    $params[':navn'] = "%$navn%";
}

$sql .= " ORDER BY l.varenavn";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$varer = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Kombineret søgning</h3>

    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-4">
            <label>Kategori</label>
            <select name="kat_id" class="form-select">
                <option value="">Alle</option>
                <?php foreach ($kategorier as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($k['id'] == $kat_id ? 'selected' : '') ?>>
                        <?= $k['kategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Placering</label>
            <select name="pl_id" class="form-select">
                <option value="">Alle</option>
                <?php foreach ($placeringer as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($p['id'] == $pl_id ? 'selected' : '') ?>>
                        <?= $p['plads'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Varenavn</label>
            <input type="text" name="navn" class="form-control" value="<?= htmlspecialchars($navn) ?>" autofocus>
        </div>

        <div class="col-12">
            <button class="btn btn-primary">Søg</button>
        </div>
    </form>

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

</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
