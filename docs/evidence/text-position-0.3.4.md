# Styled Posts 0.3.4 drag positioning acceptance

Runtime/build checkpoint: `13fa43dfed4f3db7d211c1811301e84e5e904831`.
Push CI: https://github.com/newton27/my_una_styled_posts/actions/runs/41155655220 (PASS).
PR CI: https://github.com/newton27/my_una_styled_posts/actions/runs/41155660148 (PASS; merge checkpoint differs, use push artifact below).
Frozen push artifact: `13024019013`, `styled-posts-0.3.4-candidate`.

| Gate | Evidence | Status |
| --- | --- | --- |
| Server validation / regressions | 22 positioning assertions, full PHP suite and lint; 26 Python tests; JS syntax/startup/geometry | PASS |
| Editor behavior | Chromium mouse/touch events, both-axis bounds, cancellation, keyboard/Shift, reset, 360/1280 widths, late font response, Arabic/backdrop/long text | PASS |
| Native 0.3.3 → 0.3.4 | Exact frozen updater installed through Studio Downloaded on gayjax.life | PASS |
| Preservation | Before/after semantic JSON comparison of every existing entry field and all module options; added fields auto/50/50 | PASS |
| Runtime identity | Every runtime file byte matched the frozen 0.3.4 installer after native update | PASS |
| Native persistence | Existing private post moved to custom 50/27, saved, refreshed and edit reopened with 50/27; Reset saved auto/50/50 | PASS |
| Native create / both axes | New Only me post `your-words-your-place`, actual mouse drag to 21/27, saved; reload and reopened editor retained custom/21/27 | PASS |
| Studio metadata | Apps reports 0.3.4; info popup loads, Version 0.3.4 (0.3.2 available from Marketplace) | PASS |
| Independent fresh 0.3.4 install | Not run | NOT TESTED |
| Marketplace / other sites | Not published or rolled out by this change | PENDING |

## Immutable packages

- Updater `my-una-styled-posts-0.3.4-position-rc1-update-0.3.3-to-0.3.4.zip`: 1,367,152 bytes; SHA-256 `ca29d84cad38c47d1635c1de5c01efee0c687ea945c893db1013a839d20b995d`.
- Installer `my-una-styled-posts-0.3.4-position-rc1-install.zip`: 1,363,889 bytes; SHA-256 `be3a5c016615319888f9557508da4f17d7e4974b884bd3ce1af216bbdc39086d`.
- Both ZIP integrity checks pass and have one explicit native package root. The installer is packaged but has no independent clean-install acceptance.
- Frozen 0.3.3 artifacts are unchanged. No GitHub release/tag changes and no UNA core edits.

## Native rollback checkpoint

gayjax.life was verified at 0.3.3 and all baseline runtime bytes matched the prior frozen installer before staging.
Full My UNA World backup: `/home/gayjax.life/my_una_world_backups/my_una_world_full_20261031_012935.zip`.
Size 111,881,631 bytes; SHA-256 `992c2681384804767aaf8ba6d6cb452a01c9b330f8a8abbca8cbe34cdccb5d66`.
Backup status complete; ZIP integrity passed; 29 modules, 608 tables, 2,031 files.
Private snapshots and staged exact artifact are in `/home/gayjax.life/styled-position-034-acceptance/`.
Existing test post content/comments remain, with its original auto position restored. A new private demonstration post remains for review.

## Review and scope

Independent review found a late-font-loading bounds gap. Added a document fonts loadingdone refit and a delayed unused-font browser regression; geometry/listener regression was observed failing before the fix, then passed. All suites passed after the fix.
The browser saved-card test clones a rendered card and is not claimed as database persistence evidence; native save/reload/edit above supplies that evidence.
Relative travel coordinates and an explicit auto/custom mode were chosen to preserve existing left/right layouts and adapt to screen size. Long text that fills the available width naturally has no horizontal travel; it remains readable within the card.
Native desktop mouse/keyboard acceptance and automated Chromium touch coverage passed; no physical mobile-device test is claimed.
