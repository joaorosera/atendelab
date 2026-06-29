<?php
// pra ver o que ta vindo na sessao/cookie quando testo o login pelo Postman
session_start();
echo '<pre>';
var_dump($_COOKIE);
var_dump($_SESSION);
echo '</pre>';
