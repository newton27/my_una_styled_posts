# Existing-photo Picker Implementation Plan
> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.
**Goal:** Select existing owned Photos images as styled-post backgrounds.
**Architecture:** A module-owned PhotoLibrary adapter lists and resolves native Photos entries, verifies ownership and native view permission, and validates real image bytes before copying into existing private background storage. Read-only authenticated endpoints feed a dialog; native form save reauthorizes the selection.
**Tech Stack:** PHP 8.3, UNA 15 RC2, vanilla JavaScript, Playwright.
**Spec:** docs/superpowers/specs/2026-10-26-photo-picker-design.md
## Global Constraints
- No UNA core edits; no database schema changes.
- Source photos are untouched. Copies use post visibility.
- Existing JPG/PNG/WebP and 8 MiB/16 MP/8000 px restrictions apply.
## Review Focus
- Switching profiles between selection and save must deny the previous profile's selection.
- Missing/uninstalled Photos and source deletion must fail readably.
- A new upload or preset must clear a prior photo selection.
- Cancel/error must retain the current background.
- Invalid remote/local bytes must never enter background storage.
### Task 1: Photo library and native form integration
Files: create classes/NwStyledPostsPhotoLibrary.php; modify Module.php, FormEntry.php; tests/photo-library.php and tests/photo-form.php.
Interfaces: PhotoLibrary::listing(int $before=0): array; resolve(int $id): array|false; copy(int $id,int $contentId): int; temporaryImage(int $id): array; Module::photoLibrary(); own-photo list/image actions.
- [x] Write tests for ownership, native visibility, pending/hidden/missing entries, profile switch, absent module, corrupt bytes, independent copy and cleanup; run and observe missing-feature failure.
- [x] Implement adapter and read-only endpoints; integrate selected photo with native form validation and existing cleanup.
- [x] Run PHP tests and scripts/check.sh; expect all pass; commit.
### Task 2: Picker UI and package
Files: Composer.php, js/composer.js, template/css/composer.css; tests/photo-picker.cjs, tests/preview.php; docs/evidence/photo-picker.md.
Interfaces: Composer optional endpoint base; selected photo hidden input belongs to rendered composer; browser reads items/id/url/title and next cursor.
- [x] Write browser tests for open/select/cancel, pagination/errors/empty, upload/preset clearing, mobile layout, and no unintended submit; run and observe missing-button failure.
- [x] Implement dialog and toolbar buttons, status/empty/error messages, focus restoration and preview update.
- [x] Run full PHP/browser suites; request independent review; fix important findings with regressions.
- [x] Commit and package immutable module-only patch ZIP with checksum, verify members and persist deliverable.
