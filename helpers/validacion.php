<?php

function validarEntero($valor, $nombre){
    if (!isset($valor) || $valor === '') {
        die("$nombre no enviado");
    }

    if (!filter_var($valor, FILTER_VALIDATE_INT)) {
        die("$nombre debe ser un número entero válido");
    }

    return (int)$valor;
}

function validarNumero($valor, $nombre, $min = 0){
    if (!isset($valor) || $valor === '') {
        die("$nombre no enviado");
    }

    if (!is_numeric($valor)) {
        die("$nombre debe ser numérico");
    }

    $valor = $valor + 0;

    if ($valor < $min) {
        die("$nombre no puede ser menor a $min");
    }

    return $valor;
}

function validarTexto($valor, $nombre){
    if (!isset($valor) || trim($valor) === '') {
        die("$nombre no puede estar vacío");
    }

    return trim($valor);
}