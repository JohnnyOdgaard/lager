<?php
require_once __DIR__ . "/../../includes/config.php";

$search = $_GET['search'] ?? '';
$message = '';

//
// HENT ALLE KATEGORIER TIL DROPDOWN
//
$kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();

//
// GEM ÆNDRING
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $kat_id = $_POST['kat_id'];

    if (is_numeric($kat_id)) {
        $stmt = $pdo->prepare("UPDATE lagerbeholdning SET typ_id = :k WHERE id = :id");
        $stmt->execute([':k' => $kat_id, ':id' => $id]);
        $message = "Kategori opdateret.";
    } else {
        $message = "Ugyldig kategori.";
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
               p.plads AS pladsnavn
        FROM lagerbeholdning l
        LEFT JOIN kategori k ON l.typ_id = k.id
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
    <h3>Ret kategori på vare</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control"
               placeholder="Søg varenavn..."
               value="<?= htmlspecialchars($search) ?>" autofocus>
    </form>

    <?php if ($search !== ''): ?>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Varenavn</th>
                    <th>Kategori</th>
                    <th>Placering</th>
                    <th>Mængde</th>
                    <th>Ret</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= $r['varenavn'] ?></td>
                    <td><?= $r['kategorinavn'] ?></td>
                    <td><?= $r['pladsnavn'] ?></td>
                    <td><?= $r['maengde'] ?></td>

                    <td>
                        <button class="btn btn-primary btn-sm"
                                onclick="document.getElementById('edit-<?= $r['id'] ?>').showModal()">
                            Ret
                        </button>
                    </td>
                </tr>

                <!-- MODAL -->
                <dialog id="edit-<?= $r['id'] ?>" class="p-3 rounded">
                    <form method="POST">
                        <h5>Ret kategori</h5>

                        <input type="hidden" name="id" value="<?= $r['id'] ?>">

                        <label>Ny kategori</label>
                        <select name="kat_id" class="form-control mb-3">
                            <?php foreach ($kategorier as $k): ?>
                                <option value="<?= $k['id'] ?>"
                                    <?= ($k['kategori'] === $r['kategorinavn']) ? 'selected' : '' ?>>
                                    <?= $k['kategori'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button class="btn btn-success">Gem</button>
                        <button type="button" class="btn btn-secondary"
                                onclick="document.getElementById('edit-<?= $r['id'] ?>').close()">
                            Luk
                        </button>
                    </form>
                </dialog>

            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
