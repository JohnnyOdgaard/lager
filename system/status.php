<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . "/includes/config.php";

$placering = $_GET['placering'] ?? '';

//
// HENT PLACERINGER
//
$pladser = $pdo->query("SELECT id, plads FROM plads ORDER BY plads")->fetchAll();

//
// BYG QUERY
//
$where = "";
$params = [];

if ($placering !== '') {
    $where = "WHERE l.pla_id = :p";
    $params[':p'] = $placering;
}

$stmt = $pdo->prepare("
    SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
           e.enhed AS enhedsnavn,
           k.kategori AS kategorinavn,
           p.plads AS pladsnavn
    FROM lagerbeholdning l
    LEFT JOIN enheder e ON l.enh_id = e.id
    LEFT JOIN kategori k ON l.typ_id = k.id
    LEFT JOIN plads p ON l.pla_id = p.id
    $where
    ORDER BY l.varenavn
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Status – varer pr. placering</h3>

    <form method="GET" class="row g-2 mb-4">

        <div class="col-md-12">
            <select name="placering" class="form-control">
                <option value="">Vælg placering</option>
                <?php foreach ($pladser as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $placering == $p['id'] ? 'selected' : '' ?>>
                        <?= $p['plads'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

<div class="col-md-6 mt-2">
    <button class="btn btn-primary w-100">Søg</button>
</div>

<div class="col-md-6 mt-2">
    <button type="button" class="btn btn-secondary w-100" onclick="window.print()">Print</button>
</div>

    </form>

    <div class="print-area">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Varenavn</th>
                    <th>Kategori</th>
                    <th>Enhed</th>
                    <th>Mængde</th>
                    <th>Købt</th>
                    <th>Sidste</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= $r['varenavn'] ?></td>
                    <td><?= $r['kategorinavn'] ?></td>
                    <td><?= $r['enhedsnavn'] ?></td>
                    <td><?= $r['maengde'] ?></td>
                    <td><?= $r['kobsdato'] ?></td>
                    <td><?= $r['sidstedato'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../includes/layout.php";
