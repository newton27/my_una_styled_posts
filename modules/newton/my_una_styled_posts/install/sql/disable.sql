-- My UNA Styled Posts 0.1.0; each statement occupies one physical line.
DELETE FROM `sys_objects_page` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_pages_blocks` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_objects_menu` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_menu_sets` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_menu_items` WHERE `module` = 'nw_styled_posts';
DELETE FROM `sys_objects_privacy` WHERE `module` = 'nw_styled_posts';
DELETE m FROM `sys_acl_matrix` m INNER JOIN `sys_acl_actions` a ON m.`IDAction` = a.`ID` WHERE a.`Module` = 'nw_styled_posts';
DELETE FROM `sys_acl_actions` WHERE `Module` = 'nw_styled_posts';
