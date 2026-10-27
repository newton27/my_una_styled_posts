> Editor enhancements are prepared as **0.3.0**, pending native site deployment and validation. See [editor design](docs/superpowers/specs/2026-10-27-editor-enhancements-design.md). Existing 0.2.0 artifacts remain immutable.

> Motion backgrounds are being developed as **0.2.0** on `feature/motion-backgrounds`. This branch is a test candidate, not a released update. See [motion validation evidence](docs/evidence/motion-backgrounds-2026-10-27.md).

# My UNA Styled Posts

Development candidate **0.3.0**, targeting UNA 15 / PHP 8.3. The native 0.1.0→0.2.0 update has been exercised on gayjaxonline.com with a corrected language step; see the motion update evidence. The composer/photo picker have been exercised on myunaworld.com; clean native installation and disable/re-enable preservation have passed on gayjaxonline.com. Authenticated admin comment/live-count and reaction add/change/remove cycles have also passed. Standard-member/non-owner, upload lifecycle, repost and uninstall/reinstall validation remain open. This package does not change UNA core files.

Members can create editable text cards with five font styles, text size/color/alignment, ten still backgrounds, two original four-second motion loops, a custom solid color, an uploaded photo, or a photo selected from the current profile’s native UNA Photos library. A live preview adapts the card to the available width. Posts use native UNA privacy, comments, reactions and repost services.

## Preview

Open `preview/index.html` after extracting the source bundle. It uses the actual module composer and styles. Preview posting does not publish anything.

## Installation for testing

1. Back up the chosen UNA test site.
2. Extract the installer so the module is at `modules/newton/my_una_styled_posts/install/config.php`.
3. Use Studio → Apps → Downloaded for the native installation. Do not import SQL manually.
4. Open Studio → My UNA Styled Posts. Choose the enabled fonts/backgrounds and whether members can upload photos.
5. Review Memberships permissions for My UNA Styled Posts. Defaults allow confirmed Standard/Premium members to create and delete their own posts; moderators/admins retain the native additional rights.
6. Open the community composer and choose **Styled Post**, or visit `page.php?i=create-styled-post`.

Timeline is needed for feed distribution. This module does not depend on the Posts module. Photo backgrounds require the Local storage engine used by this candidate.

## Use

Write up to 500 characters, choose a background and font, adjust the size/color/alignment, then select the audience and submit. Your own uploaded photo takes precedence over presets until removed. Editing preserves the text and style choices. If an administrator disables a preset, existing posts use a readable fallback.

Manage new post auto-approval in the module's Studio settings and user posting rights in Memberships. With automatic approval disabled, the administrator must approve pending posts through the native content workflow; verify that workflow on the test site before enabling moderation for members.

## Validation

See `docs/evidence/validation.md` for the evidence and outstanding native tests. These remain release blockers; this candidate is not represented as production-ready.

Run the local checks with PHP 8.3 and Python 3:

```sh
PHP_BINARY=php python3 -m unittest discover -s tests -v
php -n tests/style.php
php -n tests/render.php
php -n tests/native-adapters.php
php -n tests/authorization.php
node --check modules/newton/my_una_styled_posts/js/composer.js
```

Browser checks use Playwright: `node tests/editor.cjs`. Set `CODEX_PRIMARY_RUNTIME_NODE_MODULES` if Playwright is supplied by the runtime rather than installed in the project. `CHROME_EXECUTABLE_PATH` and `CHROMIUM_PACKAGE_ROOT` support a separately supplied packaged browser.

## Licensing

Original module code and decorative vector artwork are MIT licensed. Selected native registration schemas are adapted from UNA, Inc.'s MIT-licensed Posts module. Font licenses accompany the font files; bundled fonts are served locally. See `LICENSE` and `THIRD_PARTY.md`.

## Release preparation

See `docs/evidence/native-lifecycle-2026-10-26.md` for the latest native results and open findings, and `docs/evidence/release-readiness-2026-10-26.md` for the earlier gate review. RC4 includes the corrected author-count language placeholder, native comment layout compatibility, live comment/reaction counts and a native reaction picker. It is a test candidate, not a final Marketplace release. Older evidence files and immutable ZIPs describe their original checkpoints.
