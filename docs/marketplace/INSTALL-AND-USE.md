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
