<?php
error_reporting(E_ALL);
set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);
function getParam($key){return 'on';}
function _t($key){return $key;} // Existing install has not loaded new language keys.
class BxBaseModTextFormEntry {
    protected $MODULE;protected $_bAllowChangeUserForAdmins;
    public $aInputs;public $aParams;
    function __construct($info,$template=false){$this->aParams=['display'=>$info['display']];$this->aInputs=['do_submit'=>['caption'=>'do_submit','value'=>'_sys_form_input_submit']];}
}
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsFormEntry.php';
foreach(['add'=>'Post','edit'=>'Save changes','delete'=>'Delete'] as $mode=>$expected){
    $form=new NwStyledPostsFormEntry(['display'=>'nw_styled_posts_entry_'.$mode]);
    if($form->aInputs['do_submit']['caption']!=='')throw new RuntimeException('Internal field caption must not be visible');
    if($form->aInputs['do_submit']['value']!==$expected)throw new RuntimeException('Expected readable '.$mode.' button label');
}
echo "6 form label assertions passed, including existing installs without new translations\n";
