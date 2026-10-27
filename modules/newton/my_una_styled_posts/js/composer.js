(function () {
    'use strict';
    const mounted = new WeakSet();
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
            let img = card.querySelector('.nwsp-background');
            let shade = card.querySelector('.nwsp-shade');
            if (picture) {
                if (!img) { img = document.createElement('img'); img.className = 'nwsp-background'; img.alt = ''; card.prepend(img); }
                if (img.getAttribute('src') !== picture) img.src = picture;
                if (!shade) { shade = document.createElement('span'); shade.className = 'nwsp-shade'; shade.setAttribute('aria-hidden', 'true'); card.prepend(shade); }
            } else { if (img) img.remove(); if (shade) shade.remove(); }
            root.querySelectorAll('[data-background]').forEach(el => el.setAttribute('aria-pressed', el.dataset.background === state.background ? 'true' : 'false'));
            root.querySelectorAll('[data-align]').forEach(el => el.setAttribute('aria-pressed', el.dataset.align === state.text_align ? 'true' : 'false'));
            const overflow = fitCard(card);
            const count = Array.from(source.value).length;
            source.setCustomValidity(count > 500 ? 'Please shorten your post to 500 characters.' : '');
            let message = overflow ? 'This text needs a taller card. Shorten it for a compact post.' : '';
            if (count > 500) message = 'Please shorten your post to 500 characters.';
            else if (ownImage) message += (message ? ' ' : '') + 'Check that your text is readable over your photo.';
            else if (state.background === 'solid' && contrast(state.text_color, state.background_color) < 3) message += (message ? ' ' : '') + 'These colors have low contrast. Try a lighter or darker text color.';
            root.querySelector('[data-feedback]').textContent = message;
        }
        root.addEventListener('input', e => { const key = e.target.dataset.control; if (key) { state[key] = e.target.value; update(); } });
        root.addEventListener('change', e => { const key = e.target.dataset.control; if (key) { state[key] = e.target.value; update(); } });
        root.addEventListener('click', e => {
            const bg = e.target.closest('[data-background]');
            const align = e.target.closest('[data-align]');
            if (bg) {
                state.background = bg.dataset.background; ownImage = '';
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
        if (remove) remove.addEventListener('change', () => { if (selectedPhoto) selectedPhoto.value=''; ownImage = remove.checked ? '' : (objectUrl || config.backgroundUrl); update(); });
        if (fileInput) fileInput.addEventListener('change', () => {
            if (objectUrl) URL.revokeObjectURL(objectUrl);
            objectUrl = '';
            const file = fileInput.files[0];
            if (selectedPhoto) selectedPhoto.value = '';
            ownImage = config.backgroundUrl;
            fileInput.setCustomValidity('');
            if (file) {
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 8388608) {
                    fileInput.setCustomValidity('Choose a JPG, PNG or WebP image up to 8 MiB.');
                    root.querySelector('[data-feedback]').textContent = fileInput.validationMessage;
                    return;
                }
                objectUrl = URL.createObjectURL(file); ownImage = objectUrl;
                if (remove) remove.checked = false;
            }
            update();
        });
        const choose = root.querySelector('[data-choose-photos]');
        const upload = root.querySelector('[data-upload-photo]');
        if (upload) {
            upload.hidden = !fileInput;
            upload.addEventListener('click', () => fileInput.click());
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
                            ownImage=item.url; update(); close();
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
    }
    window.NwStyledPosts = { scan, fitCard, contrast };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan); else scan();
    let queued = false;
    new MutationObserver(records => {
        if (queued || !records.some(r => [...r.addedNodes].some(n => n.nodeType === 1 && (n.matches('.nwsp-composer,.nwsp-card') || n.querySelector('.nwsp-composer,.nwsp-card'))))) return;
        queued = true; requestAnimationFrame(() => { queued = false; scan(); });
    }).observe(document.documentElement, { childList: true, subtree: true });
}());
