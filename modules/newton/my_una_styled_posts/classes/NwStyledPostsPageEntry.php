<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsPageEntry extends BxBaseModTextPageEntry
{
    function __construct($aObject, $oTemplate = false)
    {
        $this->MODULE = 'nw_styled_posts';
        parent::__construct($aObject, $oTemplate);
    }
    protected function _setSubmenu($aParams) {}
}
