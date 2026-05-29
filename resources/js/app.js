import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {

    // ── Global Lightbox Store ─────────────────────────────────────────────
    // Usage: $store.lb.show([{url, title, caption}, ...], startIndex)
    // Any page can trigger it — portfolio, papers, books, gallery, anywhere.
    Alpine.store('lb', {
        open:   false,
        images: [],   // [{ url, title, caption }]
        idx:    0,

        show(images, idx = 0) {
            this.images = Array.isArray(images) ? images : [{ url: images, title: '', caption: '' }];
            this.idx    = idx;
            this.open   = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.open = false;
            document.body.style.overflow = '';
        },

        prev() {
            this.idx = (this.idx - 1 + this.images.length) % this.images.length;
        },

        next() {
            this.idx = (this.idx + 1) % this.images.length;
        },

        get img() {
            return this.images[this.idx] || { url: '', title: '', caption: '' };
        }
    });

});

Alpine.start();
