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

// Beregn månedligt forbrug og produktion
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

    $prev_el  = $r['el'];
    $prev_sol = $r['solcelle'];
    $prev_month = $month;
}

ob_start();
?>

<div class="container mt-4">
    <h3>Solcelle‑effekt – <?= $year ?></h3>

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

    <table class="table table-bordered table-sm">
        <tr>
            <th>Måned</th>
            <th>Forbrug (kWh)</th>
            <th>Produceret (kWh)</th>
            <th>Dækningsgrad</th>
        </tr>

        <?php
        $maanedsnavne = [
            1=>"Januar",2=>"Februar",3=>"Marts",4=>"April",5=>"Maj",6=>"Juni",
            7=>"Juli",8=>"August",9=>"September",10=>"Oktober",11=>"November",12=>"December"
        ];

        $total_forbrug = 0;
        $total_prod = 0;

        foreach ($maaneder as $m => $data):
            $forbrug = $data['forbrug'];
            $prod = $data['produktion'];

            $total_forbrug += $forbrug;
            $total_prod += $prod;

            $effekt = ($forbrug > 0) ? round(($prod / $forbrug) * 100, 1) : 0;
        ?>
            <tr>
                <td><?= $maanedsnavne[$m] ?></td>
                <td align="right"><?= $forbrug ?></td>
                <td align="right"><?= $prod ?></td>
                <td align="right"><?= $effekt ?>%</td>
            </tr>
        <?php endforeach; ?>

        <tr class="table-secondary fw-bold">
            <td>Total</td>
            <td align="right"><?= $total_forbrug ?></td>
            <td align="right"><?= $total_prod ?></td>
            <td align="right">
                <?= ($total_forbrug > 0) ? round(($total_prod / $total_forbrug) * 100, 1) : 0 ?>%
            </td>
        </tr>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
