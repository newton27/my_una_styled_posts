# Motion Library Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add twelve original loops and a prominent labeled motion picker, then deploy to gayjaxonline.com.

**Architecture:** Extend the existing allowlisted catalog and native options. Bundle generated MP4/poster pairs; use unchanged card/video handling.

**Tech Stack:** PHP, CSS, NumPy, FFmpeg, UNA native Studio updater.

**Spec:** docs/superpowers/specs/2026-10-28-motion-library-design.md

## Global Constraints
- Target only gayjaxonline.com; module files only; no GitHub release mutation.
- Keep both old motion asset bytes and IDs, all post rows, prior settings and media unchanged.
- Four seconds, silent H.264 yuv420p, 640×480; still thumbnails only.
- Version 0.3.1; native prior version 0.3.0.

## Review Focus
- Disabled choices omitted; old saved choices preserved.
- Thumbnail text readable on busy/dark backgrounds; narrow and RTL layout usable.
- Reduced motion and pause behavior unchanged.
- Loop joins smooth and clips/posters load without external sources.
- Native update adds settings once and preserves previous values.

### Task 1: Library and picker
Files: scripts/create_motion_assets.py; classes/NwStyledPostsStyle.php, NwStyledPostsComposer.php; template/css/composer.css; template/motion/*; install config/langs/enable.sql; updates/update_0.3.0_0.3.1/install/*; tests/motion-library.php, test_motion_library.py.
- [ ] Add a behavioral test for fourteen catalog choices, valid assets, disabled-option behavior and labeled selectable motion cards; observe RED.
- [ ] Generate twelve original seamless illustrated loops, preserving old assets. Register settings in fresh install/native updater, bump module version, style summary/grid; observe GREEN and run full suite.
- [ ] Inspect contact sheet and local browser selection/pause/reduced-motion/RTL/mobile behavior. Fresh reviewer checks final diff; fix material findings.
- [ ] Commit and push source checkpoint on existing feature branch using expected-SHA guard, build immutable install/update artifacts with hashes.

### Task 2: Native deployment
Files: docs/evidence/motion-library-2026-10-28.md and package evidence.
- [ ] Capture private server DB/module/media/settings checkpoint and verify prior runtime matches 0.3.0 source.
- [ ] Upload exact frozen updater using browser chooser and stage module-local update folder. Apply through Studio native update.
- [ ] Verify version, runtime hashes, old row/settings/media preservation and fourteen enabled choices. Exercise new background in private editor/save/reopen, verify assets/playback and health logs; save screenshot and deployment evidence.
- [ ] Push evidence and report deployment with remaining wider-release gates accurately stated.
