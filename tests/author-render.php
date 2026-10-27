<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);define('BX_FORMAT_DATE','date');
function _t($key,...$args){
    if (!isset($GLOBALS['LANG'][$key])) return $key;
    $value=$GLOBALS['LANG'][$key];
    foreach($args as $i=>$arg) $value=str_replace('{'.$i.'}',(string)$arg,$value);
    return $value;
}
function bx_time_js($time,$format){return 'Date';}
class BxDolPermalinks {static function getInstance(){return new self;}function permalink($url){return $url;}}
class BxBaseModTextConfig {public $CNF=[];protected $_aPrefixes;protected $_aGridObjects;function __construct($a){}}
class BxBaseModTextTemplate {
    protected $MODULE;
    function __construct(&$config,&$db){}
    function getModule(){return $GLOBALS['module'];}
    function parseHtmlByName($name,$vars){return json_encode($vars);}
    function getAuthorDesc($aData, $oProfile)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        $aItem = array(
            'bx_if:text' => array(
                'condition' => false,
                'content' => array(
                    'content' => ''
                )
            ),
            'bx_if:link' => array(
                'condition' => false,
                'content' => array(
                    'link' => '',
                    'content' => ''
                )
            )
        );

        $aTmplVarsItems = array();
        if(!empty($CNF['FIELD_ADDED']) && !empty($aData[$CNF['FIELD_ADDED']]))
            $aTmplVarsItems[] = array_merge($aItem, array('bx_if:text' => array(
                'condition' => true,
                'content' => array(
                    'content' => bx_time_js($aData[$CNF['FIELD_ADDED']], BX_FORMAT_DATE)
                )
            )));

        if(!empty($CNF['URI_AUTHOR_ENTRIES']))
            $aTmplVarsItems[] = array_merge($aItem, array('bx_if:link' => array(
                'condition' => true,
                'content' => array(
                    'link' => BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_AUTHOR_ENTRIES'] . '&profile_id=' . $oProfile->id()),
                    'content' => _t($CNF['T']['txt_all_entries_by'], $this->getModule()->serviceGetMenuAddonManageToolsProfileStats($oProfile->id()))
                )
            )));

        return $this->parseHtmlByName('author_desc.html', array(
            'bx_repeat:items' => $aTmplVarsItems
        ));
    }
}
$root=__DIR__.'/../modules/newton/my_una_styled_posts/classes/';
require $root.'NwStyledPostsConfig.php';require $root.'NwStyledPostsTemplate.php';
class AuthorModule {public $_oConfig;public $count=3;function serviceGetMenuAddonManageToolsProfileStats($id){return $this->count;}}
$module=new AuthorModule;$module->_oConfig=new NwStyledPostsConfig([]);$GLOBALS['module']=$module;
$db=null;$template=new NwStyledPostsTemplate($module->_oConfig,$db);
$profile=new class {function id(){return 42;}};
$result=$template->getAuthorDesc(['added'=>123],$profile);
if(str_contains($result,'_nw_styled_posts'))throw new RuntimeException('Existing language cache must not expose a raw author label');
if(!str_contains($result,'Styled Posts (3)'))throw new RuntimeException('Render readable author count');
if(!str_contains($result,'profile_id=42'))throw new RuntimeException('Link to the author collection');
$language=file_get_contents($root.'../install/langs/en.xml');
$key='_nw_styled_posts_txt_all_entries_by';
if(!preg_match('/<string name="'.preg_quote($key,'/').'"><!\[CDATA\[(.*?)\]\]><\/string>/s',$language,$matches))
    throw new RuntimeException('Author count language resource missing');
$GLOBALS['LANG'][$key]=$matches[1];
foreach([0,1,7] as $count) {
    $module->count=$count;
    $result=$template->getAuthorDesc(['added'=>123],$profile);
    if(!str_contains($result,'Styled Posts ('.$count.')'))
        throw new RuntimeException('Native language interpolation must render author count '.$count);
}
echo "6 author-render assertions passed against pinned native author-description contract\n";
