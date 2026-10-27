<?php
define('BX_DOL',1);require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsGallery.php';
$rows=[];for($id=150;$id>0;$id--)$rows[]=['id'=>$id];
$load=fn($before,$limit)=>array_slice(array_values(array_filter($rows,fn($r)=>!$before || $r['id']<$before)),0,$limit);
$visible=fn($r)=>$r['id']%3===0;
$a=NwStyledPostsGallery::page($load,$visible,0);$b=NwStyledPostsGallery::page($load,$visible,$a['next']);
if(count($a['items'])!==24 || count($b['items'])!==24 || array_intersect(array_column($a['items'],'id'),array_column($b['items'],'id')))throw new RuntimeException('Pagination skips or repeats');
foreach(array_merge($a['items'],$b['items']) as $r)if(!$visible($r))throw new RuntimeException('Private post exposed');
$c=NwStyledPostsGallery::page($load,fn($r)=>false,0);
if($c['items'] || $c['next'])throw new RuntimeException('All hidden collection');
$rows=[];for($id=1000;$id>0;$id--)$rows[]=['id'=>$id];$calls=0;$bounded=function($before,$limit)use($rows,&$calls){$calls++;return array_slice(array_values(array_filter($rows,fn($r)=>!$before||$r['id']<$before)),0,$limit);};
$d=NwStyledPostsGallery::page($bounded,fn($r)=>false,0);
if($calls!==10 || $d['next']!==501)throw new RuntimeException('Bounded scan must advance cursor');
echo "Gallery pagination, audience filtering and bounded scan passed\n";
