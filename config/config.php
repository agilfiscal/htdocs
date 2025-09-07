<?php
// Configurações gerais
define('APP_NAME', 'MDE - Agil Fiscal');
define('APP_URL', 'https://portal.agilfiscal.com.br');
define('APP_ROOT', dirname(__DIR__));

// Configurações de banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'agilfi38_isacbatista');
define('DB_PASS', 'Makaveli96#');
define('DB_NAME', 'agilfi38_mde_agil_fiscal');

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de erro
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desativa exibição de erros em produção
ini_set('log_errors', 1); // Ativa log de erros
ini_set('error_log', APP_ROOT . '/Logs/error.log'); // Define local do log

// Configurações de sessão
session_start(); 