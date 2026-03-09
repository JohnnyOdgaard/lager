<?php
require_once __DIR__ . "/../../includes/config.php";

$search = $_GET['search'] ?? '';
$message = '';

//
// HENT ALLE ENHEDER TIL DROPDOWN (bruges kun i modal)
//
$enheder = $pdo->query("SELECT id, enhed FROM enheder ORDER BY enhed")->fetchAll();

//
// GEM ÆNDRING
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $enhed_id = $_POST['enhed_id'];

    if (is_numeric($enhed_id)) {
        $stmt = $pdo->prepare("UPDATE lagerbeholdning SET enh_id = :e WHERE id = :id");
        $stmt->execute([':e' => $enhed_id, ':id' => $id]);
        $message = "Enhed opdateret.";
    } else {
        $message = "Ugyldig enhed.";
    }
}

//
// SØG EFTER VARER
//
$rows = [];
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
               e.enhed AS enhedsnavn,
               p.plads AS pladsnavn
        FROM lagerbeholdning l
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
    <h3>Ret enhed på vare</h3>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
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
                    <th>Enhed</th>
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
                    <td><?= $r['enhedsnavn'] ?></td>
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
                        <h5>Ret enhed</h5>

                        <input type="hidden" name="id" value="<?= $r['id'] ?>">

                        <label>Ny enhed</label>
                        <select name="enhed_id" class="form-control mb-3">
                            <?php foreach ($enheder as $e): ?>
                                <option value="<?= $e['id'] ?>"
                                    <?= ($e['enhed'] === $r['enhedsnavn']) ? 'selected' : '' ?>>
                                    <?= $e['enhed'] ?>
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
