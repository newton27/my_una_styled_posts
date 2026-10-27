<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);define('CHECK_ACTION_RESULT_ALLOWED',0);
function check($v,$message){if(!$v)throw new RuntimeException($message);$GLOBALS['checks']++;}
$GLOBALS['checks']=0;
class BxBaseModTextMenuViewActions {
    protected $_sModule;
    function __construct($a,$t=false){if($this->_sModule!=='nw_styled_posts')throw new RuntimeException('Native menu requires _sModule before parent constructor');}
    protected function _getMenuItemByNameActions($item){return 'native:'.$item['name'];}
}
class BxBaseModTextInstaller {}
class BxDolStudioInstaller {}
$root=__DIR__.'/../modules/newton/my_una_styled_posts/';
require $root.'classes/NwStyledPostsMenuViewActions.php';
require $root.'install/installer.php';
$menu=new NwStyledPostsMenuViewActions([]);
check(is_subclass_of('NwStyledPostsInstaller','BxBaseModTextInstaller'),'Content module must inherit native text installer');
$r=new ReflectionClass($menu);
foreach(['_getMenuItemEditStyledPost','_getMenuItemDeleteStyledPost'] as $name){check($r->hasMethod($name),'Native action adapter exists: '.$name);$method=$r->getMethod($name);check($method->invoke($menu,['name'=>'edit-styled-post'])==='native:edit-styled-post','Adapter delegates to native permission-filtered menu');}
class BxBaseModTextConfig {
    public $CNF=['TABLE_POLLS'=>'nw_styled_posts_polls','TABLE_POLLS_ANSWERS'=>'nw_styled_posts_polls_answers'];
    protected $_aPrefixes;protected $_aGridObjects;
    function __construct($module){}
}
require $root.'classes/NwStyledPostsConfig.php';
$config=new NwStyledPostsConfig([]);
check(empty($config->CNF['TABLE_POLLS']),'Native deletion must not query absent poll tables');
check(isset($config->CNF['T']['txt_all_entries_by']),'Native author description requires txt_all_entries_by');
echo $GLOBALS['checks']." native adapter assertions passed\n";
