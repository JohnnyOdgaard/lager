<?php
require_once __DIR__ . "/../includes/config.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = trim($_POST['kategori']);

    if ($kategori !== '') {
        $stmt = $pdo->prepare("INSERT INTO kategori (kategori) VALUES (:k)");
        $stmt->execute([':k' => $kategori]);
        $message = "Ny kategori oprettet.";
    } else {
        $message = "Kategori må ikke være tom.";
    }
}

ob_start();
?>

<div class="container mt-4">
    <h3>Opret ny kategori</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Kategori</label>
        <input type="text" name="kategori" class="form-control mb-3" required autofocus>

        <button class="btn btn-primary w-100">Opret</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../includes/layout.php";
