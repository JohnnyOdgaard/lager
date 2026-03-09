<?php
require_once __DIR__ . "/../../includes/config.php";

// Hvis formularen er sendt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dato      = $_POST['dato'];
    $klokken   = $_POST['klokken'];
    $el        = (int)$_POST['el'] + 92527;   // KORRIGERING
    $solcelle  = (int)$_POST['solcelle'];

    // Gem i databasen
    $stmt = $pdo->prepare("
        INSERT INTO elmaaler (dato, klokken, el, solcelle)
        VALUES (:dato, :klokken, :el, :solcelle)
    ");

    $stmt->execute([
        ':dato'      => $dato,
        ':klokken'   => $klokken,
        ':el'        => $el,
        ':solcelle'  => $solcelle
    ]);

    // Redirect for at undgå genindsendelse
    //header("Location: aflaes.php?ok=1");
    header("Location: historik.php");
    exit;
}

ob_start();
?>

<div class="container mt-4">
    <h3>Aflæsning af elmåler</h3>

    <?php if (!empty($_GET['ok'])): ?>
        <div class="alert alert-success">Aflæsning gemt.</div>
    <?php endif; ?>

    <form method="POST">

        <table class="table table-bordered table-sm">

            <tr>
                <th>Dato</th>
                <td>
                    <input type="date" name="dato" class="form-control"
                           value="<?= date('Y-m-d') ?>">
                </td>
            </tr>

            <tr>
                <th>Klokken</th>
                <td>
                    <input type="time" name="klokken" class="form-control"
                           value="<?= date('H:i:s') ?>">
                </td>
            </tr>

            <tr>
                <th>Elmåler</th>
                <td>
                    <input type="number" name="el" class="form-control" required autofocus>
                    <small class="text-muted">Der lægges automatisk +92.527 til</small>
                </td>
            </tr>

            <tr>
                <th>Solceller</th>
                <td>
                    <input type="number" name="solcelle" class="form-control" required>
                </td>
            </tr>

        </table>

        <button class="btn btn-primary">Gem aflæsning</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
