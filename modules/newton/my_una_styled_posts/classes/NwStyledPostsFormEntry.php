<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsComposer.php';
require_once __DIR__.'/NwStyledPostsMotion.php';
class NwStyledPostsFormEntry extends BxBaseModTextFormEntry
{
    protected $_styledValues=[];
    protected $_styledOriginal=[];
    protected $_storedMotion=false;
    function __construct($aInfo, $oTemplate = false)
    {
        $this->MODULE='nw_styled_posts';
        $this->_bAllowChangeUserForAdmins=false;
        parent::__construct($aInfo,$oTemplate);
        if (isset($this->aInputs['do_submit'])) {
            $display=(string)($this->aParams['display']??'');
            $action=str_ends_with($display,'_edit')?'save':(str_ends_with($display,'_delete')?'delete':'post');
            $fallback=['post'=>'Post','save'=>'Save changes','delete'=>'Delete'];
            $key='_nw_styled_posts_button_'.$action;
            $label=_t($key);
            // Existing installations may still have the original language cache.
            $this->aInputs['do_submit']['value']=$label===$key?$fallback[$action]:$label;
            $this->aInputs['do_submit']['caption']='';
        }
        if (isset($this->aInputs['background_upload'])) $this->aInputs['background_upload']['attrs']=['accept'=>'image/jpeg,image/png,image/webp'.($this->motionUploadsEnabled()?',image/gif,video/mp4,video/webm,video/quicktime':'')];
        if (getParam('nw_styled_posts_allow_uploads')!=='on') unset($this->aInputs['background_upload']);
    }
    function initChecker($aValues = [], $aSpecificValues = [])
    {
        $this->_styledOriginal=$aValues;
        parent::initChecker($aValues,$aSpecificValues);
        if (!$this->isSubmittedAndValid() || (!$this->_isModeAdd() && !$this->_isModeEdit())) return;
        try { $this->_styledValues=$this->validatedValues(); }
        catch (InvalidArgumentException $e) { $this->aInputs['text']['error']=$e->getMessage(); $this->setValid(false); }
    }
    protected function validatedValues(): array
    {
        $values=[];
        foreach(NwStyledPostsStyle::FIELDS as $field) $values[$field]=array_key_exists($field,$_POST)?$_POST[$field]:$this->getCleanValue($field);
        $photo=$this->selectedPhotoId();
        if ($photo && !$this->_oModule->photoLibrary()->resolve($photo)) throw new InvalidArgumentException('That photo is no longer available. Choose another photo.');
        $values=NwStyledPostsStyle::normalize($values,NwStyledPostsStyle::catalog());
        $values['text']=NwStyledPostsStyle::validateText($this->getCleanValue('text'));
        preg_match('/^.{1,100}/us',$values['text'],$title);
        $values['title']=trim(str_replace("\n",' ',$title[0]));
        return $values;
    }
    protected function genCustomInputStyledControls($input)
    {
        $values=$this->_styledOriginal;
        if ($this->isSubmitted()) foreach(array_merge(NwStyledPostsStyle::FIELDS,['text']) as $field) $values[$field]=$this->getCleanValue($field);
        $template=$this->_oModule->_oTemplate;
        $selected=0;
        try { $selected=$this->selectedPhotoId(); } catch (InvalidArgumentException $e) {}
        $photoOptions=['enabled'=>getParam('nw_styled_posts_allow_uploads')==='on','endpoint'=>BX_DOL_URL_ROOT.$this->_oModule->_oConfig->getBaseUri().'own_photos','imageBase'=>BX_DOL_URL_ROOT.$this->_oModule->_oConfig->getBaseUri().'own_photo/','selected'=>$selected];
        $photoOptions['motionUploads']=$this->motionUploadsEnabled();
        $photoOptions['motion']=$this->_oModule->getMotionDescriptor($this->_styledOriginal);
        return NwStyledPostsComposer::render($values,NwStyledPostsStyle::catalog(),$template->assetBase(),$this->_oModule->getBackgroundUrl($this->_styledOriginal),$photoOptions);
    }
    function getCode($bDynamicMode = false)
    {
        $dynamic=$bDynamicMode || bx_is_dynamic_request();
        $template=$this->_oModule->_oTemplate;
        $include=$template->addCss(['card.css','composer.css'],$dynamic).$template->addJs(['composer.js'],$dynamic);
        return ($dynamic?$include:'').parent::getCode($dynamic);
    }
    protected function selectedPhotoId(): int
    {
        $id=$_POST['background_photo']??'';
        if ($id==='' || $id==='0') return 0;
        if (!is_scalar($id) || !preg_match('/^[1-9][0-9]{0,9}$/D',(string)$id)) throw new InvalidArgumentException('Choose a valid photo.');
        return (int)$id;
    }
    protected function storeBackground(int $contentId): int
    {
        $this->_storedMotion=false;
        $upload=$_FILES['background_upload']??null;
        $photo=$this->selectedPhotoId();
        if ($photo) {
            if ($upload && ($upload['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE) throw new InvalidArgumentException('Choose an existing photo or upload a new photo, one at a time.');
            return $this->_oModule->photoLibrary()->copy($photo,$contentId);
        }
        if (!$upload || ($upload['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return 0;
        if (getParam('nw_styled_posts_allow_uploads')!=='on') throw new InvalidArgumentException('Background uploads are disabled.');
        if (($upload['error']??-1)!==UPLOAD_ERR_OK || !is_string($upload['tmp_name']??null) || !is_uploaded_file($upload['tmp_name'])) throw new InvalidArgumentException('The background upload did not complete. Please try again.');
        $extension=strtolower(pathinfo((string)($upload['name']??''),PATHINFO_EXTENSION));
        $motion=in_array($extension,['gif','mp4','webm','mov'],true) || in_array($upload['type']??'',NwStyledPostsMotion::MIME_TYPES,true);
        if ($motion) {
            if (!$this->motionUploadsEnabled()) throw new InvalidArgumentException('Motion uploads are unavailable. Choose a photo or a built-in background.');
            $metadata=NwStyledPostsMotion::inspect($upload['tmp_name']);
            $extensions=['image/gif'=>'gif','video/mp4'=>'mp4','video/webm'=>'webm','video/quicktime'=>'mov'];
            $upload['name']='motion-background.'.$extensions[$metadata['mime']];
            $upload['type']=$metadata['mime'];
        } else NwStyledPostsStyle::validateImage($upload['tmp_name'],(string)($upload['type']??''));
        $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
        $id=$storage ? $storage->storeFileFromForm($upload,true,$this->_oModule->getUserId(),$contentId) : false;
        if (!$id) throw new InvalidArgumentException('The background image could not be saved.');
        if ($motion && !$this->_oModule->queueMotion((int)$id)) {
            $storage->deleteFile($id,$this->_oModule->getUserId());
            throw new InvalidArgumentException('Video processing could not be queued. Your previous background is unchanged. Please try again.');
        }
        $this->_storedMotion=$motion;
        return (int)$id;
    }
    protected function motionUploadsEnabled(): bool
    {
        return getParam('nw_styled_posts_allow_motion_uploads')==='on' && NwStyledPostsMotion::available();
    }
    function insert($aValsToAdd = [], $isIgnore = false)
    {
        if (!$this->isSubmittedAndValid() || $this->_oModule->checkAllowedAdd()!==CHECK_ACTION_RESULT_ALLOWED) return false;
        $file=0;
        try {
            $values=$this->validatedValues();
            $file=$this->storeBackground(0);
            $values['background_file']=$file;
            $id=parent::insert(array_merge($aValsToAdd,$values),$isIgnore);
            if ($file) {
                $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
                if ($id) $storage->afterUploadCleanup($file,$this->_oModule->getUserId());
                else $storage->deleteFile($file,$this->_oModule->getUserId());
            }
            return $id;
        } catch (InvalidArgumentException $e) { $this->aInputs['text']['error']=$e->getMessage();$this->setValid(false);return false; }
    }
    function update($iContentId, $aValsToAdd = [], &$aTrackTextFieldsChanges = null)
    {
        $old=$this->_oModule->_oDb->getContentInfoById((int)$iContentId);
        if (!$old || !$this->isSubmittedAndValid() || $this->_oModule->checkAllowedEdit($old)!==CHECK_ACTION_RESULT_ALLOWED) return false;
        $file=0;$locked=false;$committed=false;
        try {
            $values=$this->validatedValues();
            $file=$this->storeBackground((int)$iContentId);
            $locked=true;
            $old=$this->_oModule->_oDb->lockBackgroundEntry((int)$iContentId);
            if (!$old || $this->_oModule->checkAllowedEdit($old)!==CHECK_ACTION_RESULT_ALLOWED) throw new InvalidArgumentException('This post is no longer available for editing.');
            $previous=(int)$old['background_file'];
            $values['background_file']=$file ?: ($this->getCleanValue('remove_background') ? 0 : $previous);
            $fallback=(int)($old['background_previous']??0);
            $values['background_previous']=$file && $this->_storedMotion ? ($fallback && !$this->_oModule->motionReady($previous) ? $fallback : $previous) : ($file || $values['background_file']===0 ? 0 : $fallback);
            $ok=parent::update($iContentId,array_merge($aValsToAdd,$values),$aTrackTextFieldsChanges);
            $this->_oModule->_oDb->finishBackgroundEntry($ok!==false);$locked=false;$committed=$ok!==false;
            $storage=BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds');
            if ($ok !== false) {
                if ($file) $storage->afterUploadCleanup($file,$this->_oModule->getUserId(),(int)$iContentId);
                foreach (array_unique([$previous,$fallback]) as $obsolete) if ($obsolete && !in_array($obsolete,[$values['background_file'],$values['background_previous']],true)) $storage->deleteFile($obsolete);
                if ($file && $this->_storedMotion) $this->_oModule->finalizeMotion($file);
            } elseif ($file) $storage->deleteFile($file,$this->_oModule->getUserId());
            return $ok;
        } catch (Throwable $e) {
            if ($locked) $this->_oModule->_oDb->finishBackgroundEntry(false);
            if ($file && !$committed) BxDolStorage::getObjectInstance('nw_styled_posts_backgrounds')->deleteFile($file,$this->_oModule->getUserId());
            if (!($e instanceof InvalidArgumentException)) throw $e;
            $this->aInputs['text']['error']=$e->getMessage();$this->setValid(false);return false;
        }
    }
}
