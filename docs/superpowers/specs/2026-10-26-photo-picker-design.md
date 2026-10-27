# Existing-photo backgrounds
User requested a Choose from my photos button beside Upload photo and instructed us to continue. Display an accessible thumbnail picker of the current UNA profile's native Photos entries. Selecting updates the live preview; cancel keeps the previous background. Save a private independent background copy; the post audience controls that copy. Original photos remain untouched.

Use native Photos metadata, ownership and view checks on listing, preview and submission. Recheck at save, reject other profiles, hidden/unapproved/deleted photos and invalid/oversized files. Keep upload limits: JPG/PNG/WebP, 8 MiB, 16 MP, 8000 px. No core changes or schema migration. When Photos is absent show a readable unavailable message; uploading remains usable. Albums integration is outside this increment.

Approaches: references depend on source deletion/privacy and were rejected; independent copies fit existing private background storage and were recommended before the user's continue. Native authenticated form/CSRF remains the only write path. The user-facing picker notes that the background follows the post audience.
