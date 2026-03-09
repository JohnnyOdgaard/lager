<?php
require_once __DIR__ . "/../../includes/config.php";

// Sæt headers for download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="lager_export.csv"');

// UTF-8 BOM så Excel viser æøå korrekt
echo "\xEF\xBB\xBF";

// Åbn output som fil
$output = fopen("php://output", "w");

// Skriv kolonneoverskrifter
fputcsv($output, [
    'ID', 'Varenavn', 'Købt', 'Sidste', 'Mængde',
    'Enhed', 'Kategori', 'Placering'
], ';');

// Hent alle varer
$stmt = $pdo->query("
    SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
           e.enhed AS enhedsnavn,
           k.kategori AS kategorinavn,
           p.plads AS pladsnavn
    FROM lagerbeholdning l
    LEFT JOIN enheder e ON l.enh_id = e.id
    LEFT JOIN kategori k ON l.typ_id = k.id
    LEFT JOIN plads p ON l.pla_id = p.id
    ORDER BY l.id
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['varenavn'],
        $row['kobsdato'],
        $row['sidstedato'],
        $row['maengde'],
        $row['enhedsnavn'],
        $row['kategorinavn'],
        $row['pladsnavn']
    ], ';');
}

fclose($output);
exit;
