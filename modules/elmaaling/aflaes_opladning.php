<?php
require_once __DIR__ . "/../../includes/config.php";

// Hvis formularen er sendt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Konverter dato fra YYYY-MM-DD til dd-mm-åååå
    $dato_input      = $_POST['dato'];
    $dato            = date('d-m-Y', strtotime($dato_input));
    
    $opladningsgrad  = (int)$_POST['opladningsgrad'];
    $km_stand        = (int)$_POST['km_stand'];
    $kwh             = (float)$_POST['kwh'];
    $fra_kwh         = !empty($_POST['fra_kwh']) ? (float)$_POST['fra_kwh'] : null;
    $opladning_fra   = trim($_POST['opladning_fra']);

    // Gem i databasen
    $stmt = $pdo->prepare("
        INSERT INTO opladning (dato, opladningsgrad, km_stand, kwh, fra_kwh, opladning_fra)
        VALUES (:dato, :opladningsgrad, :km_stand, :kwh, :fra_kwh, :opladning_fra)
    ");

    $stmt->execute([
        ':dato'           => $dato,
        ':opladningsgrad' => $opladningsgrad,
        ':km_stand'       => $km_stand,
        ':kwh'            => $kwh,
        ':fra_kwh'        => $fra_kwh,
        ':opladning_fra'  => $opladning_fra
    ]);

    // Redirect for at undgå genindsendelse
    header("Location: vis_opladning.php");
    exit;
}

ob_start();
?>

<div class="container mt-4">
    <h3>Aflæsning af opladning</h3>

    <?php if (!empty($_GET['ok'])): ?>
        <div class="alert alert-success">Aflæsning gemt.</div>
    <?php endif; ?>

    <form method="POST">

        <table class="table table-bordered table-sm">

            <tr>
                <th>Dato</th>
                <td>
                    <input type="date" name="dato" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                    <small class="text-muted">Vælg dato fra kalender</small>
                </td>
            </tr>

            <tr>
                <th>Opladningsgrad %</th>
                <td>
                    <input type="number" name="opladningsgrad" class="form-control" 
                           min="0" max="100" required autofocus>
                </td>
            </tr>

            <tr>
                <th>Km-stand</th>
                <td>
                    <input type="number" name="km_stand" class="form-control" required>
                </td>
            </tr>

            <tr>
                <th>Opladt kWh</th>
                <td>
                    <input type="number" name="kwh" class="form-control" 
                           step="0.01" required>
                    <small class="text-muted">Hvor mange kWh blev der opladt</small>
                </td>
            </tr>

            <tr>
                <th>Fra kWh</th>
                <td>
                    <input type="number" name="fra_kwh" class="form-control" 
                           step="0.01" placeholder="0.00">
                    <small class="text-muted">Hvor mange kWh var der før opladning (valgfrit)</small>
                </td>
            </tr>

            <tr>
                <th>Opladning fra</th>
                <td>
                    <input type="text" name="opladning_fra" class="form-control" 
                           placeholder="F.eks. Hjemme, Arbejde, Offentlig">
                    <small class="text-muted">Valgfrit felt</small>
                </td>
            </tr>

        </table>

        <button class="btn btn-primary">Gem aflæsning</button>
        <a href="vis_opladning.php" class="btn btn-secondary">Vis aflæsninger</a>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
