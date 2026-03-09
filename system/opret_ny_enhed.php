<?php
require_once __DIR__ . "/../includes/config.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enhed = trim($_POST['enhed']);

    if ($enhed !== '') {
        $stmt = $pdo->prepare("INSERT INTO enheder (enhed) VALUES (:e)");
        $stmt->execute([':e' => $enhed]);
        $message = "Ny enhed oprettet.";
    } else {
        $message = "Enhed må ikke være tom.";
    }
}

ob_start();
?>

<div class="container mt-4">
    <h3>Opret ny enhed</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Enhed</label>
        <input type="text" name="enhed" class="form-control mb-3" required autofocus>

        <button class="btn btn-primary w-100">Opret</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../includes/layout.php";
