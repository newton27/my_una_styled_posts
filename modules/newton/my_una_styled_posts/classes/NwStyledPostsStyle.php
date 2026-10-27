<?php defined('BX_DOL') or die('hack attempt');
/** Shared allowlist for the editor, server validation and saved-card rendering. */
class NwStyledPostsStyle
{
    const MAX_TEXT = 500;
    const MIN_SIZE = 18;
    const MAX_SIZE = 96;
    const MAX_IMAGE_BYTES = 8388608;
    const MAX_IMAGE_PIXELS = 16000000;

    public static function catalog(): array
    {
        $fonts = [
            'classic' => ['label' => 'Classic', 'family' => 'Georgia, serif'],
            'modern' => ['label' => 'Modern', 'family' => 'Arial, sans-serif'],
            'handwritten' => ['label' => 'Handwritten', 'family' => 'NW Caveat, cursive'],
            'editorial' => ['label' => 'Editorial', 'family' => 'NW Playfair, Georgia, serif'],
            'mono' => ['label' => 'Typewriter', 'family' => 'Courier New, monospace'],
        ];
        $backgrounds = [
            'solid' => ['label' => 'Solid color', 'kind' => 'color', 'css' => 'none'],
            'aurora' => ['label' => 'Aurora', 'kind' => 'gradient', 'css' => 'radial-gradient(at 10% 20%,#ff709c 0,transparent 55%),radial-gradient(at 85% 80%,#79dbda 0,transparent 60%),linear-gradient(135deg,#fa8b9c,#ad9af2)'],
            'sunset' => ['label' => 'Golden hour', 'kind' => 'gradient', 'css' => 'linear-gradient(135deg,#a43966,#de7760 50%,#f4be81)'],
            'midnight' => ['label' => 'Midnight', 'kind' => 'gradient', 'css' => 'radial-gradient(at 90% 0%,#7751a6,transparent 65%),linear-gradient(135deg,#171b39,#343168)'],
            'ocean' => ['label' => 'Ocean', 'kind' => 'gradient', 'css' => 'linear-gradient(135deg,#145965,#257d8e 50%,#5eaaa1)'],
            'rose' => ['label' => 'Rose', 'kind' => 'gradient', 'css' => 'linear-gradient(135deg,#9d5275,#d290a5,#efbdba)'],
            'confetti' => ['label' => 'Confetti', 'kind' => 'pattern', 'css' => 'repeating-linear-gradient(45deg,transparent 0 24px,#ffffff20 24px 28px),linear-gradient(135deg,#6e54a1,#b66997)'],
            'gingham' => ['label' => 'Gingham', 'kind' => 'pattern', 'css' => 'repeating-linear-gradient(0deg,#ffffff14 0 18px,transparent 18px 36px),repeating-linear-gradient(90deg,#ffffff18 0 18px,transparent 18px 36px),linear-gradient(#416768,#416768)'],
            'mountains' => ['label' => 'Quiet mountains', 'kind' => 'picture', 'file' => 'mountains.svg', 'css' => 'none'],
            'botanical' => ['label' => 'Botanical', 'kind' => 'picture', 'file' => 'botanical.svg', 'css' => 'none'],
        ];
        foreach ($fonts as $id => &$item) $item['enabled'] = !function_exists('getParam') || getParam('nw_styled_posts_font_'.$id) === 'on';
        unset($item);
        foreach ($backgrounds as $id => &$item) $item['enabled'] = !function_exists('getParam') || getParam('nw_styled_posts_background_'.$id) === 'on';
        unset($item);
        return ['fonts' => $fonts, 'backgrounds' => $backgrounds];
    }

