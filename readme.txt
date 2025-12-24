=== Floaty Book Now Chat ===
Contributors: vizuh, hugoc, atroci, andreluizsr90
Tags: booking, appointments, whatsapp, chat, modal
Requires at least: 6.4
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight floating “Book Now” button + WhatsApp chat. Open a link, launch a modal, and track clicks via dataLayer.

== Description ==

Floaty adds a clean floating call-to-action button to your site so visitors can **book faster** or **start a WhatsApp chat**—without digging through menus.

Choose your mode:
* WhatsApp mode: click-to-chat with optional prefilled message
* Custom mode: open a link or launch an iframe modal (great for booking widgets)

= Key Features =
* Floating CTA on every page (bottom-left / bottom-right)
* Modes: WhatsApp or Custom
* Custom mode actions: open link or iframe modal
* dataLayer click event for GTM/GA4 tracking
* Custom CSS field for quick styling overrides
* Lean, WordPress-native settings UI

= Apointoo Booking (Optional) =
If you use Apointoo, Floaty includes an optional integration tab for booking configuration used in Google Search/Maps booking flows where available via your provider setup.

Need a Merchant ID? Email support@vizuh.com.

Note: Booking visibility on Google Search/Maps depends on eligibility and provider setup.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install via the WordPress Plugins screen.
2. Activate the plugin through the 'Plugins' screen.
3. Go to Settings → Floaty.
4. Enable Floaty and choose your mode (WhatsApp or Custom).

== Frequently Asked Questions ==

= Can I use Floaty without WhatsApp? =
Yes. Use Custom mode to open a link or iframe modal.

= Can I use Floaty without Apointoo? =
Yes. The Apointoo tab is optional.

= Does Floaty guarantee booking visibility on Google Search/Maps? =
No. That depends on eligibility and provider setup. Floaty provides the on-site CTA and integration settings.

= What does the dataLayer event look like? =
Floaty pushes an event to `window.dataLayer` (if present):
`event`, `floatyActionType`, and `floatyLabel`.

== Screenshots ==

1. General tab (enable, label, position, mode, event name)
2. WhatsApp tab (phone + prefilled message)
3. Custom tab (link/modal + URL fields + custom CSS)
4. Apointoo Booking tab (enable + Merchant ID)
5. Frontend example (floating button on a page)

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
