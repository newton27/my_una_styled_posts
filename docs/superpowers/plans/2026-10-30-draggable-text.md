# Draggable Text Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Position text by dragging in the preview, persist it and support reset.
**Architecture:** Pointer Events move a content-sized text box in available padded space. Native hidden form fields store bounded percentages and an auto/custom mode; published cards reuse the same positioning and fitting.
**Tech Stack:** Existing PHP, JavaScript, CSS and UNA native updater.
**Spec:** docs/superpowers/specs/2026-10-30-draggable-text-design.md

## Global Constraints
- No UNA core edits; preserve 0.3.3 frozen bytes.
- Existing posts keep auto layout; new coordinates default to 50/50.
- Native acceptance before publication.

## Review Focus
- Long text and Arabic remain within readable padded bounds.
- Touch cancellation and pointer capture do not leave dragging stuck.
- Resize, fonts and backdrop padding recalculate bounds.
- Bad saved coordinates cannot inject CSS or produce off-card text.
- Existing left/right alignment remains unchanged until deliberately repositioned.

### Task 1: Saved position contract and migration
- [x] Write PHP tests for defaults, invalid coordinates/mode, rendered mode and persistence through native form.
- [x] Verify failure, implement Style/Card/schema/form registrations and 0.3.3→0.3.4 updater.
- [x] Run PHP suite and package contract checks.

### Task 2: Editor positioning
- [x] Write real browser tests for drag, bounds, reset, keyboard, cancel, touch, resize and saved render.
- [x] Verify missing-feature failure, implement pointer interactions and shared card layout.
- [x] Run existing editor browser regressions and new tests.

### Task 3: Candidate
- [x] Review changes, run full checks in CI, package exact source with hashes.
- [x] Open draft PR and record native acceptance pending; do not publish untested package.

Execution evidence: docs/evidence/text-position-0.3.4.md. Native updater additionally validated on gayjax.life. No standalone installer certification or Marketplace publication.
