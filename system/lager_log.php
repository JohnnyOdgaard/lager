<?php
require_once __DIR__ . "/../includes/config.php";

// Hent filtreringskriterier fra GET-parametre
$filter_handling = isset($_GET['handling']) && $_GET['handling'] !== '' ? $_GET['handling'] : null;
$filter_varenavn = isset($_GET['varenavn']) && $_GET['varenavn'] !== '' ? $_GET['varenavn'] : null;

// Hent distinkte værdier til handling-filter
$handlinger = $pdo->query("SELECT DISTINCT handling FROM lager_log ORDER BY handling")->fetchAll(PDO::FETCH_COLUMN);

// Byg SQL-forespørgsel med filtre
$sql = "SELECT id, handling, varenavn, kobsdato, maengde, enhed, aktionsdato
        FROM lager_log
        WHERE 1=1";
$params = [];

if ($filter_handling) {
    $sql .= " AND handling = :handling";
    $params[':handling'] = $filter_handling;
}

if ($filter_varenavn) {
    $sql .= " AND varenavn LIKE :varenavn";
    $params[':varenavn'] = '%' . $filter_varenavn . '%';
}

$sql .= " ORDER BY aktionsdato DESC LIMIT 500";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Beregn statistik
$stats_sql = "SELECT varenavn, handling, 
              COUNT(*) as antal_transaktioner, 
              SUM(maengde) as total_maengde,
              enhed,
              MIN(aktionsdato) as forste_transaktion,
              MAX(aktionsdato) as seneste_transaktion
              FROM lager_log
              WHERE 1=1";
$stats_params = [];

if ($filter_handling) {
    $stats_sql .= " AND handling = :handling";
    $stats_params[':handling'] = $filter_handling;
}

if ($filter_varenavn) {
    $stats_sql .= " AND varenavn LIKE :varenavn";
    $stats_params[':varenavn'] = '%' . $filter_varenavn . '%';
}

$stats_sql .= " GROUP BY varenavn, handling, enhed ORDER BY total_maengde DESC";

$stats_stmt = $pdo->prepare($stats_sql);
$stats_stmt->execute($stats_params);
$stats = $stats_stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Lager Log</h3>

    <p class="text-muted">Log over indkøb og hentning med filtrerings- og statistikmuligheder.</p>

    <!-- Filtreringsformular -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtrer log</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="handling" class="form-label">Handling</label>
                        <select name="handling" id="handling" class="form-select">
                            <option value="">-- Alle --</option>
                            <?php foreach ($handlinger as $h): ?>
                                <option value="<?= htmlspecialchars($h) ?>" <?= $filter_handling === $h ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($h) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="varenavn" class="form-label">Varenavn (søg)</label>
                        <input type="text" name="varenavn" id="varenavn" class="form-control" 
                               value="<?= htmlspecialchars($filter_varenavn ?? '') ?>"
                               placeholder="Skriv del af varenavn...">
                        <small class="form-text text-muted">Søger efter varer der indeholder teksten</small>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </div>
                <?php if ($filter_handling || $filter_varenavn): ?>
                    <div class="mt-2">
                        <a href="?" class="btn btn-secondary btn-sm">Nulstil filtre</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Statistik -->
    <?php if (!empty($stats)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Statistik</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Varenavn</th>
                            <th>Handling</th>
                            <th>Antal transaktioner</th>
                            <th>Total mængde</th>
                            <th>Enhed</th>
                            <th>Første transaktion</th>
                            <th>Seneste transaktion</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['varenavn']) ?></td>
                            <td><?= htmlspecialchars($s['handling']) ?></td>
                            <td><?= (int)$s['antal_transaktioner'] ?></td>
                            <td><?= number_format($s['total_maengde'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars((string)$s['enhed']) ?></td>
                            <td><?= htmlspecialchars($s['forste_transaktion']) ?></td>
                            <td><?= htmlspecialchars($s['seneste_transaktion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Log detaljer -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Log detaljer (seneste 500)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Antal poster: <?= count($rows) ?></p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Handling</th>
                            <th>Varenavn</th>
                            <th>Købsdato</th>
                            <th>Mængde</th>
                            <th>Enhed</th>
                            <th>Aktionsdato</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= (int)$r['id'] ?></td>
                            <td><?= htmlspecialchars($r['handling']) ?></td>
                            <td><?= htmlspecialchars($r['varenavn']) ?></td>
                            <td><?= htmlspecialchars((string)$r['kobsdato']) ?></td>
                            <td><?= htmlspecialchars((string)$r['maengde']) ?></td>
                            <td><?= htmlspecialchars((string)$r['enhed']) ?></td>
                            <td><?= htmlspecialchars($r['aktionsdato']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../includes/layout.php";
