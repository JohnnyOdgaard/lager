<?php
require_once __DIR__ . "/../../includes/config.php";

// Konstant til EL (minus/plus 92.527)
$KONSTANT = 92527;

// GEM ÆNDRING
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);

    // Brug 92.527-logikken på EL
    $nyEl = floatval($_POST['el']) + $KONSTANT;

    // Solcelle gemmes direkte
    $nySol = floatval($_POST['solcelle']);

    $stmt = $pdo->prepare("
        UPDATE elmaaler
        SET el = :el, solcelle = :sol
        WHERE id = :id
    ");
    $stmt->execute([
        ':el' => $nyEl,
        ':sol' => $nySol,
        ':id' => $id
    ]);

    $message = "Måling opdateret.";
}


// VIS ENKELT RECORD TIL RETNING
if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT * FROM elmaaler WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);

    // Hvis ingen række findes → vis fejl i stedet for blank side
    if (!$r) {
        die("<div style='padding:20px;font-family:Arial'>
                <h3>Fejl</h3>
                <p>Der findes ingen måling med ID: $id</p>
                <p><a href='el_ret.php'>Tilbage</a></p>
             </div>");
    }

    // Vis EL minus 92.527
    $visEl = $r['el'] - $KONSTANT;

    // Solcelle vises direkte
    $visSol = $r['solcelle'];

    ob_start();
    ?>

    <div class="container mt-4">
        <h3>Ret elmåling</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">

            <label>ID</label>
            <input type="text" class="form-control mb-3" value="<?= $r['id'] ?>" disabled>

            <label>Dato</label>
            <input type="text" class="form-control mb-3" value="<?= $r['dato'] ?>" disabled>

            <label>El (vises minus 92.527)</label>
            <input type="number" step="0.001" name="el" class="form-control mb-3"
                   value="<?= $visEl ?>">

            <label>Solcelle</label>
            <input type="number" step="0.001" name="solcelle" class="form-control mb-3"
                   value="<?= $visSol ?>">

            <button class="btn btn-primary w-100">Gem</button>
        </form>
    </div>

    <?php
    $content = ob_get_clean();
    include __DIR__ . "/../../includes/layout.php";
    exit;
}


// LISTE VISNING (hvis ingen ID er valgt)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$total = $pdo->query("SELECT COUNT(*) FROM elmaaler")->fetchColumn();
$pages = ceil($total / $perPage);

$stmt = $pdo->prepare("
    SELECT id, dato, el, solcelle
    FROM elmaaler
    ORDER BY id DESC
    LIMIT :off, :pp
");
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->bindValue(':pp', $perPage, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="container mt-4">
    <h3>Elmålinger</h3>

    <table class="table table-bordered table-sm">
        <thead>
        <tr>
            <th>ID</th>
            <th>Dato</th>
            <th>El</th>
            <th>Solcelle</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td>
                    <a href="?id=<?= $r['id'] ?>&page=<?= $page ?>">
                        <?= $r['id'] ?>
                    </a>
                </td>
                <td><?= $r['dato'] ?></td>
                <td><?= $r['el'] ?></td>
                <td><?= $r['solcelle'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
