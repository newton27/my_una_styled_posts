# Styled Posts editor enhancements

Status: proposed design for user review; implementation has not started.

## Intent and scope

Members should be able to make readable quotes over busy photographs, frame their backgrounds, use Arabic and additional typefaces, find suitable images, and browse the community's Styled Posts. Preserve UNA audiences, editable text, original media, and existing post appearance. The user selected readability and image positioning first, then font/Arabic support. Video upload conversion tests are explicitly deferred, not passed.

## Current source checkpoint

Local feature/motion-backgrounds commit 56659f5; remote equivalent 75ad581eeba3bcb1cb7e3fc0de9c333b9db5a486. Shared tree f06fc5d8a93ee185bc8d8f111f7f58d27635d76d. Installed test site gayjaxonline.com is 0.2.0 with the additional upload-button CSS fix. Existing immutable RC2 ZIPs predate that CSS change.

The editor, PHP card renderer, and form share an allowlisted style catalog. Images currently use object-fit:cover with no saved positioning. Entry rows have six style fields. Five font choices include two bundled local fonts. Gallery queries return the latest 60 active posts and check each post's audience; this is not a complete paginated archive.

## Approaches

1. **Recommended: save presentation settings alongside the original media.** The same renderer applies text backdrop and image framing in the editor, entry, browse cards, and Timeline. Edits remain reversible; no image recompression, new media copies, or independent layouts.
2. Permanently crop uploaded files. Adds processing and replacement bookkeeping and makes later reframing require the original or another upload. This does not fit the requested editable experience.
3. Embed a general graphic-design editor. Adds a dependency and a second rendering model and risks turning editable quotes into flattened graphics. Excessive for the requested controls.

The recommended approach is the only one that fits the current small native editor without changing its content model. Approval of this design is pending.

## Phase 1: readability and framing

Add a Text backdrop disclosure after text alignment: None (default) or Box, color, opacity 0–100%, and padding 0–32px. Box surrounds the quote block, including its multiline text, with a small fixed corner radius. The backdrop affects the box only, never the quote's opacity. Alignment remains left/center/right. Defaults: mode none, black, 65% opacity, 12px padding; disabled mode applies zero backdrop padding. Text fitting includes box padding and preserves all text at small widths.

Add an Adjust image disclosure whenever a still background is selected: horizontal position 0–100%, vertical position 0–100%, zoom 100–250%, and Reset. Default 50/50/100 reproduces centered cover. Slider controls work with touch and keyboard and update the preview immediately. Zoom is a non-destructive crop, not a change to the stored image file. Save per-post settings; retain across edit/reopen. Selecting a different still image resets framing to center/100%; selecting a preset also resets it. Motion retains its existing centered framing in this phase. Browser preview and server renderer use the same object-position and transform rules, with no exposed blank margins.

Proposed fields: text_backdrop (none/box), backdrop_color, backdrop_opacity, backdrop_padding, image_x, image_y, image_zoom. Validate enums, exact hex colors and bounded integers server-side; do not accept arbitrary CSS or paths. Invalid submitted values fail with useful feedback; invalid historical values get safe display defaults. Missing values preserve legacy appearance.

Use native form hidden fields on add/edit. Register fields and schema in both full installation SQL and a new native updater. Existing text, audiences, files and other style values are untouched. No UNA core changes. Additive schema is retained on rollback so old code can ignore it safely. Do not manually change live schema.

## Phase 2: fonts, Arabic, image links, browsing

Bundle additional locally served fonts after verifying upstream licensing, including Arabic Naskh and a modern Arabic face, plus a small Latin selection. Preserve each license and provenance in the package. No remote font requests. Add direction Auto/LTR/RTL, with Auto default and explicit overrides for mixed-script quotes. Test Arabic shaping, diacritics, punctuation, wrapping, numerals and mixed Arabic/English in preview and saved cards.

For administrator font extensions, document a module-owned font-pack location and validated JSON manifest mapping stable IDs to local WOFF2 files and labels. Only an administrator installs packs; members select from registered fonts. No arbitrary remote font URLs, uploaded CSS, executable files, or filesystem traversal. Validate basename, WOFF2 signature/size and bounded ID/label values, contain all paths in the font-pack directory, and escape generated output. Require license documentation. Bad packs are ignored with Studio feedback; unknown or removed fonts fall back to a readable local face. A Studio uploader is a later enhancement, not part of this phase.

Add clearly labeled external links to Pexels and Unsplash and their license pages in the editor. Members download there, then use Upload photo here. Links open separately without losing draft text. Do not imply that every depicted person, trademark or paid asset is unrestricted. No image API integration, hotlinking, scraping, or credentials in this phase. Official references checked 2026-10-27: https://www.pexels.com/license/ and https://unsplash.com/license . Exact bundled font sources/licenses must be verified before choosing bytes.

Make Browse Styled Posts prominent through the native menu. Extend the existing gallery with a cursor-based Older posts control, newest first, using native audience checks before output. Fill pages with up to 24 visible posts by bounded batches, preserving a cursor for remaining rows; never expose hidden post titles/counts. Cover guest, author, friends, non-owner and pending/hidden posts. Keep profile-specific browsing. Search/favorites/filter redesign is outside scope.

## Motion status and release boundaries

Built-in 4-second loops and a 3–5 second upload path already exist. Do not reimplement conversion or advertise upload readiness before live MP4/GIF conversion, privacy, replacement and failure tests complete. Keep these as explicit release gates.

Enhancements require a new candidate version (proposed 0.3.0), not overwriting 0.2.0 RC assets or relabeling its updater. Build matching full installer and 0.2.0-to-0.3.0 updater from a frozen commit; add a coherent 0.1.0 upgrade route or document the two-step chain. Package builder must reject mismatched version/source payloads. Native install/update validation, test-site data preservation and private media tests precede wider release. GitHub release/tag and Marketplace publication remain separate approval actions.

## Acceptance evidence

- Unit: defaults, input bounds/types, CSS injection rejection, malformed saved values, color/opacity conversion.
- Rendering: same saved style in preview/entry/gallery/Timeline; text escaping and original image hash unchanged.
- Browser: backdrop off/on, opacity, multiline fitting; wide/tall image framing, reset and edit restoration; 360px and desktop; keyboard and touch slider behavior.
- Native: full fresh install and actual prior-version Studio updater, registered fields/language, post/settings/file preservation and logs.
- Fonts/RTL: licensed local files load without external font traffic; Arabic and mixed-script samples remain readable; invalid pack paths and unavailable fonts are handled.
- Browse: pagination reaches older visible posts without leaking inaccessible posts, loops, duplicates, or an unbounded scan.
- Release: known motion upload gaps stay open until measured live results exist.

Builder may choose helper names, JSON manifest details, local test fixtures, CSS organization and exact neutral control labels within these bounds. No production rollout, other-site changes or release publication is implied by design approval.
