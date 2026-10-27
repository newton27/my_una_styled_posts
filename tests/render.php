<?php
error_reporting(E_ALL);set_error_handler(function($n,$m){throw new ErrorException($m,0,$n);});
define('BX_DOL',1);$GLOBALS['checks']=0;
function check($v,$msg){if(!$v)throw new RuntimeException($msg);$GLOBALS['checks']++;}
$root=__DIR__.'/../modules/newton/my_una_styled_posts/classes/';
check(is_file($root.'NwStyledPostsCard.php'),'Card renderer exists');require $root.'NwStyledPostsCard.php';
$c=NwStyledPostsStyle::catalog();
$html=NwStyledPostsCard::render(['text'=>"<script>alert('x')</script>\n世界",'font_size'=>96],$c,'/assets/','');
check(!str_contains($html,'<script>'),'Escape quote markup');
check(str_contains($html,'&lt;script&gt;'),'Preserve markup as literal text');
check(str_contains($html,'世界'),'Preserve Unicode');
check(str_contains($html,'data-font-size="96"'),'Keep selected size');
check(str_contains($html,'nwsp-text'),'Accessible text element');
$html=NwStyledPostsCard::render(['text'=>'A','font_name'=>'bad;position:fixed','text_color'=>'red'], $c,'/assets/','');
check(!str_contains($html,'position:fixed'),'Invalid persisted CSS falls back safely');
$html=NwStyledPostsCard::render(['text'=>'A','background'=>'mountains'], $c,'/assets/','');
check(str_contains($html,'/assets/images/backgrounds/mountains.svg'),'Preset picture resolves locally');
$html=NwStyledPostsCard::render(['text'=>'A'], $c,'/assets/','/modules/?r=styled_posts/background/1');
check(str_contains($html,'background/1'),'Member background uses authorized endpoint');
require $root.'NwStyledPostsComposer.php';
foreach($c['fonts'] as $id=>&$font) $font['enabled']=$id==='modern';unset($font);
$editor=NwStyledPostsComposer::render([], $c,'/assets/');
check(str_contains($editor,'value="modern" selected'),'Only-enabled font selected visibly');
preg_match('/<script type="application\/json" class="nwsp-config">(.*?)<\/script>/s',$editor,$config);
check(json_decode($config[1],true)['values']['font_name']==='modern','JSON state matches enabled selector');
$invalid=NwStyledPostsCard::render(['text'=>['bad']],$c,'/assets/');
check(!str_contains($invalid,'Array'),'Invalid text input does not raise rendering warnings');
echo $GLOBALS['checks']." render assertions passed\n";
