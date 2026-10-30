<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);
class BxDolModule {static function getInstance($name){return $GLOBALS['module'];}}
class BxTemplCmts {
    protected $_sTmplNameItem='comment.html';
    protected $_aT=['block_comments_title'=>'Comments ({0})'];
    public $received;
    public $count = 0;
    function getCommentsCountAll($parent = 0, $recalculate = false) {
        if ($parent !== 0 || $recalculate !== true) throw new RuntimeException('Heading must recalculate the total');
        return $this->count;
    }
    function __construct($system,$id,$init=true,$template=false){$this->received=[$system,$id,$init,$template];}
    function itemTemplate(){return $this->_sTmplNameItem;}
    function getCounter($params=[]){return '<span class="native-comment-counter">'.$this->_getCounterLabel(1,$params).'</span>';}
    protected function _getCounterLabel($count,$params=[]){return $count.' comment';}
}
function _t($key,$value){return str_replace('{0}',$value,$key);}
$GLOBALS['module']=(object)['_oTemplate'=>(object)['module'=>'nw_styled_posts']];
$root=__DIR__.'/../modules/newton/my_una_styled_posts/';
require $root.'classes/NwStyledPostsCmts.php';
$c=new NwStyledPostsCmts('nw_styled_posts',17);
if($c->received!==['nw_styled_posts',17,true,$GLOBALS['module']->_oTemplate])throw new RuntimeException('Keep native comment identity and use the module template lookup');
if($c->itemTemplate()==='comment.html')throw new RuntimeException('Legacy theme comment.html must not override the current native header contract');
$html=file_get_contents($root.'template/'.$c->itemTemplate());
foreach(['__header__','__content__','__actions__','__replies__','__author_unit__'] as $key)
    if(!str_contains($html,$key))throw new RuntimeException('Preserve native comment rendering slot '.$key);
foreach(['__item_note__','__meta__','bx_if:show_pinned'] as $old)
    if(str_contains($html,$old))throw new RuntimeException('Header details belong to the native header renderer');
if(!method_exists($c,'getCommentsBlockTitle'))throw new RuntimeException('Comment title must use the native live counter');
foreach ([0, 1, 12] as $count) {
    $c->count = $count;
    if ($c->getCommentsBlockTitle() !== 'Comments ('.$count.')') throw new RuntimeException('Heading must contain a plain count without widget markup');
}
echo "12 comment adapter/layout assertions passed\n";
