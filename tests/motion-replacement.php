<?php
define('BX_DOL',1);define('CHECK_ACTION_RESULT_ALLOWED',0);
function getParam($k){return 'on';}function _t($k){return $k;}
class BxBaseModTextFormEntry {public $aInputs=[],$aParams=[],$writes=[],$result=true,$remove=false;protected $_oModule,$MODULE,$_bAllowChangeUserForAdmins;
function __construct($i,$t=false){}function isSubmittedAndValid(){return true;}function getCleanValue($k){return $this->remove;}function update($id,$v=[],&$tracked=null){$this->writes=$v;return $this->result;}}
class BxDolStorage {static $deleted=[];static function getObjectInstance($s){return new self;}function deleteFile($id,$u=0){self::$deleted[]=$id;return true;}function afterUploadCleanup(...$args){}}
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsFormEntry.php';
class Form extends NwStyledPostsFormEntry {public $file=20,$motion=true;function setup($m){$this->_oModule=$m;}protected function validatedValues():array{return ['text'=>'changed'];}protected function storeBackground(int $id):int{$this->_storedMotion=$this->motion;return $this->file;}}
class Module {public $_oDb,$ready=false;function __construct($old){$this->_oDb=new class($old){public $fresh=null,$locked=false;function __construct(public $old){}function getContentInfoById($id){return $this->old;}function lockBackgroundEntry($id){$this->locked=true;return $this->fresh??$this->old;}function finishBackgroundEntry($ok){$this->locked=false;}};}function checkAllowedEdit($p){return 0;}function getUserId(){return 3;}function motionReady($id){return $this->ready;}function finalizeMotion($id){}}
$n=0;function ok($v){global $n;$n++;if(!$v)throw new RuntimeException('Replacement assertion '.$n);}
function runCase($old,$file,$motion,$result=true,$remove=false){BxDolStorage::$deleted=[];$f=new Form([]);$f->setup(new Module($old));$f->file=$file;$f->motion=$motion;$f->result=$result;$f->remove=$remove;$f->update(1);return $f;}
$f=runCase(['background_file'=>10],20,true);ok($f->writes['background_previous']===10);ok(BxDolStorage::$deleted===[]);
$f=runCase(['background_file'=>10],20,true,false);ok(BxDolStorage::$deleted===[20]);
$f=runCase(['background_file'=>20,'background_previous'=>10],30,true);ok($f->writes['background_previous']===10);ok(BxDolStorage::$deleted===[20]);
$f=runCase(['background_file'=>20,'background_previous'=>10],40,false);ok($f->writes['background_previous']===0);ok(BxDolStorage::$deleted===[20,10]);
$f=runCase(['background_file'=>20,'background_previous'=>10],0,false);ok($f->writes['background_previous']===10);ok(BxDolStorage::$deleted===[]);
$f=runCase(['background_file'=>20,'background_previous'=>10],0,false,true,true);ok($f->writes['background_file']===0);ok(BxDolStorage::$deleted===[20,10]);
$m=new Module(['background_file'=>20,'background_previous'=>10]);$m->_oDb->fresh=['background_file'=>20,'background_previous'=>0];$m->ready=true;
$f=new Form([]);$f->setup($m);$f->file=30;$f->update(1);ok($f->writes['background_previous']===20);ok(!$m->_oDb->locked);
echo "$n motion replacement assertions passed\n";
