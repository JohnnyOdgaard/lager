<?php
require_once __DIR__ . "/../../includes/config.php";

$message = "";
$antal = 0;

//
// ARKIVERING
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dato = $_POST['dato'] ?? '';

    if ($dato !== '') {

        // Hent alle varer der skal arkiveres
        $stmt = $pdo->prepare("
            SELECT * FROM lagerbeholdning
            WHERE sidstedato < :d
        ");
        $stmt->execute([':d' => $dato]);
        $varer = $stmt->fetchAll();

        // Flyt til arkiv
        $insert = $pdo->prepare("
            INSERT INTO lagerbeholdning_arkiv
            (id, varenavn, kobsdato, sidstedato, maengde, enhed_id, kat_id, pla_id, arkiveret_dato)
            VALUES (:id, :v, :k, :s, :m, :e, :c, :p, CURDATE())
        ");

        $delete = $pdo->prepare("DELETE FROM lagerbeholdning WHERE id = :id");

        foreach ($varer as $v) {
            $insert->execute([
                ':id' => $v['id'],
                ':v'  => $v['varenavn'],
                ':k'  => $v['kobsdato'],
                ':s'  => $v['sidstedato'],
                ':m'  => $v['maengde'],
                ':e'  => $v['enhed_id'],
                ':c'  => $v['kat_id'],
                ':p'  => $v['pla_id']
            ]);

            $delete->execute([':id' => $v['id']]);
            $antal++;
        }

        $message = "$antal varer er arkiveret.";
    } else {
        $message = "Du skal vælge en dato.";
    }
}

ob_start();
?>

<div class="container mt-4">
    <h3>Arkivér gamle varer</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">

        <label class="form-label">Arkivér varer med udløbsdato før:</label>
        <input type="date" name="dato" class="form-control mb-3" required>

        <button class="btn btn-danger w-100">Arkivér</button>
    </form>

    <p class="text-muted">
        Varer flyttes til tabellen <strong>lagerbeholdning_arkiv</strong> og slettes fra aktivt lager.
    </p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
