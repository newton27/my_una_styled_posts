<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});define('BX_DOL',1);
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsCard.php';
function ok($v,$m){if(!$v)throw new RuntimeException($m);$GLOBALS['n']=($GLOBALS['n']??0)+1;}
$c=NwStyledPostsStyle::catalog();$s=NwStyledPostsStyle::normalize([],$c);
ok(($s['text_backdrop']??null)==='none','Old posts default to no backdrop');
ok($s['image_x']===50 && $s['image_y']===50 && $s['image_zoom']===100,'Old images retain centered cover');
foreach(['backdrop_opacity'=>[-1,101,'12.5',[],null],'backdrop_padding'=>[-1,33],'image_x'=>[-1,101],'image_y'=>[-1,101],'image_zoom'=>[99,251,'url(x)'],'text_backdrop'=>['url(x)',[]],'backdrop_color'=>['red','#123',[]],'text_direction'=>['css',[]]] as $key=>$bad)foreach($bad as $v){try{NwStyledPostsStyle::normalize([$key=>$v],$c);throw new RuntimeException('Accepted bad '.$key);}catch(InvalidArgumentException $e){ok(true,'Reject '.$key);}}
$s=NwStyledPostsStyle::normalize(['text_backdrop'=>'box','backdrop_color'=>'#123456','backdrop_opacity'=>'75','backdrop_padding'=>'20','image_x'=>'0','image_y'=>'100','image_zoom'=>'250','text_direction'=>'rtl'],$c);
ok($s['backdrop_opacity']===75 && $s['image_zoom']===250,'Normalize bounded integers');
$html=NwStyledPostsCard::render(array_merge($s,['text'=>'<عربي>']),$c,'/assets/','/private/photo');
ok(str_contains($html,'rgba(18,52,86,0.75)'),'Backdrop color and opacity');
ok(str_contains($html,'--nwsp-image-x:0%') && str_contains($html,'--nwsp-image-zoom:2.5'),'Render framing');
ok(str_contains($html,'dir="rtl"') && str_contains($html,'&lt;عربي&gt;'),'Escaped RTL text');
$d=NwStyledPostsStyle::forDisplay(['image_zoom'=>'hack','backdrop_padding'=>999,'text_backdrop'=>[]],$c);
ok($d['image_zoom']===100 && $d['backdrop_padding']===12 && $d['text_backdrop']==='none','Corrupt saved fields safely default');
echo $GLOBALS['n']." editor style assertions passed\n";
