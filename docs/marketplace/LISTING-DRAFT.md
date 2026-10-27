# My UNA Styled Posts — listing draft

Status: RC1 preparation, not yet approved for final distribution.
Vendor: My UNA World. Native module version: 0.1.0.
Target: UNA 15 RC2 / PHP 8.3; broader UNA 15 compatibility requires validation.

Give short thoughts a visual style. Members create editable quote cards with expressive fonts, colors and backgrounds, while using UNA's native audience controls.

Features:
- Five fonts, including locally bundled Caveat and Playfair Display.
- Font size, text color, alignment and custom solid background color.
- Ten color, gradient, pattern and illustrated background presets.
- Upload JPG, PNG or WebP backgrounds, or choose an existing photo from the current profile's native UNA Photos library.
- Live preview and up to 500 Unicode characters, with responsive text fitting.
- Private, independent background copies governed by the styled post audience.
- Native UNA comments, reactions and Timeline integration (remaining authenticated release tests required).
- Studio controls for available fonts/backgrounds/uploads and UNA membership permissions.

No UNA core files are modified. The Posts module is not required. Photos is optional for the existing-photo picker; uploads and presets work independently. Timeline is needed for feed distribution. Background copies use the installed Local storage engine.

Limits: 8 MiB, 16 megapixels, 8000 pixels per dimension. Albums/avatar photos are not included. The picker currently downloads original images for thumbnails. Dedicated global search discovery is not included. English interface; bundled font licenses accompany the module.

Listing category, price, marketplace-specific media dimensions and final compatibility range remain to be confirmed. Preview screenshots are demonstration material; they do not contain member photos.
