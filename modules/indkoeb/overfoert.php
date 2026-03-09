<?php
require_once __DIR__ . "/../../includes/config.php";
session_start();

// Hent listen over overførte varer
$varer = $_SESSION['overfoert'] ?? [];

// Tøm listen så den ikke vises igen næste gang
$_SESSION['overfoert'] = [];

ob_start();
?>

<div class="container mt-4">
    <h3>Alle varer er overført til lageret</h3>

    <?php if (!empty($varer)): ?>
        <p>Følgende varer blev overført:</p>

        <ul class="list-group mb-4">
            <?php foreach ($varer as $v): ?>
                <li class="list-group-item"><?= htmlspecialchars($v) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Der blev ikke registreret nogen varer i denne omgang.</p>
    <?php endif; ?>

    <a href="/lager/modules/indkoeb/indkoeb.php" class="btn btn-primary">
        Tilbage til indkøb
    </a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../includes/layout.php";
