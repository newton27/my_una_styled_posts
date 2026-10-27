<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);define('CHECK_ACTION_RESULT_ALLOWED',0);define('BX_DOL_URL_ROOT','https://example.test/');
$GLOBALS['checks']=0;function check($v,$m){if(!$v)throw new RuntimeException($m);$GLOBALS['checks']++;}
class BxBaseModTextModule {
    public $_oDb;protected $_bIsApi=false;public $allowed=false;public $parentReads=0;
    function checkAllowedView($p){return $this->allowed ? 0 : 'denied';}
    function serviceGetInfo($id,$only=true){return $this->_oDb->getContentInfoById((int)$id);}
    function serviceGetSafeServices(){return ['GetInfo'=>'','GetPollForm'=>'','BrowseFavorite'=>''];}
    function serviceGetTimelinePost($a,$b=[]){$this->parentReads++;return ['content'=>'native'];}
}
class FakeEntries {public $post=['id'=>1,'background_file'=>8,'title'=>'Private title','text'=>'Private quote'];function getContentInfoById($id){return $id===1 ? $this->post : false;}}
class FakeStorage {public $reads=0;function getFile($id){$this->reads++;return ['id'=>$id,'private'=>1,'mime_type'=>'image/png'];}}
class BxDolStorage {static $storage;static function getObjectInstance($n){return self::$storage;}}
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsModule.php';
$m=new NwStyledPostsModule;$m->_oDb=new FakeEntries;BxDolStorage::$storage=new FakeStorage;
check($m->getAuthorizedBackground(1)===false,'Deny private image when native privacy denies access');
check(BxDolStorage::$storage->reads===0,'Do not read storage before authorization');
check($m->serviceGetTimelinePost(['object_id'=>1])===false,'Deny private Timeline representation');
check($m->parentReads===0,'Do not invoke native Timeline renderer before authorization');
check($m->serviceGetInfo(1)===[], 'Deny inherited GetInfo when native privacy denies');
check(!array_key_exists('GetPollForm',$m->serviceGetSafeServices()),'Do not advertise unsupported poll services');
check($m->serviceGetText(1)==='', 'Do not expose private text through content-info service');
check($m->serviceGetTitle(1)==='', 'Do not expose private title through content-info service');
$m->allowed=true;
check($m->serviceGetText(1)==='Private quote','Authorized text read works');
check($m->getAuthorizedBackground(1)['id']===8,'Allow image after native privacy approves');
check($m->getAuthorizedBackground(999)===false,'Missing entry denied');
$result=$m->serviceGetTimelinePost(['object_id'=>1]);
check($result['_cache']===false,'Do not cache viewer-dependent Timeline HTML');
check($m->parentReads===1,'Use native Timeline metadata and interactions');
$m->_oDb->post['background_file']=0;check($m->getAuthorizedBackground(1)===false,'No background means no image');
echo $GLOBALS['checks']." authorization boundary assertions passed (native policy simulated)\n";
