# Motion backgrounds for My UNA Styled Posts

Status: proposed design; implementation not started.

## Intent and approved approach

Members can choose bundled motion backgrounds or upload short videos and animated GIFs. User selected both options and requested reuse of UNA's existing video conversion infrastructure.

Alternatives considered: bundled media alone is simpler but excludes personal uploads; an independent conversion worker duplicates UNA infrastructure. Selected: bundled media plus uploads using module-owned UNA storage/transcoder registrations and the existing UNA conversion queue. No UNA core changes.

## Member experience

A Motion choice accompanies existing backgrounds. A small bundled collection includes license/provenance records permitting commercial redistribution. Upload video or GIF provides the same text overlay, audience selection and editing controls as photo backgrounds. Target duration is 3–5 seconds. Longer uploads are rejected with an actionable message; no trimming editor in this iteration.

Playback is muted, looping and inline. Show a poster while conversion is pending, autoplay is unavailable, or reduced motion is requested. Pause playback off screen and provide a motion pause control. Display processing/failure states with retry or replacement choices. Keep existing text and background when replacement fails.

## Native integration

Reuse BxDolTranscoderVideo for MP4 and poster generation, native queue processing and storage lifecycle hooks. Register module-owned objects rather than depending on Videos module object names. Source reference: unacms/UNA master, inc/classes/BxDolTranscoderVideo.php and modules/boonex/videos/classes/BxVideosConfig.php (inspected 2026-10-27); verify exact target UNA 15 contracts before implementation.

The native video transcoder currently accepts video/* MIME types only. GIF support therefore requires a module adapter accepting validated GIF inputs; it must not be assumed to work through the stock MIME gate. Validate frame timing/duration server-side; native getDuration truncates fractional seconds and alone cannot enforce a precise five-second maximum.

## Privacy and lifecycle

Originals, converted clips and posters must follow the Styled Post's audience. Existing background access checks cannot be bypassed by public transcoder URLs. Confirm native protected delivery supports the required behavior before exposing media. Editing, replacement, deletion and failed conversion clean up only module-owned files, including queued/derived media. Never delete members' source library media.

Timeline renders the styled card with its text and motion background, preserving native entry permissions, reactions and comments. It must not create a second public Videos entry.

## Builder decisions and release gates

Proposed upload limit: 8 MiB, maximum 1280 pixels on either side, 3–5 seconds inclusive. Enforce decoded duration and media validity, not filename or browser metadata. Output silent MP4 with a still poster; GIF originals are not served as uncontrolled animations. Settings permit disabling motion uploads and individual bundled choices.

Tests cover GIF/video boundaries, corrupt media, conversion pending/failure/success, private originals/outputs/posters, replacement/removal, reduced motion, off-screen playback, Timeline rendering and native fresh install/update. Preserve existing still-image behavior. New version and native updater are required; do not overwrite published 0.1.0 packages or deploy before review.

No existing-video library picker, external GIF search service or long-video editing in this iteration. Licensing applies to bundled choices; members remain responsible for uploaded content.
