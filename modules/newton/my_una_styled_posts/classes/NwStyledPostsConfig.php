<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsConfig extends BxBaseModTextConfig
{
    function __construct($aModule)
    {
        parent::__construct($aModule);
        $this->CNF = array_merge($this->CNF, [
            'ICON' => 'quote-right',
            'TABLE_ENTRIES' => 'nw_styled_posts_entries',
            'TABLE_ENTRIES_FULLTEXT' => 'title_text',
            'FIELD_ID' => 'id', 'FIELD_AUTHOR' => 'author',
            'FIELD_ADDED' => 'added', 'FIELD_CHANGED' => 'changed',
            'FIELD_TITLE' => 'title', 'FIELD_TEXT' => 'text', 'FIELD_TEXT_ID' => 'nw-styled-post-text',
            'FIELD_ALLOW_VIEW_TO' => 'allow_view_to', 'FIELD_CF' => 'cf',
            'FIELD_STATUS' => 'status', 'FIELD_STATUS_ADMIN' => 'status_admin',
            'FIELD_VIEWS' => 'views', 'FIELD_COMMENTS' => 'comments',
            'FIELDS_WITH_KEYWORDS' => ['title', 'text'],
            'URI_VIEW_ENTRY' => 'view-styled-post', 'URI_ADD_ENTRY' => 'create-styled-post',
            'URI_EDIT_ENTRY' => 'edit-styled-post', 'URI_AUTHOR_ENTRIES' => 'styled-posts-home',
            'URL_HOME' => 'page.php?i=styled-posts-home',
            'PARAM_AUTO_APPROVE' => 'nw_styled_posts_auto_approve',
            'PARAM_CHARS_SUMMARY' => 'nw_styled_posts_summary_chars',
            'PARAM_SEARCHABLE_FIELDS' => 'nw_styled_posts_searchable_fields',
            'PARAM_POLL_ENABLED' => false, 'PARAM_LINKS_ENABLED' => false,
            'PARAM_MULTICAT_ENABLED' => false,
            'OBJECT_VIEWS' => 'nw_styled_posts',
            'OBJECT_REACTIONS' => 'nw_styled_posts_reactions',
            'OBJECT_COMMENTS' => 'nw_styled_posts',
            'OBJECT_PRIVACY_VIEW' => 'nw_styled_posts_allow_view_to',
            'OBJECT_FORM_ENTRY' => 'nw_styled_posts',
            'OBJECT_FORM_ENTRY_DISPLAY_ADD' => 'nw_styled_posts_entry_add',
            'OBJECT_FORM_ENTRY_DISPLAY_EDIT' => 'nw_styled_posts_entry_edit',
            'OBJECT_FORM_ENTRY_DISPLAY_DELETE' => 'nw_styled_posts_entry_delete',
            'OBJECT_FORM_ENTRY_DISPLAY_VIEW' => 'nw_styled_posts_entry_view',
            'OBJECT_MENU_ACTIONS_VIEW_ENTRY' => 'nw_styled_posts_view',
            'OBJECT_MENU_ACTIONS_VIEW_ENTRY_ALL' => 'nw_styled_posts_view_actions',
            'OBJECT_CONTENT_INFO' => 'nw_styled_posts',
            'OBJECT_CMTS_CONTENT_INFO' => 'nw_styled_posts_cmts',
            'MENU_ITEM_TO_METHOD' => [
                'nw_styled_posts_view' => ['edit-styled-post' => 'checkAllowedEdit', 'delete-styled-post' => 'checkAllowedDelete', 'approve' => 'checkAllowedApprove'],
            ],
            'T' => [
                'txt_all_entries_by' => '_nw_styled_posts_txt_all_entries_by',
                'txt_sample_single' => '_nw_styled_posts_sample',
                'txt_sample_single_with_article' => '_nw_styled_posts_sample_article',
                'txt_sample_comment_single' => '_nw_styled_posts_comment',
                'txt_sample_reaction_single' => '_nw_styled_posts_reaction',
                'txt_sample_vote_single' => '_nw_styled_posts_reaction',
                'txt_sample_action' => '_nw_styled_posts_action',
                'txt_msg_status_pending' => '_nw_styled_posts_pending',
                'txt_msg_status_hidden' => '_nw_styled_posts_hidden',
            ],
        ]);
        // Unused inherited attachment features must not invoke absent tables.
        foreach (array_keys($this->CNF) as $key) {
            if (str_starts_with($key,'TABLE_POLLS') || str_starts_with($key,'FIELD_POLL') || $key==='OBJECT_VOTES_POLL_ANSWERS') unset($this->CNF[$key]);
        }
        $this->_aPrefixes = ['style' => 'nw-styled-posts'];
        $this->_aGridObjects = [];
    }
}
