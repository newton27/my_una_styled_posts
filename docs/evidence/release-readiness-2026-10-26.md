# My UNA Styled Posts: release readiness, October 26, 2026

Status: **RC4 preparation**. Final certification is blocked by remaining native validation.
Identity: nw_styled_posts, version 0.1.0, modules/newton/my_una_styled_posts/.
Observed current native runtime: UNA 15.0.0-RC3 / PHP 8.3.6 / MariaDB 10.11.14. Original reference/target: UNA 15 RC2; pinned source 05466c74ce6bbe4d8579fa901c6dc787d1dcff75. Broader 15.0.x compatibility remains a metadata declaration, not a claim of exhaustive testing.

| Gate | Evidence | Status | Next action |
|---|---|---|---|
| Source/core boundary | Module-owned runtime changes only; no core or theme edits | PASS | Preserve boundary |
| Local suite | 11 Python tests, 150 PHP assertions and PHP/JS syntax checks | PASS | Retain source checkpoint |
| Installer permissions | Explicit files 0644/directories 0755 and archive regression | PASS | Verify finished RC4 archive |
| Composer/native Photos | myunaworld.com editor and nine Photos thumbnails; independent private photo copy/edit passed | PASS | Extend member-role/upload cases |
| Guest denial | myunaworld.com private post/background denied; gayjaxonline.com private entry HTTP 403 | PASS | Extend non-owner cases |
| Native installation | gayjaxonline.com fresh Studio install/info/settings and rendered launcher icon passed for RC2 predecessor | PASS | Repeat with current adapter registrations |
| Strict database | Native strict MariaDB install passed after 52 required-field corrections | PASS | Separate strict MySQL still untested |
| Comments/reactions | Native admin browser count 1→2, Love/Haha/undo and matching database results | PASS | Extend Standard-member permissions |
| Disable/re-enable | Entry and customized settings preserved in native Studio | PASS | Retain evidence |
| Member/non-owner, upload lifecycle, repost/Timeline, logs | Remaining controlled native checks not complete | NOT TESTED | Complete remaining cycles |
| Uninstall/reinstall | Paused after new public entry/photo appeared; private backups verified | BLOCKED | Confirm scope and refresh backup before deletion |
| Native updater | First release; file repairs are not native updaters | NOT APPLICABLE | Do not represent full installer as updater |
| Marketplace text | Description, guide and changelog prepared | PASS | Select price/category and accepted media |
| Marketplace submission | Signed-in Add opens blank New Product dialog; no fields/save control | BLOCKED | Resolve form and choose price |
| GitHub source | Current main contains exact local source tree and later interaction fixes | PASS | Verify subsequent commits before packaging |
| Existing GitHub release | Vendor-created My_UNA_Styled_Post tag at e05cf2c; no installer assets | BLOCKED | Reconcile with final validated candidate; explicit approval required for release changes |
| Final certification | Remaining gates open; no Marketplace listing created | BLOCKED | Complete tests before final certification |

See native-lifecycle-2026-10-26.md for detailed observations, backups and pending work. Admin action passes do not establish Standard-member permissions. Source revisions and immutable older ZIPs describe their own checkpoints; never replace their bytes silently.

Known limits: native Photos only, no Albums/avatar picker; source-image thumbnail downloads; image header/MIME/size/dimension checks rather than a full decoder; no dedicated global search provider; English interface; Local background storage.

Installer root: newton/my_una_styled_posts/, to extract into modules/. Development kits include source, tests, preview, licenses, draft Marketplace text and a manifest. No member photos, private browser screenshots or site credentials are included. Old faulty permission patches must not be redistributed. The full installer is not a native updater for an existing development installation.
