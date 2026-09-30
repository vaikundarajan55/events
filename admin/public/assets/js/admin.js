/* Admin panel behaviour */
(function () {
    'use strict';

    // ----- Mobile sidebar -----
    const sidebar  = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle   = document.getElementById('sidebarToggle');
    const setSidebar = (open) => {
        sidebar.classList.toggle('open', open);
        backdrop.classList.toggle('show', open);
    };
    toggle && toggle.addEventListener('click', () => setSidebar(!sidebar.classList.contains('open')));
    backdrop && backdrop.addEventListener('click', () => setSidebar(false));

    // ----- Staggered reveal for anything with .reveal -----
    document.querySelectorAll('.reveal').forEach((el, i) => {
        if (!el.style.getPropertyValue('--i')) el.style.setProperty('--i', Math.min(i, 14));
    });

    // ----- Count-up numbers -----
    document.querySelectorAll('[data-count]').forEach((el) => {
        const target = parseInt(el.dataset.count, 10) || 0;
        const start  = performance.now();
        const dur    = 900;
        const step   = (now) => {
            const p = Math.min((now - start) / dur, 1);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString();
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });

    // ----- Delete confirmation modal -----
    const modalEl = document.getElementById('deleteModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-delete-url]');
            if (!btn) return;
            e.preventDefault();
            document.getElementById('deleteForm').action = btn.dataset.deleteUrl;
            document.getElementById('deleteName').textContent = btn.dataset.deleteName || 'this item';
            modal.show();
        });
    }

    // ----- Password show / hide -----
    document.querySelectorAll('.toggle-pass').forEach((b) => {
        b.addEventListener('click', () => {
            const input = document.querySelector(b.dataset.target);
            const show  = input.type === 'password';
            input.type  = show ? 'text' : 'password';
            b.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // ----- Loading state on submit -----
    document.querySelectorAll('form[data-loading]').forEach((f) => {
        f.addEventListener('submit', () => {
            const btn = f.querySelector('button[type=submit]');
            if (btn) {
                btn.classList.add('btn-loading');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Please wait…';
            }
        });
    });

    // ----- Image dropzone + preview -----
    const dz = document.getElementById('dropzone');
    if (dz) {
        const input   = dz.querySelector('input[type=file]');
        const preview = document.getElementById('previewGrid');
        const render  = () => {
            preview.innerHTML = '';
            [...input.files].forEach((f) => {
                if (!f.type.startsWith('image/')) return;
                const img = document.createElement('img');
                img.src = URL.createObjectURL(f);
                img.alt = f.name;
                preview.appendChild(img);
            });
            const label = document.getElementById('dzCount');
            if (label) label.textContent = input.files.length ? input.files.length + ' file(s) selected' : 'Drag & drop or click to browse';
        };
        dz.addEventListener('click', () => input.click());
        input.addEventListener('click', (e) => e.stopPropagation());
        input.addEventListener('change', render);
        ['dragenter', 'dragover'].forEach((ev) => dz.addEventListener(ev, (e) => { e.preventDefault(); dz.classList.add('drag'); }));
        ['dragleave', 'drop'].forEach((ev) => dz.addEventListener(ev, (e) => { e.preventDefault(); dz.classList.remove('drag'); }));
        dz.addEventListener('drop', (e) => { input.files = e.dataTransfer.files; render(); });
    }
})();
