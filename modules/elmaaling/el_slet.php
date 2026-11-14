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
    http_response_code(405);
    header('Allow: POST');
    exit('Sletning kræver en POST-forespørgsel.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['slet'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false || $id === null) {
        http_response_code(400);
        exit('Ugyldigt ID.');
    }
    $stmt = $pdo->prepare("DELETE FROM elmaaler WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header('Location: el_slet.php?page=' . $page);
    exit;
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
        <form method="POST" action="?page=<?= $page ?>"
              onsubmit="return confirm('Slet måling?')">
            <input type="hidden" name="slet" value="<?= $r['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">Slet</button>
        </form>
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

