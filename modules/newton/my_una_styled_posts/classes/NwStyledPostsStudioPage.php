<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsStudioPage extends BxTemplStudioModule
{
    protected function getSettings()
    {
        $html=parent::getSettings();
        $module=BxDolModule::getInstance('nw_styled_posts');
        if (!$module || !$module->_isAdministrator()) return $html;
        require_once __DIR__.'/NwStyledPostsFontPacks.php';
        $packs=NwStyledPostsFontPacks::load(NwStyledPostsFontPacks::directory());
        $fontHelp='<div class="bx-def-padding"><h2>Additional fonts</h2><p>Install administrator font packs in storage/nw_styled_posts_fonts/ using fonts.json, local WOFF2 files and their licenses. See the module installation guide.</p><p>'.count($packs['fonts']).' custom fonts available.</p>';
        foreach($packs['errors'] as $error) $fontHelp.='<p>'.NwStyledPostsStyle::escape($error).'</p>';
        $fontHelp.='</div>';
        $rows=$module->_oDb->pendingEntries();
        $links='';
        foreach($rows as $post) $links.='<li><a href="'.NwStyledPostsStyle::escape(BX_DOL_URL_ROOT.'page.php?i=view-styled-post&id='.(int)$post['id']).'" target="_blank" rel="noopener">'.NwStyledPostsStyle::escape($post['title']).'</a></li>';
        return $html.$fontHelp.'<div class="bx-def-padding"><h2>Pending Styled Posts</h2>'.($links?'<p>Open a post to review it and use its native Approve action.</p><ul>'.$links.'</ul>':'<p>No pending Styled Posts.</p>').'<p>Posting rights are managed in Studio → Memberships → My UNA Styled Posts.</p></div>';
    }
}
