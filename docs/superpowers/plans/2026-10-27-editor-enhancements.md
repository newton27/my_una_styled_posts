# Editor Enhancements Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Deploy the approved editor improvements to gayjaxonline.com through a native updater.
**Architecture:** Preserve original media and save allowlisted presentation fields. Share validated values between PHP rendering, native forms and live preview. Use module-owned local fonts and native audience checks.
**Tech Stack:** UNA 15, PHP 8.3, MariaDB, vanilla JS/CSS, local fonts.
**Spec:** docs/superpowers/specs/2026-10-27-editor-enhancements-design.md

## Global Constraints
- No UNA core edits, other-site changes, release/tag publication or overwritten RC artifacts.
- Backdrop none/box; opacity 0–100, padding 0–32; image position 0–100 and zoom 100–250.
- Defaults retain existing appearance. Only still images use framing. User explicitly approved implementation/deployment; execute inline.

## Review Focus
- Missing/invalid historical fields must render safely (Task 1).
- Long multiline text and Arabic diacritics must not clip (Tasks 1–2).
- New image selection must reset framing, editing must retain it (Task 1).
- Inaccessible posts must not leak through pagination (Task 3).
- Native updater must preserve user records and reject source/version mismatch (Task 4).

### Task 1: Backdrop and framing
Files: Style.php, Card.php, Composer.php, FormEntry.php, composer.js, card.css, composer.css; tests/style.php, render.php, editor-enhancements.cjs.
Interfaces: Style::normalize/forDisplay return seven new fields; native form collects all via Style::FIELDS. Card and preview consume these values.
- [x] Add and run failing validator/render assertions for bounds, default appearance and escaped output.
- [x] Implement normalization, rendering, accessible controls, reset and preview synchronization.
- [x] Run PHP assertions and browser checks for mobile/desktop, multiline text, framing/reset and edit restoration; commit.

### Task 2: Fonts and Arabic
Files: font catalog, local assets/licenses, card CSS, composer direction controls, font-pack loader/docs; tests/font-packs.php.
Interfaces: Font catalog merges validated local packs; text_direction auto/ltr/rtl travels with saved style.
- [x] Test bad pack paths/signatures and fallback; implement bounded local manifest loader.
- [x] Verify upstream font licenses; bundle Arabic faces and additional Latin faces locally.
- [x] Test direction/shaping, locally loaded fonts and long Arabic quotes; commit.

### Task 3: Image links and browsing
Files: Composer.php, Module.php, Db.php; tests/gallery.php.
Interfaces: gallery cursor reads bounded batches, applies checkAllowedView and emits Older posts link.
- [x] Add official Pexels/Unsplash/license links, retaining draft in original tab.
- [x] Test pagination against inaccessible and hidden rows; implement 24 visible posts per page with bounded scan and cursor progress.
- [x] Verify existing native menu entry and profile filter; commit.

### Task 4: Native update, packaging and deployment
Files: install/config.php, install SQL/language, updates/update_0.2.0_0.3.0, scripts/build.py, docs/evidence.
- [x] Register fields in full installer and idempotent native updater, with 0.3.0 identity; retain immutable 0.2 artifacts and document two-step route from 0.1.
- [x] Test version/source consistency and native SQL formatting; run full checks and independent review; fix findings.
- [ ] Freeze commit; build and hash new installer/updater; back up target module/data; transfer exact updater; run Studio update.
- [ ] Verify version, fields, old records, settings, original file hashes, saved new controls, logs and live screenshots.
- [ ] Push source/evidence to feature branch. Clearly retain unfinished motion and fresh-install release gates.
