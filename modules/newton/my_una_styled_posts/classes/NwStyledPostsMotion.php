<?php defined('BX_DOL') or die('hack attempt');

/** Media policy and bounded probing; conversion belongs to UNA's native queue. */
class NwStyledPostsMotion
{
    const MAX_BYTES = 8388608;
    const MIME_TYPES = ['video/mp4','video/webm','video/quicktime','image/gif'];

    public static function validateMetadata(array $m): array
    {
        $duration=$m['duration']??null;
        if (!is_numeric($duration) || !is_finite((float)$duration) || $duration<3 || $duration>5)
            throw new InvalidArgumentException('Choose a video or animated GIF between 3 and 5 seconds.');
        if (!in_array($m['mime']??'',self::MIME_TYPES,true) || ($m['size']??0)<1 || $m['size']>self::MAX_BYTES)
            throw new InvalidArgumentException('Choose an MP4, WebM, MOV or GIF up to 8 MiB.');
        if (($m['width']??0)<1 || ($m['height']??0)<1 || $m['width']>1280 || $m['height']>1280)
            throw new InvalidArgumentException('Motion backgrounds must be no larger than 1280 pixels on either side.');
        if (($m['frames']??0)<1 || ($m['mime']==='image/gif' && $m['frames']<2))
            throw new InvalidArgumentException('Choose an animated GIF or a short video.');
        return array_replace($m,['duration'=>(float)$duration]);
    }

    public static function parseProbe(array $probe, string $mime, int $size): array
    {
        $video=$probe['streams'][0]??[];
        $duration=0.0;
        foreach ([$video['duration']??null,$probe['format']['duration']??null] as $d)
            if (is_numeric($d)) $duration=max($duration,(float)$d);
        $start=null;$end=null;
        foreach ($probe['packets']??[] as $packet) {
            if (!is_numeric($packet['pts_time']??null) || !is_numeric($packet['duration_time']??null)) continue;
            $pts=(float)$packet['pts_time'];$length=(float)$packet['duration_time'];
            if (!is_finite($pts) || !is_finite($length) || $length<=0) continue;
            $start=$start===null ? $pts : min($start,$pts);
            $end=$end===null ? $pts+$length : max($end,$pts+$length);
        }
        if ($start!==null && $end!==null) $duration=max($duration,round($end-$start,6));
        return self::validateMetadata(['duration'=>$duration,'width'=>(int)($video['width']??0),'height'=>(int)($video['height']??0),'frames'=>count($probe['packets']??[]),'mime'=>$mime,'size'=>$size]);
    }

    public static function binaries(): array
    {
        $ffmpeg=defined('BX_SYSTEM_FFMPEG') ? BX_SYSTEM_FFMPEG : '';
        $ffprobe=$ffmpeg ? dirname($ffmpeg).DIRECTORY_SEPARATOR.(str_ends_with($ffmpeg,'.exe')?'ffprobe.exe':'ffprobe') : '';
        if (!is_executable($ffprobe)) foreach (['/usr/bin/ffprobe','/usr/local/bin/ffprobe'] as $candidate) if (is_executable($candidate)) { $ffprobe=$candidate;break; }
        if (defined('BX_TRANSCODER_NO_TRANSCODING') || !$ffmpeg || !is_executable($ffmpeg) || !is_executable($ffprobe) || !function_exists('proc_open'))
            throw new InvalidArgumentException('Motion uploads need local UNA video conversion and ffprobe (beside FFmpeg or in /usr/bin or /usr/local/bin). Ask the administrator to check video processing.');
        return [$ffmpeg,$ffprobe];
    }

    public static function available(): bool
    {
        try { self::binaries(); return true; } catch (InvalidArgumentException $e) { return false; }
    }

    public static function inspect(string $path): array
    {
        [$ffmpeg, $ffprobe]=self::binaries();
        $size=@filesize($path);
        if (!$size || $size>self::MAX_BYTES || !class_exists('finfo')) throw new InvalidArgumentException('Choose a valid motion file up to 8 MiB.');
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!in_array($mime,self::MIME_TYPES,true)) throw new InvalidArgumentException('Choose a valid MP4, WebM, MOV or animated GIF.');
        // Array arguments avoid shell interpretation. Protocols are restricted to the local file.
        $command=[$ffprobe,'-v','error','-protocol_whitelist','file','-select_streams','v:0','-show_entries','stream=width,height,duration:format=duration:packet=pts_time,duration_time','-of','json',$path];
        $output=self::runCommand($command);
        $probe=json_decode($output,true);
        if (!is_array($probe)) throw new InvalidArgumentException('That motion file is not valid.');
        $metadata=self::parseProbe($probe,$mime,(int)$size);
        // Probe metadata alone does not detect corrupt video packet payloads.
        $decoded=self::runCommand([$ffmpeg,'-v','error','-xerror','-err_detect','explode','-nostdin','-protocol_whitelist','file,pipe','-i',$path,'-map','0:v:0','-an','-t','6','-progress','pipe:1','-f','null','-']);
        if (!preg_match_all('/^out_time_us=([0-9]+)$/m',$decoded,$times) || max(array_map('intval',$times[1]))>5000000)
            throw new InvalidArgumentException('That motion file has invalid playback timing.');
        return $metadata;
    }

    protected static function runCommand(array $command): string
    {
        $pipes=[];$process=@proc_open($command,[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
        if (!is_resource($process)) throw new InvalidArgumentException('The motion file could not be inspected.');
        fclose($pipes[0]);stream_set_blocking($pipes[1],false);stream_set_blocking($pipes[2],false);
        $output='';$errors='';$deadline=microtime(true)+10;$status=['running'=>true];$limited=false;
        try {
            do {
                $output.=stream_get_contents($pipes[1]);$errors.=stream_get_contents($pipes[2]);
                if (strlen($output)>524288 || strlen($errors)>65536 || microtime(true)>$deadline) { $limited=true;proc_terminate($process,9);break; }
                $status=proc_get_status($process);
                if ($status['running']) usleep(10000);
            } while ($status['running']);
            $output.=stream_get_contents($pipes[1]);$errors.=stream_get_contents($pipes[2]);
        } finally { fclose($pipes[1]);fclose($pipes[2]);$exit=proc_close($process); }
        if ($limited || strlen($output)>524288 || $errors!=='' || (($status['exitcode']??$exit)!==0))
            throw new InvalidArgumentException('That motion file could not be read safely. Choose another file.');
        return $output;
    }
}
