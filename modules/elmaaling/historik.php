<?php
require_once __DIR__ . "/../../includes/config.php";

// 1) Find alle år
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

// 2) Valgt år
$year = $_GET['year'] ?? date('Y');

// 3) Hent målinger for året
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

ob_start();
?>

<div class="container mt-4">
    <h3>EL‑MÅLINGER – <?= htmlspecialchars($year) ?></h3>

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
            <th>Dato</th>
            <th>El</th>
            <th>Solceller</th>
            <th>Forbrug</th>
            <th>Produceret</th>
        </tr>

        <?php
        $prev_el = null;
        $prev_sol = null;

        foreach ($rows as $r):
            $forbrug = ($prev_el !== null) ? $r['el'] - $prev_el : 0;
            $prod    = ($prev_sol !== null) ? $r['solcelle'] - $prev_sol : 0;

            $prev_el  = $r['el'];
            $prev_sol = $r['solcelle'];
        ?>
            <tr>
                <td><?= $r['dato'] ?></td>
                <td align="center"><?= $r['el'] ?></td>
                <td align="center"><?= $r['solcelle'] ?></td>
                <td align="right"><?= $forbrug ?></td>
                <td align="right"><?= $prod ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
