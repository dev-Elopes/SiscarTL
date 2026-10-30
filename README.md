# 🚗 SISCARTL (AutoCare) — Sistema de Gestão Veicular & Telemetria Preventiva

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Testes-37%20Passed-10B981?style=for-the-badge&logo=phpunit&logoColor=white" alt="Testes Passando" />
  <img src="https://img.shields.io/badge/Licen%C3%A7a-MIT-blue?style=for-the-badge" alt="Licença MIT" />
</p>

O **SISCARTL (AutoCare)** é uma aplicação completa e moderna para gerenciamento inteligente de frotas e veículos pessoais. Desenvolvido para eliminar o uso de planilhas desorganizadas, o sistema antecipa revisões preventivas, calcula consumo real de combustível através de dashboards BI e controla vencimentos fiscais parcelados (IPVA, seguro e licenciamento) para evitar multas e prejuízos.

---

## 🎯 Principais Funcionalidades

### 1. 🏎️ Garagem Inteligente & Atualização de Odômetro
- Cadastro de carros e motocicletas com catálogo de marcas e modelos nacionais populares.
- **Regime de Uso Personalizado:** Cálculo inteligente de prazos adaptado para uso **Normal** (viagens e rodovias) ou **Severo** (trânsito urbano, anda-e-para e trajetos curtos).
- Atualização rápida do odômetro (quilometragem) direto no painel com 1 clique.

### 2. 🔧 Manutenções Preventivas & Trocas de Óleo
- Alertas visuais por semáforo de criticidade: **🔴 Vencido**, **🟡 Atenção / Próximo** e **🟢 Em Dia**.
- Monitoramento duplo: por **quilometragem** (ex: a cada 5.000 km) ou por **tempo** (ex: a cada 6 meses), disparando o alerta no que vencer primeiro.
- Botão **"Dar Baixa"**: ao registrar a manutenção realizada, o sistema recalcula automaticamente a próxima revisão futura.
- Catálogo preventivo: Óleo do motor, filtros (óleo, ar, combustível, cabine), pastilhas e discos de freio, fluido de freio, velas e correias.

### 3. ⛽ Abastecimento Inteligente & Business Intelligence (BI)
- Gráficos interativos (Chart.js) com histórico dos últimos 6 meses:
  - **Volume de Combustível (Litros)**
  - **Evolução de Gastos (R$)**
- **Telemetria de Consumo:** cálculo automático do consumo médio real (**km/l**).
- **Custo por Quilômetro:** indicador em tempo real de quanto você gasta por cada km rodado (**R$/km**) e projeção de custo mensal de combustível.

### 4. 💳 IPVA, Licenciamento & Taxas com Parcelamento
- Cadastro de tributos (IPVA, Licenciamento, Seguros, DPVAT e Multas).
- **Suporte a Parcelamento:** divisão em cotas (ex: 1ª, 2ª, 3ª parcela...) com datas de vencimento e valores individuais.
- **Baixa Parcial:** quitação individual por parcela, recalculando automaticamente o saldo devedor e o total pago.
- Zero surpresas com juros ou apreensão de veículo por atraso fiscal.

### 5. 📜 Prontuário & Certificado Técnico para Revenda
- Emissão de **Certificado de Manutenção** oficial com selo de integridade mecânica.
- Histórico completo de revisões e peças trocadas, ideal para comprovar procedência e **garantir máxima valorização na Tabela FIPE** no momento da venda.
- Exportação dos dados completos em formato **CSV**.

---

## 📂 Estrutura das Views (100% em Português)

As telas do projeto seguem o padrão arquitetural do Laravel, com nomes em português claros e padronizados:

```
resources/views/
├── abastecimentos/        # Gestão de abastecimentos, consumo médio e gráficos BI
│   └── index.blade.php
├── manutencoes/           # Registro de revisões, troca de óleo e catálogo
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php
├── ipva-taxas/            # Controle de IPVA, seguros, multas e parcelas
│   └── index.blade.php
├── veiculos/              # Garagem, ficha técnica, odômetro e configurações
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── relatorios/            # Prontuário técnico, certificado e exportação CSV
│   ├── index.blade.php
│   └── certificate.blade.php
├── layouts/               # Layouts mestre (App e Guest com identidade visual)
└── dashboard.blade.php    # Painel principal de indicadores em tempo real
```

---

## 🛠️ Stack Tecnológica

- **Backend:** PHP 8.2+ e [Laravel 11](https://laravel.com)
- **Frontend:** Blade Templates, [Tailwind CSS](https://tailwindcss.com) e [Chart.js](https://www.chartjs.org/)
- **Banco de Dados:** SQLite (Desenvolvimento/Offline-first) ou MySQL / PostgreSQL
- **Autenticação:** Laravel Breeze com proteção CSRF e isolamento de dados
- **Testes:** PHPUnit / Pest com suíte de segurança e regressão

---

## 🚀 Instalação e Execução Local

### Pré-requisitos
- PHP >= 8.2 (com extensões `pdo_sqlite`, `curl`, `mbstring`, `fileinfo`)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (versão 18+) e NPM

### Passo a Passo

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/seu-usuario/siscartl.git
   cd siscartl
   ```

2. **Instale as dependências do PHP:**
   ```bash
   composer install
   ```

3. **Instale as dependências do Frontend:**
   ```bash
   npm install
   ```

4. **Configure o arquivo de ambiente (`.env`):**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Execute as Migrações e o Seeder Inicial com Dados Demo:**
   ```bash
   php artisan migrate --seed
   ```
   *(Isto criará os veículos de teste, histórico de revisões, abastecimentos e usuário padrão)*

6. **Inicie os servidores locais:**
   - Em um terminal:
     ```bash
     php artisan serve
     ```
   - Em outro terminal (para compilar os estilos):
     ```bash
     npm run dev
     ```

7. **Acesse no navegador:**
   - URL: `http://localhost:8000`
   - **E-mail:** `demo@veiculos.com`
   - **Senha:** `password`

---

## 🧪 Testes Automatizados

O sistema conta com suíte abrangente de testes automatizados cobrindo controle de acesso multi-inquilino, cálculo de prazos de manutenção, validações de abastecimento e parcelamento de taxas:

```bash
php artisan test
```

> **Status:** 37 testes automatizados executados com **100% de aprovação** (122 asserções).

---

## 🔒 Segurança

- **Isolamento de Dados:** Cada usuário acessa única e exclusivamente os veículos e dados cadastrados por sua própria conta (`user_id`).
- **Validação Estrita:** Proteção contra manipulação de formulários (IDs externos, IDOR e injeção).
- **Proteção CSRF & Rate-Limiting:** Proteção nativa contra requisições fraudulentas e ataques de força bruta.

---

## 📄 Licença

Este projeto está sob a licença [MIT](LICENSE). Sinta-se livre para utilizar, estudar e adaptar para suas necessidades.
