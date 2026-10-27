-- My UNA Styled Posts 0.1.0; each statement occupies one physical line.
DELETE FROM `sys_objects_form` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_form_displays` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_form_inputs` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_objects_content_info` WHERE `alert_unit` = 'nw_styled_posts';
DELETE FROM `sys_objects_cmts` WHERE `Module` = 'nw_styled_posts';
DELETE FROM `sys_objects_vote` WHERE `Module` = 'nw_styled_posts';
DELETE FROM `sys_objects_view` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_form_display_inputs` WHERE `display_name` IN ('nw_styled_posts_entry_add','nw_styled_posts_entry_edit','nw_styled_posts_entry_view','nw_styled_posts_entry_delete');
DELETE FROM `sys_objects_storage` WHERE `object` = 'nw_styled_posts_backgrounds';
DELETE FROM `sys_storage_tokens` WHERE `object` = 'nw_styled_posts_backgrounds';
DELETE o FROM `sys_options` o INNER JOIN `sys_options_categories` c ON o.`category_id` = c.`id` WHERE c.`name` = 'nw_styled_posts';
DELETE FROM `sys_options_categories` WHERE `name` = 'nw_styled_posts';
DELETE FROM `sys_options_types` WHERE `name` = 'nw_styled_posts';
DELETE p,w,b,l FROM `sys_std_pages` p LEFT JOIN `sys_std_widgets` w ON p.`id` = w.`page_id` LEFT JOIN `sys_std_widgets_bookmarks` b ON w.`id` = b.`widget_id` LEFT JOIN `sys_std_pages_widgets` l ON w.`id` = l.`widget_id` WHERE p.`name` = 'nw_styled_posts';
DROP TABLE IF EXISTS `nw_styled_posts_entries`,`nw_styled_posts_backgrounds`,`nw_styled_posts_cmts`,`nw_styled_posts_reactions`,`nw_styled_posts_reactions_track`,`nw_styled_posts_views_track`;
