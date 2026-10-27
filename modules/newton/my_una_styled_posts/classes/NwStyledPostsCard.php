<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsStyle.php';
class NwStyledPostsCard
{
    public static function render(array $post, array $catalog, string $assetBase, string $backgroundUrl = '', array $motion = []): string
    {
        $s=NwStyledPostsStyle::forDisplay($post,$catalog);
        $bg=$catalog['backgrounds'][$s['background']];
        $css='--nwsp-size:'.$s['font_size'].'px;color:'.$s['text_color'].';text-align:'.$s['text_align'].';font-family:'.$catalog['fonts'][$s['font_name']]['family'].';background-color:'.$s['background_color'].';background-image:'.$bg['css'].';';
        $picture=$backgroundUrl ?: (!empty($bg['file']) ? $assetBase.'images/backgrounds/'.$bg['file'] : '');
        $e=[NwStyledPostsStyle::class,'escape'];
        if (!$motion && !$backgroundUrl && ($bg['kind']??'')==='motion') $motion=['state'=>'ready','clip'=>$assetBase.'motion/'.$bg['clip'],'poster'=>$assetBase.'motion/'.$bg['poster']];
        $media=$picture ? '<img class="nwsp-background" src="'.$e($picture).'" alt="" loading="lazy"><span class="nwsp-shade" aria-hidden="true"></span>' : '';
        if ($motion) {
            $json=$e(json_encode($motion,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT));
            $poster=$motion['poster']??'';
            $media='<video class="nwsp-background nwsp-motion" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-motion="'.$json.'"'.($poster?' poster="'.$e($poster).'"':'').'></video><span class="nwsp-shade" aria-hidden="true"></span><button type="button" class="nwsp-motion-toggle" data-motion-toggle aria-label="Play background animation">Play motion</button><span class="nwsp-motion-status" role="status">'.(($motion['state']??'')==='pending'?'Processing background…':(($motion['state']??'')==='failed'?'Background processing failed. Edit this post to replace the file.':'')).'</span>';
        }
        return '<article class="nwsp-card" data-font-size="'.$s['font_size'].'" style="'.$e($css).'">'.$media.'<div class="nwsp-text">'.$e(is_string($post['text']??null)?$post['text']:'').'</div></article>';
    }
}
