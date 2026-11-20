<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/transaction.php";

$id = $_POST['id'];
$antal = (int)$_POST['antal'];

if ($antal <= 0) {
    die("Antal skal vaere stoerre end 0.");
}

$ny = medTransaktion($pdo, function () use ($pdo, $id, $antal) {
// Hent nuværende mængde
$stmt = $pdo->prepare("SELECT l.varenavn, l.kobsdato, l.maengde, e.enhed
                       FROM lagerbeholdning l
                       LEFT JOIN enheder e ON e.id = l.enh_id
                       WHERE l.id = :id FOR UPDATE");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    return "Varen findes ikke.";
}

$ny = (int)$row['maengde'] - $antal;

if ($ny < 0) {
    return "Du kan ikke hente mere end der er på lager.";
}

// Opdater mængde
$stmt = $pdo->prepare("UPDATE lagerbeholdning SET maengde = :ny WHERE id = :id");
$stmt->execute([':ny' => $ny, ':id' => $id]);

$logStmt = $pdo->prepare("INSERT INTO lager_log (handling, varenavn, kobsdato, maengde, enhed, aktionsdato)
                          VALUES (:handling, :varenavn, :kobsdato, :maengde, :enhed, NOW())");
$logStmt->execute([
    ':handling' => 'hent',
    ':varenavn' => $row['varenavn'],
    ':kobsdato' => $row['kobsdato'],
    ':maengde' => $antal,
    ':enhed' => $row['enhed']
]);
return $ny;
});

if (is_string($ny)) {
    die($ny);
}

// Hvis der stadig er noget tilbage → tilbage til søgning
if ($ny > 0) {
    header("Location: hent.php?search=");
    exit;
}

// Hvis mængden er 0 → videre til bestil.php
$fra = ($_POST['fra'] ?? '') === 'hent_advanced' ? '&fra=hent_advanced' : '';
header("Location: ../bestilling/bestil.php?id=$id$fra");
exit;
