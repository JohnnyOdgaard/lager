<?php
require_once __DIR__ . "/../../includes/config.php";

$search = $_GET['search'] ?? '';
$message = '';

//
// HENT ALLE PLACERINGER TIL DROPDOWN
//
$pladser = $pdo->query("SELECT id, plads FROM plads ORDER BY plads")->fetchAll();

//
// GEM ÆNDRING
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $pla_id = $_POST['pla_id'];

    if (is_numeric($pla_id)) {
        $stmt = $pdo->prepare("UPDATE lagerbeholdning SET pla_id = :p WHERE id = :id");
        $stmt->execute([':p' => $pla_id, ':id' => $id]);
        $message = "Placering opdateret.";
    } else {
        $message = "Ugyldig placering.";
    }
}

//
// SØG EFTER VARER
//
$rows = [];
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
               p.plads AS pladsnavn,
               k.kategori AS kategorinavn
        FROM lagerbeholdning l
        LEFT JOIN plads p ON l.pla_id = p.id
        LEFT JOIN kategori k ON l.typ_id = k.id
        WHERE l.varenavn LIKE :search
        ORDER BY l.varenavn
    ");
    $stmt->execute([':search' => "%$search%"]);
    $rows = $stmt->fetchAll();
}

ob_start();
?>

<div class="container mt-4">
    <h3>Ret placering på vare</h3>

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
                        <h5>Ret placering</h5>

                        <input type="hidden" name="id" value="<?= $r['id'] ?>">

                        <label>Ny placering</label>
                        <select name="pla_id" class="form-control mb-3">
                            <?php foreach ($pladser as $p): ?>
                                <option value="<?= $p['id'] ?>"
                                    <?= ($p['plads'] === $r['pladsnavn']) ? 'selected' : '' ?>>
                                    <?= $p['plads'] ?>
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
