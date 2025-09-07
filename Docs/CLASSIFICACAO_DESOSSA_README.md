# Classificação de Desossa - Sistema AgilFiscal

## Visão Geral

A funcionalidade de **Classificação de Desossa** permite realizar uma análise detalhada e analítica de cada boi, calculando rendimentos, classificando carnes e analisando a rentabilidade do processo de desossa.

## Funcionalidades Principais

### 1. Gestão de Dados do Boi
- **Identificação**: Código único para cada boi
- **Data de Abate**: Controle temporal do processo
- **Peso Total**: Peso em quilogramas do boi
- **Preço por kg**: Custo de aquisição por quilograma
- **Custo Total**: Calculado automaticamente (Peso × Preço/kg)
- **Fornecedor**: Informações do fornecedor do boi

### 2. Importação de Dados
- **Arquivo XML**: Importação de dados de nota fiscal
- **Arquivo TXT**: Importação da classificação de carnes
- **Entrada Manual**: Adição manual de cada corte

### 3. Classificação de Carnes
#### Carnes Nobres
- Picanha
- Alcatra
- Maminha
- Coxão Mole
- Coxão Duro

#### Carnes Secundárias
- Patinho
- Contra Filé
- Filé Mignon
- Costela
- Paleta
- Acém
- Músculo

#### Desperdícios
- Osso
- Sebo
- Pelanca
- Outros

### 4. Análise de Rendimento
- **Peso Total**: Peso total do boi
- **Peso Carnes**: Soma dos pesos de carnes classificadas
- **Rendimento**: Percentual de aproveitamento
- **Custo Total**: Custo de aquisição
- **Valor Carnes**: Valor total das carnes
- **Valor Desperdício**: Valor dos desperdícios
- **Margem Bruta**: Lucro bruto
- **Margem Percentual**: Percentual de lucro

## Como Usar

### Passo 1: Dados do Boi
1. Acesse a página "Classificação de Desossa" no menu Extras
2. Preencha as informações básicas do boi:
   - Identificação (ex: BOI001)
   - Data de Abate
   - Peso Total (kg)
   - Preço por kg (R$)
   - Fornecedor (opcional)

### Passo 2: Modo de Inserção de Dados
Antes de classificar as carnes, você deve selecionar um dos três modos disponíveis:

#### 1. Arquivo TXT
- **Formato**: `CÓDIGO;CORTE;PESO;PREÇO_VENDA`
- **Uso**: Ideal para importar grandes volumes de dados
- **Exemplo**:
  ```
  001;PICANHA;15.50;45.00
  002;ALCATRA;25.30;38.00
  003;MAMINHA;12.80;42.00
  ```

#### 2. Inserção Manual
- **Funcionamento**: Sistema apresenta cada corte sequencialmente
- **Processo**: Digite o peso e pressione ENTER para avançar
- **Vantagem**: Controle total sobre cada entrada
- **Fluxo**: Após inserir o peso, o próximo corte é automaticamente selecionado

#### 3. Etiqueta (Código de Barras)
- **Formato**: Código EAN-13 que começa com "2"
- **Estrutura**: `2XXXXXX|XXXXX|X`
  - **Dígito 1**: Código de balança (sempre "2")
  - **Dígitos 2-7**: Código do produto
  - **Dígitos 8-12**: Peso do produto em gramas
  - **Dígito 13**: Dígito verificador
- **Exemplo**: `2069600017009` = 0,170 kg (170 gramas)
- **Processo**: Escaneie a etiqueta e o sistema avança automaticamente

### Passo 3: Importação de Dados do Boi
#### Importação XML
- Arraste e solte um arquivo XML ou clique para selecionar
- O sistema processará automaticamente os dados da nota fiscal
- **Campos extraídos automaticamente:**
  - **Identificação do Boi**: `<xProd>` (ex: "BOI CASADO - 1,00")
  - **Data de Abate**: `<dhEmi>` (extrai apenas a data da string ISO)
  - **Peso Total**: `<qCom>` (quantidade em kg)
  - **Preço por kg**: `<vUnTrib>` (valor unitário tributável)
  - **Custo Total**: `<vProd>` (valor total do produto)
  - **Fornecedor**: `<xNome>` do emitente
