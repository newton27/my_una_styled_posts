<?php
define('BX_DOL',true);
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsMediaDelivery.php';
$n=0;function ok($v){global $n;$n++;if(!$v)throw new RuntimeException('Range assertion '.$n);}
foreach ([''=>[0,99,false],'bytes=0-9'=>[0,9,true],'bytes=90-'=>[90,99,true],'bytes=-20'=>[80,99,true],'bytes=0-999'=>[0,99,true]] as $v=>$expected) ok(NwStyledPostsMediaDelivery::range($v,100)===$expected);
foreach (['bytes=100-','bytes=20-10','bytes=-0','bytes=0-1,3-4','bytes=-','bytes=abc-5'] as $v) {try{NwStyledPostsMediaDelivery::range($v,100);throw new RuntimeException('Bad range accepted');}catch(InvalidArgumentException $e){ok(true);}}
echo "$n motion delivery assertions passed\n";
