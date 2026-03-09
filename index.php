<?php 
require_once __DIR__ . "/includes/config.php";
include __DIR__ . "/includes/header.php";
?>

<div class="container mt-4">
    <h1 class="mb-4">Velkommen til Madsen Odgaards Lager</h1>

    <p class="lead">
        Brug genvejene herunder til de mest brugte funktioner i systemet.
    </p>

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
                    <a href="/lager/modules/indkoeb/indkoeb.php" class="btn btn-outline-success mt-2">Åbn indkøb</a>
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
                    <a href="/lager/modules/hent/hent_advanced.php" class="btn btn-outline-success mt-2">Åbn hent</a>
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
                        <a href="/lager/modules/bestilling/bestil.php" class="btn btn-outline-success flex-fill">Opret</a>
                        <a href="/lager/modules/bestilling/bestil_liste.php" class="btn btn-outline-primary flex-fill">Fjern</a>
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
                        <a href="/lager/modules/elmaaling/aflaes.php" class="btn btn-outline-success flex-fill">Aflæs EL</a>
                        <a href="/lager/modules/elmaaling/historik.php" class="btn btn-outline-primary flex-fill">Vis</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
