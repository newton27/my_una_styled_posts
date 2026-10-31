<?php
$aConfig = [
    'title' => 'My UNA Styled Posts',
    'version_from' => '0.3.3',
    'version_to' => '0.3.4',
    'vendor' => 'My UNA World',
    'compatible_with' => ['15.0.x'],
    'home_dir' => 'newton/my_una_styled_posts/updates/update_0.3.3_0.3.4/',
    'home_uri' => 'styled_posts_update_033_034',
    'module_dir' => 'newton/my_una_styled_posts/',
    'module_uri' => 'styled_posts',
    'db_prefix' => 'nw_styled_posts_',
    'class_prefix' => 'NwStyledPosts',
    'transcoders' => ['nw_styled_posts_motion_clip','nw_styled_posts_motion_poster'],
    'install' => ['execute_sql' => 1, 'update_files' => 1, 'update_languages' => 1, 'clear_db_cache' => 1, 'register_transcoders' => 0, 'clear_template_cache' => 1],
    'language_category' => 'My UNA Styled Posts',
    'delete_files' => [],
];

