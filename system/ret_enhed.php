<?php
require_once __DIR__ . "/../includes/config.php";

$message = "";

$enheder = $pdo->query("SELECT id, enhed FROM enheder ORDER BY enhed")->fetchAll();

//
// OMDØB ENHED
//
if (isset($_POST['rename_id'])) {
    $id = $_POST['rename_id'];
    $nyt_navn = trim($_POST['nyt_navn']);

    if ($nyt_navn !== '') {
        $stmt = $pdo->prepare("UPDATE enheder SET enhed = :e WHERE id = :id");
        $stmt->execute([':e' => $nyt_navn, ':id' => $id]);
        $message = "Enhed omdøbt.";
    }
}

//
// FLET ENHED
//
if (isset($_POST['merge_from']) && isset($_POST['merge_to'])) {
    $fra = $_POST['merge_from'];
    $til = $_POST['merge_to'];

    if ($fra != $til) {

        $count = $pdo->prepare("SELECT COUNT(*) FROM lagerbeholdning WHERE enh_id = :id");
        $count->execute([':id' => $fra]);
        $antal = $count->fetchColumn();

        $update = $pdo->prepare("UPDATE lagerbeholdning SET enh_id = :ny WHERE enh_id = :gammel");
        $update->execute([':ny' => $til, ':gammel' => $fra]);

        $del = $pdo->prepare("DELETE FROM enheder WHERE id = :id");
        $del->execute([':id' => $fra]);

        $message = "$antal varer flyttet. Enhed flettet og slettet.";
    }
}

ob_start();
?>

<div class="container mt-4">
    <h3>Ret / flet enhed</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <h5>Omdøb enhed</h5>
    <form method="POST" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="rename_id" class="form-control" required>
                <?php foreach ($enheder as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= $e['enhed'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <input type="text" name="nyt_navn" class="form-control" placeholder="Nyt navn" required autofocus>
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary w-100">Omdøb</button>
        </div>
    </form>

    <h5>Flet enhed ind i en anden</h5>
    <form method="POST" class="row g-2">
        <div class="col-md-4">
            <select name="merge_from" class="form-control" required>
                <?php foreach ($enheder as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= $e['enhed'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <select name="merge_to" class="form-control" required>
                <?php foreach ($enheder as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= $e['enhed'] ?></option>
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
