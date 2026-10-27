<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsStyle.php';
/** Read-only source adapter. Only the destination background storage is mutated. */
class NwStyledPostsPhotoLibrary
{
    private $module, $photos, $source, $destination;
    public function __construct($module, $photos, $source, $destination)
    {
        $this->module=$module; $this->photos=$photos; $this->source=$source; $this->destination=$destination;
    }
    public function available(): bool
    {
        return $this->module->getUserId()>0 && $this->photos && $this->photos->isEnabled() && $this->source && getParam('nw_styled_posts_allow_uploads')==='on';
    }
    public function resolve(int $id)
    {
        if (!$this->available() || $id<1) return false;
        $entry=$this->photos->_oDb->getContentInfoById($id);
        if (!$entry || (int)$entry['author']!==$this->module->getUserId() || $entry['status']!=='active' || $entry['status_admin']!=='active' || $this->photos->checkAllowedView($entry)!==CHECK_ACTION_RESULT_ALLOWED) return false;
        $file=$this->source->getFile((int)$entry['thumb']);
        if (!$file || (int)$file['profile_id']!==$this->module->getUserId() || !in_array($file['mime_type'],['image/jpeg','image/png','image/webp'],true) || (int)$file['size']<1 || (int)$file['size']>8388608) return false;
        return $file;
    }
    public function listing(int $before=0): array
    {
        $result=['available'=>$this->available(),'items'=>[],'next'=>0];
        if (!$result['available']) return $result;
        $cnf=$this->photos->_oConfig->CNF;
        // Identifiers come exclusively from native module configuration, never the request.
        $entries=$cnf['TABLE_ENTRIES']; $files=$cnf['TABLE_FILES'];
        if (!preg_match('/^[a-zA-Z0-9_]+$/D',$entries) || !preg_match('/^[a-zA-Z0-9_]+$/D',$files)) return $result;
        $rows=$this->photos->_oDb->getAll("SELECT e.* FROM `$entries` e INNER JOIN `$files` f ON f.id=e.thumb WHERE e.author=:owner AND f.profile_id=:file_owner AND e.status='active' AND e.status_admin='active' AND f.size BETWEEN 1 AND 8388608 AND f.mime_type IN ('image/jpeg','image/png','image/webp')".($before>0?' AND e.id<:before':'')." ORDER BY e.id DESC LIMIT 25",$before>0?['owner'=>$this->module->getUserId(),'file_owner'=>$this->module->getUserId(),'before'=>$before]:['owner'=>$this->module->getUserId(),'file_owner'=>$this->module->getUserId()]);
        $more=count($rows)>24; $rows=array_slice($rows,0,24);
        foreach($rows as $row) {
            if ($this->resolve((int)$row['id'])) $result['items'][]=['id'=>(int)$row['id'],'title'=>(string)$row['title']];
        }
        if ($more && $rows) $result['next']=(int)end($rows)['id'];
        return $result;
    }
    /** Caller owns and must unlink the returned temporary file, including on stream errors. */
    public function temporaryImage(int $id): array
    {
        $file=$this->resolve($id);
        if (!$file) throw new InvalidArgumentException('That photo is no longer available. Choose another photo.');
        $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$file['mime_type']];
        $seed=tempnam(BX_DIRECTORY_PATH_TMP,'nwsp-photo-');
        if ($seed===false) throw new InvalidArgumentException('The photo could not be opened. Please try again.');
        $path=$seed.'.'.$ext;
        if (!rename($seed,$path)) { unlink($seed); throw new InvalidArgumentException('The photo could not be opened.'); }
        try {
            $helper=new BxDolStorageHelperStorage(['id'=>(int)$file['id'],'storage'=>$this->photos->_oConfig->CNF['OBJECT_STORAGE']]);
            if ($helper->getImmediateError() || !$helper->save($path)) throw new InvalidArgumentException('The photo could not be loaded. Please try another photo.');
            NwStyledPostsStyle::validateImage($path,$file['mime_type']);
            return ['path'=>$path,'mime_type'=>$file['mime_type']];
        } catch (Throwable $e) { unlink($path); throw $e; }
    }
    public function copy(int $id,int $contentId): int
    {
        $image=$this->temporaryImage($id);
        try {
            $copy=$this->destination ? $this->destination->storeFileFromPath($image['path'],true,$this->module->getUserId(),$contentId) : false;
            if (!$copy) throw new InvalidArgumentException('The background image could not be saved.');
            return (int)$copy;
        } finally { unlink($image['path']); }
    }
}
