# Floaty Button Plugin Walkthrough

## Overview
The Floaty Button plugin adds a customizable floating CTA button to your WordPress site. It can open a link in a new tab or display an iframe in a modal window while pushing a dataLayer event for tracking.

**Security goal:** This plugin aims to comply with WordPress.org’s plugin guidelines and the WordPress Plugin Security Handbook, prioritizing least privilege, full input validation/sanitization, and secure use of the WordPress APIs.

## Installation
1. Place the `floaty-button` folder in your `wp-content/plugins/` directory.
2. Activate **Floaty Button** from **Plugins** in the WordPress Admin Dashboard.

## Configuration
Navigate to **Settings > Floaty Button** to configure the plugin.

### Main Settings
- **Enable Plugin**: Toggle to show or hide the button globally.
- **Button Label**: Text displayed on the button (e.g., "Book Now").
- **Button Position**: Choose where the button appears (Bottom Right or Bottom Left).
- **Action Type**:
  - **Open Link**: Opens a URL (e.g., WhatsApp, calendar, booking link) in the selected target.
  - **Open Iframe Modal**: Displays a URL inside a modal popup (e.g., NexHealth, Calendly).
- **Link URL**: URL to open when "Open Link" is selected.
- **Link Target**: `_blank` (new tab) or `_self` (same tab).
- **Iframe URL**: URL to embed when "Open Iframe Modal" is selected.
- **DataLayer Event Name**: Event name pushed to `dataLayer` on click (default: `floaty_click`).
- **Custom CSS**: Additional CSS injected on the front end for styling overrides.

### DataLayer Event
When the button is clicked, the plugin pushes an event with core metadata:

```js
{
  event: 'floaty_click', // or your configured event name
  floatyActionType: 'link' | 'iframe_modal',
  floatyLabel: 'Book Now'
}
```

### Customizing Styles
Use the **Custom CSS** field to override colors, spacing, or positioning. Example:

```css
.floaty-button {
    background-color: #ff0000; /* Red button */
}

.floaty-position-bottom_left {
    left: 40px;
}
```

## Requirements

- WordPress 6.4 or later (tested up to 6.6)
- PHP 8.0 or later

## Licensing

Floaty Button is released under the **GPLv2 or later** license. See https://www.gnu.org/licenses/gpl-2.0.html for the full text and ensure all bundled assets remain GPL-compatible.
