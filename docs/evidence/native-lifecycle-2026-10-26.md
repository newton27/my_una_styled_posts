# Native lifecycle tests — October 26, 2026

In progress. User selected **gayjaxonline.com** as the test target after the initial isolated strict-mode run. Remaining native UI/lifecycle/member tests will use that site. GitHub repository discovered in the user's browser: `newton27/My_UNA_Styled_Posts` (earlier hyphenated-name 404 did not establish absence of this differently named repository).

## Initial isolated native run

Created dedicated database `myunaworld_styled_posts_qa_20261026` through Virtualmin. Prepared native UNA code under `/home/myunaworld/module-lab/styled-posts-qa-20261026/site/`, with separate cache/storage/temp directories and an isolation guard checking the exact database and root. No primary UNA core source was modified. Site configuration changes were confined to the isolated copy. Schemas for 660 native tables and 169 configuration tables were staged; private uploads and member content were not copied. Only the initial administrator account/profile was retained for potential native UI checks.

The native Studio installer dispatcher (`BxDolStudioInstallerUtils::perform`) ran through the module's `BxBaseModTextInstaller` adapter. This is actual native PHP/MariaDB execution, not stand-ins. A CLI installer-check flag was limited to the guarded isolated process; native Studio UI installation still needs verification.

MariaDB: `10.11.14-MariaDB-0ubuntu0.24.04.1`.
Session SQL mode: `STRICT_ALL_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION`.

1. Baseline native uninstall: code 0. Verified no module registration, no six module tables, zero module pages and zero Studio widgets.
2. First native strict install: code 200, rejected the text input registration because required `sys_form_inputs.help` was omitted.
3. Inspected native INFORMATION_SCHEMA mandatory columns for every registration table. Found omissions in six native tables across 52 INSERT statements.
4. Regression `tests/test_strict_registrations.py` failed on 52 omissions before the fix, then passed after supplying explicit empty/zero values. 11 Python tests and 132 PHP assertions plus syntax checks passed.
5. Cleared the partial installation only in the isolated database through the module's native uninstall SQL, then reran the real native installer.
6. Corrected strict install: code 0, enabled module 0.1.0, six module tables, five native pages and one Studio widget. No warning/error shown in the successful installer log.

The temporary web symlink was removed and the private QA parent directory returned to mode 0700 after the user selected gayjaxonline.com. The isolated database/logs remain as evidence; there is no scheduled QA cron.

## Fix scope

Only module `install/sql/install.sql` and `enable.sql` changed. Explicit mandatory native fields now cover form help/icons, option checker parameters, page metadata/injections/API configuration, page block text/help/API configuration and menu addon/markers/visibility/API configuration. SQL remains one physical line per statement. Existing native defaults/behavior are preserved by empty or zero values; no UNA core schema was altered. RC1 artifacts remain immutable; a later candidate must contain this fix.

## Remaining

- gayjaxonline.com baseline, backup/checkpoint and clean native Studio install/info popup/icon.
- Member create/edit/delete, preset/upload/existing-photo behavior, audience checks, comments/reactions/reposts and non-owner permissions.
- Disable/re-enable settings/content preservation; native uninstall cleanup; repeat installation.
- Strict MySQL as a separate database platform remains untested; MariaDB success is not a MySQL pass.
- No final GitHub release or marketplace publication.

## gayjaxonline.com native Studio run

User opened the target's native Studio session and its Virtualmin server, `server.michaelandbrian.com`. Explicitly selected gayjaxonline.com in Virtualmin before any mutation.

- Server account: gayjaxonline; site root `/home/gayjaxonline/public_html/`.
- PHP 8.3.6; MariaDB `10.11.14-MariaDB-0ubuntu0.24.04.1`. Native normal connection SQL mode empty; strict-mode installation was separately exercised in the isolated run above.
- Baseline: no `nw_styled_posts` module registration, no module tables and no module directory. 47 installed modules and 383 native page objects before installation.
- Pre-install full database backup: `/home/gayjaxonline/styled-posts-release-qa-20261026/baseline.sql`, mode 0600 under a mode-0700 directory, 12,876,629 bytes. SHA-256 `2be3617bce1a812c9cd8395941cc1436716f31b9d3f9fb585ab446e5c869476c`. Database credentials were used only in a temporary mode-0600 client configuration, then the temporary file was removed; credentials were not printed.
- Candidate source checkpoint: `087e75fb28e7e37ed11e73dec1f6c25cc4e723c8`. All 34 module file hashes verified on the iMac before transfer. The resulting ZIP was byte-identical to the frozen host build.
- Candidate archive `una-styled-posts-0.1.0-rc2-install.zip`, 396,065 bytes; SHA-256 `d1a8253f869f639ae52770d05f455014a52eb14116d3ad2cc60b6ddc6b2ca185`. Same hash verified on the server before extraction. ZIP members were staged exclusively in `modules/newton/my_una_styled_posts/`, files 0644 and directories 0755. No original core files were overwritten.
- Native Studio → Apps → Downloaded displayed My UNA Styled Posts, version 0.1.0, vendor My UNA World. The information popup opened and displayed title/vendor/version/description without an error.
- Clicked the module's native Install control. It completed and changed to Installed. Opened the newly registered native Studio launcher link and module settings page.
- Native settings displayed the auto-approval setting, 500-character summary, searchable fields, member image backgrounds, all five fonts and all ten background presets; pending-post panel rendered without warnings.
- Next browser action was opening the create-styled-post page for member-interaction checks. Chrome blocked automation with: another extension UI is open. No claim of successful member/lifecycle cycles is made. User was asked to dismiss the extension popup.

