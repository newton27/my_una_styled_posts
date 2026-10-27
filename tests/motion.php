<?php
define('BX_DOL', true);
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsMotion.php';
$n=0;
function check($ok) { global $n; ++$n; if (!$ok) throw new RuntimeException('Motion assertion '.$n); }
function invalid(array $m) { try { NwStyledPostsMotion::validateMetadata($m); return false; } catch (InvalidArgumentException $e) { return true; } }
$base=['duration'=>3.0,'width'=>640,'height'=>480,'frames'=>60,'mime'=>'video/mp4','size'=>1024];
check(NwStyledPostsMotion::validateMetadata($base)['duration']===3.0);
check(!invalid(array_replace($base,['duration'=>5.0])));
foreach ([2.999,5.001,0,INF,NAN] as $v) check(invalid(array_replace($base,['duration'=>$v])));
foreach (['width'=>1281,'height'=>0,'size'=>8388609,'mime'=>'image/svg+xml','frames'=>0] as $k=>$v) check(invalid(array_replace($base,[$k=>$v])));
check(invalid(array_replace($base,['mime'=>'image/gif','frames'=>1])));
check(!invalid(array_replace($base,['mime'=>'image/gif','frames'=>30])));
check(NwStyledPostsMotion::parseProbe(['streams'=>[['codec_type'=>'video','width'=>320,'height'=>240]],'packets'=>[['pts_time'=>'0','duration_time'=>'0.1'],['pts_time'=>'2.9','duration_time'=>'0.1']]],'image/gif',100)['duration']===3.0);
check(invalid(array_replace($base,['duration'=>null])));
if (!empty($argv[1])) { $m=NwStyledPostsMotion::inspect($argv[1]); check($m['duration']>=3 && $m['duration']<=5); }
echo "Motion: $n assertions passed\n";
