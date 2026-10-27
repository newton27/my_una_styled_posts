# My UNA Styled Posts

**Turn a few words into a colorful, expressive post.**

My UNA Styled Posts lets members create editable quote cards and short statements with fonts, colors, patterns, and photo backgrounds. A live preview shows their design before posting.

## Features

- Five font styles, including handwritten and editorial fonts.
- Adjustable font size, text color, and alignment.
- Ten backgrounds featuring colors, gradients, patterns, and illustrations.
- Custom solid background colors.
- **Upload photo** and **Choose from my photos** options.
- Up to 500 characters, with text fitting for smaller screens.
- Native UNA audience controls.
- Editable text and styling after posting.
- Integration with UNA comments, reactions, and Timeline.

## Webmaster controls

Choose which fonts and backgrounds members can use, enable or disable photo uploads, configure automatic approval, and manage posting permissions through UNA Memberships.

## Photo backgrounds

Uploaded backgrounds support JPG, PNG, and WebP files up to 8 MiB. Selecting an existing photo creates an independent background copy, shared with the Styled Post’s chosen audience.

## Requirements

UNA 15 and PHP 8.3. Native Photos is optional and enables the existing-photo picker; Albums and avatars are not included. Timeline is required for feed distribution. This candidate uses Local storage for background copies.

This standalone module does not require Posts and does not modify UNA core files. Bundled fonts are served locally.

**By My UNA World · Version 0.1.0**

**Release status:** Release candidate. Remaining member-role, upload lifecycle, repost and uninstall/reinstall validation is pending.

---

Internal publishing notes — omit this section from the public description:

- Price and Marketplace category have not been selected by the vendor.
- The My UNA World account’s Manage Products view has no existing Styled Posts listing.
- The Marketplace Add control opens a blank New Product dialog; no form fields or save control render. No listing was submitted or saved.
- Actual live tests include UNA 15.0.0-RC3 / PHP 8.3.6 / MariaDB 10.11.14. Earlier target/reference was UNA 15 RC2; metadata declares 15.0.x. Broader platform coverage is not certified.
- The existing-photo picker supports native Photos, not Albums or avatars. Current thumbnails download source images. Dedicated global search discovery is not included.
- Image limits are 8 MiB, 16 megapixels, and 8000 pixels per dimension.
- Use the current candidate installer, not GitHub’s automatic Source code ZIP. No native updater is provided for development installations.
- The vendor-created My_UNA_Styled_Post release points to e05cf2c and contains no installer assets. Its tag/release has not been modified by this test run.
