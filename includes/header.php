<?php ?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Madsen Odgaard Lager</title>

    <!-- Bootstrap 5 CSS -->
    <link href="/lager/assets/css/bootstrap-5.3.8.css" rel="stylesheet">

    <!-- Custom styles -->
    <link rel="stylesheet" href="/lager/assets/css/styles.css">

<style>

/* ---------------------------------------------------------
   MOBILVISNING — KUN FOR TABELLER MED .table-mobile
   --------------------------------------------------------- */
@media (max-width: 768px) {

    table.table-mobile {
        display: block !important;
        width: 100% !important;
    }

    table.table-mobile thead {
        display: none !important;
    }

    table.table-mobile tr {
        display: block !important;
        margin-bottom: 12px !important;
        border: 1px solid #ddd !important;
        padding: 10px !important;
        border-radius: 6px !important;
        background: #fff !important;
    }

    table.table-mobile td {
        display: flex !important;
        justify-content: space-between !important;
        padding: 6px 0 !important;
        border: none !important;
    }

    table.table-mobile td::before {
        content: attr(data-label) !important;
        font-weight: bold !important;
        margin-right: 10px !important;
        color: #333 !important;
    }

    a.btn-sm {
        width: 100% !important;
        text-align: center !important;
    }
}

/* ---------------------------------------------------------
   GLOBAL AUTOFOKUS — VIRKER I HELE SYSTEMET
   --------------------------------------------------------- */
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const af = document.querySelector("[autofocus]");
    if (af) {
        af.focus();
        if (af.select) af.select();
    }
});
</script>

</head>

