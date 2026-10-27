<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsGallery
{
    public static function page(callable $load, callable $allowed, int $before): array
    {
        $items=[];$cursor=max(0,$before);
        for($batch=0;$batch<10;$batch++) {
            $rows=$load($cursor,50);
            if (!$rows) return ['items'=>$items,'next'=>0];
            foreach ($rows as $row) {
                $id=(int)$row['id'];
                if ($id<1 || ($cursor && $id>=$cursor)) return ['items'=>$items,'next'=>0];
                $cursor=$id;
                if ($allowed($row)) $items[]=$row;
                if (count($items)===24) return ['items'=>$items,'next'=>$cursor];
            }
            if (count($rows)<50) return ['items'=>$items,'next'=>0];
        }
        return ['items'=>$items,'next'=>$cursor];
    }
}
