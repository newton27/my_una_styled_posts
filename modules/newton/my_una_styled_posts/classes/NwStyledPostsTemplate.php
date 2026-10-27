<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsCard.php';
class NwStyledPostsTemplate extends BxBaseModTextTemplate
{
    function __construct(&$oConfig, &$oDb)
    {
        $this->MODULE = 'nw_styled_posts';
        parent::__construct($oConfig, $oDb);
    }
    public function assetBase(): string
    {
        return BX_DOL_URL_MODULES.'newton/my_una_styled_posts/template/';
    }
    public function card(array $post, bool $dynamic = false): string
    {
        $include=$this->addCss(['card.css'],$dynamic).$this->addJs(['composer.js'],$dynamic);
        return ($dynamic?$include:'').NwStyledPostsCard::render($post,NwStyledPostsStyle::catalog(),$this->assetBase(),$this->getModule()->getBackgroundUrl($post));
    }
    function getAuthorDesc($aData, $oProfile)
    {
        $module=$this->getModule();
        $count=(int)$module->serviceGetMenuAddonManageToolsProfileStats($oProfile->id());
        $key='_nw_styled_posts_txt_all_entries_by';
        $label=_t($key,$count);
        if ($label===$key) $label='Styled Posts ('.$count.')';
        $items=[];
        if (!empty($aData['added'])) $items[]=[
            'bx_if:text'=>['condition'=>true,'content'=>['content'=>bx_time_js($aData['added'],BX_FORMAT_DATE)]],
            'bx_if:link'=>['condition'=>false,'content'=>['link'=>'','content'=>'']],
        ];
        $items[]=[
            'bx_if:text'=>['condition'=>false,'content'=>['content'=>'']],
            'bx_if:link'=>['condition'=>true,'content'=>[
                'link'=>BxDolPermalinks::getInstance()->permalink('page.php?i=styled-posts-home&profile_id='.(int)$oProfile->id()),
                'content'=>NwStyledPostsStyle::escape($label),
            ]],
        ];
        return $this->parseHtmlByName('author_desc.html',['bx_repeat:items'=>$items]);
    }

    function unit($aData, $isCheckPrivateContent = true, $sTemplateName = 'unit.html', $aParams = [])
    {
        // Search/browse cards must never use attachment fields this module does not own.
        if ($this->getModule()->checkAllowedView($aData)!==CHECK_ACTION_RESULT_ALLOWED) return '';
        $url=BX_DOL_URL_ROOT.'page.php?i=view-styled-post&id='.(int)$aData['id'];
        return '<a href="'.NwStyledPostsStyle::escape($url).'">'.$this->card($aData,bx_is_dynamic_request()).'</a>';
    }

    public function entryText($aData, $sTemplateName = 'entry-text.html')
    {
        if ($this->getModule()->checkAllowedView($aData)!==CHECK_ACTION_RESULT_ALLOWED) return '';
        return bx_is_api() ? ['entry_text'=>$aData['text']] : $this->card($aData,bx_is_dynamic_request());
    }
}
