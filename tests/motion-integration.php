<?php
define('BX_DOL',true);define('CHECK_ACTION_RESULT_ALLOWED',0);define('BX_DOL_URL_ROOT','https://example.test/');
function getParam($k){return 'on';}
class BxBaseModTextModule {public $_oDb,$_oConfig,$allowed=false;function checkAllowedView($p){return $this->allowed?0:1;}}
class BxDolStorage {static function getObjectInstance($n){return new class {function getFile($id){return ['id'=>$id,'private'=>1,'mime_type'=>'video/mp4'];}};}}
class BxDolTranscoder {static $reads=0;static function getObjectInstance($n){self::$reads++;return new class {function state($id){return 'ready';}function readyFile($id){return ['path'=>'/tmp/not-public','file'=>['private'=>1,'mime_type'=>'video/mp4']];}function enqueue($id){return true;}};}}
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsModule.php';
$m=new NwStyledPostsModule;$m->_oDb=new class {function getContentInfoById($id){return $id===1?['id'=>1,'background_file'=>8]:false;}};
$m->_oConfig=new class {function getBaseUri(){return 'm/styled_posts/';}};
function ok($v){if(!$v)throw new RuntimeException('Motion integration assertion');$GLOBALS['n']=($GLOBALS['n']??0)+1;}
ok($m->getMotionDescriptor(['id'=>1,'background_file'=>8])===[]);ok(BxDolTranscoder::$reads===0);
ok($m->getAuthorizedMotion(1,'clip')===false);ok(BxDolTranscoder::$reads===0);
$m->allowed=true;$d=$m->getMotionDescriptor(['id'=>1,'background_file'=>8]);
ok($d['state']==='ready');ok($d['clip']==='https://example.test/m/styled_posts/motion/1/clip');
ok(!str_contains(json_encode($d),'not-public'));ok(!str_contains(json_encode($d),'token'));
ok($m->getAuthorizedMotion(1,'clip')['path']==='/tmp/not-public');
ok($m->getAuthorizedMotion(1,'../../clip')===false);ok($m->getAuthorizedMotion(999,'clip')===false);
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsCard.php';
$html=NwStyledPostsCard::render(['text'=>'hello'],NwStyledPostsStyle::catalog(),'/assets/','',$d);
ok(str_contains($html,'<video'));ok(str_contains($html,'muted'));ok(str_contains($html,'playsinline'));ok(str_contains($html,'data-motion-toggle'));
ok(!str_contains($html,' autoplay')); // JavaScript starts only visible cards, after motion preference check.
echo ($GLOBALS['n']??0)." motion integration assertions passed\n";
