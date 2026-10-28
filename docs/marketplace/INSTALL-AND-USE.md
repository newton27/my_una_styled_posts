# Install and use My UNA Styled Posts

This is a release candidate for testing, not a final marketplace release.

1. Back up a disposable UNA 15 RC2 / PHP 8.3 test installation.
2. Extract the inner install ZIP into public_html/modules/. The resulting path must be modules/newton/my_una_styled_posts/install/config.php.
3. Open Studio → Apps → Downloaded and install My UNA Styled Posts. Do not import SQL manually.
4. Check the Studio launcher and information popup, then configure fonts, backgrounds, uploads and automatic approval.
5. Review membership posting/edit/delete permissions. Check guest visibility before public deployment.
6. Use Styled Post in the community composer, or open create-styled-post. Enter text, choose a style and an audience, then Post.
7. Choose from my photos shows the current profile's active native Photos entries. A selected photo is copied; the post's audience determines who sees that copy. Deleting the original does not remove the post background.
8. Use Edit to change words/style or replace/remove the background.

This full installer is not a native updater for development installations. Do not reinstall an existing site or apply its uninstall SQL to update files. Existing development sites require a separately reviewed migration/maintenance procedure, including language updates where needed.

The source kit includes an interactive demonstration in source/preview/index.html. Its Post control does not publish anything.

## Motion backgrounds (0.2.0 candidate)

Motion includes two built-in four-second loops and uploads of MP4, WebM, MOV or animated GIF. Upload limits are 3–5 seconds, 8 MiB and 1280 pixels on either side. GIFs are converted to silent MP4; their motion preview appears after conversion. Built-in loops work without conversion.

Uploads require UNA's configured FFmpeg, PHP fileinfo/proc_open, ffprobe beside FFmpeg or in `/usr/bin` or `/usr/local/bin`, and a working native local conversion cron. Remote-only conversion is not supported in this iteration. Keep the page's processing message visible; do not repeatedly submit while waiting. Failed conversion can be replaced through Edit; the previous background remains available.

For an existing **0.1.0** installation, back up first and extract the dedicated updater ZIP into the module's `updates/` directory. Verify `modules/newton/my_una_styled_posts/updates/update_0.1.0_0.2.0/install/config.php`, then run the native Studio update. Do not replace this with an uninstall/reinstall.


## 0.3.0 editor candidate

Text backdrop offers None or Box, a color, opacity and padding. Adjust image offers horizontal/vertical position, zoom and Reset. Crops preserve the original file; the framing follows the card's shape. Changing to another image resets framing. Text direction offers Automatic, Left to right and Right to left. Amiri is bundled locally; Arabic Modern, Friendly and Book use the device's fonts and may look different on different devices.

Find background photos opens Pexels or Unsplash separately so your draft stays open. Download a free image there, check its license and usage terms, then use Upload photo. This is a set of links, not an integrated stock-photo search. Browse Styled Posts now offers Older posts and respects the audience of every post.

### Administrator font packs

Create `storage/nw_styled_posts_fonts/` under the UNA site and upload local WOFF2 fonts and their licenses there. This directory is outside module source so updates preserve it. Add `fonts.json` containing a list such as:

```json
[{"id":"my_arabic","label":"My Arabic font","file":"MyArabic.woff2","license":"MyArabic-LICENSE.txt"}]
```

Use at most 32 font entries, each with a unique lowercase ID (letter first, then letters/digits/underscores, up to 24 characters), a label up to 80 characters, a WOFF2 file up to 2 MiB, and a nonempty license text file. Filenames must be simple basenames. No CSS, PHP or remote URLs are accepted. Install only fonts licensed for website embedding and redistribution. Studio reports ignored entries. Members can select registered fonts but cannot install them. This version uses administrator file upload through the host's file manager; it has no Studio font-upload button. Removing a font makes affected cards use a readable fallback. Fonts are publicly served, not private media.

### Upgrade route

Sites on 0.1.0 must first apply the existing 0.1.0-to-0.2.0 updater, then the 0.2.0-to-0.3.0 updater. Do not copy 0.3.0 source into an updater labelled 0.2.0. Existing immutable 0.2.0 ZIPs remain unchanged. Native 0.3.0 validation and motion upload testing are release gates; a prepared package is not evidence of a completed site update.

## 0.3.1 motion library

Open **Motion backgrounds** to choose from fourteen original silent loops. Each thumbnail has a name; selecting it updates the live preview. The twelve new nature/abstract choices are illustrated animations, not stock footage. Upload video or GIF is available below the grid when enabled by the administrator. Playback still respects reduced-motion preferences and offers Play/Pause.

Update an existing 0.3.0 installation with the native 0.3.0 → 0.3.1 updater in Studio. Existing posts, media and settings are preserved; twelve new background switches appear in module settings.

### 0.3.2 corrective update

0.3.2 fixes editor startup while larger pages are still loading. The tested native route on the authorized test site is0.3.0→0.3.1→0.3.2; do not distribute0.3.1alone. The0.3.1→0.3.2 updater changes files and clears caches only. A direct0.3.0→0.3.2 updater and fresh0.3.2installation still require separate validation before wider release.
