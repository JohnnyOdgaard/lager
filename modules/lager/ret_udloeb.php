<?php
require_once __DIR__ . "/../../includes/config.php";

$search = $_GET['search'] ?? '';
$message = '';

//
// GEM ÆNDRING
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $dato = trim($_POST['sidstedato']);

    if ($dato !== '') {
        $stmt = $pdo->prepare("UPDATE lagerbeholdning SET sidstedato = :d WHERE id = :id");
        $stmt->execute([':d' => $dato, ':id' => $id]);
        $message = "Udløbsdato opdateret.";
    } else {
        $message = "Ugyldig dato.";
    }
}

//
// SØG EFTER VARER
//
$rows = [];
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
               p.plads AS pladsnavn
        FROM lagerbeholdning l
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
    <h3>Ret udløbsdato</h3>

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
                    <th>Købt</th>
                    <th>Udløber</th>
                    <th>Placering</th>
                    <th>Mængde</th>
                    <th>Ret</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td data-label="ID">
                        <span class="btn btn-secondary btn-sm disabled"><?= $r['id'] ?></span>
                    </td>

                    <td data-label="Varenavn"><?= $r['varenavn'] ?></td>
                    <td data-label="Købt"><?= $r['kobsdato'] ?></td>
                    <td data-label="Udløber"><?= $r['sidstedato'] ?></td>
                    <td data-label="Placering"><?= $r['pladsnavn'] ?></td>
                    <td data-label="Mængde"><?= $r['maengde'] ?></td>

                    <td data-label="Ret">
                        <button class="btn btn-primary btn-sm"
                                onclick="document.getElementById('edit-<?= $r['id'] ?>').showModal()">
                            Ret
                        </button>
                    </td>
                </tr>

                <!-- MODAL -->
                <dialog id="edit-<?= $r['id'] ?>" class="p-3 rounded">
                    <form method="POST">
                        <h5>Ret udløbsdato</h5>

                        <input type="hidden" name="id" value="<?= $r['id'] ?>">

                        <label>Udløbsdato</label>
                        <input type="date" name="sidstedato" class="form-control mb-3"
                               value="<?= htmlspecialchars($r['sidstedato']) ?>">

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
