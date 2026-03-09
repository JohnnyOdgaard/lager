<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../includes/config.php";

$per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

//
// SLET RECORD
//
if (isset($_GET['slet'])) {
    $id = intval($_GET['slet']);
    $stmt = $pdo->prepare("DELETE FROM elmaaler WHERE id = :id");
    $stmt->execute([':id' => $id]);
}


//
// HENT ANTAL
//
$total = $pdo->query("SELECT COUNT(*) FROM elmaaler")->fetchColumn();

//
// HENT 10 RÆKKER
//
$stmt = $pdo->prepare("
    SELECT id, dato, el, solcelle
    FROM elmaaler
    ORDER BY id DESC
    LIMIT :off, :pp
");
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->bindValue(':pp', $per_page, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Slet elmåling</h3>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Dato</th>
                <th>El</th>
                <th>Solcelle</th>
                <th>Slet</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($rows as $r): ?>
<tr>
    <td><?= $r['id'] ?></td>
    <td><?= $r['dato'] ?></td>
    <td><?= $r['el'] ?></td>
    <td><?= $r['solcelle'] ?></td>
    <td>
        <a href="?slet=<?= $r['id'] ?>&page=<?= $page ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Slet måling?')">Slet</a>
    </td>
</tr>

        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between">
        <a class="btn btn-secondary <?= $page <= 1 ? 'disabled' : '' ?>"
           href="">Forrige</a>

        <a class="btn btn-secondary <?= $offset + $per_page >= $total ? 'disabled' : '' ?>"
           href="">Næste</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";

