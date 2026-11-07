<?php
require_once("../model/Usuario.php");
session_start();

if (!isset($_SESSION['estaLogado']) || $_SESSION['estaLogado'] != true) {
    header('Location: login.php');
    exit;
}
?>