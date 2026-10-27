from pathlib import Path
import numpy as np, subprocess
root=Path('modules/newton/my_una_styled_posts/template/motion');root.mkdir(exist_ok=True)
w,h,fps=640,480,20
x,y=np.meshgrid(np.linspace(0,1,w),np.linspace(0,1,h))
for name in ['tides','glow']:
 p=subprocess.Popen(['ffmpeg','-v','error','-f','rawvideo','-pix_fmt','rgb24','-s',f'{w}x{h}','-r',str(fps),'-i','pipe:0','-an','-c:v','libx264','-crf','25','-pix_fmt','yuv420p','-movflags','+faststart','-y',str(root/(name+'.mp4'))],stdin=subprocess.PIPE)
 for frame in range(4*fps):
  t=frame/(4*fps)*2*np.pi
  if name=='tides':
   a=(np.sin(2*np.pi*(x*.8+y*.5)+t)*.5+.5);b=(np.cos(2*np.pi*(y*.8-x*.3)-t)*.5+.5)
   rgb=np.stack([20+25*a,70+55*a+20*b,90+55*b],axis=-1)
  else:
   a=np.exp(-((x-(.5+.2*np.cos(t)))**2+(y-(.5+.2*np.sin(t)))**2)*5)
   b=(np.cos(x*3+y*2+t)*.5+.5)
   rgb=np.stack([75+90*a,45+40*a+10*b,95+45*b],axis=-1)
  p.stdin.write(np.clip(rgb,0,255).astype('uint8').tobytes())
 p.stdin.close();assert p.wait()==0
 subprocess.run(['ffmpeg','-v','error','-i',str(root/(name+'.mp4')),'-frames:v','1','-y',str(root/(name+'.jpg'))],check=True)
