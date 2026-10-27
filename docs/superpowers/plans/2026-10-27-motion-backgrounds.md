# Motion Backgrounds Implementation Plan

> **For agentic workers:** Use superpowers:executing-plans to implement task by task. Track progress below.

**Goal:** Add bundled and uploaded 3–5 second motion backgrounds to Styled Posts.
**Architecture:** Module-owned private originals and derivatives, native UNA video queue/transcoder adapter, audience-checked delivery. Reuse card rendering for Timeline.
**Tech Stack:** PHP 8.3, UNA 15, native FFmpeg, JavaScript, CSS.
**Spec:** ../specs/2026-10-27-motion-backgrounds-design.md (approved in conversation).

## Global Constraints

No UNA core changes. Maximum upload 8 MiB, 1280 pixels per side, duration 3–5 seconds inclusive. Silent MP4 output and poster; GIF inputs use a module adapter. Existing image posts remain supported. Keep published 0.1.0 artifacts unchanged. New version/updater required before release.

## Review Focus

- Fractional duration, corrupt GIF and oversized decoded media must be rejected server-side.
- Private originals, converted MP4 and posters must not expose bearer URLs.
- Failed replacements retain prior media and clean up newly staged files.
- Pending/failed conversion cannot leave broken media or infinite browser polling.
- Dynamically inserted Timeline cards respect reduced motion and pause off-screen.

### Task 1: Media validation and native adapter

Files: classes/NwStyledPostsMotion.php, classes/NwStyledPostsTranscoder.php, tests/motion.php.
Interface: validateMetadata(array): array; inspect(string): array; adapter accepts validated GIF/video and exposes private ready-file metadata.
- [ ] Write boundary tests for exact/fractional duration, dimensions, animation, missing duration, corrupt/mismatched media.
- [ ] Watch new tests fail.
- [ ] Implement validation with bounded native FFmpeg probe; native conversion adapter strips audio and supports GIF.
- [ ] Run metadata tests and actual FFmpeg fixtures; commit.

### Task 2: Storage, form and audience-checked delivery

Files: FormEntry.php, Module.php, native install SQL/installer; tests/motion-integration.php.
Interface: module motion descriptor yields pending/failed/ready state and protected clip/poster URLs; form persists a single owned media reference.
- [ ] Write failure/permission/replacement tests, watch RED.
- [ ] Register private source/derived stores, MP4/poster transcoders and cleanup handlers; reuse source deletion hooks.
- [ ] Add uploads with preflight validation, queued conversion, pending/failure display and safe replacement/removal.
- [ ] Implement protected status/clip/poster actions with byte ranges and content checks; test denied viewers and invalid variants.
- [ ] Run task tests and full suite; commit.

### Task 3: Motion choices and playback

Files: Composer.php, Card.php, Template.php, js/composer.js, CSS, bundled media/provenance.
Interface: render consumes the motion descriptor and catalog; JS mounts card playback and composer media choices.
- [ ] Write render and browser checks for motion selection, uploads, failed previews, still fallback and dynamic cards; watch RED.
- [ ] Add original generated abstract loops with source/license; Motion picker and upload button; keep font/text/audience workflow.
- [ ] Add silent looping inline playback, bounded status polling, pause control, reduced motion, off-screen pause and cleanup.
- [ ] Run task tests and full suite; commit.

### Task 4: Version/update and release evidence

Files: install/config.php, updates/0.1.0_0.2.0/, tests, documentation and build script.
- [ ] Write installer/updater identity and SQL registration checks; watch RED.
- [ ] Add native 0.1.0→0.2.0 updater preserving current data and settings; document native conversion requirements.
- [ ] Run full suite, real media conversion and browser checks; inspect ZIP contents and hashes.
- [ ] Obtain fresh branch review; correct important findings. Record native live validation still required if no test environment access.
- [ ] Leave publication and existing release/tag untouched until explicitly authorized.

## Progress

Implementation and local checks complete. Native live validation remains open; no release certification.

Ruling: native Local queue processing is used even when optional queue-file transport is enabled, because one-second source tokens expire before delayed cron execution. Remote-only installations show uploads unavailable.

Ruling: ffprobe may be located in standard system paths as well as beside FFmpeg; live UNA uses plugins/ffmpeg/ffmpeg.exe with system ffprobe.

Fresh review completed. Four important/critical findings fixed: generic transcoder privacy route, expired queue URL transport, edit/finalization synchronization and strict decoded-file validation. Unit/browser/native-filter checks pass; native database concurrency remains untested.

Separate worktree preserves the previous installation-guide edits and untracked screenshots. No existing release/tag was changed.
