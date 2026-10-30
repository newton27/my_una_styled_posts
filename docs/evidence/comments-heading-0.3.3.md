# Styled Posts 0.3.3 comments heading correction

The previous adapter inserted getCounter() HTML into Comments ({0}), splitting the heading around a block widget. The correction uses getCommentsCountAll(0, true), matching UNA's native heading implementation.

## Frozen package checkpoint

Runtime/build source: ca691d40ce1c1219741f5af7ff2e9bf8f5cd3e5c.
Base 0.3.2: d25e19ad0c5b645994ad5765e7ba0c75487306ef.
Later evidence-only commits do not change these artifact bytes.

- Installer: my-una-styled-posts-0.3.3-comments-rc1-install.zip; 1,362,200 bytes; SHA-256 64f7a0be7967d89b8da0a61b78ef30961dcc08a48ff48bc921dbb10c98c55c8e.
- Updater: my-una-styled-posts-0.3.3-comments-rc1-update-0.3.2-to-0.3.3.zip; 1,364,215 bytes; SHA-256 2bd85225cbcd8ba89c1043d5e89236d13730d102a05117d315d6afdb465f6d9c.
- Published Marketplace 0.3.2 baseline ZIP: SHA-256 d54633a9822a8cf4d4992707c94607252fdea32465511d2b67e198bc2015ddcc.

Both candidate ZIP checksums passed on the test server. Baseline-to-candidate runtime comparison found only classes/NwStyledPostsCmts.php and install/config.php changed, with no added or removed runtime files.

## Native acceptance — gayjax.life, 30 October 2026

Authorized host: server.michaelandbrian.com:10000.
Rollback checkpoint: /home/gayjax.life/my_una_world_backups/my_una_world_full_20261030_172635.zip.
Backup size: 109,388,012 bytes. SHA-256 e454a9136a468e88aeb49d46113b96035da16f5af0e43437d78e3035f43f6666.
My UNA World reported full backup complete and ZIP integrity passed: 28 modules, 601 tables and 1,958 files.
Initial backup failed because an empty Modzzz Online TV updates directory was root-owned with mode 700. Only that empty directory's ownership was corrected to gayjax.life:gayjax.life; mode 700 was retained. The retry then passed.

1. Staged the exact published Marketplace 0.3.2 package into an absent module directory.
2. Installed 0.3.2 using UNA Studio's native Install action.
3. Created an Only me acceptance post (entry 1) through the normal member form.
4. Saved entry and module-setting snapshots to the private acceptance directory.
5. Staged the exact checksummed updater into the module's updates/ folder.
6. Studio detected 0.3.2 - 0.3.3. Ran the native updater through its Install action; did not bypass version/hash checks.
7. Database installed version reports 0.3.3. Before/after entry and setting JSON values are semantically identical.
8. Browser heading reads Comments (0) as plain inline text.
9. Posted one acceptance comment through the native editor. After refresh, heading reads Comments (1), and the comment body/header render correctly. Immediate asynchronous heading refresh was not changed; this follows the native plain-count heading.
10. Studio Apps → Downloaded information popup loads, reports Version 0.3.3 (0.3.2 is available), and displays Marketplace screenshots and description.

Private server evidence: /home/gayjax.life/updater-acceptance-20261030/ (backup logs, exact ZIPs, baseline packages, and before/after snapshots).
No UNA core files were edited. The private acceptance post/comment remain for review.

## Gates

| Gate | Status |
|---|---|
| PHP syntax and 0/1/12 comment adapter regression | PASS in CI at package checkpoint |
| Package root, integrity and matching installer/updater bytes | PASS |
| Native 0.3.2-to-0.3.3 update using exact ZIP bytes | PASS |
| Existing test post and module settings preserved | PASS |
| Browser Comments (0) and Comments (1) after refresh | PASS |
| Studio information popup | PASS |
| Independent clean installation of the 0.3.3 installer ZIP | NOT TESTED |
| Marketplace publication of 0.3.3 updater/full installer | NOT PERFORMED |
| Older update chain 0.1.0/0.2.0/0.3.0/0.3.1 | NOT TESTED in this acceptance |

The 0.3.2-to-0.3.3 updater has native evidence. Do not describe the full 0.3.3 installer or older update paths as independently native-validated.
