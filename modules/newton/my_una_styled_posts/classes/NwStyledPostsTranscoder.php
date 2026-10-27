<?php defined('BX_DOL') or die('hack attempt');
bx_import('BxDolTranscoderVideo');

/** Uses UNA's queue, filters, file tracing and deletion hooks. */
class NwStyledPostsTranscoder extends BxDolTranscoderVideo
{
    protected $_internalSource=false;
    protected function __construct($object, $storage)
    {
        parent::__construct($object,$storage);
        // This module explicitly uses Local storage. Keep the native video queue,
        // but read originals locally instead of persisting one-second bearer URLs
        // in the optional remote queue-file transport.
        $this->_sQueueStorage='';
    }
    public function isMimeTypeSupported($mime)
    {
        return in_array($mime,['video/mp4','video/webm','video/quicktime','image/gif'],true);
    }

    // Generic image_transcoder.php must never mint bearer URLs for private cards.
    public function getFileUrl($source) { return false; }
    public function getFileUrlById($source) { return false; }
    public function getOrigFileUrl($source)
    {
        return $this->_internalSource ? parent::getOrigFileUrl($source) : false;
    }

    public function transcode($source, $profile=0)
    {
        foreach (['SCRIPT_FILENAME','SCRIPT_NAME'] as $key) if (basename((string)($_SERVER[$key]??''))==='image_transcoder.php') return false;
        if (!$this->_oDb->getFromQueue($source)) return false;
        return parent::transcode($source,$profile);
    }

    protected function storeFileLocally_Storage($source)
    {
        $storage=BxDolStorage::getObjectInstance($this->_aObject['source_params']['object']);
        $file=$storage ? $storage->getFile($source) : false;
        $path=$file && !empty($file['private']) ? $storage->getFileLocalPath($source) : false;
        if (!$path || !$this->isMimeTypeSupported($file['mime_type'])) return false;
        $tmp=$this->getTmpFilename('.'.$file['ext']);
        if (!copy($path,$tmp)) { @unlink($tmp);return false; }
        return $tmp;
    }

    public function readyFile($source)
    {
        if (!$this->isFileReady($source)) return false;
        $id=$this->_oDb->getFileIdByHandler($source);
        $file=$id ? $this->_oStorage->getFile($id) : false;
        $path=$id ? $this->_oStorage->getFileLocalPath($id) : false;
        return $file && !empty($file['private']) && $path ? ['file'=>$file,'path'=>$path] : false;
    }

    public function enqueue($source): bool
    {
        $this->_internalSource=true;
        try { return (bool)$this->addToQueue($source); }
        finally { $this->_internalSource=false; }
    }

    public function storeTranscodedFile($handler, $path, $profile)
    {
        $ok=parent::storeTranscodedFile($handler,$path,$profile);
        if ($ok) {
            $module=BxDolModule::getInstance('nw_styled_posts');
            if ($module) $module->finalizeMotion((int)$handler);
        }
        return $ok;
    }

    public function state($source): string
    {
        if ($this->readyFile($source)) return 'ready';
        $queue=$this->_oDb->getFromQueue($source);
        return ($queue['status']??'')==='failed' ? 'failed' : 'pending';
    }

    protected function applyFilter_Mp4($file, $params)
    {
        // Parameters come exclusively from our fixed registration, never request data.
        $params['ffmpeg_options']=['an'=>' ','t'=>'5','map_metadata'=>'-1'];
        return parent::applyFilter_Mp4($file,$params);
    }
}
