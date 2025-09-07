/**
 * Consulta MDE - Manifestação do Destinatário Eletrônico
 * 
 * Este módulo realiza consultas ao serviço MDE da SEFAZ para verificar
 * documentos eletrônicos disponíveis para manifestação.
 */

require('dotenv').config();
const fs = require('fs');
const { DistribuicaoDFe } = require('node-mde');

/**
 * Valida as variáveis de ambiente necessárias
 * @throws {Error} Se alguma variável obrigatória não estiver definida
 */
function validarVariaveisAmbiente() {
  const variaveisObrigatorias = [
    'CERTIFICADO_PFX',
    'CERTIFICADO_SENHA',
    'CNPJ',
    'UF_AUTOR',
    'AMBIENTE'
  ];

  for (const variavel of variaveisObrigatorias) {
    if (!process.env[variavel]) {
      throw new Error(`${variavel} não definido no arquivo .env`);
    }
  }
}

/**
 * Verifica se o certificado digital existe no caminho especificado
 * @throws {Error} Se o certificado não for encontrado
 */
function verificarCertificado() {
  if (!fs.existsSync(process.env.CERTIFICADO_PFX)) {
    throw new Error(`Certificado não encontrado em: ${process.env.CERTIFICADO_PFX}`);
  }
}

/**
 * Configura e retorna o cliente MDE
 * @returns {DistribuicaoDFe} Cliente MDE configurado
 */
function configurarClienteMDE() {
  return new DistribuicaoDFe({
    pfx: fs.readFileSync(process.env.CERTIFICADO_PFX),
    passphrase: process.env.CERTIFICADO_SENHA,
    cnpj: process.env.CNPJ,
    cUFAutor: process.env.UF_AUTOR,
    tpAmb: process.env.AMBIENTE
  });
}

/**
 * Função principal que realiza a consulta MDE
 */
async function consultarMDE() {
  try {
    console.log('🚀 Iniciando consulta MDE...');
    
    // Validações iniciais
    validarVariaveisAmbiente();
    verificarCertificado();

    // Exibe informações da consulta
    console.log('📋 Informações da Consulta:');
    console.log('   Ambiente:', process.env.AMBIENTE === '1' ? 'Produção' : 'Homologação');
    console.log('   UF:', process.env.UF_AUTOR);
    console.log('   CNPJ:', process.env.CNPJ);

    // Configura e executa a consulta
    const distribuicao = configurarClienteMDE();
    console.log('🔍 Consultando último NSU...');
    
    // Primeiro, consulta o último NSU disponível
    const ultimoNSU = await distribuicao.consultaUltNSU('000000000000000');
    
    if (ultimoNSU.error) {
      throw new Error(ultimoNSU.error);
    }

    // Extrai o último NSU da resposta
    const ultNSU = ultimoNSU.data?.ultNSU || '000000000000000';
    console.log('📊 Último NSU disponível:', ultNSU);

    // Agora consulta usando o último NSU
    const consulta = await distribuicao.consultaUltNSU(ultNSU);

    if (consulta.error) {
      throw new Error(consulta.error);
    }

    // Exibe o resultado
    console.log('✅ Resultado da consulta:');
    console.log(JSON.stringify(consulta, null, 2));

    // Salva o resultado em um arquivo JSON para uso pelo PHP
    fs.writeFileSync(__dirname + '/../resultado_mde.json', JSON.stringify(consulta, null, 2));
    console.log('Arquivo resultado_mde.json gerado em ./public/');

    return consulta;

  } catch (error) {
    console.error('❌ Erro ao consultar MDE:', error.message);
    if (error.code === 'ENOENT') {
      console.error('   Verifique se o caminho do certificado está correto no arquivo .env');
    }
    throw error;
  }
}

// Exporta a função consultarMDE
module.exports = { consultarMDE };