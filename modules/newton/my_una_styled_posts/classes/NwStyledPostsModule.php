<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsStyle.php';
require_once __DIR__.'/NwStyledPostsPhotoLibrary.php';
require_once __DIR__.'/NwStyledPostsMotion.php';
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
        $rawBefore=bx_get('before');$before=is_scalar($rawBefore) && preg_match('/^[0-9]{1,10}$/D',(string)$rawBefore)?(int)$rawBefore:0;
        require_once __DIR__.'/NwStyledPostsGallery.php';
        $page=NwStyledPostsGallery::page(fn($cursor,$limit)=>$this->_oDb->recentEntries($profileId,$cursor,$limit),fn($post)=>$this->checkAllowedView($post)===CHECK_ACTION_RESULT_ALLOWED,$before);
        foreach($page['items'] as $post) {
            if ($this->checkAllowedView($post) !== CHECK_ACTION_RESULT_ALLOWED) continue;
            $url=BX_DOL_URL_ROOT.'page.php?i=view-styled-post&id='.(int)$post['id'];
            $html.='<a href="'.NwStyledPostsStyle::escape($url).'" aria-label="'.NwStyledPostsStyle::escape($post['title']).'">'.$this->_oTemplate->card($post).'</a>';
        }
        $older=$page['next']?'<p><a class="bx-btn" href="'.NwStyledPostsStyle::escape(BX_DOL_URL_ROOT.'page.php?i=styled-posts-home&before='.$page['next'].($profileId?'&profile_id='.$profileId:'')).'">Older posts</a></p>':'';
        return ($html?'<div class="nwsp-gallery">'.$html.'</div>':MsgBox(_t('_Empty'))).$older;
    }

    public function actionFont($id='')
    {
        if (!is_string($id) || !preg_match('/^[a-z][a-z0-9_]{0,23}$/D',$id)) {http_response_code(404);return;}
        require_once __DIR__.'/NwStyledPostsFontPacks.php';
        $packs=NwStyledPostsFontPacks::load(NwStyledPostsFontPacks::directory());
        $font=$packs['fonts']['custom_'.$id]??null;
        if (!$font) {http_response_code(404);return;}
        header('Content-Type: font/woff2');header('X-Content-Type-Options: nosniff');header('Cache-Control: public, max-age=3600');
        readfile($font['path']);
    }

    public function getBackgroundUrl(array $post): string
    {
        if (empty($post['background_file'])) return '';
        $file=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds')->getFile((int)$post['background_file']);
        if (!$file || in_array($file['mime_type'],NwStyledPostsMotion::MIME_TYPES,true)) return '';
        return BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'background/'.(int)$post['id'];
    }

    public function queueMotion(int $source): bool
    {
        foreach (['clip','poster'] as $variant) {
            $transcoder=BxDolTranscoder::getObjectInstance('nw_styled_posts_motion_'.$variant);
            if (!$transcoder || !$transcoder->enqueue($source)) return false;
        }
        return true;
    }

    public function motionReady(int $source): bool
    {
        foreach (['clip','poster'] as $variant) {
            $object=BxDolTranscoder::getObjectInstance('nw_styled_posts_motion_'.$variant);
            if (!$object || !$object->readyFile($source)) return false;
        }
        return true;
    }

    public function finalizeMotion(int $source): void
    {
        if (!$this->motionReady($source)) return;
        foreach ($this->_oDb->releasePreviousBackgrounds($source) as $id) {
            $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
            if ($storage) $storage->deleteFile($id);
        }
    }

    protected function authorizedMotionSource($id)
    {
        $file=$this->getAuthorizedBackground((int)$id);
        return $file && !empty($file['private']) && in_array($file['mime_type'],NwStyledPostsMotion::MIME_TYPES,true) ? $file : false;
    }

    public function getAuthorizedMotion($id, string $variant)
    {
        if ($variant==='previous_image' || $variant==='previous_poster') {
            $previous=$this->getAuthorizedBackground((int)$id,true);
            if (!$previous || empty($previous['private'])) return false;
            if ($variant==='previous_image') {
                if (!in_array($previous['mime_type'],['image/jpeg','image/png','image/webp'],true)) return false;
                $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
                $path=$storage->getFileLocalPath((int)$previous['id']);
                return $path ? ['file'=>$previous,'path'=>$path] : false;
            }
            $poster=BxDolTranscoder::getObjectInstance('nw_styled_posts_motion_poster');
            return $poster ? $poster->readyFile((int)$previous['id']) : false;
        }
        if (!in_array($variant,['clip','poster'],true)) return false;
        $file=$this->authorizedMotionSource($id);
        if (!$file) return false;
        $transcoder=BxDolTranscoder::getObjectInstance('nw_styled_posts_motion_'.$variant);
        return $transcoder ? $transcoder->readyFile((int)$file['id']) : false;
    }

    public function getMotionDescriptor(array $post): array
    {
        if (empty($post['background_file'])) return [];
        $file=$this->authorizedMotionSource((int)$post['id']);
        if (!$file) return [];
        $base=BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'motion/'.(int)$post['id'].'/';
        $clip=BxDolTranscoder::getObjectInstance('nw_styled_posts_motion_clip');
        $poster=BxDolTranscoder::getObjectInstance('nw_styled_posts_motion_poster');
        $state=$clip ? $clip->state((int)$file['id']) : 'failed';
        if ($state==='ready' && (!$poster || !$poster->readyFile((int)$file['id']))) $state=$poster && $poster->state((int)$file['id'])!=='failed' ? 'pending' : 'failed';
        $posterUrl=$poster && $poster->readyFile((int)$file['id'])?$base.'poster':'';
        if ($state!=='ready') {
            if ($this->getAuthorizedMotion((int)$post['id'],'previous_image')) $posterUrl=$base.'previous_image';
            elseif ($this->getAuthorizedMotion((int)$post['id'],'previous_poster')) $posterUrl=$base.'previous_poster';
        }
        return ['state'=>$state,'clip'=>$state==='ready'?$base.'clip':'','poster'=>$posterUrl,'status'=>$base.'status'];
    }

    public function actionMotion($id=0, $variant='status')
    {
        header('Cache-Control: private, no-store, max-age=0');
        header('X-Content-Type-Options: nosniff');
        if (!in_array($variant,['clip','poster','status','previous_image','previous_poster'],true) || !$this->authorizedMotionSource((int)$id)) { http_response_code(404);return; }
        if ($variant==='status') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($this->getMotionDescriptor(['id'=>(int)$id,'background_file'=>1]),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);return;
        }
        $media=$this->getAuthorizedMotion((int)$id,$variant);
        $base=realpath(BX_DIRECTORY_STORAGE.($variant==='previous_image'?'nw_styled_posts_backgrounds':'nw_styled_posts_motion_files'));
        $path=$media ? realpath($media['path']) : false;
        $mime=$variant==='clip'?'video/mp4':($variant==='previous_image'?($media['file']['mime_type']??''):'image/jpeg');
        if (!$base || !$path || !str_starts_with($path,$base.DIRECTORY_SEPARATOR) || !is_file($path) || ($media['file']['mime_type']??'')!==$mime) { http_response_code(404);return; }
        require_once __DIR__.'/NwStyledPostsMediaDelivery.php';
        NwStyledPostsMediaDelivery::send($path,$mime);
    }

    public function getAuthorizedBackground($id, bool $previous=false)
    {
        $post=$this->_oDb->getContentInfoById((int)$id);
        $field=$previous?'background_previous':'background_file';
        if (!$post || $this->checkAllowedView($post) !== CHECK_ACTION_RESULT_ALLOWED || empty($post[$field])) return false;
        $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
        return $storage ? $storage->getFile((int)$post[$field]) : false;
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
