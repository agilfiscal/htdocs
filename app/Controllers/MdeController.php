<?php

namespace App\Controllers;

class MdeController {
    public function testImportar() {
        try {
            // Verifica se o arquivo resultado_mde.json existe
            $arquivo = __DIR__ . '/../../public/resultado_mde.json';
            if (!file_exists($arquivo)) {
                throw new \Exception('Arquivo resultado_mde.json não encontrado');
            }

            // Lê o conteúdo do arquivo
            $conteudo = file_get_contents($arquivo);
            $dados = json_decode($conteudo, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao decodificar o JSON: ' . json_last_error_msg());
            }

            // Verifica se há documentos na resposta
            if (empty($dados['data']['docZip'])) {
                echo "Nenhum documento encontrado na resposta da SEFAZ.";
                return;
            }

            // Processa cada documento
            foreach ($dados['data']['docZip'] as $doc) {
                // Aqui você pode adicionar a lógica para processar cada documento
                // Por exemplo, salvar no banco de dados
                echo "Processando documento: " . $doc['xml'] . "\n";
            }

            echo "Importação concluída com sucesso!";

        } catch (\Exception $e) {
            echo "Erro ao importar MDE: " . $e->getMessage();
        }
    }
} 