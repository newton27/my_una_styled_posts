<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);
class BxTemplVoteReactions {
    protected $_bQuickMode=true;
    public $id;
    function init($id){$this->id=$id;$this->_bQuickMode=true;if($id<=0)return false;}
    function quickMode(){return $this->_bQuickMode;}
}
$root=__DIR__.'/../modules/newton/my_una_styled_posts/';
if(!is_file($root.'classes/NwStyledPostsVoteReactions.php'))throw new RuntimeException('A native reaction selector adapter is required');
require $root.'classes/NwStyledPostsVoteReactions.php';
$v=new NwStyledPostsVoteReactions;
if($v->init(17)!==null||$v->id!==17)throw new RuntimeException('Keep native reaction initialization and its void success return');
if($v->quickMode())throw new RuntimeException('Open the native reaction chooser instead of casting an implicit Like');
if($v->init(0)!==false)throw new RuntimeException('Keep native initialization failures');
echo "3 reaction adapter assertions passed\n";
