(function () {
    'use strict';
    const mounted = new WeakSet();
    const motionPlayers = new WeakMap();
    function stopMotion(video) {
        const cleanup = motionPlayers.get(video);
        if (cleanup) cleanup();
        motionPlayers.delete(video);
    }
    function mountMotion(video) {
        if (motionPlayers.has(video)) return;
        const card = video.closest('.nwsp-card');
        if (!card) return;
        let descriptor;
        try { descriptor = JSON.parse(video.dataset.motion); } catch (_) { return; }
        let button = card.querySelector('[data-motion-toggle]');
        if (!button) { button=document.createElement('button');button.type='button';button.className='nwsp-motion-toggle';button.dataset.motionToggle='';card.append(button); }
        let status = card.querySelector('.nwsp-motion-status');
        if (!status) {status=document.createElement('span');status.className='nwsp-motion-status';status.setAttribute('role','status');card.append(status);}
        const preference = matchMedia('(prefers-reduced-motion: reduce)');
        let visible=false, paused=false, allowReduced=false, disposed=false, polls=0, timer=0, request=null;
        video.muted=true;video.loop=true;video.playsInline=true;
        function label() {
            button.textContent=video.paused?'Play motion':'Pause motion';
            button.setAttribute('aria-label',video.paused?'Play background animation':'Pause background animation');
        }
        function playback() {
            if (disposed) return;
            const play=visible && !document.hidden && !paused && (!preference.matches || allowReduced) && descriptor.state==='ready' && descriptor.clip;
            if (play) {
                if (video.getAttribute('src')!==descriptor.clip) video.src=descriptor.clip;
                video.play().then(label).catch(()=>{if (!disposed) {paused=true;label();}});
            } else {video.pause();label();}
        }
        function display() {
            if (descriptor.poster) video.poster=descriptor.poster;
            button.hidden=descriptor.state!=='ready';
            status.textContent=descriptor.state==='pending'?'Processing background…':descriptor.state==='failed'?'Background processing failed. Edit this post to replace the file.':descriptor.state==='preview-gif'?'GIF preview available after saving and processing.':'';
            playback();
        }
        async function poll() {
            if (disposed || !video.isConnected || descriptor.state!=='pending' || !descriptor.status) return;
            if (++polls>24) {status.textContent='Background is still processing. Reopen the post later.';return;}
            request=new AbortController();
            try {
                const endpoint=new URL(descriptor.status,document.baseURI);
                if (endpoint.origin!==location.origin) throw new Error('Invalid media endpoint');
                const response=await fetch(endpoint,{credentials:'same-origin',cache:'no-store',signal:request.signal,headers:{Accept:'application/json'}});
                if (!response.ok) throw new Error('Unavailable');
                const next=await response.json();
                if (disposed) return;
                descriptor=next;display();
                if (descriptor.state==='pending') timer=setTimeout(poll,5000);
            } catch (_) {if (!disposed) status.textContent='Background status unavailable. Reopen the post to try again.';}
        }
        const toggle=e=>{e.preventDefault();e.stopPropagation();if (video.paused) {paused=false;allowReduced=true;} else paused=true;playback();};
        const changed=()=>{allowReduced=false;playback();};
        const visibility=()=>playback();
        const observer=new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;playback();},{threshold:0.1});
        observer.observe(video);button.addEventListener('click',toggle);preference.addEventListener('change',changed);document.addEventListener('visibilitychange',visibility);
        video.addEventListener('play',label);video.addEventListener('pause',label);
        motionPlayers.set(video,()=>{disposed=true;clearTimeout(timer);request?.abort();observer.disconnect();button.removeEventListener('click',toggle);preference.removeEventListener('change',changed);document.removeEventListener('visibilitychange',visibility);video.removeEventListener('play',label);video.removeEventListener('pause',label);video.pause();video.removeAttribute('src');video.load();});
        display();if (descriptor.state==='pending') timer=setTimeout(poll,5000);
    }
    function fitCard(card) {
        const text = card.querySelector('.nwsp-text');
        if (!text || !card.isConnected) return false;
        card.classList.remove('nwsp-overflow');
        const base = Math.max(18, Math.min(96, Number(card.dataset.fontSize) || 48));
        card.style.setProperty('--nwsp-fit', base + 'px');
        const cs = getComputedStyle(card);
        const room = card.clientHeight - parseFloat(cs.paddingTop) - parseFloat(cs.paddingBottom);
        let low = 18, high = base, best = 18;
        while (low <= high) {
            const mid = Math.floor((low + high) / 2);
            card.style.setProperty('--nwsp-fit', mid + 'px');
            if (text.scrollHeight <= room + 1) { best = mid; low = mid + 1; } else high = mid - 1;
        }
        card.style.setProperty('--nwsp-fit', best + 'px');
        const overflow = text.scrollHeight > room + 1;
        // All text remains visible even when 500 large glyphs cannot fit a fixed card.
        card.classList.toggle('nwsp-overflow', overflow);
        return overflow;
    }
    function mount(root) {
        if (mounted.has(root)) return;
        const form = root.closest('form');
        if (!form) return;
        const source = form.querySelector('[name="text"]');
        if (!source) return;
        mounted.add(root);
        const config = JSON.parse(root.querySelector('.nwsp-config').textContent);
        const state = { ...config.values };
        const card = root.querySelector('.nwsp-card');
        const quote = card.querySelector('.nwsp-text');
        const fileInput = form.querySelector('[name="background_upload"]');
        const remove = form.querySelector('[name="remove_background"]');
        let objectUrl = '';
        let ownMotion = config.photos?.motion?.state ? config.photos.motion : null;
        const originalMotion = ownMotion;
        const selectedPhoto = root.querySelector('[name=background_photo]');
        let ownImage = selectedPhoto && Number(selectedPhoto.value)>0 && config.photos.imageBase ? config.photos.imageBase + selectedPhoto.value : config.backgroundUrl;
        const syncField = (key, value) => {
            const field = form.querySelector('[name="' + key + '"]');
            if (field) field.value = String(value);
        };
        function update() {
            if (config.available === false) {
                const message = 'An administrator needs to enable a font and a background before you can post.';
                source.setCustomValidity(message);root.querySelector('[data-feedback]').textContent=message;return;
            }
            source.maxLength = 1000; // UTF-16 pairs: server validates the exact 500-code-point limit.
            quote.textContent = source.value;
            const bg = config.catalog.backgrounds[state.background];
            card.style.backgroundColor = state.background_color;
            card.style.backgroundImage = bg.css;
            card.style.color = state.text_color;
            card.style.fontFamily = config.catalog.fonts[state.font_name].family;
            card.style.textAlign = state.text_align;
            card.dataset.fontSize = state.font_size;
            for (const [key, value] of Object.entries(state)) syncField(key, value);
            root.querySelector('[data-size-label]').textContent = state.font_size;
            root.querySelector('[data-count]').textContent = Array.from(source.value).length + ' / 500';
            const picture = ownImage || (bg.file ? config.assetBase + 'images/backgrounds/' + bg.file : '');
            let img = card.querySelector('img.nwsp-background');
            let shade = card.querySelector('.nwsp-shade');
            const motion = ownMotion || (!ownImage && bg.kind==='motion' ? {state:'ready',clip:config.assetBase+'motion/'+bg.clip,poster:config.assetBase+'motion/'+bg.poster} : null);
            let video = card.querySelector('video.nwsp-motion');
            if (motion) {
                if (img) img.remove();
                const json=JSON.stringify(motion);
                if (!video) {video=document.createElement('video');video.className='nwsp-background nwsp-motion';video.setAttribute('aria-hidden','true');video.tabIndex=-1;video.preload='none';card.prepend(video);}
                if (video.dataset.motion!==json) {stopMotion(video);video.dataset.motion=json;}
                if (!shade) {shade=document.createElement('span');shade.className='nwsp-shade';shade.setAttribute('aria-hidden','true');card.prepend(shade);}
                mountMotion(video);
            } else {
                if (video) {stopMotion(video);video.remove();}
                card.querySelector('[data-motion-toggle]')?.remove();card.querySelector('.nwsp-motion-status')?.remove();
            }
            if (!motion && picture) {
                if (!img) { img = document.createElement('img'); img.className = 'nwsp-background'; img.alt = ''; card.prepend(img); }
                if (img.getAttribute('src') !== picture) img.src = picture;
                if (!shade) { shade = document.createElement('span'); shade.className = 'nwsp-shade'; shade.setAttribute('aria-hidden', 'true'); card.prepend(shade); }
            } else if (!motion) { if (img) img.remove(); if (shade) shade.remove(); }
            root.querySelectorAll('[data-background]').forEach(el => el.setAttribute('aria-pressed', el.dataset.background === state.background ? 'true' : 'false'));
            root.querySelectorAll('[data-align]').forEach(el => el.setAttribute('aria-pressed', el.dataset.align === state.text_align ? 'true' : 'false'));
            const overflow = fitCard(card);
            const count = Array.from(source.value).length;
            source.setCustomValidity(count > 500 ? 'Please shorten your post to 500 characters.' : '');
            let message = overflow ? 'This text needs a taller card. Shorten it for a compact post.' : '';
            if (count > 500) message = 'Please shorten your post to 500 characters.';
            else if (ownImage) message += (message ? ' ' : '') + 'Check that your text is readable over your photo.';
            else if (state.background === 'solid' && contrast(state.text_color, state.background_color) < 3) message += (message ? ' ' : '') + 'These colors have low contrast. Try a lighter or darker text color.';
            if (fileInput && !fileInput.validity.valid) message=fileInput.validationMessage;
            root.querySelector('[data-feedback]').textContent = message;
        }
        root.addEventListener('input', e => { const key = e.target.dataset.control; if (key) { state[key] = e.target.value; update(); } });
        root.addEventListener('change', e => { const key = e.target.dataset.control; if (key) { state[key] = e.target.value; update(); } });
        root.addEventListener('click', e => {
            const bg = e.target.closest('[data-background]');
            const align = e.target.closest('[data-align]');
            if (bg) {
                state.background = bg.dataset.background; ownImage = ''; ownMotion=null;
                if (selectedPhoto) selectedPhoto.value = '';
                if (fileInput) fileInput.setCustomValidity('');
                if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = ''; }
                if (fileInput) fileInput.value = '';
                if (remove) remove.checked = true;
                update();
            }
            if (align) { state.text_align = align.dataset.align; update(); }
        });
        source.addEventListener('input', update);
        if (remove) remove.addEventListener('change', () => { if (selectedPhoto) selectedPhoto.value=''; ownImage = remove.checked ? '' : (objectUrl || config.backgroundUrl);ownMotion=remove.checked?null:originalMotion; update(); });
        if (fileInput) fileInput.addEventListener('change', () => {
            if (objectUrl) URL.revokeObjectURL(objectUrl);
            objectUrl = '';
            const file = fileInput.files[0];
            if (selectedPhoto) selectedPhoto.value = '';
            ownImage = config.backgroundUrl;
            ownMotion=originalMotion;
            fileInput.setCustomValidity('');
            if (file) {
                const motion = ['image/gif','video/mp4','video/webm','video/quicktime'].includes(file.type);
                if ((!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) && !(config.photos?.motionUploads && motion)) || file.size > 8388608) {
                    fileInput.setCustomValidity('Choose a supported image or 3–5 second motion file up to 8 MiB.');
                    root.querySelector('[data-feedback]').textContent = fileInput.validationMessage;
                    return;
                }
                objectUrl = URL.createObjectURL(file); ownImage = motion ? '' : objectUrl;ownMotion=motion?{state:file.type==='image/gif'?'preview-gif':'ready',clip:file.type==='image/gif'?'':objectUrl,poster:''}:null;
                if (motion && file.type!=='image/gif') {
                    const probe=document.createElement('video');probe.preload='metadata';probe.muted=true;
                    const validate=()=>{if(objectUrl!==probe.src)return;if (!Number.isFinite(probe.duration) || probe.duration<3 || probe.duration>5 || probe.videoWidth>1280 || probe.videoHeight>1280) {fileInput.setCustomValidity('Choose a 3–5 second video, no larger than 1280 pixels per side.');root.querySelector('[data-feedback]').textContent=fileInput.validationMessage;ownMotion=originalMotion;ownImage=config.backgroundUrl;update();}};
                    probe.addEventListener('loadedmetadata',validate,{once:true});probe.src=objectUrl;
                }
                if (remove) remove.checked = false;
            }
            update();
        });
        const choose = root.querySelector('[data-choose-photos]');
        const upload = root.querySelector('[data-upload-photo]');
        const uploadMotion = root.querySelector('[data-upload-motion]');
        if (uploadMotion) uploadMotion.addEventListener('click',()=>{if(fileInput){fileInput.accept='image/gif,video/mp4,video/webm,video/quicktime';fileInput.click();}});
        if (upload) {
            upload.hidden = !fileInput;
            upload.addEventListener('click', () => {fileInput.accept='image/jpeg,image/png,image/webp';fileInput.click();});
        }
        if (choose) {
            const dialog = root.querySelector('.nwsp-photo-dialog');
            const grid = dialog.querySelector('.nwsp-photo-grid');
            const status = dialog.querySelector('[data-photo-status]');
            const more = dialog.querySelector('[data-more-photos]');
            let cursor = 0, busy = false, generation = 0;
            const close = () => dialog.close();
            dialog.querySelector('[data-close-photos]').addEventListener('click', close);
            dialog.addEventListener('close', () => { generation++; busy=false; choose.focus(); });
            async function load(before = 0) {
                if (busy) return;
                busy = true; more.disabled = true;
                const request = ++generation;
                status.textContent = 'Loading your photos…';
                if (!before) grid.replaceChildren();
                more.hidden = true;
                try {
                    if (!config.photos.endpoint) { status.textContent='Your photos are not available in this preview.'; return; }
                    const endpoint = new URL(config.photos.endpoint, document.baseURI);
                    if (before) endpoint.searchParams.set('before', String(before));
                    const response = await fetch(endpoint.href, {credentials:'same-origin',cache:'no-store',headers:{Accept:'application/json'}});
                    if (!response.ok) throw new Error('Photo list unavailable');
                    const result = await response.json();
                    if (request !== generation || !dialog.open) return;
                    if (!result.available) { status.textContent='Your photos are not available. Upload a photo instead, or ask the site administrator to enable Photos.'; return; }
                    for (const item of result.items) {
                        const button = document.createElement('button'); button.type='button'; button.disabled=true; button.dataset.photoId=item.id;
                        const img = document.createElement('img'); img.alt=''; img.loading='lazy';
                        const caption = document.createElement('span'); caption.textContent=item.title || 'Untitled photo';
                        button.append(img,caption); button.setAttribute('aria-label',item.title || 'Untitled photo');
                        img.addEventListener('load',()=>{button.disabled=img.naturalWidth<1;});
                        img.addEventListener('error',()=>{button.disabled=true;caption.textContent='Photo unavailable';});
                        button.addEventListener('click',()=>{
                            selectedPhoto.value=String(item.id);
                            if (fileInput) { fileInput.value=''; fileInput.setCustomValidity(''); }
                            if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl=''; }
                            if (remove) remove.checked=false;
                            ownImage=item.url; ownMotion=null; update(); close();
                        });
                        img.src=item.url;
                        grid.append(button);
                    }
                    cursor=Number(result.next)||0; more.hidden=!cursor;
                    status.textContent=grid.children.length ? 'Choose a photo for your background.' : 'No photos to choose yet. Upload a photo instead.';
                } catch (error) {
                    if (request===generation && dialog.open) { status.textContent='Your photos could not be loaded. Close this picker and try again, or upload a photo.'; more.hidden=true; }
                } finally { if (request===generation) {busy=false;more.disabled=false;} }
            }
            choose.addEventListener('click',()=>{if (!dialog.open) {dialog.showModal();load();}});
            more.addEventListener('click',()=>load(cursor));
        }
        const observer = new ResizeObserver(update); observer.observe(card);
        update();
        if (document.fonts) document.fonts.ready.then(update);
    }
    function luminance(hex) {
        const c = hex.slice(1).match(/.{2}/g).map(v => parseInt(v, 16) / 255).map(v => v <= 0.04045 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4));
        return c[0] * 0.2126 + c[1] * 0.7152 + c[2] * 0.0722;
    }
    function contrast(a, b) { const x = luminance(a), y = luminance(b); return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05); }
    function scan() {
        document.querySelectorAll('.nwsp-composer').forEach(mount);
        document.querySelectorAll('.nwsp-card').forEach(card => {
            if (card.closest('.nwsp-composer') || mounted.has(card)) return;
            mounted.add(card);
            new ResizeObserver(() => fitCard(card)).observe(card);
            fitCard(card);
            if (document.fonts) document.fonts.ready.then(() => fitCard(card));
        });
        document.querySelectorAll('video.nwsp-motion').forEach(mountMotion);
    }
    window.NwStyledPosts = { scan, fitCard, contrast };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan); else scan();
    let queued = false;
    new MutationObserver(records => {
        records.forEach(r=>r.removedNodes.forEach(n=>{if(n.nodeType!==1)return;if(n.matches('video.nwsp-motion') && !n.isConnected)stopMotion(n);n.querySelectorAll('video.nwsp-motion').forEach(v=>{if(!v.isConnected)stopMotion(v);});}));
        if (queued || !records.some(r => [...r.addedNodes].some(n => n.nodeType === 1 && (n.matches('.nwsp-composer,.nwsp-card') || n.querySelector('.nwsp-composer,.nwsp-card'))))) return;
        queued = true; requestAnimationFrame(() => { queued = false; scan(); });
    }).observe(document.documentElement, { childList: true, subtree: true });
}());
