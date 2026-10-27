<?php
/** Optional integration test against a fetched native UNA class; never modifies core. */
define('BX_DOL',true);define('BX_SYSTEM_FFMPEG','/usr/bin/ffmpeg');
interface iBxDolFactoryObject {}
function bx_import($v){}
function bx_log(...$args){}
define('BX_LOG_ERR',1);
class BxDolTranscoder {public function addToLog($s){}public function getTmpFilename($ext){$p=tempnam(sys_get_temp_dir(),'nwsp-native-');unlink($p);return $p.$ext;}}
class BxDolImageResize {static function getInstance(){return new self;}function getImageSize($p){$s=getimagesize($p);return $s?['w'=>$s[0],'h'=>$s[1]]:false;}}
require getenv('UNA_NATIVE_TRANSCODER');
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsTranscoder.php';
class NativeAdapter extends NwStyledPostsTranscoder {public function __construct(){} public function convert($path){return $this->applyFilter_Mp4($path,['h'=>240]);}}
$a=new NativeAdapter;
if (!$a->convert($argv[1])) throw new RuntimeException('Native MP4 conversion failed');
echo "Native UNA MP4 filter with GIF/silent motion adapter passed\n";
