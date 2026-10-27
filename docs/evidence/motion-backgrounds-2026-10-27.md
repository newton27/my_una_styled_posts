# Motion backgrounds 0.2.0 development candidate

Approved scope: bundled 3–5 second loops and member video/GIF uploads, reusing UNA conversion. No core changes.

## Implemented

- Two original MIT-licensed four-second abstract loops, JPEG posters, reproducible generation source.
- Motion picker and upload choice; existing fonts, photos, text and audience workflow retained.
- Server limits: 8 MiB, 1280 pixels per side, 3–5 seconds. Fileinfo, packet timing and bounded strict FFmpeg decode checks; corrupt packet payload fixture rejected.
- Module-owned native MP4/poster transcoders and native queue. Local originals are copied directly at conversion time; optional queue-file transport is bypassed to avoid expired one-second source URLs. Remote-only conversion installations cannot upload motion in this iteration.
- Silent MP4 output. Generic image_transcoder URL delivery and conversion denied by module adapter; clip/poster/status/previous-background routes authorize the entry first.
- Byte ranges and HEAD delivery. Previous background retained until both replacements finish. Editing and conversion finalization use the same entry row lock. Native deletion hooks remove derivatives and queues.
- Muted inline looping, explicit play/pause, reduced-motion still poster, off-screen/page-hidden pause and bounded conversion-status polling. Dynamic card insertion supported.
- Native 0.1.0-to-0.2.0 updater template and verified source/ZIP builder. Published 0.1.0 artifacts unchanged.

## Local evidence

- 16 Python tests; 213 PHP assertions; PHP and JavaScript syntax checks passed.
- Chromium editor checks (four viewport widths), photo picker regression and motion checks passed. Motion covered reduced-motion no-fetch, explicit play/pause, autoplay, off-screen pause and dynamic pending-to-ready insertion.
- Actual UNA BxDolTranscoderVideo MP4 filter exercised with module GIF/silent adapter: 3-second GIF became H.264 MP4, 320×240, duration 3.000000; no audio stream.
- Independent reviewer found generic endpoint privacy exposure, expired queue source URLs, edit/finalization race and probe-only corruption acceptance. All four corrected; regression checks added. Native database concurrency still needs live validation.

## Live preflight / remaining gates

Read-only gayjaxonline.com preflight confirms PHP 8.3.6, configured native plugins/ffmpeg/ffmpeg.exe, /usr/bin/ffprobe, proc_open and fileinfo available.

Native 0.2.0 fresh installation, existing-post preservation through Studio update, real authenticated upload/queue conversion, guest/non-owner clip/poster denial, native Timeline and concurrent editing/deletion remain NOT TESTED for this candidate. No production or Marketplace readiness claim. Existing GitHub release/tag has not been changed.
