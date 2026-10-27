<?php defined('BX_DOL') or die('hack attempt');
/** Native query adapter, following UNA 15 RC2's MIT-licensed text-module contract. */
class NwStyledPostsSearchResult extends BxBaseModTextSearchResult
{
    function __construct($sMode = '', $aParams = [])
    {
        parent::__construct($sMode,$aParams);
        $this->aCurrent=[
            'name'=>'nw_styled_posts',
            'module_name'=>'nw_styled_posts',
            'title'=>_t('_nw_styled_posts'),
            'table'=>'nw_styled_posts_entries',
            'ownFields'=>['id','author','added','changed','title','text','views','rrate','rvotes','comments','cf','allow_view_to','status','status_admin','font_name','font_size','text_color','text_align','background','background_color','background_file'],
            'searchFields'=>['title','text'],
            'restriction'=>[
                'author'=>['value'=>'','field'=>'author','operator'=>'='],
                'status'=>['value'=>'active','field'=>'status','operator'=>'='],
                'statusAdmin'=>['value'=>'active','field'=>'status_admin','operator'=>'='],
            ],
            'join'=>[],
            'paginate'=>['perPage'=>12,'start'=>0],
            'sorting'=>'last',
            'ident'=>'id',
        ];
        $this->sFilterName='nw_styled_posts_filter';
        $this->oModule=$this->getMain();
        $CNF=&$this->oModule->_oConfig->CNF;
        $this->sBrowseUrl=BX_DOL_URL_ROOT.$CNF['URL_HOME'];
        $oProfileAuthor=null;
        switch($sMode) {
            case '':
            case 'public':
            case 'recent':
                break;
            case 'author':
                if (!$this->_updateCurrentForAuthor($sMode,$aParams,$oProfileAuthor)) $this->isError=true;
                break;
            case 'context':
                if (!$this->_updateCurrentForContext($sMode,$aParams,$oProfileAuthor)) $this->isError=true;
                break;
            case 'popular':
                $this->aCurrent['sorting']='popular';break;
            case 'top':
                $this->aCurrent['sorting']='top';break;
            case 'updated':
                $this->aCurrent['sorting']='updated';break;
            default:
                $this->isError=true;
        }
        $this->processReplaceableMarkers($oProfileAuthor);
        $this->addConditionsForPrivateContent($CNF,$oProfileAuthor);
        $this->addCustomConditions($CNF,$oProfileAuthor,$sMode,$aParams);
    }
}
