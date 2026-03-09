<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_GET['id'] ?? 0;

// Hent varen
$stmt = $pdo->prepare("
    SELECT * FROM lagerbeholdning WHERE id = :id
");
$stmt->execute([':id' => $id]);
$vare = $stmt->fetch();

if (!$vare) {
    die("Varen findes ikke.");
}

// Hent dropdowns
$kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();
$enheder = $pdo->query("SELECT id, enhed FROM enheder ORDER BY enhed")->fetchAll();
$pladser = $pdo->query("SELECT id, plads FROM plads ORDER BY plads")->fetchAll();

// Gem ændringer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        UPDATE lagerbeholdning
        SET varenavn = :varenavn,
            kobsdato = :kobsdato,
            sidstedato = :sidstedato,
            maengde = :maengde,
            pris = :pris,
            pla_id = :pla_id,
            typ_id = :typ_id,
            enh_id = :enh_id
        WHERE id = :id
    ");

    $stmt->execute([
        ':varenavn' => $_POST['varenavn'],
        ':kobsdato' => $_POST['kobsdato'],
        ':sidstedato' => $_POST['sidstedato'],
        ':maengde' => $_POST['maengde'],
        ':pris' => $_POST['pris'],
        ':pla_id' => $_POST['pla_id'],
        ':typ_id' => $_POST['typ_id'],
        ':enh_id' => $_POST['enh_id'],
        ':id' => $id
    ]);

    header("Location: ../soeg/vareliste.php");
    exit;
}

ob_start();
?>

<div class="container mt-4">
    <h3>Ret indkøb</h3>

    <form method="POST" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Varenavn</label>
            <input type="text" name="varenavn" class="form-control" value="<?= $vare['varenavn'] ?>" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Købsdato</label>
            <input type="text" name="kobsdato" class="form-control" value="<?= $vare['kobsdato'] ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Sidste dato</label>
            <input type="text" name="sidstedato" class="form-control" value="<?= $vare['sidstedato'] ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Mængde</label>
            <input type="text" name="maengde" class="form-control" value="<?= $vare['maengde'] ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Pris</label>
            <input type="text" name="pris" class="form-control" value="<?= $vare['pris'] ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Kategori</label>
            <select name="typ_id" class="form-select">
                <?php foreach ($kategorier as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $vare['typ_id'] ? 'selected' : '' ?>>
                        <?= $k['kategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Enhed</label>
            <select name="enh_id" class="form-select">
                <?php foreach ($enheder as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= $e['id'] == $vare['enh_id'] ? 'selected' : '' ?>>
                        <?= $e['enhed'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Placering</label>
            <select name="pla_id" class="form-select">
                <?php foreach ($pladser as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $vare['pla_id'] ? 'selected' : '' ?>>
                        <?= $p['plads'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary">Gem ændringer</button>
            <a href="../soeg/vareliste.php" class="btn btn-secondary">Tilbage</a>
        </div>

    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
