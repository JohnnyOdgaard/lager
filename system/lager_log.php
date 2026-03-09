<?php
require_once __DIR__ . "/../includes/config.php";

$stmt = $pdo->query("
    SELECT id, handling, varenavn, kobsdato, maengde, enhed, aktionsdato
    FROM lager_log
    ORDER BY aktionsdato DESC
    LIMIT 500
");
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Lager log</h3>

    <p class="text-muted">Viser de seneste 500 logposter for indkøb og hentning.</p>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Handling</th>
                <th>Varenavn</th>
                <th>Kobsdato</th>
                <th>Maengde</th>
                <th>Enhed</th>
                <th>Aktionsdato</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td data-label="ID"><?= (int)$r['id'] ?></td>
                <td data-label="Handling"><?= htmlspecialchars($r['handling']) ?></td>
                <td data-label="Varenavn"><?= htmlspecialchars($r['varenavn']) ?></td>
                <td data-label="Kobsdato"><?= htmlspecialchars((string)$r['kobsdato']) ?></td>
                <td data-label="Maengde"><?= htmlspecialchars((string)$r['maengde']) ?></td>
                <td data-label="Enhed"><?= htmlspecialchars((string)$r['enhed']) ?></td>
                <td data-label="Aktionsdato"><?= htmlspecialchars($r['aktionsdato']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../includes/layout.php";
