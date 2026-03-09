<?php include "../includes/header.php"; ?>

<main class="container my-4 flex-grow-1">
    <?php
        // Her indsætter hver side sit eget indhold
        if (isset($content)) {
            echo $content;
        }
    ?> Fjern købte
</main>

<?php include "../includes/footer.php"; ?>
