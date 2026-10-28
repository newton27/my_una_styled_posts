<?php
define('BX_DOL',1);
$disabled='';
function getParam($name) {global $disabled;return $name===$disabled?'':'on';}
require __DIR__.'/../modules/newton/my_una_styled_posts/classes/NwStyledPostsComposer.php';
function check($ok,$message) {if(!$ok)throw new RuntimeException($message);}
$catalog=NwStyledPostsStyle::catalog();
$motion=array_filter($catalog['backgrounds'],fn($b)=>($b['kind']??'')==='motion');
check(count($motion)===14,'Expected fourteen motion choices');
$html=NwStyledPostsComposer::render(['text'=>'Hello'],$catalog,'/assets/','',['motionUploads'=>true]);
check(substr_count($html,'class="nwsp-motion-choice"')===14,'All motion choices must have labeled thumbnail buttons');
foreach($motion as $id=>$b) {
    check(is_file(__DIR__.'/../modules/newton/my_una_styled_posts/template/motion/'.$b['clip']),'Missing clip');
    check(strpos($html,'<span class="nwsp-motion-label">'.$b['label'].'</span>')!==false,'Missing visible label');
    check(NwStyledPostsStyle::normalize(['background'=>$id],$catalog)['background']===$id,'Selection must save');
}
check(strpos($html,'Motion backgrounds')!==false && strpos($html,'3–5 second loops')!==false,'Clear motion heading');
check(strpos($html,'data-upload-motion>Upload video or GIF</button>')!==false,'Visible upload button');
$disabled='nw_styled_posts_background_motion_clouds';$catalog=NwStyledPostsStyle::catalog();
$html=NwStyledPostsComposer::render(['text'=>'Hello'],$catalog,'/assets/');
check(strpos($html,'data-background="motion_clouds"')===false,'Disabled motion omitted');
try {NwStyledPostsStyle::normalize(['background'=>'motion_clouds'],$catalog);throw new RuntimeException('Disabled choice accepted');}catch(InvalidArgumentException $e){}
echo "Motion library: fourteen choices, labels, save validation, disabled choice and upload button PASS\n";
