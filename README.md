=== Floaty Button ===
Contributors: vizuh, hugoc, Atroci, andreluizsr90
Tags: floating button, cta, whatsapp, booking
Requires at least: 6.4
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

<div align="center">
  <a href="https://apointoo.com" target="_blank">
    <img src="assets/images/apointoo-logo.png" alt="Appointo Logo" width="200">
  </a>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <a href="https://vizuh.com" target="_blank">
    <img src="assets/images/vizuh-logo.png" alt="Vizuh Logo" width="150">
  </a>
  <h1>Floaty Button</h1>
  <p>
    <strong>A customizable floating CTA button for WordPress.</strong>
  </p>
  <p>
    <a href="#-english">🇺🇸 English</a> &nbsp; | &nbsp; <a href="#-português-portuguese">🇧🇷 Português</a>
  </p>
</div>

---

<a name="english"></a>
## 🇺🇸 English

### Overview

The **Floaty Button** plugin adds a customizable floating CTA button to your WordPress site. It is designed to be lightweight, secure, and easy to configure. Whether you need a simple link, a booking modal, or a direct WhatsApp chat, Floaty Button handles it with style.

### ✨ Features

*   **🎨 Customizable Button:** Change the label, position (bottom right/left), and action.
*   **🔗 Multiple Actions:**
    *   Open a link (new/same tab).
    *   Display an iframe modal (perfect for booking widgets like NexHealth, Calendly).
    *   Open a WhatsApp chat.
*   **💬 WhatsApp Integration:** Dedicated WhatsApp template with native styling and prefilled messages.
*   **📅 Google Reserve Integration:** Add your Appointo Merchant ID to enable "Reserve with Google" features.
*   **📊 DataLayer Tracking:** Automatically pushes events to `dataLayer` for easy tracking with Google Tag Manager.
*   **💅 Custom CSS:** Add your own CSS directly from the settings page.

> **Security Goal:** This plugin aims to comply with WordPress.org’s plugin guidelines and the WordPress Plugin Security Handbook, prioritizing least privilege, full input validation/sanitization, and secure use of the WordPress APIs.

### 🚀 Installation

1.  Download the plugin folder `floaty-button`.
2.  Place it in your `wp-content/plugins/` directory.
3.  Activate **Floaty Button** from **Plugins** in the WordPress Admin Dashboard.

### ⚙️ Configuration

Navigate to **Settings > Floaty Button** to configure the plugin.

#### Main Settings
*   **Enable Plugin:** Toggle to show or hide the button globally.
*   **Button Template:** Choose between "Default Button" or "WhatsApp Floating Button".
*   **Button Label:** Text displayed on the button (e.g., "Book Now").
*   **Button Position:** Choose where the button appears (Bottom Right or Bottom Left).
*   **Action Type:**
    *   **Open Link:** Opens a URL (e.g., calendar, booking link) in the selected target.
    *   **Open Iframe Modal:** Displays a URL inside a modal popup (e.g., NexHealth, Calendly).
*   **Link URL:** URL to open when "Open Link" is selected.
*   **Link Target:** `_blank` (new tab) or `_self` (same tab).
*   **Iframe URL:** URL to embed when "Open Iframe Modal" is selected.
*   **DataLayer Event Name:** Event name pushed to `dataLayer` on click (default: `floaty_click`).
*   **Custom CSS:** Additional CSS injected on the front end for styling overrides.

#### WhatsApp Settings
*   **WhatsApp Phone Number:** Enter your number in international format (digits only).
*   **Prefilled Message:** Optional message to start the conversation.

#### Google Reserve Integration
*   **Enable Google Reserve:** Toggle to enable the integration.
*   **Merchant ID:** Enter the Merchant ID provided by Appointo.
    > To request a Merchant ID, please contact **support@vizuh.com**.

### 📊 DataLayer Event

When the button is clicked, the plugin pushes an event with core metadata:

```js
{
  event: 'floaty_click', // or your configured event name
  floatyActionType: 'link' | 'iframe_modal' | 'whatsapp',
  floatyLabel: 'Book Now' // or 'WhatsApp'
}
```

### 🎨 Customizing Styles

Use the **Custom CSS** field to override colors, spacing, or positioning. Example:

```css
.floaty-button {
    background-color: #ff0000; /* Red button */
}

.floaty-position-bottom_left {
    left: 40px;
}
```

### 📋 Requirements

*   WordPress 6.4 or later (tested up to 6.6)
*   PHP 7.4 or later

### 📄 Licensing

