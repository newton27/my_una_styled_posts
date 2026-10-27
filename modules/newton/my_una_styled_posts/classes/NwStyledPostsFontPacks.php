<?php defined('BX_DOL') or die('hack attempt');
/** Administrator-installed fonts, outside module code so updates preserve packs. */
class NwStyledPostsFontPacks
{
    public static function directory(): string { return defined('BX_DIRECTORY_STORAGE')?BX_DIRECTORY_STORAGE.'nw_styled_posts_fonts':''; }
    public static function load(string $directory): array
    {
        $result=['fonts'=>[],'errors'=>[]];$base=realpath($directory);
        if (!$base || !is_file($base.'/fonts.json')) return $result;
        if (filesize($base.'/fonts.json')>32768) {$result['errors'][]='Font manifest exceeds 32 KiB.';return $result;}
        $items=json_decode(file_get_contents($base.'/fonts.json'),true,8);
        if (!is_array($items) || !array_is_list($items) || count($items)>32) {$result['errors'][]='Use a JSON list of at most 32 fonts.';return $result;}
        foreach ($items as $item) {
            $id=$item['id']??null;$label=$item['label']??null;$file=$item['file']??null;$license=$item['license']??null;
            $valid=is_string($id) && preg_match('/^[a-z][a-z0-9_]{0,23}$/D',$id) && is_string($label) && preg_match('/^[^\x00-\x1f<>]{1,80}$/uD',$label) && is_string($file) && preg_match('/^[a-zA-Z0-9_-]+\.woff2$/D',$file) && is_string($license) && preg_match('/^[a-zA-Z0-9_.-]+\.txt$/D',$license);
            $path=$valid?realpath($base.'/'.$file):false;$lp=$valid?realpath($base.'/'.$license):false;
            if (!$valid || !$path || !$lp || dirname($path)!==$base || dirname($lp)!==$base || !is_file($path) || !is_file($lp) || filesize($path)<48 || filesize($path)>2097152 || filesize($lp)<1 || substr(file_get_contents($path,false,null,0,4),0,4)!=='wOF2' || isset($result['fonts']['custom_'.$id])) {
                $result['errors'][]='A font was ignored: use a unique safe ID, local WOFF2 file (up to 2 MiB) and license text.';continue;
            }
            $result['fonts']['custom_'.$id]=['label'=>$label,'family'=>'NWCustom_'.$id.', sans-serif','enabled'=>true,'path'=>$path,'custom'=>true];
        }
        return $result;
    }
}