    public static function normalize(array $input, array $catalog): array
    {
        $out = array_replace(['font_name'=>'classic','font_size'=>48,'text_color'=>'#FFFFFF','text_align'=>'center','background'=>'aurora','background_color'=>'#6554C0'], array_intersect_key($input, array_flip(['font_name','font_size','text_color','text_align','background','background_color'])));
        foreach (['font_name'=>'fonts','background'=>'backgrounds'] as $key=>$group) {
            if (!is_string($out[$key]) || empty($catalog[$group][$out[$key]]['enabled'])) throw new InvalidArgumentException('Choose an enabled '.$key.'.');
        }
        $size = $out['font_size'];
        if ((!is_int($size) && (!is_string($size) || !preg_match('/^\d{2}$/D',$size))) || (int)$size < self::MIN_SIZE || (int)$size > self::MAX_SIZE) throw new InvalidArgumentException('Font size must be between 18 and 96.');
        $out['font_size'] = (int)$size;
        foreach (['text_color','background_color'] as $key) {
            if (!is_string($out[$key]) || !preg_match('/^#[A-Fa-f0-9]{6}$/D',$out[$key])) throw new InvalidArgumentException('Choose a valid six-digit color.');
            $out[$key] = strtoupper($out[$key]);
        }
        if (!is_string($out['text_align']) || !in_array($out['text_align'],['left','center','right'],true)) throw new InvalidArgumentException('Choose left, center or right alignment.');
        return $out;
    }

    public static function forDisplay(array $input, array $catalog): array
    {
        $fallback = ['font_name'=>'classic','font_size'=>48,'text_color'=>'#FFFFFF','text_align'=>'center','background'=>'solid','background_color'=>'#343168'];
        $background=$input['background']??'solid';
        if (!is_string($background) || empty($catalog['backgrounds'][$background]['enabled'])) {
            $input['background']='solid';$input['background_color']='#343168';$input['text_color']='#FFFFFF';
        }
        // Existing content stays readable even if every choice is disabled in Studio.
        foreach (['fonts'=>'classic','backgrounds'=>'solid'] as $group=>$id) $catalog[$group][$id]['enabled'] = true;
        foreach ($fallback as $key=>$value) {
            $try = $fallback;
            $try[$key] = $input[$key] ?? $value;
            try { $fallback[$key] = self::normalize($try,$catalog)[$key]; }
            catch (InvalidArgumentException $e) { /* Use the known readable default. */ }
        }
        return $fallback;
    }

    public static function forEditor(array $input, array $catalog): array
    {
        $state=self::forDisplay(array_replace(['background'=>'aurora'],$input),$catalog);
        foreach (['font_name'=>'fonts','background'=>'backgrounds'] as $key=>$group) {
            if (!empty($catalog[$group][$state[$key]]['enabled'])) continue;
            foreach ($catalog[$group] as $id=>$item) if ($item['enabled']) { $state[$key]=$id; break; }
        }
        return $state;
    }

    public static function validateText($text): string
    {
        if (!is_string($text) || preg_match('//u',$text) !== 1) throw new InvalidArgumentException('Enter valid text.');
        $text = str_replace(["\r\n","\r"],"\n",$text);
        $count = preg_match_all('/./us',$text);
        if (!preg_match('/\S/u',$text) || $count > self::MAX_TEXT || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/',$text)) throw new InvalidArgumentException('Enter 1 to 500 characters.');
        return $text;
    }

    public static function escape(string $text): string
    {
        return htmlspecialchars($text,ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8');
    }

    public static function validateImage(string $path, string $claimedMime): array
    {
        $size = is_file($path) ? filesize($path) : 0;
        if (!$size || $size > self::MAX_IMAGE_BYTES) throw new InvalidArgumentException('Choose an image up to 8 MiB.');
        $info = @getimagesize($path);
        if (!$info || !in_array($info['mime'],['image/jpeg','image/png','image/webp'],true) || $claimedMime !== $info['mime']) throw new InvalidArgumentException('Choose a valid JPG, PNG or WebP image.');
        if ($info[0] < 1 || $info[1] < 1 || $info[0] > 8000 || $info[1] > 8000 || $info[0]*$info[1] > self::MAX_IMAGE_PIXELS) throw new InvalidArgumentException('Choose an image no larger than 16 megapixels.');
        return ['mime'=>$info['mime'],'width'=>$info[0],'height'=>$info[1],'size'=>$size];
    }

    public static function contrast(string $a, string $b): float
    {
        $luminance = function($hex) {
            $rgb = array_map(function($c){$v=hexdec($c)/255;return $v<=0.04045 ? $v/12.92 : pow(($v+0.055)/1.055,2.4);},str_split(substr($hex,1),2));
            return 0.2126*$rgb[0]+0.7152*$rgb[1]+0.0722*$rgb[2];
        };
        $x=$luminance($a);$y=$luminance($b);
        return (max($x,$y)+0.05)/(min($x,$y)+0.05);
    }
}
