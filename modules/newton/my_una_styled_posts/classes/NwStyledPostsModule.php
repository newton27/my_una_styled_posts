<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsStyle.php';
require_once __DIR__.'/NwStyledPostsPhotoLibrary.php';
class NwStyledPostsModule extends BxBaseModTextModule
{
    public function photoLibrary(): NwStyledPostsPhotoLibrary
    {
        $photos=BxDolModule::getInstance('bx_photos');
        $source=$photos ? BxDolStorage::getObjectInstance($photos->_oConfig->CNF['OBJECT_STORAGE']) : null;
        return new NwStyledPostsPhotoLibrary($this,$photos,$source,BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds'));
    }
    public function actionOwnPhotos()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: private, no-store, max-age=0');
        header('X-Content-Type-Options: nosniff');
        if ($this->getUserId()<1) { http_response_code(403); echo json_encode(['error'=>'Please sign in to choose your photos.']); return; }
        $before=bx_get('before');
        $before=is_scalar($before) && preg_match('/^[0-9]{1,10}$/D',(string)$before)?(int)$before:0;
        $result=$this->photoLibrary()->listing($before);
        foreach($result['items'] as &$item) $item['url']=BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'own_photo/'.$item['id'];
        unset($item);
        echo json_encode($result,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE);
    }
    public function actionOwnPhoto($id=0)
    {
        header('Cache-Control: private, no-store, max-age=0');
        header('X-Content-Type-Options: nosniff');
        if ($this->getUserId()<1) { http_response_code(404); return; }
        try { $image=$this->photoLibrary()->temporaryImage((int)$id); }
        catch (InvalidArgumentException $e) { http_response_code(404); return; }
        try {
            header('Content-Type: '.$image['mime_type']);
            header('Content-Disposition: inline');
            header('Content-Length: '.filesize($image['path']));
            readfile($image['path']);
        } finally { unlink($image['path']); }
    }

    public function serviceGetSafeServices()
    {
        return array_fill_keys(['ModuleIcon','GetLink','GetSearchResultUnit','GetCreatePostForm','EntityCreate','EntityEdit','EntityDelete','GetInfo','EditEntity','EntityTextBlock','EntityInfo','EntityComments','EntityAllActions','EntityActions','EntityAuthor','EntitySocialSharing','BrowseCards'],'');
    }

    public function serviceGetInfo($iContentId, $bSearchableFieldsOnly = true)
    {
        $post=$this->_oDb->getContentInfoById((int)$iContentId);
        if (!$post || $this->checkAllowedView($post)!==CHECK_ACTION_RESULT_ALLOWED) return [];
        return parent::serviceGetInfo($iContentId,$bSearchableFieldsOnly);
    }

    public function serviceGetText($iContentId)
    {
        $post=$this->_oDb->getContentInfoById((int)$iContentId);
        return $post && $this->checkAllowedView($post)===CHECK_ACTION_RESULT_ALLOWED ? $post['text'] : '';
    }

    public function serviceGetTitle($iContentId)
    {
        $post=$this->_oDb->getContentInfoById((int)$iContentId);
        return $post && $this->checkAllowedView($post)===CHECK_ACTION_RESULT_ALLOWED ? $post['title'] : '';
    }

    public function serviceGetSearchResultUnit($iContentId, $sUnitTemplate = '')
    {
        $post=$this->_oDb->getContentInfoById((int)$iContentId);
        if (!$post || $this->checkAllowedView($post)!==CHECK_ACTION_RESULT_ALLOWED) return '';
        return $this->_bIsApi ? ['title'=>$post['title'],'text'=>$post['text']] : $this->_oTemplate->card($post,bx_is_dynamic_request());
    }

    public function serviceGetTimelinePost($aEvent, $aBrowseParams = [])
    {
        $post=$this->_oDb->getContentInfoById((int)$aEvent['object_id']);
        if (!$post || $this->checkAllowedView($post) !== CHECK_ACTION_RESULT_ALLOWED) return false;
        $result=parent::serviceGetTimelinePost($aEvent,$aBrowseParams);
        if (is_array($result)) $result['_cache']=false;
        return $result;
    }

    protected function _getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams = [])
    {
        if ($this->_bIsApi) return ['text'=>$aContentInfo['text'],'url'=>BX_DOL_URL_ROOT.'page.php?i=view-styled-post&id='.(int)$aContentInfo['id']];
        return ['raw'=>$this->_oTemplate->card($aContentInfo,!empty($aBrowseParams['dynamic_mode'])),'_cache'=>false];
    }

    public function serviceBrowseCards()
    {
        if ($this->checkAllowedBrowse() !== CHECK_ACTION_RESULT_ALLOWED) return MsgBox(_t('_sys_txt_access_denied'));
        $html='';
        $requested=bx_get('profile_id');
        $profileId=is_scalar($requested) && preg_match('/^\d+$/D',(string)$requested)?(int)$requested:0;
        foreach($this->_oDb->recentEntries($profileId) as $post) {
            if ($this->checkAllowedView($post) !== CHECK_ACTION_RESULT_ALLOWED) continue;
            $url=BX_DOL_URL_ROOT.'page.php?i=view-styled-post&id='.(int)$post['id'];
            $html.='<a href="'.NwStyledPostsStyle::escape($url).'" aria-label="'.NwStyledPostsStyle::escape($post['title']).'">'.$this->_oTemplate->card($post).'</a>';
        }
        return $html ? '<div class="nwsp-gallery">'.$html.'</div>' : MsgBox(_t('_Empty'));
    }

    public function getBackgroundUrl(array $post): string
    {
        if (empty($post['background_file'])) return '';
        return BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'background/'.(int)$post['id'];
    }

    public function getAuthorizedBackground($id)
    {
        $post=$this->_oDb->getContentInfoById((int)$id);
        if (!$post || $this->checkAllowedView($post) !== CHECK_ACTION_RESULT_ALLOWED || empty($post['background_file'])) return false;
        $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
        return $storage ? $storage->getFile((int)$post['background_file']) : false;
    }

    public function actionBackground($id = 0)
    {
        $file=$this->getAuthorizedBackground((int)$id);
        if (!$file || !in_array($file['mime_type'],['image/jpeg','image/png','image/webp'],true) || empty($file['private'])) {
            http_response_code(404); return;
        }
        // Local storage is selected explicitly in install.sql. Never emit a bearer-token URL.
        $base=realpath(BX_DIRECTORY_STORAGE.'nw_styled_posts_backgrounds');
        $path=$base ? realpath($base.DIRECTORY_SEPARATOR.$file['path']) : false;
        if (!$path || !str_starts_with($path,$base.DIRECTORY_SEPARATOR) || !is_file($path)) { http_response_code(404); return; }
        header('Content-Type: '.$file['mime_type']);
        header('Content-Disposition: inline');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        header('Content-Length: '.filesize($path));
        readfile($path);
    }
}