<body class="d-flex flex-column min-vh-100">

    <!-- HEADER-BILLEDE -->
    <div class="header-image position-relative w-100">
        <img src="/lager/assets/images/topBanner.png" alt="Lager" class="img-fluid w-100 header-img">

        <div class="header-title position-absolute top-50 start-50 translate-middle text-white text-center">
            <h1 class="display-5 fw-bold">Madsen Odgaards Lager</h1>
        </div>
    </div>

    <!-- NAVBAR (FULL-WIDTH) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100">
        <div class="container">

            <a href="/lager/index.php" class="navbar-brand">Lager</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWithDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarWithDropdown">
                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a href="/lager/index.php" class="nav-link">Forside</a>
                    </li>

                    <li class="nav-item">
                        <a href="/lager/modules/indkoeb/indkoeb.php" class="nav-link">Indkøb</a>
                    </li>

                    <li class="nav-item">
                        <a href="/lager/modules/soeg/vareliste.php" class="nav-link">Vareliste</a>
                    </li>

                    <!-- Bestilling -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-bs-toggle" data-bs-toggle="dropdown">Bestilling</a>
                        <div class="dropdown-menu">
                            <a href="/lager/modules/bestilling/bestillingsliste.php" class="dropdown-item">Vis bestillinger</a>
                            <a href="/lager/modules/bestilling/bestil.php" class="dropdown-item">Opret bestilling</a>
                            <a href="/lager/modules/bestilling/bestil_update.php" class="dropdown-item">Overfør købte varer</a>
                            <a href="/lager/modules/bestilling/bestil_liste.php" class="dropdown-item">Fjern bestilling</a>
                        </div>
                    </li>

                    <!-- Hent -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-bs-toggle" data-bs-toggle="dropdown">Hent</a>
                        <div class="dropdown-menu">
                            <a href="/lager/modules/hent/hent.php" class="dropdown-item">Hent via varer</a>
                            <a href="/lager/modules/hent/hent_kategori.php" class="dropdown-item">Hent via kategori</a>
                            <a href="/lager/modules/hent/hent_placering.php" class="dropdown-item">Hent via placering</a><br>
                            <a href="/lager/modules/hent/hent_advanced.php" class="dropdown-item">Hent via navn/kategori</a>
                            <a href="/lager/modules/hent/hent_udloeb.php" class="dropdown-item">Søg udløbet varer</a>
                        </div>
                    </li>

                                        

                    <!-- Rettelser -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-bs-toggle" data-bs-toggle="dropdown">Rettelser</a>
                        <div class="dropdown-menu">
                            <a href="/lager/modules/lager/ret_enhed.php" class="dropdown-item">Ret enhed</a>
                            <a href="/lager/modules/lager/ret_kategori.php" class="dropdown-item">Ret kategori</a>
                            <a href="/lager/modules/lager/ret_placering.php" class="dropdown-item">Ret placering</a>
                            <a href="/lager/modules/lager/ret_maengde.php" class="dropdown-item">Ret mængde</a><br>
                            <a href="/lager/modules/lager/ret_udloeb.php" class="dropdown-item">Ret udløbsdato</a>
                            <a href="/lager/modules/lager/ret_varenavn.php" class="dropdown-item">Ret varenavn</a>
                            <div class="dropdown-divider"></div>
                            <a href="/lager/modules/lager/skift_enhed_multi.php" class="dropdown-item">Skift flere enheder</a>
                           <!-- <a href="/lager/modules/lager/rediger_indkoeb.php" class="dropdown-item">Ret indkøb/forbrug</a>-->
                            <a href="/lager/modules/lager/flyt_placering_multi.php" class="dropdown-item">Flyt flere varer</a>
                            <a href="/lager/modules/lager/skift_kategori_multi.php" class="dropdown-item">Ændre kategori flere varer</a>
                            <div class="dropdown-divider"></div>
                            <a href="/lager/modules/lager/export_csv.php" class="dropdown-item">Exporter csv</a>
                            <a href="/lager/modules/lager/import_csv.php" class="dropdown-item">Importer csv</a>
                            <a href="/lager/modules/lager/arkiver.php" class="dropdown-item">Arkiver</a>
                        </div>
                    </li>
                    <!-- opret kategori, enhed og pladsering -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-bs-toggle" data-bs-toggle="dropdown">System</a>
                        <div class="dropdown-menu">
                            <a href="/lager/system/opret_ny_enhed.php" class="dropdown-item">Opret enhed</a>
                            <a href="/lager/system/opret_ny_kategori.php" class="dropdown-item">Opret kategori</a>
                            <a href="/lager/system/opret_ny_placering.php" class="dropdown-item">Opret placering</a>
                            <a href="/lager/system/lager_log.php" class="dropdown-item">Vis lager log</a>
                            <div class="dropdown-divider"></div>
                            <a href="/lager/system/ret_enhed.php" class="dropdown-item">Ret enhed</a>
                            <a href="/lager/system/ret_kategori.php" class="dropdown-item">Ret kategori</a>
                            <a href="/lager/system/ret_placering.php" class="dropdown-item">Ret placering</a>
                        </div>
                    </li>
                    <!-- Elmålinger -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-bs-toggle" data-bs-toggle="dropdown">Elmålinger</a>
                        <div class="dropdown-menu">
                            <a href="/lager/modules/elmaaling/aflaes.php" class="dropdown-item">Aflæs</a>
                            <a href="/lager/modules/elmaaling/historik.php" class="dropdown-item">Vis</a>
                            <div class="dropdown-divider"></div>
                            <a href="/lager/modules/elmaaling/maanedlig.php" class="dropdown-item">Månedlig</a>
                            <a href="/lager/modules/elmaaling/graf.php" class="dropdown-item">Graf</a>
                            <a href="/lager/modules/elmaaling/effekt.php" class="dropdown-item">Effekt</a>
                            <div class="dropdown-divider"></div>
                            <a href="/lager/modules/elmaaling/el_ret.php" class="dropdown-item">Ret</a>
                            <a href="/lager/modules/elmaaling/el_slet.php" class="dropdown-item">Slet</a>
                            <a href="/lager/modules/elmaaling/overfoer.php" class="dropdown-item">Arkiver</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="/lager/system/status.php" class="nav-link">Lav status</a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">Kontakt</a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>

    <!-- Bootstrap 5 JS -->
   <script src="/lager/assets/js/bootstrap.bundle.min.js"></script>


