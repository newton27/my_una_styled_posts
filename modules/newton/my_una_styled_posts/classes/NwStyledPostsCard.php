<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsStyle.php';
class NwStyledPostsCard
{
    public static function render(array $post, array $catalog, string $assetBase, string $backgroundUrl = ''): string
    {
        $s=NwStyledPostsStyle::forDisplay($post,$catalog);
        $bg=$catalog['backgrounds'][$s['background']];
        $css='--nwsp-size:'.$s['font_size'].'px;color:'.$s['text_color'].';text-align:'.$s['text_align'].';font-family:'.$catalog['fonts'][$s['font_name']]['family'].';background-color:'.$s['background_color'].';background-image:'.$bg['css'].';';
        $picture=$backgroundUrl ?: (!empty($bg['file']) ? $assetBase.'images/backgrounds/'.$bg['file'] : '');
        $e=[NwStyledPostsStyle::class,'escape'];
        return '<article class="nwsp-card" data-font-size="'.$s['font_size'].'" style="'.$e($css).'">'.($picture ? '<img class="nwsp-background" src="'.$e($picture).'" alt="" loading="lazy"><span class="nwsp-shade" aria-hidden="true"></span>' : '').'<div class="nwsp-text">'.$e(is_string($post['text']??null)?$post['text']:'').'</div></article>';
    }
}
