<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../includes/config.php";

// Håndter indsendt formular
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $maengde = $_POST['maengde'] ?: null;
    $enhId = $_POST['enh_id'];

    $sql = "INSERT INTO lagerbeholdning 
            (varenavn, kobsdato, sidstedato, maengde, enh_id, typ_id, pris, pla_id)
            VALUES 
            (:varenavn, :kobsdato, :sidstedato, :maengde, :enh_id, :typ_id, :pris, :pla_id)";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':varenavn' => $_POST['varenavn'],
            ':kobsdato' => $_POST['kobsdato'],
            ':sidstedato' => $_POST['sidstedag'] ?: null,
            ':maengde'   => $maengde,
            ':enh_id'    => $enhId,
            ':typ_id'    => $_POST['typ_id'],
            ':pris'      => $_POST['pris']      ?: null,
            ':pla_id'    => $_POST['pla_id']
        ]);

        $enhedStmt = $pdo->prepare("SELECT enhed FROM enheder WHERE id = :id");
        $enhedStmt->execute([':id' => $enhId]);
        $enhed = $enhedStmt->fetchColumn() ?: null;

        $logStmt = $pdo->prepare("INSERT INTO lager_log (handling, varenavn, kobsdato, maengde, enhed, aktionsdato)
                                  VALUES (:handling, :varenavn, :kobsdato, :maengde, :enhed, NOW())");
        $logStmt->execute([
            ':handling' => 'indkoeb',
            ':varenavn' => $_POST['varenavn'],
            ':kobsdato' => $_POST['kobsdato'] ?: null,
            ':maengde' => $maengde,
            ':enhed' => $enhed
        ]);
    } catch (PDOException $e) {
        echo "FEJL: " . $e->getMessage();
    }
    header("Location: /lager/modules/indkoeb/indkoeb.php");
    exit;
}


// Hent dropdown-data
$enheder = $pdo->query("SELECT id, enhed FROM enheder ORDER BY enhed")->fetchAll();
$kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();
$pladser = $pdo->query("SELECT id, plads FROM plads ORDER BY plads")->fetchAll();

// Hent bestillingslisten
$bestil = $pdo->query("SELECT id, vare, bestilt FROM bestil ORDER BY vare ASC")->fetchAll();

ob_start();
?>


<div class="container my-4">
    <div class="row">

        <!-- VENSTRE SIDE -->
        <div class="col-sm-6">
            <h4>Indkøb</h4>
            <form action="" method="POST" name="frm_indkoeb">
                <table class="table table-sm table-bordered">
                    <tr>
                        <th>Hvad er der købt</th>
                        <td><input type="text" name="varenavn" class="form-control" required autofocus></td>
                    </tr>

                    <tr>
                        <th>Købt den</th>
                        <td><input type="date" name="kobsdato" class="form-control" value="<?= date('Y-m-d') ?>"></td>
                    </tr>

                    <tr>
                        <th>Bedst før</th>
                        <td><input type="date" name="sidstedag" class="form-control"></td>
                    </tr>

                    <tr>
                        <th>Mængde</th>
                        <td><input type="number" name="maengde" class="form-control" step="0.01"></td>
                    </tr>

                    <tr>
                        <th>Enhed</th>
                        <td>
                            <select name="enh_id" class="form-select">
                                <?php foreach ($enheder as $e): ?>
                                    <option value="<?= $e['id'] ?>"><?= $e['enhed'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Kategori</th>
                        <td>
                            <select name="typ_id" class="form-select">
                                <?php foreach ($kategorier as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= $k['kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Pris</th>
                        <td><input type="number" step="0.01" name="pris" class="form-control"></td>
                    </tr>

                    <tr>
                        <th>Placering</th>
                        <td>
                            <select name="pla_id" class="form-select">
                                <?php foreach ($pladser as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['plads'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <button class="btn btn-info">Gem indkøb</button>
            </form>
        </div>

        <!-- HØJRE SIDE -->
        <div class="col-sm-6">
            <h4>Overfør indkøb</h4>

            <form action="/lager/modules/bestilling/bestil_update.php" method="POST">
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th>ID</th>
                        <th>Vare</th>
                        <th>Købt</th>
                    </tr>

                    <?php foreach ($bestil as $b): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td><?= $b['vare'] ?></td>
                            <td>
                                <select name="bestilt[<?= $b['id'] ?>]" class="form-select form-select-sm">
                                    <option value="Nej" <?= $b['bestilt'] == "Nej" ? "selected" : "" ?>>Nej</option>
                                    <option value="Ja" <?= $b['bestilt'] == "Ja" ? "selected" : "" ?>>Ja</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <button class="btn btn-info">Opdater</button>
            </form>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";

