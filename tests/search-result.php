<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);define('BX_DOL_URL_ROOT','https://example.test/');
function _t($key,...$args){return $key;}
function getParam($key){return $key==='nw_styled_posts_searchable_fields'?'title,text':false;}
function check($v,$m){if(!$v)throw new RuntimeException($m);$GLOBALS['checks']++;}
$GLOBALS['checks']=0;
class BxBaseModTextSearchResult {
    public $aCurrent;public $sFilterName;public $oModule;public $sBrowseUrl;public $isError=false;
    public $privacyAdded=false;public $customAdded=false;public $sUnitTemplate;
    function __construct($mode='',$params=[]){}
    function getMain(){return (object)['_oConfig'=>(object)['CNF'=>['PARAM_SEARCHABLE_FIELDS'=>'nw_styled_posts_searchable_fields','URL_HOME'=>'page.php?i=styled-posts-home','URI_AUTHOR_ENTRIES'=>'styled-posts-home']]];}
    protected function addConditionsForPrivateContent($c,$p){$this->privacyAdded=true;}
    protected function addCustomConditions($c,$p,$m,$params){$this->customAdded=true;}
    protected function _updateCurrentForAuthor($mode,$params,&$profile){$this->aCurrent['restriction']['author']['value']=(int)$params['author'];return true;}
    protected function _updateCurrentForContext($mode,$params,&$profile){$this->aCurrent['restriction']['context']=['value'=>(int)$params['context'],'field'=>'allow_view_to','operator'=>'='];return true;}
    function processReplaceableMarkers($profile){}
}
$root=__DIR__.'/../modules/newton/my_una_styled_posts/';
check(is_file($root.'classes/NwStyledPostsSearchResult.php'),'Native author stats require SearchResult class');
require $root.'classes/NwStyledPostsSearchResult.php';
$s=new NwStyledPostsSearchResult;
check($s->aCurrent['table']==='nw_styled_posts_entries','Use module-owned entries');
check($s->privacyAdded && $s->customAdded,'Native privacy and content filters are applied');
check($s->aCurrent['searchFields']===['title','text'],'Search editable text fields');
$schema=file_get_contents($root.'install/sql/install.sql');
foreach($s->aCurrent['ownFields'] as $field)check(str_contains(explode("\n",$schema)[1],'`'.$field.'`'),'Search only installed fields: '.$field);
$s=new NwStyledPostsSearchResult('author',['author'=>42]);
check($s->aCurrent['restriction']['author']['value']===42,'Author browsing is filtered');
$s=new NwStyledPostsSearchResult('favorite');check($s->isError,'Do not query unsupported favorite tables');
echo $GLOBALS['checks']." search/author contract assertions passed (native query engine not available locally)\n";
