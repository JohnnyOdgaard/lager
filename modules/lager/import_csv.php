<?php
require_once __DIR__ . "/../../includes/config.php";

$message = "";
$importeret = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvfile'])) {

    if ($_FILES['csvfile']['error'] === UPLOAD_ERR_OK) {

        $tmp = $_FILES['csvfile']['tmp_name'];
        $handle = fopen($tmp, "r");

        if ($handle) {

            while (($data = fgetcsv($handle, 1000, ";")) !== false) {

                // Spring tomme linjer over
                if (count($data) < 7) continue;

                // CSV-format:
                // varenavn;kobsdato;sidstedato;maengde;enh_id;typ_id;pla_id
                list($varenavn, $kobsdato, $sidstedato, $maengde, $enh_id, $typ_id, $pla_id) = $data;

                $stmt = $pdo->prepare("
                    INSERT INTO lagerbeholdning 
                    (varenavn, kobsdato, sidstedato, maengde, enh_id, typ_id, pla_id)
                    VALUES (:v, :k, :s, :m, :e, :t, :p)
                ");

                $stmt->execute([
                    ':v' => $varenavn,
                    ':k' => $kobsdato,
                    ':s' => $sidstedato,
                    ':m' => $maengde,
                    ':e' => $enh_id,
                    ':t' => $typ_id,
                    ':p' => $pla_id
                ]);

                $importeret++;
            }

            fclose($handle);
            $message = "Import færdig. $importeret varer indlæst.";
        } else {
            $message = "Kunne ikke læse filen.";
        }

    } else {
        $message = "Fejl ved upload.";
    }
}

ob_start();
?>

<div class="container mt-4">
    <h3>Import fra CSV</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4">

        <label class="form-label">Vælg CSV-fil</label>
        <input type="file" name="csvfile" accept=".csv" class="form-control mb-3" required>

        <button class="btn btn-primary w-100">Importér</button>
    </form>

    <h5>CSV-format:</h5>
    <pre class="bg-light p-3 border rounded">
varenavn;kobsdato;sidstedato;maengde;enh_id;typ_id;pla_id
    </pre>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
