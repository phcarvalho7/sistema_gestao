<?php
// Encerra a sessão e volta para a tela de login.

session_start();

include "funcoes.php";

// apaga só os dados de login (a sessão continua para levar a mensagem)
unset($_SESSION["usuario"]);

definirMensagem("info", "Sessão encerrada.");

header("Location: " . urlPainel("index.php"));
exit;
