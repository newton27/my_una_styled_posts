<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsStyle.php';
class NwStyledPostsCard
{
    public static function render(array $post, array $catalog, string $assetBase, string $backgroundUrl = '', array $motion = []): string
    {
        $s=NwStyledPostsStyle::forDisplay($post,$catalog);
        $bg=$catalog['backgrounds'][$s['background']];
        $css='--nwsp-size:'.$s['font_size'].'px;color:'.$s['text_color'].';text-align:'.$s['text_align'].';font-family:'.$catalog['fonts'][$s['font_name']]['family'].';background-color:'.$s['background_color'].';background-image:'.$bg['css'].';';
        $color=sscanf($s['backdrop_color'],'#%02x%02x%02x');
        $box=$s['text_backdrop']==='box';
        $css.='--nwsp-backdrop:'.($box?'rgba('.implode(',',$color).','.($s['backdrop_opacity']/100).')':'transparent').';--nwsp-backdrop-padding:'.($box?$s['backdrop_padding']:0).'px;--nwsp-image-x:'.$s['image_x'].'%;--nwsp-image-y:'.$s['image_y'].'%;--nwsp-image-zoom:'.($s['image_zoom']/100).';';
        $picture=$backgroundUrl ?: (!empty($bg['file']) ? $assetBase.'images/backgrounds/'.$bg['file'] : '');
        $e=[NwStyledPostsStyle::class,'escape'];
        if (!$motion && !$backgroundUrl && ($bg['kind']??'')==='motion') $motion=['state'=>'ready','clip'=>$assetBase.'motion/'.$bg['clip'],'poster'=>$assetBase.'motion/'.$bg['poster']];
        $media=$picture ? '<img class="nwsp-background" src="'.$e($picture).'" alt="" loading="lazy"><span class="nwsp-shade" aria-hidden="true"></span>' : '';
        if ($motion) {
            $json=$e(json_encode($motion,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT));
            $poster=$motion['poster']??'';
            $media='<video class="nwsp-background nwsp-motion" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-motion="'.$json.'"'.($poster?' poster="'.$e($poster).'"':'').'></video><span class="nwsp-shade" aria-hidden="true"></span><button type="button" class="nwsp-motion-toggle" data-motion-toggle aria-label="Play background animation">Play motion</button><span class="nwsp-motion-status" role="status">'.(($motion['state']??'')==='pending'?'Processing background…':(($motion['state']??'')==='failed'?'Background processing failed. Edit this post to replace the file.':'')).'</span>';
        }
        $font=$catalog['fonts'][$s['font_name']];
        $face='';
        if (!empty($font['custom']) && defined('BX_DOL_URL_ROOT')) $face='<style>@font-face{font-family:NWCustom_'.substr($s['font_name'],7).';src:url("'.$e(BX_DOL_URL_ROOT.'modules/?r=styled_posts/font/'.substr($s['font_name'],7)).'") format("woff2");font-display:swap}</style>';
        return $face.'<article class="nwsp-card" data-font-size="'.$s['font_size'].'" data-text-position="'.$s['text_position'].'" data-text-x="'.$s['text_x'].'" data-text-y="'.$s['text_y'].'" style="'.$e($css).'">'.$media.'<div class="nwsp-text" dir="'.$s['text_direction'].'"><span class="nwsp-text-content">'.$e(is_string($post['text']??null)?$post['text']:'').'</span></div></article>';
    }
}
