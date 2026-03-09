<?php
require_once __DIR__ . "/../../includes/config.php";

// 1) Opdater bestilt-status
if (!empty($_POST['bestilt'])) {
    foreach ($_POST['bestilt'] as $id => $status) {
        $stmt = $pdo->prepare("UPDATE bestil SET bestilt = :status WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':id'     => $id
        ]);
    }
}

// 2) Hent alle varer der er markeret som købt
try {
    $stmt = $pdo->query("
        SELECT 
            b.id AS bestil_id,
            b.vare,
            b.bestilt,
            b.beholdID AS behold_id,
            l.varenavn
        FROM bestil b
        LEFT JOIN lagerbeholdning l ON l.id = b.beholdID
        WHERE b.bestilt = 'Ja'
        ORDER BY l.varenavn ASC
    ");
    $varer = $stmt->fetchAll();
} catch (PDOException $e) {
    echo 'SQL FEJL: ' . $e->getMessage();
    exit;
}

ob_start();
?>

<div class="container mt-4">
    <h3>Varer markeret som købt</h3>

    <table class="table table-bordered table-striped">
        <tr>
            <th>ID</th>
            <th>Vare</th>
            <th>Købt</th>
            <th>Handling</th>
        </tr>

        <?php foreach ($varer as $v): ?>
            <tr>
                <td><?= $v['bestil_id'] ?></td>
                <td><?= $v['vare'] ?></td>
                <td><?= $v['bestilt'] ?></td>
                <td>
                    <form action="/lager/modules/indkoeb/indkoeb_fra_fjern.php" method="GET">
                        <input type="hidden" name="emne" value="<?= $v['vare'] ?>">
                        <input type="hidden" name="varid" value="<?= $v['behold_id'] ?>">
                        <button class="btn btn-info btn-sm">Overfør</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
