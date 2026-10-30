<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

# Diretrizes do Projeto (AGENTS.md)
**Projeto:** Sistema de Gerenciamento de Veículos e Manutenção Preventiva
**Stack Principal:** PHP (Laravel), Tailwind CSS, Docker, Git, Banco Relacional + SQLite (Offline)

---

## 1. Idioma e Nomenclatura (Regra Estrita: 100% Português)
- **Nomes de Arquivos e Pastas:** TODOS os arquivos criados (Controllers, Models, Services, Repositories, Migrations, Views Blade, Componentes, Scripts JS/CSS) DEVEM ter nomes em **Português** (ex.: `VeiculoController.php`, `ManutencaoService.php`, `TrocaOleo.php`, `painel-veiculo.blade.php`).
- **Código e Banco de Dados:** Tabelas, colunas de migrations, variáveis, métodos e rotas devem ser escritos em português claro e semântico (ex.: `quilometragem_atual`, `data_proxima_troca`, `registrarManutencao()`).
- **Textos da Interface:** Toda a interface, mensagens de validação, alertas e logs devem estar em Português (pt-BR).

---

## 2. Identidade Visual: "Manual Técnico & Telemetria Clara" (Inspiração Raio-X / Infográfico Automotivo)

O sistema deve transmitir a sensação imediata de um **Manual de Engenharia Automotiva e Infográfico Técnico de Fórmula 1**, porém 100% moderno, iluminado, vivo e rápido de usar no celular.

### 2.1 Paleta de Cores (100% Light Mode — Proibido Modo Escuro)
- **Fundo da Página:** Branco Papel Técnico (`bg-[#F8F8F6]` ou `bg-stone-50`) e Cinza Diagrama Claro (`bg-zinc-100`) para colunas de itens.
- **Superfície dos Cards e Inputs:** Branco Puro (`bg-white`).
- **Traço Técnico (Bordas e Textos):** Preto Nanquim (`border-zinc-900`, `text-zinc-900`).
- **Cores Vivas de Telemetria (Destaques e Callouts):**
  - **Vermelho Telemetria (`#E11D48` / `bg-rose-600` ou `bg-red-600`):** Cor principal da marca. Usada nos pontos de conexão (bolinhas de alerta), linhas guia de atenção, botões primários de destaque e revisões vencidas.
  - **Âmbar Técnico (`bg-amber-500`):** Avisos de revisão próxima (2.000 km ou 40/20/5 dias).
  - **Verde Pista (`bg-emerald-600`):** Componentes 100% em dia.

### 2.2 Elementos Gráficos Obrigatórios em Todas as Telas
1. **Bordas de Diagrama (Zero Sombras Difusas):**
   - Nunca use sombras borradas (`shadow-lg`).
   - Use bordas sólidas pretas de `1px` (`border border-zinc-900`) para cards normais e `2px` (`border-2 border-zinc-900`) com sombra dura deslocada (`shadow-[3px_3px_0px_#18181b]`) para caixas em destaque, imitando o destaque da "Caixa da direção" dos manuais mecânicos.
   - Cantos sempre retos ou quase retos (`rounded-none` ou `rounded-sm`).
2. **Pontos e Linhas de Chamada (Leader Lines / Callouts):**
   - Sempre que mostrar o status de uma peça ou alerta do veículo, use o padrão visual de infográfico: um ponto colorido (`w-3 h-3 rounded-full bg-red-600`) conectado a uma linha fina (`border-t-2 border-red-600` ou `border-zinc-900`) levando até o card da peça.
3. **Listas e Rótulos Numerados (Estilo Catálogo de Peças):**
   - Todo campo de formulário, etapa ou lista de peças deve começar com índice numérico monoespaçado: `01. ÓLEO DO MOTOR`, `02. FILTRO DE ÓLEO`, `03. PASTILHAS DE FREIO` (`font-mono text-xs font-bold uppercase tracking-wider`).
4. **Blocos Verticais com Ícones Sólidos (Padrão Infográfico):**
   - Para seleção rápida no celular (ex.: escolher o que trocou sem digitar), use cards verticais com fundo cinza claro (`bg-zinc-100`), borda preta no clique, ícone automotivo preto bem forte e centralizado no topo (motor, óleo, freio, bateria, combustível) e título em negrito abaixo.

---

## 3. Responsividade (Mobile-First Obrigatório)
- **Pensamento Mobile em Cada Componente:** Todo componente ou tela criada DEVE ser projetado primeiro para a tela do celular (uso rápido dentro do carro ou na oficina) e depois expandido para o desktop (`sm:`, `md:`, `lg:`).
- **Ergonomia no Celular:**
  - Botões e áreas clicáveis devem ter altura mínima confortável para o toque do polegar (`min-h-[48px]`).
  - Tabelas grandes no desktop devem se transformar automaticamente em **Cards compactos** no celular (nunca gerar rolagem horizontal quebrada).
  - Menus e ações principais (como "Atualizar KM" ou "Registrar Troca") devem ficar sempre acessíveis na parte inferior ou em destaque imediato no mobile.

---

## 4. UX para Preenchimento Rápido (Zero Atrito / Anti-Burocracia)
O usuário-alvo busca praticidade máxima e não tem hábito de preencher planilhas complexas. O sistema deve trabalhar pelo usuário:
- **Mínimo de Campos Obrigatórios:** Nunca crie formulários longos. Peça apenas o essencial (ex.: para registrar uma troca, bastam 2 ou 3 cliques). Campos extras devem ficar ocultos em um botão opcional *"Adicionar mais detalhes"*.
- **Valores pré-preenchidos (Smart Defaults):**
  - Data do serviço sempre vem preenchida com a **data de hoje** (`now()`).
  - Quilometragem do serviço já vem sugerida com base na `quilometragem_atual` do veículo.
  - Tipo de óleo e peças já vêm sugeridos com base na última troca ou no catálogo do veículo.
- **Seleção em vez de Digitação:** Prefira botões rápidos de seleção (chips/tags clicáveis como `[+ 500 km]`, `[+ 1.000 km]`, `[Óleo + Filtro]`, `[Pastilha de Freio]`) em vez de obrigar o usuário a digitar textos longos.
- **Feedback Instantâneo:** Ao salvar qualquer dado, mostre de forma clara e visual quando será a próxima revisão, sem exigir recarregamentos lentos ou múltiplos passos.

---

## 5. Padrão Arquitetural no Laravel
- **Desacoplamento:** Mantenha os Controllers enxutos. Toda regra de negócio (cálculo de vencimento por km/tempo, disparo de alertas, integração FIPE) deve ficar isolada em classes `Services` (ex.: `CalculadoraRevisaoService.php`).
- **Componentização no Blade:** Divida a interface em componentes Blade reutilizáveis em português (`<x-cartao-veiculo />`, `<x-modal-atualizar-km />`, `<x-alerta-vencimento />`) garantindo que todos já nasçam responsivos e no tema claro.
