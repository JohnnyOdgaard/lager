<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Sletning kræver en POST-forespørgsel.');
}

require_once __DIR__ . "/../../includes/config.php";

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    http_response_code(400);
    exit('Ugyldigt ID.');
}

$stmt = $pdo->prepare("DELETE FROM lagerbeholdning WHERE id = :id");
$stmt->execute([':id' => $id]);

header("Location: ../soeg/vareliste.php");
exit;
