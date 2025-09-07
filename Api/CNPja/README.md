# API do CNPJa - Configuração

## API Pública do CNPJa

O sistema utiliza a API pública do CNPJa disponível em: https://open.cnpja.com/

### Características:
- **Sem necessidade de chave de API**
- **Acesso gratuito**
- **Dados atualizados**
- **Limite de consultas por minuto**

### URL da API:
```
https://open.cnpja.com/office/{CNPJ}
```

### Exemplo de uso:
```
https://open.cnpja.com/office/07526557011659
```

## Estrutura dos Dados

A API retorna dados no seguinte formato:

```json
{
  "taxId": "07526557011659",
  "alias": "Nome Fantasia",
  "founded": "2023-07-31",
  "company": {
    "name": "RAZÃO SOCIAL LTDA",
    "equity": 1000000.00,
    "nature": {
      "text": "Sociedade Anônima Aberta"
    }
  },
  "status": {
    "text": "Ativa"
  },
  "address": {
    "street": "Rua Exemplo",
    "number": "123",
    "district": "Centro",
    "city": "São Paulo",
    "state": "SP",
    "zip": "01234-567"
  }
}
```

## Limitações da API

- **Rate Limiting**: A API possui limites de consultas por minuto/hora
- **Cobertura**: Nem todos os CNPJs podem estar disponíveis na base
- **Atualização**: Os dados são atualizados periodicamente

## Tratamento de Erros

O sistema trata os seguintes erros da API:

- **401**: Chave da API inválida ou expirada
- **404**: CNPJ não encontrado
- **429**: Limite de consultas excedido
- **Outros**: Erros de conexão ou processamento

## Logs

Os erros da API são registrados no log do sistema para facilitar o debug. 