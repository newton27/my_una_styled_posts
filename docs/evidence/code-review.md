# Independent code review and fixes

An independent read-only review of the implementation identified four Important issues; no Critical or Minor findings were established.

1. Inherited safe GetInfo could bypass native visibility checking. Fixed with a visibility guard, a narrow safe-service allowlist, and guarded text/title/search-unit helpers. Regression tests deny private reads and allow authorized text reads.
2. Native deletion cleanup could query an unused polls table. Removed the inherited poll table and field mappings. A configuration regression pins this guard; native deletion on an installed UNA site remains pending.
3. Disabling Classic could leave editor JSON/hidden state disagreeing with the only enabled visible font. Added separate editor default selection and tests checking both the visible choice and JSON state.
4. Disabling a background could retain colors that become white-on-white. Disabled/unknown backgrounds now use a known readable dark/white pair. Contrast regression passes.

Each finding was reproduced by a failing local regression before its fix, and the full local suite passed afterward. A separate malformed-text rendering regression also caught and removed an array-to-string warning.

The reviewer did not certify native installation, database execution, authenticated workflows, uninstall, end-to-end Timeline behavior, or public HTTP reachability. Those require a live test installation and remain unresolved release gates. No second reviewer pass was used; fixes were verified by regressions and the complete local suite.
