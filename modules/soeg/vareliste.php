<?php
require_once __DIR__ . "/../../includes/config.php";

$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$enhed = $_GET['enhed'] ?? '';
$placering = $_GET['placering'] ?? '';

//
// HENT DROPDOWNS
//
$kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();
$enheder = $pdo->query("SELECT id, enhed FROM enheder ORDER BY enhed")->fetchAll();
$pladser = $pdo->query("SELECT id, plads FROM plads ORDER BY plads")->fetchAll();

//
// BYG SØGEQUERY
//
$where = [];
$params = [];

if ($search !== '') {
    $where[] = "l.varenavn LIKE :s";
    $params[':s'] = "%$search%";
}
if ($kategori !== '') {
    $where[] = "l.typ_id = :k";
    $params[':k'] = $kategori;
}
if ($enhed !== '') {
    $where[] = "l.enh_id = :e";
    $params[':e'] = $enhed;
}
if ($placering !== '') {
    $where[] = "l.pla_id = :p";
    $params[':p'] = $placering;
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

$stmt = $pdo->prepare("
    SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
           e.enhed AS enhedsnavn,
           k.kategori AS kategorinavn,
           p.plads AS pladsnavn
    FROM lagerbeholdning l
    LEFT JOIN enheder e ON l.enh_id = e.id
    LEFT JOIN kategori k ON l.typ_id = k.id
    LEFT JOIN plads p ON l.pla_id = p.id
    $whereSQL
    ORDER BY l.varenavn
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Vareliste</h3>

    <form method="GET" class="row g-2 mb-4">

        <div class="col-md-3">
            <input type="text" name="search" class="form-control"
                   placeholder="Søg varenavn..."
                   value="<?= htmlspecialchars($search) ?>" autofocus>
        </div>

        <div class="col-md-3">
            <select name="kategori" class="form-control">
                <option value="">Alle kategorier</option>
                <?php foreach ($kategorier as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kategori == $k['id'] ? 'selected' : '' ?>>
                        <?= $k['kategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="enhed" class="form-control">
                <option value="">Alle enheder</option>
                <?php foreach ($enheder as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= $enhed == $e['id'] ? 'selected' : '' ?>>
                        <?= $e['enhed'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="placering" class="form-control">
                <option value="">Alle placeringer</option>
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
                <th>Placering</th>
                <th>Mængde</th>
                <th>Købt</th>
                <th>Sidste</th>
                <th>Handling</th>
            </tr>
        </thead>

        <tbody>
<?php foreach ($rows as $r): ?>
    <tr>
        <td><?= $r['id'] ?></td>
        <td><?= $r['varenavn'] ?></td>
        <td><?= $r['kategorinavn'] ?></td>
        <td><?= $r['enhedsnavn'] ?></td>
        <td><?= $r['pladsnavn'] ?></td>
        <td><?= $r['maengde'] ?></td>
        <td><?= $r['kobsdato'] ?></td>
        <td><?= $r['sidstedato'] ?></td>
        <td>
    <a href="../indkoeb/indkoeb_edit.php?id=<?= $r['id'] ?>" 
       class="btn btn-outline-primary btn-sm">
        Ret
    </a>

    <a href="../indkoeb/indkoeb_slet.php?id=<?= $r['id'] ?>" 
       class="btn btn-outline-danger btn-sm"
       onclick="return confirm('Er du sikker på, at du vil slette dette indkøb?');">
        Slet
    </a>
</td>

    </tr>
<?php endforeach; ?>

        </tbody>
    </table>
</div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
