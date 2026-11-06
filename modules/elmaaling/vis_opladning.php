<?php
require_once __DIR__ . "/../../includes/config.php";

// Hent alle opladninger, nyeste dato øverst
$stmt = $pdo->query("
    SELECT * FROM opladning
    ORDER BY 
        CASE 
            WHEN dato LIKE '__-__-____' THEN STR_TO_DATE(dato, '%d-%m-%Y')
            ELSE STR_TO_DATE(dato, '%Y-%m-%d')
        END DESC,
        id DESC
");
$rows = $stmt->fetchAll();

// CSV-eksport
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="opladninger_' . date('Y-m-d') . '.csv"');

    $out = fopen('php://output', 'w');
    // UTF-8 BOM så Excel viser æøå korrekt
    fwrite($out, "\xEF\xBB\xBF");

    fputcsv($out, ['Dato', 'Opladningsgrad %', 'Km-stand', 'Opladt kWh', 'Fra kWh', 'Opladning fra'], ';');

    foreach ($rows as $r) {
        fputcsv($out, [
            $r['dato'],
            $r['opladningsgrad'],
            $r['km_stand'],
            number_format($r['kwh'], 2, ',', ''),
            !empty($r['fra_kwh']) ? number_format($r['fra_kwh'], 2, ',', '') : '',
            $r['opladning_fra'] ?? ''
        ], ';');
    }

    fclose($out);
    exit;
}

ob_start();
?>

<div class="container mt-4">
    <h3>Opladnings-aflæsninger</h3>

    <div class="mb-3">
        <a href="aflaes_opladning.php" class="btn btn-primary">Ny aflæsning</a>
        <a href="ret_opladning.php" class="btn btn-secondary">Ret aflæsning</a>
        <a href="vis_opladning.php?export=csv" class="btn btn-success">Eksportér CSV</a>
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
                    <th>Opladt kWh</th>
                    <th>Fra kWh</th>
                    <th>Opladning fra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td data-label="Dato"><?= htmlspecialchars($r['dato']) ?></td>
                        <td data-label="Opladningsgrad %" align="center"><?= htmlspecialchars($r['opladningsgrad']) ?>%</td>
                        <td data-label="Km-stand" align="right"><?= number_format($r['km_stand'], 0, ',', '.') ?></td>
                        <td data-label="Opladt kWh" align="right"><?= number_format($r['kwh'], 2, ',', '.') ?></td>
                        <td data-label="Fra kWh" align="right"><?= !empty($r['fra_kwh']) ? number_format($r['fra_kwh'], 2, ',', '.') : '-' ?></td>
                        <td data-label="Opladning fra"><?= htmlspecialchars($r['opladning_fra'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
