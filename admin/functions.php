<?php
/**
 * Formata um valor numérico para o padrão monetário brasileiro (R$).
 */
function formatarMoeda($valor) {
    return "R$ " . number_format((float) $valor, 2, ",", ".");
}
