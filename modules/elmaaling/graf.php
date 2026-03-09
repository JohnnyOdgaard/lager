<?php
require_once __DIR__ . "/../../includes/config.php";

// Find alle år
$years = $pdo->query("
    SELECT DISTINCT
        YEAR(
            CASE
                WHEN dato LIKE '__-__-____' THEN STR_TO_DATE(dato, '%d-%m-%Y')
                ELSE STR_TO_DATE(dato, '%Y-%m-%d')
            END
        ) AS aar
    FROM elmaaler
    ORDER BY aar DESC
")->fetchAll();

$year = $_GET['year'] ?? date('Y');

// Hent alle målinger for året
$stmt = $pdo->prepare("
    SELECT *,
        CASE
            WHEN dato LIKE '__-__-____' THEN STR_TO_DATE(dato, '%d-%m-%Y')
            ELSE STR_TO_DATE(dato, '%Y-%m-%d')
        END AS dato_sort
    FROM elmaaler
    WHERE YEAR(
        CASE
            WHEN dato LIKE '__-__-____' THEN STR_TO_DATE(dato, '%d-%m-%Y')
            ELSE STR_TO_DATE(dato, '%Y-%m-%d')
        END
    ) = :year
    ORDER BY dato_sort
");
$stmt->execute([':year' => $year]);
$rows = $stmt->fetchAll();

// Beregn månedligt forbrug
$maaneder = array_fill(1, 12, ['forbrug' => 0, 'produktion' => 0]);

$prev_el = null;
$prev_sol = null;
$prev_month = null;

foreach ($rows as $r) {
    $month = (int)date('n', strtotime($r['dato_sort']));

    if ($prev_el !== null && $prev_month === $month) {
        $maaneder[$month]['forbrug'] += $r['el'] - $prev_el;
        $maaneder[$month]['produktion'] += $r['solcelle'] - $prev_sol;
    }

    $prev_el = $r['el'];
    $prev_sol = $r['solcelle'];
    $prev_month = $month;
}

$forbrug_data = array_column($maaneder, 'forbrug');
$produktion_data = array_column($maaneder, 'produktion');

ob_start();
?>

<div class="container mt-4">
    <h3>Grafisk visning – <?= $year ?></h3>

    <form method="GET" class="mb-3">
        <label for="year">Vælg år:</label>
        <select name="year" id="year" class="form-select" style="width:150px; display:inline-block;">
            <?php foreach ($years as $y): ?>
                <option value="<?= $y['aar'] ?>" <?= $y['aar'] == $year ? 'selected' : '' ?>>
                    <?= $y['aar'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-sm">Vis</button>
    </form>

    <canvas id="forbrugChart" height="100"></canvas>
    <canvas id="produktionChart" height="100" class="mt-4"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const labels = ["Jan","Feb","Mar","Apr","Maj","Jun","Jul","Aug","Sep","Okt","Nov","Dec"];

new Chart(document.getElementById('forbrugChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'El-forbrug (kWh)',
            data: <?= json_encode($forbrug_data) ?>,
            borderColor: 'red',
            backgroundColor: 'rgba(255,0,0,0.2)',
            tension: 0.2
        }]
    }
});

new Chart(document.getElementById('produktionChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Solcelle-produktion (kWh)',
            data: <?= json_encode($produktion_data) ?>,
            borderColor: 'green',
            backgroundColor: 'rgba(0,255,0,0.2)',
            tension: 0.2
        }]
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
