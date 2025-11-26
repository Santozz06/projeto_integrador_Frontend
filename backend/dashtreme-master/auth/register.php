<?php
session_start();
// Auto-cadastro desativado: redireciona para login
header('Location: login.php');
exit();