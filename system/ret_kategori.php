<?php
require_once __DIR__ . "/../includes/config.php";

$message = "";

// Hent alle kategorier
$kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();

//
// OMDØB KATEGORI
//
if (isset($_POST['rename_id'])) {
    $id = $_POST['rename_id'];
    $nyt_navn = trim($_POST['nyt_navn']);

    if ($nyt_navn !== '') {
        $stmt = $pdo->prepare("UPDATE kategori SET kategori = :k WHERE id = :id");
        $stmt->execute([':k' => $nyt_navn, ':id' => $id]);
        $message = "Kategori omdøbt.";
    }
}

//
// FLET KATEGORI
//
if (isset($_POST['merge_from']) && isset($_POST['merge_to'])) {
    $fra = $_POST['merge_from'];
    $til = $_POST['merge_to'];

    if ($fra != $til) {

        // Hvor mange varer påvirkes?
        $count = $pdo->prepare("SELECT COUNT(*) FROM lagerbeholdning WHERE typ_id = :id");
        $count->execute([':id' => $fra]);
        $antal = $count->fetchColumn();

        // Flyt varer
        $update = $pdo->prepare("UPDATE lagerbeholdning SET typ_id = :ny WHERE typ_id = :gammel");
        $update->execute([':ny' => $til, ':gammel' => $fra]);

        // Slet gammel kategori
        $del = $pdo->prepare("DELETE FROM kategori WHERE id = :id");
        $del->execute([':id' => $fra]);

        $message = "$antal varer flyttet. Kategori flettet og slettet.";
    }
}

ob_start();
?>

<div class="container mt-4">
    <h3>Ret / flet kategori</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <h5>Omdøb kategori</h5>
    <form method="POST" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="rename_id" class="form-control" required>
                <?php foreach ($kategorier as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= $k['kategori'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <input type="text" name="nyt_navn" class="form-control" placeholder="Nyt navn" required>
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary w-100">Omdøb</button>
        </div>
    </form>

    <h5>Flet kategori ind i en anden</h5>
    <form method="POST" class="row g-2">
        <div class="col-md-4">
            <select name="merge_from" class="form-control" required>
                <?php foreach ($kategorier as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= $k['kategori'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <select name="merge_to" class="form-control" required>
                <?php foreach ($kategorier as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= $k['kategori'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <button class="btn btn-danger w-100">Flet og slet</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../includes/layout.php";
