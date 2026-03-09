<?php
require_once __DIR__ . "/../../includes/config.php";

// Byg WHERE-delen dynamisk
$where = [];
$params = [];

// Filtrér på kategori (typ_id i lagerbeholdning)
if (!empty($_GET['typ_id'])) {
    $where[] = "lb.typ_id = :typ_id";
    $params[':typ_id'] = $_GET['typ_id'];
}

// Filtrér på placering (pla_id)
if (!empty($_GET['pla_id'])) {
    $where[] = "lb.pla_id = :pla_id";
    $params[':pla_id'] = $_GET['pla_id'];
}

// Dato-betingelsen (altid aktiv)
$where[] = "
    CASE
        WHEN lb.sidstedato LIKE '__-__-____' THEN STR_TO_DATE(lb.sidstedato, '%d-%m-%Y')
        ELSE STR_TO_DATE(lb.sidstedato, '%Y-%m-%d')
    END < CURDATE()
";

$whereSQL = implode(" AND ", $where);

// Hent udløbne varer
$stmt = $pdo->prepare("
    SELECT 
        lb.*,
        p.plads AS pladsnavn,
        k.kategori AS kategorinavn,
        CASE
            WHEN lb.sidstedato LIKE '__-__-____' THEN STR_TO_DATE(lb.sidstedato, '%d-%m-%Y')
            ELSE STR_TO_DATE(lb.sidstedato, '%Y-%m-%d')
        END AS dato_sort
    FROM lagerbeholdning lb
    LEFT JOIN plads p ON p.id = lb.pla_id
    LEFT JOIN kategori k ON k.id = lb.typ_id
    WHERE $whereSQL
    ORDER BY dato_sort ASC
");

$stmt->execute($params);
$varer = $stmt->fetchAll();

ob_start();
?>

<div class="container mt-4">
    <h3>Udløbne varer</h3>

    <!-- FILTRERING -->
    <form method="GET" class="row g-3 mb-3">

        <!-- Kategori -->
        <div class="col-md-4">
            <label for="typ_id" class="form-label">Kategori</label>
            <select name="typ_id" id="typ_id" class="form-select">
                <option value="">Alle</option>
                <?php
                $kategorier = $pdo->query("SELECT id, kategori FROM kategori ORDER BY kategori")->fetchAll();
                foreach ($kategorier as $k):
                ?>
                    <option value="<?= $k['id'] ?>" <?= ($_GET['typ_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                        <?= $k['kategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Placering -->
        <div class="col-md-4">
            <label for="pla_id" class="form-label">Placering</label>
            <select name="pla_id" id="pla_id" class="form-select">
                <option value="">Alle</option>
                <?php
                $pladser = $pdo->query("SELECT id, plads FROM plads ORDER BY plads")->fetchAll();
                foreach ($pladser as $p):
                ?>
                    <option value="<?= $p['id'] ?>" <?= ($_GET['pla_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                        <?= $p['plads'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filtrer-knap -->
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filtrer</button>
        </div>

    </form>

    <!-- TABELLEN -->
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Varenavn</th>
                <th>Kategori</th>
                <th>Købt</th>
                <th>Sidste</th>
                <th>Placering</th>
                <th>Mængde</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($varer as $v): ?>
            <tr>
                <td data-label="ID">
                    <a class="btn btn-primary btn-sm" href="hent_vare.php?id=<?= $v['id'] ?>">
                        <?= $v['id'] ?>
                    </a>
                </td>

                <td data-label="Varenavn"><?= $v['varenavn'] ?></td>
                <td data-label="Kategori"><?= $v['kategorinavn'] ?></td>
                <td data-label="Købt"><?= $v['kobsdato'] ?></td>
                <td data-label="Sidste"><?= $v['sidstedato'] ?></td>
                <td data-label="Placering"><?= $v['pladsnavn'] ?></td>
                <td data-label="Mængde"><?= $v['maengde'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
