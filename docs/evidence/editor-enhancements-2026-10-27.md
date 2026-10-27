# Editor enhancements 0.3.0 candidate

User approved design and explicitly requested deployment on 2026-10-27. Target remains gayjaxonline.com only. No other site or UNA core change is authorized by this work.

Implemented candidate: text backdrop (color, opacity, padding), non-destructive still-image position/zoom/reset, automatic/LTR/RTL direction, locally bundled Amiri, three additional system-font choices, administrator local font packs, external Pexels/Unsplash/license links, and audience-filtered gallery pagination.

Scope ruling: Arabic Modern, Friendly and Book use device fonts, explicitly documented; only Amiri is newly bundled. Font packs use administrator host-file upload and a validated manifest, not a new Studio upload UI. No external fonts or integrated stock-photo API are used.

Verification before freeze: PHP/Python/syntax suite; local browser checks for backdrop, crop/reset, long Arabic at 360/1280px, Amiri loading, image links, and motion playback/reduced-motion regression. Fresh independent review completed: no critical findings; three important findings fixed with observed failing/passing regression tests. Failed-submit restoration and raw numeric validation pass tests/editor-form.php. Long-card zoom containment passes the mobile browser reproduction. Final full suite: 19 Python tests, existing PHP assertions plus 28 new style assertions and form/gallery/font-pack checks; PHP/JS syntax passes. Editor and motion browser checks pass.

Deployment BLOCKED: the Browser connector reported that the originating connected browser context is unavailable. Existing iMac SSH BatchMode authentication to the known server was denied (no new credentials or permissions created). No server files, schema, or site posts changed in this turn. The live site remains at the prior 0.2.0 candidate plus its earlier motion-upload button CSS fix until actual native update evidence says otherwise.

Still required: native 0.2.0-to-0.3.0 Studio update and data/settings/original-file preservation, new saved-post edit/reopen on the site, health-log checks, fresh native 0.3.0 installation, Standard-member/non-owner role coverage, and previously deferred real MP4/GIF upload conversion/privacy/replacement/failure tests. Local browser fixtures are not live-native proof. No GitHub release/tag/assets or Marketplace listing changed.

Minor findings deferred: (1) an administrator-installed fonts.json symlink is not itself canonicalized, although font and license file paths are contained; no member can install packs. An incorrect assumption here could let an administrator load a manifest outside the font-pack directory. (2) exactly 24 final visible rows may show an Older posts link leading to an empty page; this is a navigation polish issue, not a privacy leak.
