<?php
define('BX_DOL',true);function bx_import($v){}
class BxDolTranscoderVideo {
    protected $options=[];
    protected function applyFilter_Mp4($f,$p){$this->options=$p;return true;}
}
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsTranscoder.php';
class Adapter extends NwStyledPostsTranscoder {public function __construct(){}function options(){ $this->applyFilter_Mp4('/tmp/input',[]);return $this->options; }}
$a=new Adapter;
if(!$a->isMimeTypeSupported('image/gif') || $a->isMimeTypeSupported('image/svg+xml'))throw new RuntimeException('GIF adapter MIME policy');
$p=$a->options();if(!isset($p['ffmpeg_options']['an']) || $p['ffmpeg_options']['t']!=='5')throw new RuntimeException('Silent short output policy');
if ($a->getFileUrl(1)!==false || $a->getFileUrlById(1)!==false || $a->getOrigFileUrl(1)!==false) throw new RuntimeException('Generic transcoder URL delivery bypasses post privacy');
$_SERVER['SCRIPT_FILENAME']='/site/image_transcoder.php';if ($a->transcode(1)!==false)throw new RuntimeException('Generic endpoint can convert private media');
echo "7 motion transcoder policy assertions passed\n";
