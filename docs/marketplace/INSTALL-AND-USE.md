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
