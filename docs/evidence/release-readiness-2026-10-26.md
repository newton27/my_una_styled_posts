# My UNA Styled Posts: release readiness, October 26, 2026

Status: RC1 preparation; final publication blocked by remaining native validation.
Internal identity `nw_styled_posts`, version `0.1.0`, path `modules/newton/my_una_styled_posts/`.
Target UNA 15 RC2 / PHP 8.3. Official native reference commit `05466c74ce6bbe4d8579fa901c6dc787d1dcff75`.

| Gate | Evidence | Status | Next action |
|---|---|---|---|
| Module source and core separation | Only module-owned runtime files; native registrations in module SQL | PASS | Preserve boundary |
| Local behavior/contracts | 132 PHP assertions; 10 Python tests; syntax checks; Chromium editor/picker/theme suites | PASS | Freeze source checkpoint |
| ZIP permissions | Regression test checks files 0644, parent directories 0755 | PASS | Verify every final ZIP member |
| Live editor/photo picker | myunaworld.com dark-theme editor restored; nine Photos thumbnails loaded | PASS | Preserve native styling |
| Live private photo copy and edit | Created post 3 as Only me; native photo 5 copied; image loaded; edit retained image and privacy | PASS | Extend to member role |
| Guest denial | Guest post HTTP 403 and background/3 HTTP 404 | PASS | Extend to other member/guest cases |
| Clean Studio installation/info/launcher | Initial user installation predates final repairs; no clean install of frozen RC1 | NOT TESTED | Install on disposable UNA site |
| Native database compatibility | Installed schema exists; no controlled fresh strict MariaDB/MySQL run recorded | NOT TESTED | Execute native install on target database |
| Member actions and lifecycle | Full create/edit/delete/comments/reactions/reposts and disable/re-enable/uninstall not validated | NOT TESTED | Complete authenticated cycle on disposable test site |
| Native updater | First release; development file patches are not native updaters | NOT APPLICABLE | Do not label file patch as Studio updater |
| Marketplace material | Draft description, guide, changelog and demo preview included | PASS (draft) | Confirm listing fields/media requirements and production status |
| GitHub and analysis | No remote configured; proposed newton27/my-una-styled-posts returned 404; no SonarCloud project | BLOCKED | Create/connect repository, publish source and configure analysis |
| Final release/marketplace publication | No tag, GitHub release, marketplace upload or listing published | BLOCKED | Close remaining gates before final publication |

Private QA post retained at `https://myunaworld.com/view-styled-post/styled-posts-release-check-private-photo` (post 3, Only me). It contains an explicitly labeled test statement; no public test post was made. No source photos were deleted or modified. Existing homepage draft was left untouched.

Known limits: native Photos only (no Albums/avatar picker); full-source thumbnail downloads; header/MIME/size/dimension image validation rather than a full decoder; no dedicated global search provider. The candidate changes the unfinished action-block heading to Actions; installed language database still requires native language update before the changed label appears there.

Packaging: native installer rooted at `newton/my_una_styled_posts/`; extract into `modules/` and use Studio. Development kit includes the native installer, source, tests, draft marketplace copy, demo preview, licenses and manifest. No live account photos/screenshots are included. Old ZIPs remain immutable, including the earlier faulty permission patch; do not redistribute those old patches.

RC1 denotes preparation status; native manifest remains 0.1.0. No claim of a tested native 0.1.0-to-0.1.0 updater. Build manifest records the frozen source commit separately from future release-record edits.
