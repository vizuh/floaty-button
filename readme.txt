=== Floaty Book Now Chat ===
Contributors: vizuh, hugoc, andreluizsr90, atroci
Tags: booking, call to action, whatsapp, chat, button
Requires at least: 6.4
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Floating “book now” and WhatsApp chat button with optional iframe modal and GTM-ready click events.

== Description ==
Floaty Book Now Chat adds a lightweight floating call-to-action button that can open a link, launch an iframe modal for booking widgets, or start a WhatsApp chat with an optional prefilled message. Configure label, position, link targets, DataLayer event names, and Apointoo Merchant ID from a single settings page.

== Installation ==
1. Upload the `floaty-book-now-chat` folder to the `/wp-content/plugins/` directory or install via the Plugins screen.
2. Activate **Floaty Book Now Chat** through the **Plugins** menu.
3. Go to **Settings → Floaty Book Now Chat** to configure the button.

== Frequently Asked Questions ==
= Where do I change the button text or action? =
All options are in **Settings → Floaty Book Now Chat** under the General, WhatsApp, and Apointoo tabs.

= Does it support WhatsApp without a link URL? =
Yes. Choose the WhatsApp template and enter a phone number (international format) and optional prefilled message.

= How do I embed a booking widget in a modal? =
Set **Action type** to **Open iframe modal** and paste the iframe URL. The modal loads your booking widget in place.

= Does it emit a Google Tag Manager event? =
Yes. On click it pushes a DataLayer event name of your choice (default `floaty_click`) along with the action type and label.

== Screenshots ==
1. Front-end floating button examples (default and WhatsApp templates).
2. Settings screen showing General, WhatsApp, and Apointoo tabs.

== Changelog ==
= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 1.0.0 =
First public release with booking link, iframe modal, and WhatsApp options.
