<?php
define('BX_DOL',1);
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsFontPacks.php';
$d=sys_get_temp_dir().'/nwsp-fonts-'.bin2hex(random_bytes(4));mkdir($d);
file_put_contents($d.'/sample.woff2','wOF2'.str_repeat("\0",100));file_put_contents($d.'/LICENSE.txt','Test fixture license');
$items=[['id'=>'sample','label'=>'Sample Arabic','file'=>'sample.woff2','license'=>'LICENSE.txt'],['id'=>'escape','label'=>'Bad','file'=>'../sample.woff2','license'=>'LICENSE.txt'],['id'=>'bad;css','label'=>'Bad','file'=>'sample.woff2','license'=>'LICENSE.txt']];
file_put_contents($d.'/fonts.json',json_encode($items));
$r=NwStyledPostsFontPacks::load($d);
if(count($r['fonts'])!==1 || count($r['errors'])!==2)throw new RuntimeException('Reject unsafe fonts');
file_put_contents($d.'/sample.woff2','not a font');
if(NwStyledPostsFontPacks::load($d)['fonts'])throw new RuntimeException('Reject fake font');
foreach(glob($d.'/*') as $f)unlink($f);rmdir($d);echo "Font pack path, ID, license and signature checks passed\n";
