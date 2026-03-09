<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_POST['id'];
$antal = (int)$_POST['antal'];

if ($antal <= 0) {
    die("Antal skal vaere stoerre end 0.");
}

// Hent nuværende mængde
$stmt = $pdo->prepare("SELECT l.varenavn, l.kobsdato, l.maengde, e.enhed
                       FROM lagerbeholdning l
                       LEFT JOIN enheder e ON e.id = l.enh_id
                       WHERE l.id = :id");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    die("Varen findes ikke.");
}

$ny = (int)$row['maengde'] - $antal;

if ($ny < 0) {
    die("Du kan ikke hente mere end der er på lager.");
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

// Hvis der stadig er noget tilbage → tilbage til søgning
if ($ny > 0) {
    header("Location: hent.php?search=");
    exit;
}

// Hvis mængden er 0 → videre til bestil.php
header("Location: ../bestilling/bestil.php?id=$id");
exit;
