# My UNA Styled Posts 0.1.0 — development validation

This is a development candidate, not a certified release. This report records the initial development checkpoint. See `release-readiness-2026-10-26.md` for later live-site verification.

## Source baseline

Native interfaces inspected from official UNA 15 RC2, commit `05466c74ce6bbe4d8579fa901c6dc787d1dcff75`.

Relevant contracts: `BxBaseModTextModule`, `BxBaseModTextFormEntry`, `BxBaseModTextFormsEntryHelper`, `BxBaseModTextInstaller`, `BxBaseModTextMenuViewActions`, `BxBaseStudioModule`, `BxDolStorageLocal` and the Posts module's SQL registrations.

No `inc/`, `studio/`, `template/`, `modules/base/` or `modules/boonex/` source files are included or modified. Module-specific SQL registers objects in UNA's standard registration tables, as native modules require.

## Verification coverage

- Package identity, native file paths, SQL physical-line safety, registered file existence, language XML format and PHP service-call serialization.
- Server style allowlist, disabled-preset fallback, valid colors/font sizes/alignment, multiline Unicode, 500-code-point boundary, image MIME/header checks, SVG rejection and contrast calculation.
- Escaped literal quote text and safe rendering of invalid persisted style values.
- Native adapter checks with deliberately small stand-ins for required UNA classes. These verify delegation contracts, not an installed site's behavior.
- Background and Timeline authorization boundary checks with simulated native policy results. These prove the module checks native authorization before accessing storage or rendering; they do not validate UNA's underlying privacy engine.
- PHP 8.3 syntax checks and JavaScript syntax check.
- Actual Chromium 131 browser checks passed for font/background/color/alignment controls, literal markup, 320/375/768/1280 pixel viewports, visible long text, and the 500-character validity boundary. Browser log is included. A screenshot was visually inspected.

## Remaining release gates

- Native Studio fresh installation, Apps information popup and launcher rendering.
- Real database execution under MariaDB and MySQL strict modes.
- Real member/guest/admin create, edit, delete, privacy, background-download, comment, reaction and repost cycles.
- Disabled/re-enabled content preservation and native uninstall cleanup on a disposable test installation.
- SonarCloud analysis; there is no remote repository/project configured for this candidate.
- Final marketplace media and production release approval.

Do not infer any live PASS from the local checks above.

## Implementation decisions

- Initial version `0.1.0`.
- 500 Unicode code points per post; size selection from 18 to 96 pixels, automatically fitted down to 18 pixels when possible. Extremely long text expands the card rather than being clipped.
- Five font styles, including two locally bundled OFL fonts; ten backgrounds, including original vector illustrations.
- Member photo backgrounds: JPG, PNG and WebP only; 8 MiB, 16 megapixels and 8,000 pixels per dimension maximum.
- Images use private UNA Local storage. Downloads go through a post-specific module action with a fresh native visibility check and no-store headers; no bearer storage URL is exposed by this module.
- Studio settings enable/disable individual fonts, individual background presets and member photo uploads. Posting rights use UNA Memberships.
- Module home initially shows up to 60 recent active entries filtered by native visibility. The main ongoing feed is Timeline.

## Final local result

Nine Python contract tests passed. PHP behavioral checks passed: 36 style assertions, 12 rendering/editor-state assertions, six native adapter assertions and 14 authorization-boundary assertions. All 17 module PHP files passed PHP 8.3 syntax checking. JavaScript syntax and the Chromium editor tests passed. The independent review findings and regression fixes are recorded in `code-review.md`.

Saved text remains plain editable content. This candidate does not yet register a dedicated global UNA search result provider; global search discovery is a remaining integration limitation.
