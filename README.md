# Floaty Button

A lightweight, customizable floating call-to-action button for WordPress sites.

> WordPress.org listing: **coming soon** – add the link here as soon as the plugin is published.

---

## What it does

Floaty Button adds a persistent floating button to your WordPress pages. You can point it to an external URL, open a booking experience inside an iframe modal, or start a WhatsApp conversation—all while keeping the footprint small, accessible, and easy to configure.

## Key features

- Floating CTA that works on every page and can be positioned bottom-left or bottom-right.
- Multiple actions: open a link, launch an iframe modal (ideal for booking widgets like Calendly or NexHealth), or start WhatsApp chat.
- WhatsApp-ready template with native styling and optional prefilled message.
- Apointoo/Reserve with Google integration via Merchant ID.
- Custom CSS field for quick styling overrides.
- DataLayer event (`floaty_click` by default) emitted on click for Google Tag Manager tracking.
- Built with WordPress security best practices: sanitized input, capability checks, and nonce-protected settings.

## Use cases

- Promote booking or scheduling flows without redesigning the page layout.
- Provide instant WhatsApp support or sales conversations.
- Embed third-party booking widgets inside a modal to keep users on-site.
- Highlight time-bound campaigns or consultation CTAs anywhere on the site.

## Installation

1. Download or clone this repository and copy the `floaty-button` folder into `wp-content/plugins/`.
2. From **WordPress Admin → Plugins**, activate **Floaty Button**.
3. Open **Settings → Floaty Button** to configure the button.

> Optional: add screenshots or a short GIF here that shows the button on the front end and the admin settings page.

## Configuration

All options live under **Settings → Floaty Button** with three tabs: **General**, **WhatsApp**, and **Apointoo Booking**.

### General tab

- **Enable plugin**: Toggle the button on or off site-wide.
- **Button template**: Choose **Default** (generic CTA) or **WhatsApp** (WhatsApp-first styling).
- **Button label**: Text shown on the button (default: “Book now”).
- **Button position**: Bottom-right or bottom-left.
- **Action type**:
  - **Open link**: Open a URL with target `_blank` (new tab) or `_self` (same tab).
  - **Open iframe modal**: Load a URL inside a modal overlay (great for embedded booking widgets).
- **Link URL / Iframe URL**: Target URLs for the selected action.
- **DataLayer event name**: Event name pushed to `dataLayer` when clicked (`floaty_click` by default). The payload includes `floatyActionType` and `floatyLabel` for easier GTM mapping.
- **Custom CSS**: Inline CSS injected on the front end for quick styling overrides.

### WhatsApp tab

- **Phone number**: Enter in international format (digits only).
- **Prefilled message**: Optional starter text that appears when the chat opens.
- Pairing the WhatsApp template with these fields enables a native-feeling WhatsApp entry point.

### Apointoo tab (Reserve with Google)

- **Enable Apointoo integration**: Turn on the connection for Reserve with Google support.
- **Merchant ID**: Enter the Merchant ID provided by Appointoo. Contact **support@vizuh.com** if you need one.

## FAQ

**Does the plugin add its own styling?**  Yes. It ships with lightweight styles for the default and WhatsApp templates, plus a Custom CSS field so you can override anything without editing theme files.

**What does the DataLayer event look like?**  On click, Floaty Button pushes `{ event: '<your_event_name>', floatyActionType: 'link' | 'iframe_modal' | 'whatsapp', floatyLabel: '<label>' }` to `window.dataLayer` if it exists.

**How do I disable the button temporarily?**  Toggle **Enable plugin** off in the General tab; settings are preserved for when you re-enable it.

**Does it work with caching plugins?**  Yes. The button renders via standard WordPress hooks and does not rely on dynamic PHP endpoints that caches might block.

## Development

### Local install steps

1. Clone the repo into `wp-content/plugins/floaty-button` (or symlink it from your plugin workspace).
2. Activate **Floaty Button** from the Plugins screen.
3. There is no build step—PHP and asset files are ready to run. Clear any page cache after updates.

### Project structure

- `floaty.php`: Plugin bootstrap and hooks.
- `includes/admin/`: Settings page, tabs, and sanitization.
- `includes/frontend/`: Front-end rendering and assets enqueueing.
- `includes/helpers.php`: Shared helpers and defaults.
- `assets/`: Logos and style assets for the admin and front end.

### PHPCS commands

- Run checks: `phpcs --standard=WordPress --ignore=vendor --extensions=php .`
- Auto-fix where possible: `phpcbf --standard=WordPress --ignore=vendor --extensions=php .`

### Versioning

- Follows **SemVer** (`MAJOR.MINOR.PATCH`).
- Update plugin headers and the `Stable tag` in the WordPress.org readme before tagging releases.
- Document notable changes in the GitHub Releases page or changelog once the plugin is published.

## Roadmap

- Publish the plugin on WordPress.org and replace the placeholder link above.
- Add more button positions and animations.
- Provide preset color themes and icon options.

## License

GPLv2 or later. See the [GNU GPL v2.0](https://www.gnu.org/licenses/gpl-2.0.html) for details.

## Credits

Maintained by Vizuh and Apointoo.

Contributors: vizuh, hugoc, Atroci, andreluizsr90.