Floaty Button is released under the **GPLv2 or later** license. See [GNU Licenses](https://www.gnu.org/licenses/gpl-2.0.html) for the full text.

**Contributors:** vizuh, hugoc, Atroci, andreluizsr90

---

<a name="portuguese"></a>
## 🇧🇷 Português (Portuguese)

### Visão Geral

O plugin **Floaty Button** adiciona um botão de CTA flutuante personalizável ao seu site WordPress. Ele foi projetado para ser leve, seguro e fácil de configurar. Seja para um link simples, um modal de agendamento ou um chat direto no WhatsApp, o Floaty Button resolve com estilo.

### ✨ Funcionalidades

*   **🎨 Botão Personalizável:** Altere o rótulo, a posição (inferior direito/esquerdo) e a ação.
*   **🔗 Múltiplas Ações:**
    *   Abra um link (nova/mesma aba).
    *   Exiba um modal iframe (perfeito para widgets de agendamento como NexHealth, Calendly).
    *   Abra uma conversa no WhatsApp.
*   **💬 Integração com WhatsApp:** Modelo dedicado do WhatsApp com estilo nativo e mensagens pré-preenchidas.
*   **📅 Integração Google Reserve:** Adicione seu Merchant ID do Appointo para habilitar recursos do "Reserve com Google".
*   **📊 Rastreamento DataLayer:** Envia automaticamente eventos para o `dataLayer` para fácil rastreamento com o Google Tag Manager.
*   **💅 CSS Personalizado:** Adicione seu próprio CSS diretamente da página de configurações.

> **Objetivo de Segurança:** Este plugin visa cumprir as diretrizes de plugins do WordPress.org e o Manual de Segurança de Plugins do WordPress, priorizando o privilégio mínimo, validação/sanitização completa de entrada e uso seguro das APIs do WordPress.

### 🚀 Instalação

1.  Baixe a pasta do plugin `floaty-button`.
2.  Coloque-a no diretório `wp-content/plugins/` do seu site.
3.  Ative o **Floaty Button** no menu **Plugins** do Painel Administrativo do WordPress.

### ⚙️ Configuração

Navegue até **Configurações > Floaty Button** para configurar o plugin.

#### Configurações Principais
*   **Habilitar Plugin:** Ative ou desative o botão globalmente.
*   **Modelo do Botão:** Escolha entre "Botão Padrão" ou "Botão Flutuante WhatsApp".
*   **Rótulo do Botão:** Texto exibido no botão (ex: "Agendar Agora").
*   **Posição do Botão:** Escolha onde o botão aparece (Inferior Direito ou Inferior Esquerdo).
*   **Tipo de Ação:**
    *   **Abrir Link:** Abre uma URL (ex: calendário, link de agendamento) no destino selecionado.
    *   **Abrir Modal Iframe:** Exibe uma URL dentro de um popup modal (ex: NexHealth, Calendly).
*   **URL do Link:** URL para abrir quando "Abrir Link" for selecionado.
*   **Destino do Link:** `_blank` (nova aba) ou `_self` (mesma aba).
*   **URL do Iframe:** URL para incorporar quando "Abrir Modal Iframe" for selecionado.
*   **Nome do Evento DataLayer:** Nome do evento enviado ao `dataLayer` no clique (padrão: `floaty_click`).
*   **CSS Personalizado:** CSS adicional injetado no front-end para substituições de estilo.

#### Configurações do WhatsApp
*   **Número de Telefone WhatsApp:** Digite seu número no formato internacional (apenas dígitos).
*   **Mensagem Pré-preenchida:** Mensagem opcional para iniciar a conversa.

#### Integração Google Reserve
*   **Habilitar Google Reserve:** Ative para habilitar a integração.
*   **Merchant ID:** Insira o Merchant ID fornecido pelo Appointo.
    > Para solicitar um Merchant ID, entre em contato com **support@vizuh.com**.

### 📊 Evento DataLayer

Quando o botão é clicado, o plugin envia um evento com metadados principais:

```js
{
  event: 'floaty_click', // ou o nome do evento configurado
  floatyActionType: 'link' | 'iframe_modal' | 'whatsapp',
  floatyLabel: 'Book Now' // ou 'WhatsApp'
}
```

### 🎨 Personalizando Estilos

Use o campo **CSS Personalizado** para substituir cores, espaçamento ou posicionamento. Exemplo:

```css
.floaty-button {
    background-color: #ff0000; /* Botão vermelho */
}

.floaty-position-bottom_left {
    left: 40px;
}
```

### 📋 Requisitos

*   WordPress 6.4 ou superior (testado até 6.6)
*   PHP 7.4 ou superior

### 📄 Licenciamento

O Floaty Button é lançado sob a licença **GPLv2 ou posterior**. Veja [Licenças GNU](https://www.gnu.org/licenses/gpl-2.0.html) para o texto completo.

**Colaboradores:** vizuh, hugoc, Atroci, andreluizsr90
