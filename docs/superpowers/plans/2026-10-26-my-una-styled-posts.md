# My UNA Styled Posts Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a separate UNA module for editable quotes and short statements with customizable text and backgrounds.

**Architecture:** Use UNA's native content-module base and registered Timeline services. Store the text, style choices, and uploaded image references in module-owned data; render accessible HTML rather than a flattened picture. Register composer and Studio controls using native extension mechanisms only.

**Tech Stack:** UNA 15, PHP 8.3, MariaDB, JavaScript, CSS, UNA storage and native content services.

**Spec:** Approved design reproduced below.

## Approved design

- Name: My UNA Styled Posts; install directory `modules/newton/my_una_styled_posts/`.
- Colors, gradients, patterns, and picture backgrounds, including uploaded photos.
- Font family, size, color, and alignment controls.
- Live preview and text fitting for mobile screens.
- Editable posts connected to Timeline, with privacy, comments, reactions, and reposts.
- Studio controls for backgrounds, fonts, and posting permissions.
- No UNA core file changes.

### Approaches

The selected approach is a separate native content module with editable text and Timeline integration. Flattening posts to pictures would simplify some rendering, but would lose searchable and accessible text. Modifying Timeline source would break the approved core-file constraint. Neither alternative is part of this build.

## Global constraints

- No UNA core file changes.
- All module source lives in `modules/newton/my_una_styled_posts/`.
- Retain editable text rather than flattening it into a picture.
- Use UNA's server-side authorization, privacy and storage services.
- Do not copy an entire existing module and leave unrelated template files.
- Keep installation SQL statements on individual physical lines for UNA installer compatibility.
- Initial development version: 0.1.0; this is a planning choice, not a published release.

## Current evidence and remaining checks

Inspected upstream `unacms/UNA` Timeline and Posts modules. Posts extends `BxBaseModTextModule` and supplies `serviceGetTimelinePost`; Timeline exposes `serviceGetCreatePostForm` for the United Post Form. The upstream default branch inspected declares Posts 15.0.6.DEV, so this is architectural evidence, not proof of compatibility with an installed UNA 15 RC site. Pin the matching target revision before implementation.

No implementation repository or live test environment has been selected or modified. Native installation and runtime checks are pending. Public release publication is outside this implementation plan.

## Review Focus

1. Private content must not leak through Timeline, direct links, search, or background downloads.
2. Unicode, multiline text, and long unbroken strings must fit mobile cards without disappearing.
3. Invalid style values, markup, and malicious image uploads must be rejected on the server.
4. Editing or deleting a post must update its Timeline representation and owned assets consistently.
5. Disabling a font or background must not corrupt existing posts or make them unreadable.

## File structure

Paths below are relative to `modules/newton/my_una_styled_posts/`.

- `install/config.php`, `install/installer.php`, `install/sql/{install,enable,disable,uninstall}.sql`: native lifecycle, version, registrations and permissions.
- `install/langs/en.xml`: visible text and labels.
- `classes/NwStyledPosts{Module,Config,Db,Template,FormEntry}.php`: native content services, field mappings, persistence, rendering and forms.
- `classes/NwStyledPostsStyle.php`: style validation and normalized rendering values.
- `classes/NwStyledPostsStudioPage.php`: approved background and font catalog management.
- `js/composer.js`, `template/css/{composer,card}.css`, `template/{composer,card}.html`: editor, live preview and post cards.
- `template/images/icons/std-icon.svg`: Studio launcher artwork.
- `tests/`: focused policy, rendering and lifecycle checks.
- Repository `docs/`: native-install evidence, test matrix, package manifest and user guide.

Exact supplementary native adapter classes will be determined from the pinned UNA revision; do not invent service signatures.

### Task 1: Establish the native module and lifecycle

**Interfaces:** `NwStyledPostsModule` extends UNA's content base; `NwStyledPostsConfig` maps module-owned entry fields to native services. Native public identity is `nw_styled_posts`.

