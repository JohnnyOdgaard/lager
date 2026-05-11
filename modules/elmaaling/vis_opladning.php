<?php
require_once __DIR__ . "/../../includes/config.php";

// Hent alle opladninger
$stmt = $pdo->query("
    SELECT * FROM opladning
    ORDER BY id DESC
");
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Opladnings-aflæsninger</h3>

    <div class="mb-3">
        <a href="aflaes_opladning.php" class="btn btn-primary">Ny aflæsning</a>
        <a href="ret_opladning.php" class="btn btn-secondary">Ret aflæsning</a>
    </div>

    <?php if (empty($rows)): ?>
        <div class="alert alert-info">Ingen aflæsninger fundet.</div>
    <?php else: ?>
        <table class="table table-bordered table-sm table-mobile">
            <thead>
                <tr>
                    <th>Dato</th>
                    <th>Opladningsgrad %</th>
                    <th>Km-stand</th>
                    <th>KwH</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td data-label="Dato"><?= htmlspecialchars($r['dato']) ?></td>
                        <td data-label="Opladningsgrad %" align="center"><?= htmlspecialchars($r['opladningsgrad']) ?>%</td>
                        <td data-label="Km-stand" align="right"><?= number_format($r['km_stand'], 0, ',', '.') ?></td>
                        <td data-label="KwH" align="right"><?= number_format($r['kwh'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
