<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsVoteReactions extends BxTemplVoteReactions
{
    public function init($iId)
    {
        $result = parent::init($iId);
        // Native initialization has a void success return and false on failure.
        if ($result !== false)
            $this->_bQuickMode = false;
        return $result;
    }
}
