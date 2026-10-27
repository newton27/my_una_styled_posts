<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsMediaDelivery
{
    public static function range(string $value, int $size): array
    {
        if ($size<1) throw new InvalidArgumentException('Empty media');
        if ($value==='') return [0,$size-1,false];
        if (!preg_match('/^bytes=(\d*)-(\d*)$/D',$value,$m) || ($m[1]==='' && $m[2]==='')) throw new InvalidArgumentException('Invalid range');
        if ($m[1]==='') { $length=min((float)$m[2],$size);if ($length<1) throw new InvalidArgumentException('Invalid range');$start=$size-(int)$length;$end=$size-1; }
        else { $start=(float)$m[1];$end=$m[2]===''?$size-1:min((float)$m[2],$size-1); }
        if ($start>$end || $start>=$size) throw new InvalidArgumentException('Invalid range');
        return [(int)$start,(int)$end,true];
    }

    public static function send(string $path, string $mime): void
    {
        $size=filesize($path);
        try { [$start,$end,$partial]=self::range((string)($_SERVER['HTTP_RANGE']??''),$size); }
        catch (InvalidArgumentException $e) { http_response_code(416);header('Content-Range: bytes */'.$size);return; }
        $file=fopen($path,'rb');if (!$file) { http_response_code(404);return; }
        header('Content-Type: '.$mime);header('Content-Disposition: inline');header('Accept-Ranges: bytes');
        header('Content-Length: '.($end-$start+1));
        if ($partial) { http_response_code(206);header("Content-Range: bytes $start-$end/$size"); }
        try {
            if (($_SERVER['REQUEST_METHOD']??'GET')==='HEAD') return;
            fseek($file,$start);$remaining=$end-$start+1;
            while ($remaining>0 && !feof($file) && !connection_aborted()) { $data=fread($file,min(65536,$remaining));if ($data===false || $data==='') break;echo $data;$remaining-=strlen($data); }
        } finally { fclose($file); }
    }
}
