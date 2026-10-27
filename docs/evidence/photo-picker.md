# Existing photo background picker validation

Target: My UNA Styled Posts 0.1.0 development installation on myunaworld.com (manual module-only repair; no native version migration).
Native reference: unacms/UNA 05466c74ce6bbe4d8579fa901c6dc787d1dcff75 (15 RC2).

Implemented: native Photos entry picker, current-profile ownership and native visibility checks, source-file ownership/type/size checks, validation of actual image bytes, private independent copies, source photo preservation, preview/cancel/pagination/error states. Existing native create/edit authorization and form CSRF remain in control of all writes. Read endpoints are authenticated, viewer-specific and no-store. Albums photos and avatars are not included in this increment.

Verified locally:
- scripts/check.sh: nine Python package/native contracts; 132 PHP assertions; all module PHP and JS syntax checks.
- tests/photo-library.php: 19 assertions with native metadata/storage simulated.
- tests/photo-form.php: eight assertions with native form/storage simulated.
- tests/photo-picker.cjs: actual Chromium selection/pagination/cancel/focus/escaping, upload and preset switching, absent/empty/error states, mobile overflow and no unintended submission (fetch fixtures).
- tests/editor.cjs: existing controls, escaping, four widths, 500-code-point limits and overflow passed.
- tests/theme.cjs: 14 light/dark label contrast checks passed.

Not validated locally: live authenticated photo listing and actual source-to-destination native storage copy on myunaworld.com, native database execution and server image transport. Native StorageHelperStorage fetches source through its configured storage URL internally; sites must permit their storage URL to be read by PHP. No source photo URL/token is emitted to the browser by this picker.

Install: extract the photo-picker ZIP into public_html/modules/, replacing files. Clear UNA CSS/JS cache and refresh. No reinstall, SQL import or core edits. Test with an existing photo from the native UNA Photos module; create a styled post, confirm image after refresh, then edit and replace/remove its background.

Independent review: two Important findings (disabled Photos availability; selectable pending thumbnails) fixed with failing-then-passing regressions. All PHP, browser and contrast checks green afterward. Minor deferred: thumbnails currently fetch full source images; a smaller authenticated rendition is a future optimization.

Review scope decisions: live native database/session/storage/theme integration remains not tested; local checks and simulated boundaries are not substitutes for the authenticated site cycle. Existing shared image validation checks actual header MIME, byte size and dimensions; full image decoding remains outside this increment. Release ZIP identity is verified at packaging, separately from the code review.
