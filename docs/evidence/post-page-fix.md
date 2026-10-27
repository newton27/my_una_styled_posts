# Post page author-block repair

Reported on myunaworld.com after publishing a Styled Post: undefined `txt_all_entries_by`, then a fatal import of absent `NwStyledPostsSearchResult.php` from the native author-count callback.

The author card's native `getAuthorDesc()` calls `serviceGetMenuAddonManageToolsProfileStats()`, which imports the module's SearchResult adapter. The initial development build omitted that adapter and the associated text mapping. This was a module completeness defect, not a core-file problem.

Repairs:
- Add `NwStyledPostsSearchResult` using UNA's native text search contract, only installed entry columns, and native privacy/content conditions.
- Supply the missing author text mapping and a readable author-label fallback for existing language caches.
- Render native search/browse units through the module card renderer and preserve its native visibility check.
- Honor the author filter on the collection linked from the author card.
- Include the previously supplied readable Post / Save changes / Delete buttons.

The missing adapter and author-label warning were each observed in failing local regressions, then verified passing after repair. The local suite passes: nine Python tests, 105 PHP assertions and syntax checks for all module PHP files. The native query engine and the live post page still need verification after applying this patch; the assistant has not changed the live site.

Patch root: `newton/my_una_styled_posts/`. Extract into the site's `public_html/modules/`, allow replacement and refresh the post page. Do not uninstall/reinstall, import SQL, or change UNA core files.
