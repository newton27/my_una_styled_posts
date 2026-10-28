"""Original periodic illustrations, MIT licensed; never rewrites the two legacy loops."""
from pathlib import Path
import argparse
import subprocess
import numpy as np

NAMES = ['gradients','waves','clouds','rain','snow','particles','bokeh','northern','ripples','smoke','stars','ribbons']
W, H, FPS, FRAMES = 640, 480, 20, 80
X, Y = np.meshgrid(np.linspace(0, 1, W), np.linspace(0, 1, H))
TAU = 2*np.pi

def frame(name, phase):
    """phase is radians; each function returns exactly the same image at 0 and 2π."""
    t = phase
    palettes = {
        'gradients': ([40,36,87],[98,49,123]), 'waves': ([13,52,72],[23,73,88]),
        'clouds': ([43,69,104],[47,63,80]), 'rain': ([22,37,56],[20,27,35]),
        'snow': ([30,47,78],[28,33,46]), 'particles': ([35,31,75],[36,30,53]),
        'bokeh': ([64,37,63],[35,27,50]), 'northern': ([15,31,56],[17,31,50]),
        'ripples': ([12,62,69],[20,35,53]), 'smoke': ([47,36,70],[36,27,48]),
        'stars': ([15,24,52],[20,20,42]), 'ribbons': ([62,30,72],[38,30,60]),
    }
    base, shade = [np.array(c) for c in palettes[name]]
    rgb = base + Y[...,None]*shade*.35
    def add(mask, color):
        nonlocal rgb
        rgb = rgb + mask[...,None]*np.array(color)
    if name == 'gradients':
        for k,color in enumerate([[75,25,48],[12,65,73],[46,24,71]]):
            a=t+k*TAU/3
            add(np.exp(-((X-(.5+.3*np.cos(a)))**2+(Y-(.5+.3*np.sin(a)))**2)*7),color)
    elif name == 'waves':
        for k in range(5):
            center=.25+k*.17+.035*np.sin(X*9-t+k)
            add(np.exp(-((Y-center)/.035)**2),[12,24,26])
            add(np.exp(-((Y-center-.035)/.07)**2),[0,10,16])
    elif name == 'clouds':
        for k in range(7):
            a=t+k*2.3; cx=(k%3)*.38+.12+.10*np.cos(a); cy=.15+(k//3)*.3+.04*np.sin(a)
            add(np.exp(-((X-cx)**2/.06+(Y-cy)**2/.009)),[32,34,38])
    elif name in ['rain','snow','particles','bokeh','stars']:
        rng=np.random.default_rng(2731)
        count={'rain':65,'snow':48,'particles':25,'bokeh':15,'stars':65}[name]
        for k in range(count):
            cx,cy=rng.random(2); radius=rng.uniform(.002,.006)
            if name in ['rain','snow']:
                cx=(cx+.025*np.sin(t+k))%1; cy=(cy+t/TAU)%1
            elif name in ['particles','bokeh']:
                cx += .04*np.cos(t+k);cy += .055*np.sin(t+k)
            dx=(X-cx+.5)%1-.5;dy=(Y-cy+.5)%1-.5
            if name=='rain':
                mask=np.exp(-((dx+.13*dy)/.0018)**2-(dy/.025)**2)
                color=[35,49,57]
            else:
                if name=='bokeh':radius=rng.uniform(.035,.095)
                if name=='particles':radius*=1.6
                mask=np.exp(-(dx*dx+dy*dy)/(radius*radius))
                strength=.55+.45*np.cos(t+k) if name=='stars' else .85
                mask*=strength
                color={'snow':[104,111,123],'particles':[53,42,82],'bokeh':[47,30,37],'stars':[115,112,131]}[name]
            add(mask,color)
    elif name=='northern':
        a=.34+.10*np.sin(X*7+t)+.025*np.sin(X*24-t)
        curtain=np.exp(-((Y-a)/.12)**2)*(.6+.4*np.sin(X*32+t)**2)
        add(curtain,[10,73,48]);add(np.exp(-((Y-a-.12)/.09)**2),[34,12,50])
    elif name=='ripples':
        r=np.sqrt(((X-.48)*1.2)**2+((Y-.5)*.65)**2)
        a=(.5+.5*np.sin(r*75-t*2))**7
        add(a*np.exp(-r*2.5),[13,35,35])
    elif name=='smoke':
        a=np.sin(X*7+Y*5+t+.8*np.sin(Y*8-t))
        b=np.cos(Y*9-X*5-t+.7*np.sin(X*8+t))
        add((a+b+2)/4,[34,30,44]);add(np.exp(-((a-b)/.35)**2),[12,9,22])
    elif name=='ribbons':
        for k,color in enumerate([[57,24,51],[24,38,67],[50,21,44]]):
            a=.25+k*.22+.14*np.sin(X*4+t+k)
            add(np.exp(-((Y-a)/.075)**2),color)
    return np.clip(rgb,0,255).astype('uint8')

def main():
    parser=argparse.ArgumentParser();parser.add_argument('--name',choices=NAMES);args=parser.parse_args()
    root=Path(__file__).resolve().parents[1]/'modules/newton/my_una_styled_posts/template/motion'
    for name in ([args.name] if args.name else NAMES):
        assert np.array_equal(frame(name,0),frame(name,TAU)),name+' must be periodic'
        target=root/(name+'.mp4')
        p=subprocess.Popen(['ffmpeg','-v','error','-f','rawvideo','-pix_fmt','rgb24','-s',f'{W}x{H}','-r',str(FPS),'-i','pipe:0','-an','-c:v','libx264','-preset','medium','-crf','25','-pix_fmt','yuv420p','-movflags','+faststart','-y',str(target)],stdin=subprocess.PIPE)
        for f in range(FRAMES):p.stdin.write(frame(name,f/FRAMES*TAU).tobytes())
        p.stdin.close();assert p.wait()==0
        subprocess.run(['ffmpeg','-v','error','-i',str(target),'-frames:v','1','-y',str(root/(name+'.jpg'))],check=True)
        print(name,target.stat().st_size,flush=True)

if __name__=='__main__':main()
