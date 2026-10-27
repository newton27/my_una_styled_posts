<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);define('CHECK_ACTION_RESULT_ALLOWED',0);define('BX_DIRECTORY_PATH_TMP',sys_get_temp_dir().'/');
function getParam($key){return 'on';}
function check($v,$m){if(!$v)throw new RuntimeException($m);$GLOBALS['checks']=($GLOBALS['checks']??0)+1;}
class PhotoDb {public $rows;function getContentInfoById($id){return $this->rows[$id]??false;}function getAll($sql,$bindings){return array_values($this->rows);}}
class PhotoModule {public $_oDb;public $_oConfig;public $enabled=true;function isEnabled(){return $this->enabled;}public $allowed=true;function checkAllowedView($row){return $this->allowed?0:'denied';}}
class OwnerModule {public $user=7;function getUserId(){return $this->user;}}
class Storage {public $files;public $copies=[];function getFile($id){return $this->files[$id]??false;}function storeFileFromPath($path,$private,$owner,$content){$this->copies[]=[$private,$owner,$content,file_get_contents($path)];return 99;}}
class BxDolStorageHelperStorage {static $bytes;function __construct($p){}function getImmediateError(){return 0;}function save($p){return file_put_contents($p,self::$bytes)!==false;}}
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsPhotoLibrary.php';
$m=new OwnerModule;$p=new PhotoModule;$p->_oDb=new PhotoDb;$p->_oConfig=(object)['CNF'=>['TABLE_ENTRIES'=>'bx_photos_entries','TABLE_FILES'=>'bx_photos_photos','OBJECT_STORAGE'=>'bx_photos_photos']];
$row=['id'=>1,'author'=>7,'thumb'=>10,'title'=>'My photo','status'=>'active','status_admin'=>'active'];$p->_oDb->rows=[1=>$row];
$s=new Storage;$s->files=[10=>['id'=>10,'profile_id'=>7,'mime_type'=>'image/png','size'=>68]];$d=new Storage;
$l=new NwStyledPostsPhotoLibrary($m,$p,$s,$d);
check($l->resolve(1)['id']===10,'Own active image resolves');
foreach(['author'=>8,'status'=>'hidden','status_admin'=>'pending'] as $key=>$value){$p->_oDb->rows[1][$key]=$value;check($l->resolve(1)===false,'Reject '.$key);$p->_oDb->rows[1]=$row;}
$p->enabled=false;check($l->resolve(1)===false,'Disabled Photos cannot supply images');check($l->listing()['available']===false,'Disabled Photos has readable unavailable state');try{$l->copy(1,0);throw new RuntimeException('Disabled source copied');}catch(InvalidArgumentException $e){check(count($d->copies)===0,'Disabled Photos cannot be copied');}$p->enabled=true;
$p->allowed=false;check($l->resolve(1)===false,'Native privacy denial');$p->allowed=true;
$m->user=8;check($l->resolve(1)===false,'Profile switch denies previous selection');$m->user=0;check($l->resolve(1)===false,'Guest denied');$m->user=7;
$s->files[10]['profile_id']=8;check($l->resolve(1)===false,'Storage ownership checked');$s->files[10]['profile_id']=7;
check($l->resolve(404)===false,'Deleted photo denied');
$s->files[10]['size']=8388609;check($l->resolve(1)===false,'Oversized metadata denied');$s->files[10]['size']=68;
BxDolStorageHelperStorage::$bytes=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9ZQmcAAAAASUVORK5CYII=');
check($l->copy(1,123)===99,'Separate storage copy created');check($d->copies[0][0]===true && $d->copies[0][1]===7 && $d->copies[0][2]===123,'Copy is private and belongs to current profile/content');
check($s->files[10]['id']===10,'Original metadata untouched');
BxDolStorageHelperStorage::$bytes='not an image';try{$l->copy(1,0);throw new RuntimeException('Corrupt bytes accepted');}catch(InvalidArgumentException $e){check(count($d->copies)===1,'Corrupt bytes never stored');}
$absent=new NwStyledPostsPhotoLibrary($m,null,null,$d);check($absent->listing()['available']===false,'Absent Photos is readable');
check($absent->resolve(1)===false,'Absent Photos resolves nothing');
echo $GLOBALS['checks']." photo library assertions passed (native metadata/storage simulated)\n";
