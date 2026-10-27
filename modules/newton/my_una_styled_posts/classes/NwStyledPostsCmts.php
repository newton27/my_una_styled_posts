<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsCmts extends BxTemplCmts
{
    function __construct($sSystem, $iId, $bInit = true, $oTemplate = false)
    {
        if (!$oTemplate)
            $oTemplate = BxDolModule::getInstance('nw_styled_posts')->_oTemplate;
        parent::__construct($sSystem, $iId, $bInit, $oTemplate);
        // Older theme comment layouts embed header variables no longer supplied
        // by UNA 15's item renderer. Keep the current native header as a slot.
        $this->_sTmplNameItem = 'nwsp_comment.html';
    }
    function getCommentsBlockTitle()
    {
        return _t($this->_aT['block_comments_title'], $this->getCounter([
            'show_counter_only' => true,
            'show_counter_empty' => true,
        ]));
    }
    protected function _getCounterLabel($iCount, $aParams = [])
    {
        return (string)(int)$iCount;
    }
}
