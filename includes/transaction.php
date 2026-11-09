<?php

function medTransaktion(PDO $pdo, callable $handling)
{
    $pdo->beginTransaction();

    try {
        $resultat = $handling();
        $pdo->commit();
        return $resultat;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
