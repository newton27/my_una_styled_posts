<?php
define('BX_DOL',1);define('BX_DOL_URL_ROOT','/');
function getParam($k){return $k==='nw_styled_posts_allow_motion_uploads'?'':'on';}function _t($k){return $k;}
class BxBaseModTextFormEntry {protected $MODULE,$_bAllowChangeUserForAdmins,$_oModule;public $aInputs=[],$aParams=[];function __construct($i,$t=false){} }
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsFormEntry.php';
class EditForm extends NwStyledPostsFormEntry {public $values=[];function setup($m){$this->_oModule=$m;}function isSubmitted(){return true;}function getCleanValue($k){return $this->values[$k]??'';}function validate(){return $this->validatedValues();}function controls(){return $this->genCustomInputStyledControls([]);}}
$m=new stdClass;$m->_oTemplate=new class {function assetBase(){return '/assets/';}};$m->_oConfig=new class {function getBaseUri(){return 'styled_posts/';}};
$module=new class($m) {public $_oTemplate,$_oConfig;function __construct($m){$this->_oTemplate=$m->_oTemplate;$this->_oConfig=$m->_oConfig;}function getMotionDescriptor($v){return [];}function getBackgroundUrl($v){return '';}};
$f=new EditForm([]);$f->setup($module);$f->values=array_merge(NwStyledPostsStyle::normalize([],NwStyledPostsStyle::catalog()),['text'=>'Draft','text_backdrop'=>'box','backdrop_opacity'=>25,'image_zoom'=>170,'image_x'=>15,'text_direction'=>'rtl','text_position'=>'custom','text_x'=>15,'text_y'=>85]);
preg_match('/<script type="application\/json" class="nwsp-config">(.*?)<\/script>/s',$f->controls(),$match);$values=json_decode($match[1],true)['values'];
foreach(['text_backdrop','backdrop_opacity','image_zoom','image_x','text_direction','text_position','text_x','text_y'] as $key)if($values[$key]!==$f->values[$key])throw new RuntimeException('Failed submission lost '.$key);
echo "Failed submission retains backdrop, framing and direction\n";

foreach(['backdrop_opacity'=>'bad','image_zoom'=>'150junk','text_x'=>'70junk','text_y'=>'12.5'] as $key=>$raw){$_POST=[$key=>$raw];$f->values[$key]=(int)$raw;try{$f->validate();throw new RuntimeException('Native cleaning accepted malformed '.$key);}catch(InvalidArgumentException $e){}}
echo "Raw malformed numeric submissions rejected before native coercion\n";

