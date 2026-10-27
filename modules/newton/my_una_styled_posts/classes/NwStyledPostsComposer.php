<?php defined('BX_DOL') or die('hack attempt');
require_once __DIR__.'/NwStyledPostsCard.php';
class NwStyledPostsComposer
{
    public static function render(array $values, array $catalog, string $assetBase, string $backgroundUrl = '', array $photos = []): string
    {
        $s=NwStyledPostsStyle::forEditor($values,$catalog);
        $e=[NwStyledPostsStyle::class,'escape'];
        $options='';
        foreach($catalog['fonts'] as $id=>$font) if($font['enabled']) $options.='<option value="'.$e($id).'"'.($s['font_name']===$id?' selected':'').'>'.$e($font['label']).'</option>';
        $swatches='';
        foreach($catalog['backgrounds'] as $id=>$bg) {
            if(!$bg['enabled']) continue;
            $css='background-color:'.($id==='solid'?$s['background_color']:'#6554C0').';background-image:'.$bg['css'];
            if(!empty($bg['file'])) $css.=';background-image:url('.$assetBase.'images/backgrounds/'.$bg['file'].')';
            $swatches.='<button type="button" class="nwsp-swatch" data-background="'.$e($id).'" aria-label="'.$e($bg['label']).'" aria-pressed="'.($id===$s['background']?'true':'false').'" title="'.$e($bg['label']).'" style="'.$e($css).'"></button>';
        }
        $align='';foreach(['left'=>'Left','center'=>'Center','right'=>'Right'] as $id=>$label) $align.='<button type="button" data-align="'.$id.'" aria-label="Align '.$label.'" aria-pressed="'.($s['text_align']===$id?'true':'false').'">'.$label.'</button>';
        $available=count(array_filter($catalog['fonts'],fn($f)=>$f['enabled']))>0 && count(array_filter($catalog['backgrounds'],fn($b)=>$b['enabled']))>0;
        $json=json_encode(['available'=>$available,'catalog'=>$catalog,'assetBase'=>$assetBase,'values'=>$s,'backgroundUrl'=>$backgroundUrl,'photos'=>$photos],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE);
        $picker='';
        if (($photos['enabled']??true)) $picker='<div class="nwsp-photo-actions"><button type="button" data-choose-photos>Choose from my photos</button><button type="button" data-upload-photo>Upload photo</button><input type="hidden" name="background_photo" value="'.(int)($photos['selected']??0).'"></div><p class="nwsp-help">Your selected photo is copied as a background and shared with your post’s audience.</p><dialog class="nwsp-photo-dialog" aria-label="Choose from my photos"><div class="nwsp-photo-heading"><strong>Choose from my photos</strong><button type="button" data-close-photos aria-label="Close photo picker">Close</button></div><p data-photo-status role="status" aria-live="polite"></p><div class="nwsp-photo-grid"></div><button type="button" data-more-photos hidden>Load more photos</button></dialog>';
        return '<section class="nwsp-composer" aria-label="Style your post"><div class="nwsp-toolbar"><label>Font<select data-control="font_name">'.$options.'</select></label><label>Size <output data-size-label>'.$s['font_size'].'</output><input type="range" data-control="font_size" min="18" max="96" step="1" value="'.$s['font_size'].'"></label><label>Text color<input type="color" data-control="text_color" value="'.$s['text_color'].'"></label><label>Background color<input type="color" data-control="background_color" value="'.$s['background_color'].'"></label></div><div class="nwsp-align" role="group" aria-label="Text alignment">'.$align.'</div><div class="nwsp-background-heading"><strong>Choose your backdrop</strong><span>Colors · gradients · patterns · pictures</span></div><div class="nwsp-swatches" role="group" aria-label="Backgrounds">'.$swatches.'</div>'.$picker.'<div class="nwsp-preview">'.NwStyledPostsCard::render($values,$catalog,$assetBase,$backgroundUrl).'</div><div class="nwsp-preview-footer"><span>LIVE PREVIEW</span><span data-count>0 / 500</span></div><p class="nwsp-feedback" data-feedback role="status" aria-live="polite"></p><p class="nwsp-help">Your words stay editable. Choose your audience below before posting.</p><script type="application/json" class="nwsp-config">'.$json.'</script></section>';
    }
}
