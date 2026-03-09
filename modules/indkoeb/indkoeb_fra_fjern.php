<?php
require_once __DIR__ . "/../../includes/config.php";
session_start();

// 1) Modtag parametre
$varid = $_GET['varid'] ?? null;

if (!$varid) {
    echo "Mangler varid";
    exit;
}

// 2) Hent data fra Lagerbeholdning
$stmt = $pdo->prepare("SELECT * FROM lagerbeholdning WHERE id = :id");
$stmt->execute([':id' => $varid]);
$vare = $stmt->fetch();

if (!$vare) {
    echo "Varen findes ikke i lagerbeholdning";
    exit;
}

// 3) Hvis formularen er sendt → opdater og redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Opdater lagerbeholdning
    $sql = "UPDATE lagerbeholdning SET
                varenavn = :varenavn,
                kobsdato = :kobsdato,
                sidstedato = :sidstedato,
                maengde = :maengde,
                pris = :pris,
                enh_id = :enh_id,
                typ_id = :typ_id,
                pla_id = :pla_id
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':varenavn'   => $_POST['varenavn'],
        ':kobsdato'   => $_POST['kobsdato'],
        ':sidstedato' => $_POST['sidstedato'],
        ':maengde'    => $_POST['maengde'],
        ':pris'       => $_POST['pris'],
        ':enh_id'     => $_POST['enh_id'],
        ':typ_id'     => $_POST['typ_id'],
        ':pla_id'     => $_POST['pla_id'],
        ':id'         => $varid
    ]);

    $enhedStmt = $pdo->prepare("SELECT enhed FROM enheder WHERE id = :id");
    $enhedStmt->execute([':id' => $_POST['enh_id']]);
    $enhed = $enhedStmt->fetchColumn() ?: null;

    $logStmt = $pdo->prepare("INSERT INTO lager_log (handling, varenavn, kobsdato, maengde, enhed, aktionsdato)
                              VALUES (:handling, :varenavn, :kobsdato, :maengde, :enhed, NOW())");
    $logStmt->execute([
        ':handling' => 'indkoeb',
        ':varenavn' => $_POST['varenavn'],
        ':kobsdato' => $_POST['kobsdato'] ?: null,
        ':maengde' => $_POST['maengde'] ?: null,
        ':enhed' => $enhed
    ]);

    // Slet fra bestil-tabellen
    $del = $pdo->prepare("DELETE FROM bestil WHERE beholdID = :id");
    $del->execute([':id' => $varid]);
    
    // Tilføj varen til listen over overførte varer
    $_SESSION['overfoert'][] = $vare['varenavn'];


    // Tjek om der er flere varer markeret som købt
    $check = $pdo->query("SELECT COUNT(*) FROM bestil WHERE bestilt = 'Ja'")->fetchColumn();

    if ($check == 0) {
        header("Location: /lager/modules/indkoeb/overfoert.php");
        exit;
    }

    header("Location: /lager/modules/bestilling/bestil_update.php");
    exit;
}

// 5) Hent dropdown-data
$enheder = $pdo->query("SELECT id, enhed FROM enheder ORDER BY enhed")->fetchAll();
$kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();
$pladser = $pdo->query("SELECT id, plads FROM plads ORDER BY plads")->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Overfør vare til lager</h3>

    <form method="POST">

        <table class="table table-bordered table-sm">
            <tr>
                <th>Varenavn</th>
                <td><input type="text" name="varenavn" class="form-control" value="<?= $vare['varenavn'] ?>"></td>
            </tr>

            <tr>
                <th>Købt den</th>
                <td><input type="date" name="kobsdato" class="form-control" value="<?= $vare['kobsdato'] ?>"></td>
            </tr>

            <tr>
                <th>Bedst før</th>
                <td><input type="date" name="sidstedato" class="form-control" value="<?= $vare['sidstedato'] ?>"></td>
            </tr>

            <tr>
                <th>Mængde</th>
                <td><input type="number" step="0.01" name="maengde" class="form-control" value="<?= $vare['maengde'] ?>"></td>
            </tr>

            <tr>
                <th>Enhed</th>
                <td>
                    <select name="enh_id" class="form-select">
                        <?php foreach ($enheder as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $e['id'] == $vare['enh_id'] ? 'selected' : '' ?>>
                                <?= $e['enhed'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>Kategori</th>
                <td>
                    <select name="typ_id" class="form-select">
                        <?php foreach ($kategorier as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= $k['id'] == $vare['typ_id'] ? 'selected' : '' ?>>
                                <?= $k['kategori'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>Pris</th>
                <td><input type="number" step="0.01" name="pris" class="form-control" value="<?= $vare['pris'] ?>"></td>
            </tr>

            <tr>
                <th>Placering</th>
                <td>
                    <select name="pla_id" class="form-select">
                        <?php foreach ($pladser as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $vare['pla_id'] ? 'selected' : '' ?>>
                                <?= $p['plads'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <button class="btn btn-success">Gem og fortsæt</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
