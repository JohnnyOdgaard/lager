<?php
require_once __DIR__ . "/../../includes/config.php";

//
// HENT LOGBOG
//
$stmt = $pdo->query("
    SELECT l.id, l.vare_id, l.handling, l.gammel_vaerdi, l.ny_vaerdi, l.tidspunkt,
           b.varenavn
    FROM logbog l
    LEFT JOIN lagerbeholdning b ON l.vare_id = b.id
    ORDER BY l.tidspunkt DESC
    LIMIT 500
");
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Logbog / historik</h3>

    <p class="text-muted">Viser de seneste 500 ændringer.</p>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vare</th>
                <th>Handling</th>
                <th>Gammel værdi</th>
                <th>Ny værdi</th>
                <th>Tidspunkt</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td data-label="ID"><?= $r['id'] ?></td>
                <td data-label="Vare">
                    <?= $r['varenavn'] ?: '(Slettet vare)' ?>
                </td>
                <td data-label="Handling"><?= $r['handling'] ?></td>
                <td data-label="Gammel værdi"><?= nl2br(htmlspecialchars($r['gammel_vaerdi'])) ?></td>
                <td data-label="Ny værdi"><?= nl2br(htmlspecialchars($r['ny_vaerdi'])) ?></td>
                <td data-label="Tidspunkt"><?= $r['tidspunkt'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
