<?php
require_once __DIR__ . "/../../includes/config.php";

$stmt = $pdo->query("
    SELECT id, vare, bestilt, beholdID
    FROM bestil
    ORDER BY vare ASC
");
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
            <!-- Venstre tom kolonne -->
        <div class="col-12 col-md-2"></div>

        <!-- Midterste kolonne med tabel -->
        <div class="col-12 col-md-8">
    <h3>Alle bestillinger</h3>

    <table class="table table-bordered table-sm">
        <tr>
            <th>ID</th>
            <th>Vare</th>
            <th>Bestilt</th>
            <th>BeholdID</th>
            <th>Handling</th>
        </tr>

        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= $r['vare'] ?></td>
                <td><?= $r['bestilt'] ?></td>
                <td><?= $r['beholdID'] ?: '-' ?></td>
                <td>
                    <a href="bestil_slet.php?id=<?= $r['id'] ?>"
                       onclick="return confirm('Vil du slette denne bestilling?')">
                       Slet
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    </div>
 <!-- Højre tom kolonne -->
        <div class="col-12 col-md-2"></div>            
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
