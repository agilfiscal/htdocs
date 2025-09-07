/**
 * Arquivo principal para execução da consulta MDE
 */

// Importa o módulo de consulta MDE
const { consultarMDE } = require('./public/js/mde.js');

// Executa a consulta
consultarMDE().catch(error => {
    console.error('Erro ao executar consulta MDE:', error);
    process.exit(1);
}); 