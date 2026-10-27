<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});define('BX_DOL',1);define('CHECK_ACTION_RESULT_ALLOWED',0);
function getParam($k){return 'on';}function _t($k){return $k;}
class BxBaseModTextFormEntry {protected $MODULE;protected $_bAllowChangeUserForAdmins;public $aInputs=[],$aParams=[];protected $_oModule;function __construct($i,$t=false){} }
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsFormEntry.php';
class Library {public $allowed=true,$copies=[];function resolve($id){return $this->allowed && $id===2?['id'=>22]:false;}function copy($id,$content){if(!$this->resolve($id))throw new InvalidArgumentException('denied');$this->copies[]=[$id,$content];return 88;}}
class Module {public $library;function photoLibrary(){return $this->library;}}
class Form extends NwStyledPostsFormEntry {function setup($m){$this->_oModule=$m;}function selected(){return $this->selectedPhotoId();}function savePhoto($id){return $this->storeBackground($id);}}
function ok($v,$m){if(!$v)throw new RuntimeException($m);$GLOBALS['n']=($GLOBALS['n']??0)+1;}
$f=new Form([]);$m=new Module;$m->library=new Library;$f->setup($m);
$_POST=['background_photo'=>'2'];ok($f->savePhoto(9)===88,'Selected photo copied on save');ok($m->library->copies===[[2,9]],'Entry ID not storage ID sent to library');
foreach([['background_photo'=>['2']],['background_photo'=>'2junk'],['background_photo'=>'-2']] as $post){$_POST=$post;try{$f->selected();throw new RuntimeException('Malformed selection accepted');}catch(InvalidArgumentException $e){ok(true,'Reject malformed selection');}}
$_POST=['background_photo'=>'2'];$_FILES=['background_upload'=>['error'=>UPLOAD_ERR_OK]];try{$f->savePhoto(9);throw new RuntimeException('Two sources accepted');}catch(InvalidArgumentException $e){ok(count($m->library->copies)===1,'Conflicting upload rejected before copy');}
$_FILES=[];$m->library->allowed=false;try{$f->savePhoto(9);throw new RuntimeException('Deleted photo accepted');}catch(InvalidArgumentException $e){ok(count($m->library->copies)===1,'Source reauthorized at save');}
$_POST=[];ok($f->savePhoto(9)===0,'No selection preserves existing background behavior');
echo $GLOBALS['n']." photo form assertions passed\n";
