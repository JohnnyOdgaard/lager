<?php
require_once __DIR__ . "/../../includes/config.php";

// Hvis formularen er sendt (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {

    $id              = (int)$_POST['id'];
    $opladningsgrad  = (int)$_POST['opladningsgrad'];
    $km_stand        = (int)$_POST['km_stand'];
    $kwh             = (float)$_POST['kwh'];
    $fra_kwh         = !empty($_POST['fra_kwh']) ? (float)$_POST['fra_kwh'] : null;
    $opladning_fra   = trim($_POST['opladning_fra']);

    // Opdater i databasen (dato kan ikke ændres)
    $stmt = $pdo->prepare("
        UPDATE opladning 
        SET opladningsgrad = :opladningsgrad,
            km_stand = :km_stand,
            kwh = :kwh,
            fra_kwh = :fra_kwh,
            opladning_fra = :opladning_fra
        WHERE id = :id
    ");

    $stmt->execute([
        ':id'             => $id,
        ':opladningsgrad' => $opladningsgrad,
        ':km_stand'       => $km_stand,
        ':kwh'            => $kwh,
        ':fra_kwh'        => $fra_kwh,
        ':opladning_fra'  => $opladning_fra
    ]);

    // Redirect
    header("Location: vis_opladning.php");
    exit;
}

// Hent alle opladninger for vælger
$stmt = $pdo->query("
    SELECT * FROM opladning
    ORDER BY id DESC
");
$rows = $stmt->fetchAll();

// Hvis en opladning er valgt
$selected = null;
if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM opladning WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['id']]);
    $selected = $stmt->fetch();
}

ob_start();
?>

<div class="container mt-4">
    <h3>Ret opladnings-aflæsning</h3>

    <form method="GET" class="mb-4">
        <label for="id">Vælg en aflæsning at rette:</label>
        <select name="id" id="id" class="form-select" style="width:300px; display:inline-block;">
            <option value="">-- Vælg aflæsning --</option>
            <?php foreach ($rows as $r): ?>
                <option value="<?= $r['id'] ?>" <?= (!empty($_GET['id']) && $_GET['id'] == $r['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['dato']) ?> - <?= $r['opladningsgrad'] ?>% - <?= number_format($r['km_stand'], 0, ',', '.') ?> km - Opladt: <?= number_format($r['kwh'], 2, ',', '.') ?> kWh<?= !empty($r['fra_kwh']) ? ' (Fra: ' . number_format($r['fra_kwh'], 2, ',', '.') . ')' : '' ?> - <?= htmlspecialchars($r['opladning_fra'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-sm">Vælg</button>
    </form>

    <?php if ($selected): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $selected['id'] ?>">

            <table class="table table-bordered table-sm">

                <tr>
                    <th>Dato</th>
                    <td>
                        <input type="text" class="form-control" 
                               value="<?= htmlspecialchars($selected['dato']) ?>" disabled>
                        <small class="text-muted">Dato kan ikke ændres</small>
                    </td>
                </tr>

                <tr>
                    <th>Opladningsgrad %</th>
                    <td>
                        <input type="number" name="opladningsgrad" class="form-control" 
                               min="0" max="100" value="<?= $selected['opladningsgrad'] ?>" required autofocus>
                    </td>
                </tr>

                <tr>
                    <th>Km-stand</th>
                    <td>
                        <input type="number" name="km_stand" class="form-control" 
                               value="<?= $selected['km_stand'] ?>" required>
                    </td>
                </tr>

                <tr>
                    <th>Opladt kWh</th>
                    <td>
                        <input type="number" name="kwh" class="form-control" 
                               step="0.01" value="<?= number_format($selected['kwh'], 2, '.', '') ?>" required>
                        <small class="text-muted">Hvor mange kWh blev der opladt</small>
                    </td>
                </tr>

                <tr>
                    <th>Fra kWh</th>
                    <td>
                        <input type="number" name="fra_kwh" class="form-control" 
                               step="0.01" value="<?= !empty($selected['fra_kwh']) ? number_format($selected['fra_kwh'], 2, '.', '') : '' ?>" 
                               placeholder="0.00">
                        <small class="text-muted">Hvor mange kWh var der før opladning (valgfrit)</small>
                    </td>
                </tr>

                <tr>
                    <th>Opladning fra</th>
                    <td>
                        <input type="text" name="opladning_fra" class="form-control" 
                               value="<?= htmlspecialchars($selected['opladning_fra'] ?? '') ?>" 
                               placeholder="F.eks. Hjemme, Arbejde, Offentlig">
                        <small class="text-muted">Valgfrit felt</small>
                    </td>
                </tr>

            </table>

            <button class="btn btn-primary">Gem ændringer</button>
            <a href="vis_opladning.php" class="btn btn-secondary">Annuller</a>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
