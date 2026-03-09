<?php
require_once __DIR__ . "/../includes/config.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plads = trim($_POST['plads']);

if ($plads !== '') {
    try {
        $stmt = $pdo->prepare("INSERT INTO plads (plads) VALUES (:p)");
        $stmt->execute([':p' => $plads]);
        $message = "Ny placering oprettet.";
    } catch (PDOException $e) {
        $message = "DB-fejl: " . $e->getMessage();
    }
} else {
    $message = "Placering må ikke være tom.";
}

}

ob_start();
?>

<div class="container mt-4">
    <h3>Opret ny placering</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Placering</label>
        <input type="text" name="plads" class="form-control mb-3" required autofocus>

        <button class="btn btn-primary w-100">Opret</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../includes/layout.php";