- **Múltiplos Produtos**: Se o XML contiver mais de um produto, será exibido um modal para seleção
- Suporte para arquivos XML padrão de notas fiscais eletrônicas (NFe)

### Passo 4: Classificação das Carnes
Após selecionar o modo de inserção, o sistema guiará você através do processo:

#### Modo Manual
1. O sistema apresenta o primeiro corte automaticamente
2. Digite o peso e pressione ENTER
3. O próximo corte é selecionado automaticamente
4. Continue até classificar todos os cortes

#### Modo Etiqueta
1. O sistema apresenta o corte atual
2. Escaneie o código de barras da etiqueta
3. O peso é extraído automaticamente
4. O próximo corte é selecionado automaticamente

#### Modo TXT
1. Selecione o arquivo TXT com os dados
2. O sistema processa todas as linhas automaticamente
3. Todos os cortes são importados de uma vez

### Passo 5: Cálculo e Análise
1. Após classificar os cortes, clique em "Calcular Rendimento"
2. O sistema processará todos os dados
3. Visualize os resultados na seção de análise

## Formatos de Arquivo

### Arquivo TXT
```
CORTE|PESO|PRECO_VENDA
```
- **CORTE**: Nome do corte (ex: PICANHA, ALCATRA)
- **PESO**: Peso em quilogramas (ex: 15.50)
- **PRECO_VENDA**: Preço de venda por kg (ex: 45.00)

### Arquivo XML
```xml
<?xml version="1.0" encoding="UTF-8"?>
<nfeProc xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
    <NFe>
        <infNFe versao="4.00">
            <ide>
                <dhEmi>2025-08-10T02:37:00-03:00</dhEmi>
            </ide>
            <emit>
                <xNome>KYC PROCESSADORA DE CARNES LTDA</xNome>
                <CNPJ>12.345.678/0001-90</CNPJ>
            </emit>
            <det>
                <prod>
                    <xProd>BOI CASADO - 1,00</xProd>
                    <qCom>224.5000</qCom>
                    <vUnTrib>20.9900000000</vUnTrib>
                    <vProd>4712.26</vProd>
                    <uTrib>KG</uTrib>
                </prod>
            </det>
        </infNFe>
    </NFe>
</nfeProc>
```

## Exemplos de Uso

### Cenário 1: Boi de 450kg
- **Peso Total**: 450,00 kg
- **Custo**: R$ 5,00/kg = R$ 2.250,00
- **Rendimento Esperado**: 70-75%
- **Carnes Nobres**: ~40% do peso
- **Carnes Secundárias**: ~30% do peso
- **Desperdícios**: ~30% do peso

### Cenário 2: Análise de Rentabilidade
- **Custo Total**: R$ 2.250,00
- **Valor Carnes**: R$ 3.150,00
- **Margem Bruta**: R$ 900,00
- **Margem Percentual**: 40%

## Benefícios

1. **Controle Total**: Acompanhe cada boi individualmente
2. **Análise Financeira**: Calcule margens e rentabilidade
3. **Gestão de Qualidade**: Monitore rendimentos e desperdícios
4. **Relatórios**: Gere relatórios detalhados para análise
5. **Importação**: Automatize a entrada de dados
6. **Histórico**: Mantenha histórico de todos os abates

## Dicas de Uso

1. **Padronização**: Use sempre os mesmos nomes para os cortes
2. **Precisão**: Meça os pesos com precisão para cálculos corretos
3. **Preços**: Atualize os preços de venda regularmente
4. **Backup**: Faça backup dos dados regularmente
5. **Análise**: Compare rendimentos entre diferentes fornecedores

## Suporte

Para dúvidas ou problemas:
- Consulte o botão de ajuda (?) na página
- Verifique os exemplos de arquivos na pasta `public/`:
  - `exemplo_boi.xml` - XML com um produto
  - `exemplo_multiplos_produtos.xml` - XML com múltiplos produtos
  - `exemplo_classificacao_desossa.txt` - Formato TXT para classificação
- Entre em contato com o suporte técnico

---

**Desenvolvido para o Sistema AgilFiscal**  
*Versão 1.0 - Janeiro 2025*
