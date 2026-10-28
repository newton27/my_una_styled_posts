# Native editor update — 2026-10-28

## Outcome
PASS: gayjaxonline.com updated through Studio's native 0.2.0 → 0.3.0 module updater. Module ID 65 remains enabled. Only this site was updated; no UNA core source changed, no GitHub release/tag/assets or Marketplace listing changed.

## Frozen source and package
Runtime source: local 80f36e1bbb4b176d3590b4bc4a5c444d74a94ce8, remote equivalent d0fc89bf5bbb2c7c2946bcd3cd7aa9343ff7cad2, tree c01fe2921e6f15db0b8b1fc43fc7afc5d7eef46a. Later commits record evidence only.
Updater: my-una-styled-posts-0.3.0-editor-rc1-update-0.2.0-to-0.3.0.zip, 790919 bytes, SHA-256 4485963c78c729f6cfd27a441e804e6c679a819eeab4169e6c1779dc3b6ece12.
Recovered kit SHA-256 d4ebbf8ca3fe3fc1aa68c79617f2ed822b46708d21819554cebe61357224810d. The ZIP was transferred through Webmin using the documented shared-file upload route and its exact bytes verified on the server before extraction. Immutable packages were not rebuilt.

## Backup and native path
Private checkpoint: /home/gayjaxonline/styled-posts-editor-qa-20261028/.
Full database before.sql: 17387993 bytes, SHA-256 7530e14244e1a8b39aa7504706ebd7643a77821803d3995af32f5681d5956cab.
before.json records all module tables, all settings, module metadata and 48 then-present module/staging file hashes. module-and-media-before.tar preserves module source and both background storage directories. Checkpoints are private, site-user owned.
The old three-file language-step staging remnant from 0.2.0 validation was moved to the private checkpoint. Only the verified update_0.2.0_0.3.0 directory was placed in native module updates. Studio displayed My UNA Styled Posts 0.2.0–0.3.0 and its Install action completed. No force-update option, integrity threshold or tracked hash was changed.

## Preservation and schema
- 49/49 installed module files match the frozen updater payload SHA-256 values; no drift.
- Both pre-existing post rows match all their prior columns exactly immediately after the update.
- All 22 prior settings retain their values; four font settings bring the total to 26.
- Eight presentation columns exist and all 16 add/edit hidden-field registrations are present.
- The one pre-existing stored image matches its archived SHA-256 exactly.
Private machine-readable results: after-update.json, expected-source-hashes.json and editor-live-checks.json.

## Real authenticated editor cycle
Created private admin QA post ID 6 through the real editor:
https://gayjaxonline.com/view-styled-post/editor-0-3-0-qa-readable-text-and
Text: “Editor 0.3.0 QA — readable text and framing.” followed by an Arabic line.
Uploaded a synthetic 640×480 JPEG, selected Amiri, Box, black at 65% opacity, padding 32px, position 0%/100%, zoom 250%, RTL, Only me.
Saved successfully; server-rendered card shows the exact settings. Reopened Edit and all controls retained them. Reset image changed the controls to 50%/50%/100%; restored the chosen framing and Save changes completed.
Database confirms author 3, audience 2, font amiri, box/#000000/65/32, position 0/100, zoom 250, RTL and background file 3.
Source JPEG SHA-256 remains a1582ba398cdef1d2bc8bcbe223e783e76968492b9999280ecaef677a14ae14f: positioning does not rewrite the image.
Amiri @font-face is present in the compiled live stylesheet and its font URL returns HTTP 200. Screenshot shows shaped Arabic text. A cookie-free HEAD request to the private post image returns HTTP 404; the authenticated editor loads the 640-pixel image.
The native gallery opens and includes the private QA card for its author. Only three posts exist after this test, so real multi-page browsing is not claimed; bounded pagination/privacy unit checks remain the evidence for that case.
The test post is retained for review; neither original post was edited or removed.

## Health and remaining gates
The last 256 KiB of /home/gayjaxonline/logs/error_log and /var/log/virtualmin/gayjaxonline.com_error_log contain zero module error lines since the checkpoint and zero undated module matches. No PHP warnings appeared in the tested Studio/create/view/edit/gallery pages.

| Gate | Status | Evidence / next action |
|---|---|---|
| Native 0.2.0 → 0.3.0 update | PASS | Actual Studio action, version/schema/file hashes |
| Existing data/settings/media preservation | PASS | Before/after rows and file hashes |
| Live backdrop/framing/RTL save/reopen/reset | PASS | Private QA post 6, database and rendered UI |
| Private uploaded still-image guest denial | PASS | Cookie-free HEAD 404; author image loads |
| Native fresh 0.3.0 install | NOT TESTED | Separate clean-install cycle |
| Standard-member/non-owner role matrix | NOT TESTED | Admin-only editor test does not prove this |
| Video/GIF conversion lifecycle | NOT TESTED | Explicitly deferred by user; no new pass claimed |
| Wider release / Marketplace publication | NOT APPLICABLE | Not authorized by this deployment |

Two previously documented minor issues remain: administrator font-manifest symlink containment, and an extra Older posts link on an exactly full final gallery page. Neither is silently marked fixed.