- [ ] Pin the target UNA revision; inspect its content base, unified composer registration, privacy, reactions, repost and Studio extension contracts. Record actual signatures.
- [ ] Add a failing package-contract test for identity, install path, launcher icon, language file, single-line SQL statements and absence of core-file paths.
- [ ] Run the contract check and confirm the missing-module failure.
- [ ] Implement the smallest lifecycle and schema using the pinned native contracts, without depending on the Posts module being installed.
- [ ] Pass the package-contract check and PHP syntax checks; commit the module baseline.

### Task 2: Validate style choices and backgrounds

**Interfaces:** `NwStyledPostsStyle::normalize(array $input, array $catalog): array` returns validated style fields or throws `InvalidArgumentException`. Rendering consumes only normalized values and resolved UNA storage references.

- [ ] Write failing cases for invalid font identifiers, CSS injection, malformed colors, unsupported alignment, disabled presets and excessive size.
- [ ] Define bounded font sizes and text length after checking UNA form limits; document the chosen values in config and test those boundaries.
- [ ] Implement validation and the Studio-managed catalog with locally served licensed fonts, starter gradients/patterns, and licensed picture presets.
- [ ] Implement UNA-managed image upload with authorization, size/dimension limits and server-side raster validation; reject SVG, spoofed files and unauthorized storage references.
- [ ] Verify private background access, rejected uploads and existing-post fallback after a catalog item is disabled; run tests and commit.

### Task 3: Build the editor and accessible card renderer

**Interfaces:** Composer submits plain text plus normalized style identifiers through the native content form. `template/card.html` receives escaped text and validated style values; preview uses the same CSS rules as saved cards.

- [ ] Write failing rendering cases for markup escaping, multiline Unicode, emoji and long unbroken text.
- [ ] Implement background, font, size, color and alignment controls, upload selection, and live preview.
- [ ] Fit text within bounded sizes; if it cannot fit, preserve all text and show a clear editing message rather than silently clipping it.
- [ ] Verify keyboard use, visible control labels, contrast guidance and readable fallback fonts.
- [ ] Exercise 320, 375, 768 and 1280 pixel layouts; verify preview and saved card consistency; run checks and commit.

### Task 4: Connect native publishing and interactions

**Interfaces:** Use the pinned UNA contracts for create/edit/delete, composer registration, Timeline events, visibility checks, comments, reactions and reposts. Do not create a second authorization model.

- [ ] Add failing authorization cases for guest creation, another member's edits/deletes, forged requests and private-post access.
- [ ] Register Styled Post in the native composer and render module cards through Timeline services.
- [ ] Implement native privacy and posting permissions plus editable entry forms and event cleanup.
- [ ] Test authenticated create/edit/delete, comment, reaction change and repost, verifying both interface feedback and persisted results.
- [ ] Verify anonymous, author, other-member and administrator visibility across feed, direct URL, search and asset access; commit only after checks pass.

### Task 5: Validate Studio controls and deliver a test candidate

**Interfaces:** Native installer ZIP rooted at `newton/my_una_styled_posts/`; source checkpoint, package manifest and SHA-256 tied to the candidate.

- [ ] Test Studio catalog management and membership permissions, including denying non-admin catalog mutations.
- [ ] Perform native fresh installation on an authorized test site, verify launcher and Apps info popup, then create and interact with representative posts.
- [ ] Disable and re-enable the module; verify existing content remains consistent. Validate uninstall cleanup on a disposable installation, including owned storage and Timeline registrations.
- [ ] Run PHP syntax, focused tests, package contract and relevant automated code-quality checks; record actual results and any unavailable checks.
- [ ] Freeze a source checkpoint, build and inspect the candidate ZIP, calculate its SHA-256 and prepare concise install/use documentation.
- [ ] Deliver the test candidate with explicit native-test status. Do not label it production-ready or publish a GitHub release without the required evidence and approval.

## Plan review

All approved controls and native interactions are assigned above. Exact target-version service signatures are deliberately an initial read-only task rather than guessed interfaces. Native execution in this session is recommended because the editor, renderer and Timeline adapter share the same content model.
