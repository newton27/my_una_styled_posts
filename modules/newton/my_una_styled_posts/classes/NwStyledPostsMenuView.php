<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsMenuView extends BxBaseModTextMenuView
{
    function __construct($aObject, $oTemplate = false)
    {
        $this->MODULE = 'nw_styled_posts';
        parent::__construct($aObject, $oTemplate);
    }
}
