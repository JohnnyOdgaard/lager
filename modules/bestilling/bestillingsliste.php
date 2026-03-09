<?php
require_once __DIR__ . "/../../includes/config.php";

// Hent bestillingslisten – alfabetisk sorteret
$bestil = $pdo->query("
    SELECT id, vare, bestilt
    FROM bestil
    ORDER BY vare ASC
")->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">

        <!-- Venstre tom kolonne -->
        <div class="col-12 col-md-2"></div>

        <!-- Midterste kolonne med tabel -->
        <div class="col-12 col-md-8 text-center">

            <h3>Bestillingsliste</h3>

            <form action="/lager/modules/bestilling/bestil_update.php" method="POST">
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vare</th>
                            <th>Bestilt</th>
                        </tr>
                    </thead>

                    <tbody>
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
                    </tbody>
                </table>

                <button class="btn btn-info w-100">Opdater</button>
            </form>

        </div>

        <!-- Højre tom kolonne -->
        <div class="col-12 col-md-2"></div>

    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