Remaining target work: verify launcher icon rendered, normal-member/guest/admin actions, comments/reactions/reposts, upload and image cleanup, content/settings preservation across disable/re-enable, native uninstall/reinstall with cleanup. Existing original site content remains untouched; only the newly installed module and its registrations were added.

GitHub repository currently visible in the user's Chrome tab is `newton27/my_una_styled_posts` (renamed since the earlier observations). No remote push/tag/release was performed in this run.

## Follow-up native checks and count-label correction

- Created one explicitly labeled private QA entry as the authenticated admin profile (author 3, entry 1). Saved Handwritten/Ocean/Only me; reopening Edit preserved all three choices. Editing the text succeeded. These are admin-session checks, not proof of Standard-member permissions.
- The Photos module is absent on this target. Choose from my photos opened a working dialog explaining that photos are unavailable and suggesting upload or enabling Photos.
- A 501-character edit was blocked with the visible 500-character validation message.
- Native Studio launcher icon loaded successfully at 256 by 256 from the module's template/images/icons/std-icon.svg.
- Changed summary length from 500 to 480 through native Studio. Disabled and re-enabled the module using its Studio context menu. Native database snapshots confirmed enabled 0 then 1, with identical entry and settings data across the cycle. Restored summary length to 500 afterward.
- Found a raw `%s` in the author count. Native UNA language interpolation uses `{0}`. A native regression failed before the module language correction, then passed with counts 0, 1 and 7. Updated only module en.xml and refreshed its language through native Studio language utilities. Browser subsequently displayed Styled Posts (1). The same correction and regression now exist in source; older RC2 ZIP bytes remain unchanged.
- A private QA comment was saved and persisted across reload. Its count changed from 0 to 1 only after reload, and unresolved `__item_note__` / `__meta__` placeholders were visible. A post reaction changed to a thumbs-up without navigation and persisted after reload; numerical feedback/change/removal still needs validation. These interaction findings are open, not release passes.

Remaining: Standard-member/non-owner/guest coverage on this target, uploads and cleanup, comment rendering and immediate counts, reaction count/change/removal, repost/Timeline behavior, native uninstall/reinstall cleanup, and health-log review. No final release or Marketplace publication is authorized by this source checkpoint alone.

## Comment/reaction stabilization follow-up

The target's current Studio reports UNA **15.0.0-RC3**, PHP 8.3.6 and MariaDB 10.11.14. The module's original target description remains UNA 15 RC2; the current test results should be attributed to the observed RC3 runtime.

Traced the raw comment placeholders to the actual selected `modules/boonex/lucid/data/template/system/comment.html`. That older layout embeds `show_notes`, `meta` and pinned-header variables directly, whereas the current `BxBaseCmts::getComment` supplies a separately rendered `header`. Direct header rendering succeeded; rendering the full old theme item exposed the missing placeholders. Clearing template/content/JavaScript caches did not resolve this structural mismatch.

Added module-owned `NwStyledPostsCmts` and `nwsp_comment.html`, retaining the current native author/header/content/actions/replies slots. The adapter uses the module template lookup and leaves the installed theme and UNA core unchanged. Updated the module's native comments registration and explicitly invalidated its `sys_objects_cmts` database cache after applying the test-site repair. Browser verification then showed the author/time and comment text without raw placeholders. The temporary diagnostic method in NwStyledPostsModule was removed and its original SHA-256 verified.

The native reaction implementation defaults to quick Like and hides its counter. Added a module reaction adapter that opens the native selector while retaining the native allowed reaction list and single/multiple-reaction policy; the module menu requests counter/legend display. Native init returns void on success, false on failure: the regression was corrected to reflect that real contract and failed before the corresponding fix. The comment title now contains the native AJAX counter rather than a static number, with a numeric counter label to avoid repeating the word Comments.

Observed native PHP checks after deployment: comments class NwStyledPostsCmts; no raw note/meta placeholders in the rendered comment; title contains bx-cmt-counter markup; reactions class NwStyledPostsVoteReactions; generated click calls toggleDoPopup. Earlier browser add/remove Like actions worked without navigation, with persisted rows and rvotes=1 after adding. The new picker, numerical feedback and live title still require browser verification; PHP markup inspection alone is not an interaction pass.

Anonymous HTTP request to the private QA entry returned **403 Access denied** and did not expose its text. Upload fixture selection could not complete through Chrome; file-URL access must be enabled in the extension. Later, another extension UI blocked further interaction with the post page. No upload is claimed as successful.

Two entries now exist: private QA entry 1 and a new public entry 2 not created by this test run. Uninstall/reinstall is paused to preserve the latter. A private module-data snapshot was created before any potential destructive lifecycle work. No uninstall occurred. No account credentials were reset, no original site content was deleted, and no final release was published.

Local regression suite after these corrections: **11 Python tests and 150 PHP assertions**, plus PHP and JavaScript syntax checks, passed. Remaining gates include real browser comment/count/reaction cycles, photo upload/replacement/removal, Standard-member/non-owner checks, repost/Timeline checks, health logs and the approved uninstall/reinstall cycle.
