# Draggable text in Styled Posts

User approved: drag text with mouse or touch within the background, save the position, reset to the original position, and retain existing posts' layout.

Use the existing editor and native form pipeline. Store integer text_x/text_y (0–100, relative to available travel space) plus text_position (auto/custom). Auto preserves the existing full-width alignment; custom uses a content-sized bounded text box. Do not use screen pixels or drag published cards. Keep text readable through existing font fitting/overflow handling. Pointer cancellation restores the starting position. Arrow keys move a focused preview text box (Shift for larger steps); Reset position returns to auto. Bounds recalculate after resize, text, font and backdrop changes.

Approaches: fixed pixels would break responsive cards; adding a canvas dependency would replace native selectable text. Use Pointer Events and the existing DOM/CSS, with normalized coordinates. This is the implementation choice under the approved behavior.

Release as 0.3.4 with a native 0.3.3-to-0.3.4 SQL/form migration. Preserve the 0.3.3 frozen packages, UNA core, existing content and media. Native acceptance remains a separate evidence gate before Marketplace publication.
