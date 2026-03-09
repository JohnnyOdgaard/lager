<?php
require_once __DIR__ . "/../../includes/config.php";

$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("
    SELECT l.id, l.varenavn, l.kobsdato, l.sidstedato, l.maengde,
           p.plads AS pladsnavn
    FROM lagerbeholdning l
    LEFT JOIN plads p ON l.pla_id = p.id
    WHERE l.varenavn LIKE :search
    ORDER BY l.varenavn
");

$stmt->execute([':search' => "%$search%"]);
$rows = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Hent varer</h3>

    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control"
               placeholder="Søg varenavn..."
               value="<?= htmlspecialchars($search) ?>" autofocus>
    </form>

    <?php if ($search !== ''): ?>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Varenavn</th>
                    <th>Købt</th>
                    <th>Sidste</th>
                    <th>Placering</th>
                    <th>Mængde</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td data-label="ID">
                        <a class="btn btn-primary btn-sm" href="hent_vare.php?id=<?= $r['id'] ?>">
                            <?= $r['id'] ?>
                        </a>
                    </td>

                    <td data-label="Varenavn"><?= $r['varenavn'] ?></td>
                    <td data-label="Købt"><?= $r['kobsdato'] ?></td>
                    <td data-label="Sidste"><?= $r['sidstedato'] ?></td>
                    <td data-label="Placering"><?= $r['pladsnavn'] ?></td>
                    <td data-label="Mængde"><?= $r['maengde'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
