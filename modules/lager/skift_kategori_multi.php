<?php
require_once __DIR__ . "/../../includes/config.php";

$search = $_GET['search'] ?? '';
$message = '';

//
// HENT ALLE KATEGORIER TIL DROPDOWN
//
$kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();

//
// MASSEOPDATERING
//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids'])) {
    $ids = $_POST['ids']; // array af ID'er
    $ny_kat = $_POST['ny_kat'];

    if (is_numeric($ny_kat) && count($ids) > 0) {
        $stmt = $pdo->prepare("UPDATE lagerbeholdning SET typ_id = :k WHERE id = :id");

        foreach ($ids as $id) {
            $stmt->execute([':k' => $ny_kat, ':id' => $id]);
        }

        $message = count($ids) . " varer har fået ny kategori.";
    } else {
        $message = "Ingen varer valgt eller ugyldig kategori.";
    }
}

//
// SØG EFTER VARER
//
$rows = [];
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
               k.kategori AS kategorinavn,
               e.enhed AS enhedsnavn,
               p.plads AS pladsnavn
        FROM lagerbeholdning l
        LEFT JOIN kategori k ON l.typ_id = k.id
        LEFT JOIN enheder e ON l.enh_id = e.id
        LEFT JOIN plads p ON l.pla_id = p.id
        WHERE l.varenavn LIKE :search
        ORDER BY l.varenavn
    ");
    $stmt->execute([':search' => "%$search%"]);
    $rows = $stmt->fetchAll();
}

ob_start();
?>

<div class="container mt-4">
    <h3>Skift kategori for flere varer</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control"
               placeholder="Søg varenavn..."
               value="<?= htmlspecialchars($search) ?>" autofocus>
    </form>

    <?php if ($search !== ''): ?>

    <form method="POST">

        <div class="mb-3">
            <label>Ny kategori</label>
            <select name="ny_kat" class="form-control">
                <?php foreach ($kategorier as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= $k['kategori'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Vælg</th>
                    <th>ID</th>
                    <th>Varenavn</th>
                    <th>Kategori</th>
                    <th>Enhed</th>
                    <th>Placering</th>
                    <th>Mængde</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?= $r['id'] ?>"></td>
                    <td><?= $r['id'] ?></td>
                    <td><?= $r['varenavn'] ?></td>
                    <td><?= $r['kategorinavn'] ?></td>
                    <td><?= $r['enhedsnavn'] ?></td>
                    <td><?= $r['pladsnavn'] ?></td>
                    <td><?= $r['maengde'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <button class="btn btn-primary w-100 mt-3">Skift kategori for valgte varer</button>

    </form>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
