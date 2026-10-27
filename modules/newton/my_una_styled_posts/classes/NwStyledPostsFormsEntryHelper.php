<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsFormsEntryHelper extends BxBaseModTextFormsEntryHelper
{
    public function onDataDeleteAfter($iContentId, $aContentInfo, $oProfile)
    {
        // Use the general helper: this module does not register text-module link/category tables.
        $result=BxBaseModGeneralFormsEntryHelper::onDataDeleteAfter($iContentId,$aContentInfo,$oProfile);
        if (!$result && !empty($aContentInfo['background_file'])) {
            $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
            if ($storage) $storage->deleteFile((int)$aContentInfo['background_file']);
            if ($storage && !empty($aContentInfo['background_previous']) && $aContentInfo['background_previous']!==$aContentInfo['background_file']) $storage->deleteFile((int)$aContentInfo['background_previous']);
        }
        return $result;
    }
}
