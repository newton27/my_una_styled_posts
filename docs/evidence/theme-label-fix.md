# Theme label contrast repair

The composer set a fixed dark foreground, overriding the UNA dark theme. Its muted labels also had only 4.33:1 contrast against white.

The module composer now inherits the site's text color for headings, control labels, hints, counts, and feedback. White form controls retain an explicit dark value color. Quote-card text colors remain member-selected.

Browser verification observed a failing light-theme label contrast check before the change, then passed 14 label contrast checks across simulated light/dark UNA panels. Existing editor control, escaping, mobile layout, overflow and length checks also passed. The dark preview screenshot was visually inspected. Live-site verification is pending.

The repair ZIP contains only `newton/my_una_styled_posts/template/css/composer.css`; extract under `public_html/modules/`, clear UNA's CSS cache and refresh. No reinstall or SQL import is needed.
