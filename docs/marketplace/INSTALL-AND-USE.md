# Install and use My UNA Styled Posts 0.3.2

Requirements: UNA 15 and PHP 8.3. Native Photos is optional for Choose from my photos; Timeline is required for feed distribution. This module does not require Posts or modify UNA core.

## New installation

1. Back up the site.
2. Extract `my-una-styled-posts-0.3.2-install.zip` into the site's `modules/` directory. This ZIP contains `newton/my_una_styled_posts/`. The final path must be `modules/newton/my_una_styled_posts/install/config.php`.
3. If uploading through a file manager, upload only the actual `my_una_styled_posts` directory under `modules/newton/`. Avoid duplicated `modules/modules/` or `newton/newton/` paths. Development kits may contain a nested `source/modules/` directory; the kit itself is not the installer.
4. Open Studio → Apps → Downloaded, inspect My UNA Styled Posts, then Install.
5. Open the module's Studio settings. Configure fonts, backgrounds, photo/motion uploads and automatic approval. Review posting/edit/delete permissions through Memberships.
6. Create a Styled Post, enter up to 500 characters, choose its design and audience, and Post. Use Edit to change words or styling afterward.

## Design and motion

Text backdrop offers a readable color box with opacity and padding. Image framing saves position and zoom without altering the uploaded original; Reset restores centered framing. Automatic/LTR/RTL text direction supports Arabic and mixed-language quotes. Selecting Amiri changes the font; it does not translate text or interface labels.

Motion backgrounds provides fourteen original silent four-second loops, with named thumbnails and Play/Pause. They respect reduced-motion preferences and work without server conversion. The nature choices are illustrated animations.

Photo backgrounds accept JPG, PNG and WebP up to 8 MiB. Choose from my photos copies an active native Photos image; the Styled Post's audience controls the independent copy. Albums and avatars are excluded. Background copies and motion uploads require Local storage.

Video/GIF uploads accept MP4, WebM, MOV and animated GIF, 3–5 seconds, at most 8 MiB and 1280 pixels per side. They require configured local UNA conversion, FFmpeg, ffprobe, PHP fileinfo/process execution, and the normal UNA conversion cron. GIFs become silent MP4s. Wait for conversion rather than repeatedly submitting. A failed replacement retains the saved background.

Pexels and Unsplash links open separate sites. Check the chosen image's usage terms before downloading and uploading it. The links are not an integrated search picker.

## Administrator font packs

Create `storage/nw_styled_posts_fonts/` and place licensed local WOFF2 files plus license texts there. Add `fonts.json`, for example:

```json
[{"id":"my_arabic","label":"My Arabic font","file":"MyArabic.woff2","license":"MyArabic-LICENSE.txt"}]
```

Up to 32 entries are accepted. Each needs a unique lowercase ID (letter first, then letters/digits/underscores, up to 24 characters), a label up to 80 characters, a WOFF2 file up to 2 MiB and a nonempty license text. Use simple filenames, not remote URLs, PHP or CSS. Studio reports ignored entries. This is an administrator file-manager workflow, not a member upload or Studio font-upload button. Font files are public; removing a font uses a fallback.

## Existing installations

Do not uninstall/reinstall to upgrade. Use only the native updater matching the installed version. The validated route is 0.1.0 → 0.2.0 → 0.3.0 → 0.3.1 → 0.3.2. Do not stop at 0.3.1. Stage each updater under the module's `updates/` folder, verify its checksum and run Studio Update. There is no certified direct 0.3.0 → 0.3.2 package in this release.

If My UNA World blocks installation because a safety backup is already running, wait for it to finish and inspect its backup status/logs. Repeated Install clicks do not resolve the blocker. A stale lock versus active backup requires diagnosis in My UNA World, not manual SQL import.

Native lifecycle validation passed on the authorized UNA 15/PHP 8.3/MariaDB test environment using its normal SQL mode. Forcing strict SQL mode exposed a UNA core `sys_alerts_handlers.ts` default-value error during enable; strict-mode enable is not certified. No core changes or live SQL-mode changes were made.
