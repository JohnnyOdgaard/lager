<?php 
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/url.php";

$exportMessage = '';
$exportError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'export_inventory') {
    $csvDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'csv';
    $csvPath = $csvDirectory . DIRECTORY_SEPARATOR . 'lagebeholdning.csv';
    $temporaryPath = null;
    $output = null;

    try {
        $stmt = $pdo->query("
            SELECT l.varenavn, e.enhed, p.plads, l.maengde, l.kobsdato, l.sidstedato
            FROM lagerbeholdning l
            LEFT JOIN enheder e ON l.enh_id = e.id
            LEFT JOIN plads p ON l.pla_id = p.id
            ORDER BY
                CASE
                    WHEN l.kobsdato LIKE '__-__-____' THEN STR_TO_DATE(l.kobsdato, '%d-%m-%Y')
                    ELSE STR_TO_DATE(l.kobsdato, '%Y-%m-%d')
                END DESC,
                l.varenavn, l.id
        ");

        if (!is_dir($csvDirectory) && !@mkdir($csvDirectory, 0775) && !is_dir($csvDirectory)) {
            throw new RuntimeException('Kunne ikke oprette CSV-mappen. Kontrollér serverens skriverettigheder.');
        }

        // Skriv først en midlertidig fil, så en fejl ikke ødelægger den seneste eksport.
        $temporaryPath = $csvDirectory . DIRECTORY_SEPARATOR . 'export-' . bin2hex(random_bytes(16)) . '.tmp';
        $output = @fopen($temporaryPath, 'xb');
        if ($output === false) {
            throw new RuntimeException('Kunne ikke oprette CSV-filen. Kontrollér serverens skriverettigheder.');
        }

        if (fwrite($output, "\xEF\xBB\xBF") !== 3 ||
            fputcsv($output, ['emne', 'Enhed', 'Placering', 'maengde', 'kobsdato', 'Sidstedato'], ';', '"', '') === false) {
            throw new RuntimeException('Kunne ikke skrive CSV-filens feltnavne.');
        }

        $exported = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (fputcsv($output, [
                $row['varenavn'],
                $row['enhed'],
                $row['plads'],
                $row['maengde'],
                $row['kobsdato'],
                $row['sidstedato']
            ], ';', '"', '') === false) {
                throw new RuntimeException('Kunne ikke skrive lagerbeholdningen til CSV-filen.');
            }
            $exported++;
        }

        $closed = fclose($output);
        $output = null;
        if (!$closed || !@rename($temporaryPath, $csvPath)) {
            throw new RuntimeException('Kunne ikke gemme CSV-filen. Kontrollér serverens skriverettigheder.');
        }
        $temporaryPath = null;
        $exportMessage = "Eksport færdig. $exported lagerposter er gemt i csv/lagebeholdning.csv.";
    } catch (PDOException | RuntimeException $e) {
        error_log('Eksport af lagerbeholdning fejlede: ' . $e->getMessage());
        $exportError = 'Eksporten fejlede. Kontrollér serverens log og skriverettigheder til CSV-mappen.';
    } finally {
        if (is_resource($output)) {
            fclose($output);
        }
        if ($temporaryPath !== null && is_file($temporaryPath) && !@unlink($temporaryPath)) {
            error_log('Kunne ikke slette midlertidig CSV-fil: ' . $temporaryPath);
        }
    }
}

include __DIR__ . "/includes/header.php";
?>

<div class="container mt-4">
    <h1 class="mb-4">Velkommen til Madsen Odgaards Lager</h1>

    <p class="lead">
        Brug genvejene herunder til de mest brugte funktioner i systemet.
    </p>

    <?php if ($exportMessage !== ''): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($exportMessage, ENT_QUOTES, 'UTF-8') ?>
            <a href="<?= htmlspecialchars(lagerUrl('/csv/lagebeholdning.csv')) ?>" class="alert-link">Hent CSV-filen</a>
        </div>
    <?php endif; ?>
    <?php if ($exportError !== ''): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($exportError, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-xl-3 d-flex">
            <div class="card h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Indkøb</h5>
                    <p class="card-text flex-grow-1">
                        Via nedenstående link kan du lægge indkøbte varer på lager,
                        både dem der ligger i bestillingslisten og dem, hvor der ikke
                        var oprettet en bestilling.
                    </p>
                    <a href="<?= htmlspecialchars(lagerUrl('/modules/indkoeb/indkoeb.php')) ?>" class="btn btn-outline-success mt-2">Åbn indkøb</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 d-flex">
            <div class="card h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Hent vare</h5>
                    <p class="card-text flex-grow-1">
                        Via nedenstående link kan du udtage varer fra lageret,
                        både efter varens navn, dens placering eller dens kategori.
                    </p>
                    <a href="<?= htmlspecialchars(lagerUrl('/modules/hent/hent_advanced.php')) ?>" class="btn btn-outline-success mt-2">Åbn hent</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 d-flex">
            <div class="card h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Bestillinger</h5>
                    <p class="card-text flex-grow-1">
                        Her har du mulighed for at oprette og fjerne en bestilling.
                    </p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="<?= htmlspecialchars(lagerUrl('/modules/bestilling/bestil.php')) ?>" class="btn btn-outline-success flex-fill">Opret</a>
                        <a href="<?= htmlspecialchars(lagerUrl('/modules/bestilling/bestil_liste.php')) ?>" class="btn btn-outline-primary flex-fill">Fjern</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 d-flex">
            <div class="card h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Aflæs EL</h5>
                    <p class="card-text flex-grow-1">
                        Her indtastes aflæsninger fra el-forbrug og el-produktion
                        via nedenstående knapper.
                    </p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="<?= htmlspecialchars(lagerUrl('/modules/elmaaling/aflaes.php')) ?>" class="btn btn-outline-success flex-fill">Aflæs EL</a>
                        <a href="<?= htmlspecialchars(lagerUrl('/modules/elmaaling/historik.php')) ?>" class="btn btn-outline-primary flex-fill">Vis</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 d-flex">
            <div class="card h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Opladning</h5>
                    <p class="card-text flex-grow-1">
                        Her indtastes aflæsninger fra opladning af elbil
                        via nedenstående knapper.
                    </p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="<?= htmlspecialchars(lagerUrl('/modules/elmaaling/aflaes_opladning.php')) ?>" class="btn btn-outline-success flex-fill">Aflæs</a>
                        <a href="<?= htmlspecialchars(lagerUrl('/modules/elmaaling/vis_opladning.php')) ?>" class="btn btn-outline-primary flex-fill">Vis</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 d-flex">
            <div class="card h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Lager Log</h5>
                    <p class="card-text flex-grow-1">
                        Se log over alle indkøb og hentninger med statistik.
                        Filtrer efter handling og varenavn.
                    </p>
                    <a href="<?= htmlspecialchars(lagerUrl('/system/lager_log.php')) ?>" class="btn btn-outline-success mt-2">Åbn log</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 d-flex">
            <div class="card h-100 w-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Eksportér lagerbeholdning</h5>
                    <p class="card-text flex-grow-1">
                        Gem lagerbeholdningen i csv/lagebeholdning.csv med varenavn,
                        enhed, placering, mængde, købt og sidste dato.
                    </p>
                    <form method="POST" action="<?= htmlspecialchars(lagerUrl('/index.php')) ?>" class="mt-2">
                        <button type="submit" name="action" value="export_inventory" class="btn btn-outline-success w-100">Eksportér CSV</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
