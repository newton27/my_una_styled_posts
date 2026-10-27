# Single-site motion update: staged, awaiting browser file access

User explicitly selected one site update with our updater. Target: gayjaxonline.com only. No native update has run yet; installed nw_styled_posts remains enabled at 0.1.0, module ID 65, two posts.

Candidate runtime source: local 17b5a73a316fddecc050cfa8275e7bbe82add798, remote feature/motion-backgrounds f836c21941b99085eb3922aae4a79bcfa62b486f. Exact source tree identity 5c2f02a42da2f6bd0c4c063d4697e2311940b57d.

Updater ZIP SHA-256: 7b9bee16e32c27267bc19c884ac33e31d222892e1aff389050ff76bd069d0efc. Built locally; immutable RC1. Native updater not yet staged into module.

Full database backup completed before update, 12,585,951 bytes, SHA-256 ffa2a0ed8c6ce279ecfa3367e5627e8a0ebaccf6a4e5784d9a7ab24557721bf8. Private server checkpoint: /home/gayjaxonline/styled-posts-motion-qa-20261027/before.sql. Private entry/settings snapshot before.json, six table rows snapshot module-rows-before.json. Module code and background files archived separately as module-and-backgrounds-before.tar. No core files changed.

Repository is private; anonymous codeload returned 404. Authenticated browser downloaded source to /Users/michaelnewton/Downloads/timeline poster backgrd/my_una_styled_posts-feature-motion-backgrounds.zip. Upload to Webmin did not submit: file chooser setFiles timed out and reset browser bindings. Follow advertised Chromium file upload troubleshooting: user must enable Allow access to file URLs for ChatGPT extension. Do not bypass this browser restriction.

Next: transfer archive, build server updater ZIP using scripts/package_update.py and compare exact hash above; extract only update_0.1.0_0.2.0 into module updates (readable files/dirs), run native Studio update. Verify preservation against before.json before motion upload/queue/privacy tests. Do not uninstall, remove user post 2, change core, update other sites or alter existing GitHub release/tag.


## Native update completed

The browser file chooser remained unresponsive after extension changes. The development-workspace payload was transferred through the existing authorized Webmin terminal, without reading local browser files. Gzip payload SHA-256 a3f18df3878661fa26a88fe790aaff2320eb82b51dd0e38fc5ea722fcd47a1db. Server assembly verified all 45 module source files and reproduced the exact RC1 updater ZIP hash above before staging it.

Studio detected My UNA Styled Posts 0.1.0–0.2.0. First native attempt stopped at the existing integrity gate: one changed tracked MenuViewActions file plus three untracked comment/reaction compatibility additions from prior testing (4/27 = 15%). The 27 original tracked hashes were preserved, not rewritten. Restored the MenuViewActions bytes from verified RC2 archive (MD5 3b3183f73ae5a89c7268702181240606), temporarily restored original native comment/reaction registrations, and moved the three compatibility additions into private baseline-hold. All 27 original file hashes then matched. No force-update setting, core file or integrity threshold changed.

Second Studio update completed. Native module ID 65 is version 0.2.0, enabled. Both pre-existing entry rows match the before.json text/style/audience/background/status snapshot exactly. Every existing setting retains its value; three new settings bring total to 22. All 45 runtime files match candidate SHA-256 hashes. Native file tracking now contains 38 runtime files. Both motion objects instantiate as NwStyledPostsTranscoder; motion preflight is available and generic transcoder URL delivery returns false.

Native update revealed a language-package defect: full-install `<string>` tags are ignored by UNA's updater. Corrected updater XML to native `<string_add>` and `<string_upd>` tags; new regression test failed then passed. Applied the corrected language step through actual BxDolStudioUpdater::actionUpdateLanguages (result 0/success), and verified Studio displays Background: Moving tides. Runtime source files remain unchanged. This is a successful native update with a separately corrected language step; the corrected RC2 package has not yet repeated the full 0.1.0→0.2.0 cycle.

Authenticated admin editor exposes Moving tides, Soft glow and Upload video or GIF. Created private QA entry 3, text “Motion background QA — four-second private loop.”, background motion_glow, audience Only me. Its real browser video reports duration 4 seconds, muted, looping, playing; Pause control worked. The original two posts were preserved before this added fixture. Existing member upload/post 2 remains untouched.

Still open: real browser upload/GIF queue lifecycle, guest/non-owner protected motion delivery, fresh installation and database concurrency. No other site updated, no core files modified, no GitHub tag/release changed. RC1 ZIP remains immutable; RC2 corrects updater language only.
