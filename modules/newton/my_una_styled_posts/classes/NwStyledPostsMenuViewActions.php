<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsMenuViewActions extends BxBaseModTextMenuViewActions
{
    function __construct($aObject, $oTemplate = false)
    {
        $this->_sModule = 'nw_styled_posts';
        parent::__construct($aObject, $oTemplate);
    }
    protected function _getMenuItemEditStyledPost($item) { return $this->_getMenuItemByNameActions($item); }
    protected function _getMenuItemDeleteStyledPost($item) { return $this->_getMenuItemByNameActions($item); }
    protected function _getMenuItemReaction($item, $params = [])
    {
        $params['object_options'] = array_merge($params['object_options'] ?? [], [
            'show_counter' => true,
            'show_legend' => true,
        ]);
        return parent::_getMenuItemReaction($item, $params);
    }
}
