<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});define('BX_DOL',1);
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsComposer.php';
function ok($v,$m){if(!$v)throw new RuntimeException($m);$GLOBALS['n']=($GLOBALS['n']??0)+1;}
$c=NwStyledPostsStyle::catalog();$s=NwStyledPostsStyle::normalize([],$c);
ok(($s['text_position']??null)==='auto' && $s['text_x']===50 && $s['text_y']===50,'Existing layout remains auto');
foreach(['text_x','text_y'] as $key)foreach([-1,101,'1.5','NaN',[],null,'50;display:none'] as $v){try{NwStyledPostsStyle::normalize([$key=>$v],$c);throw new RuntimeException('Accepted bad '.$key);}catch(InvalidArgumentException $e){ok(true,'Reject bad coordinate');}}
foreach(['free',[],null] as $v){try{NwStyledPostsStyle::normalize(['text_position'=>$v],$c);throw new RuntimeException('Accepted mode');}catch(InvalidArgumentException $e){ok(true,'Reject mode');}}
$s=NwStyledPostsStyle::normalize(['text_position'=>'custom','text_x'=>'0','text_y'=>'100'],$c);
ok($s['text_x']===0 && $s['text_y']===100,'Save integer boundaries');
$html=NwStyledPostsCard::render($s+['text'=>'Hello'],$c,'/assets/');
ok(str_contains($html,'data-text-position="custom"') && str_contains($html,'data-text-x="0"') && str_contains($html,'data-text-y="100"'),'Render saved position');
$f=NwStyledPostsStyle::forDisplay(['text_x'=>999,'text_y'=>'bad','text_position'=>'css'],$c);ok($f['text_x']===50 && $f['text_y']===50 && $f['text_position']==='auto','Corrupt saved position defaults safely');
ok(str_contains(NwStyledPostsComposer::render([],$c,'/assets/'),'data-reset-position'),'Reset control exists');
echo $GLOBALS['n']." text positioning assertions passed\n";
