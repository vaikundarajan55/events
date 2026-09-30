(function () {
    'use strict';

    // ----- Scroll animations -----
    if (window.AOS) {
        AOS.init({ duration: 650, easing: 'ease-out-cubic', once: true, offset: 40,
                   disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches });
    }

    // ----- Navbar shadow on scroll -----
    const nav = document.getElementById('siteNav');
    const onScroll = () => nav && nav.classList.toggle('scrolled', window.scrollY > 8);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // ----- Auto-hide flash message -----
    const flash = document.querySelector('.flash-wrap .alert');
    if (flash) setTimeout(() => bootstrap.Alert.getOrCreateInstance(flash).close(), 7000);

    // ----- Gallery lightbox -----
    const items = [...document.querySelectorAll('.g-item')];
    const lbEl  = document.getElementById('lightbox');
    if (items.length && lbEl) {
        const lb = new bootstrap.Modal(lbEl);
        const img = document.getElementById('lbImg');
        const cap = document.getElementById('lbCaption');
        let idx = 0;
        const show = (i) => {
            idx = (i + items.length) % items.length;
            img.style.animation = 'none'; void img.offsetWidth; img.style.animation = '';
            img.src = items[idx].getAttribute('href');
            img.alt = items[idx].dataset.title || '';
            cap.textContent = items[idx].dataset.title || '';
        };
        items.forEach((a, i) => a.addEventListener('click', (e) => { e.preventDefault(); show(i); lb.show(); }));
        lbEl.querySelector('.lb-prev').addEventListener('click', () => show(idx - 1));
        lbEl.querySelector('.lb-next').addEventListener('click', () => show(idx + 1));
        document.addEventListener('keydown', (e) => {
            if (!lbEl.classList.contains('show')) return;
            if (e.key === 'ArrowLeft') show(idx - 1);
            if (e.key === 'ArrowRight') show(idx + 1);
        });
        let sx = 0;
        lbEl.addEventListener('touchstart', (e) => { sx = e.touches[0].clientX; }, { passive: true });
        lbEl.addEventListener('touchend', (e) => {
            const dx = e.changedTouches[0].clientX - sx;
            if (Math.abs(dx) > 50) show(dx > 0 ? idx - 1 : idx + 1);
        });
    }

    // ----- Apply modal -----
    const modalEl = document.getElementById('applyModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        const form  = document.getElementById('applyForm');
        const setJob = (id, title) => {
            document.getElementById('careerId').value = id;
            document.getElementById('applyJob').textContent = title;
        };
        document.querySelectorAll('.btn-apply').forEach((b) =>
            b.addEventListener('click', () => { setJob(b.dataset.jobId, b.dataset.jobTitle); modal.show(); }));

        // Re-open after a server-side validation error
        const reopen = parseInt(modalEl.dataset.openJob, 10);
        if (reopen) {
            const btn = document.querySelector('.btn-apply[data-job-id="' + reopen + '"]');
            if (btn) { setJob(btn.dataset.jobId, btn.dataset.jobTitle); modal.show(); }
        }

        // Client-side checks (server validates again)
        const resume = document.getElementById('f_resume');
        const feedback = document.getElementById('resumeFeedback');
        const checkResume = () => {
            const f = resume.files[0];
            let msg = '';
            if (!f) msg = 'Attach your resume.';
            else if (!/\.(pdf|docx?)$/i.test(f.name)) msg = 'Resume must be a PDF, DOC or DOCX file.';
            else if (f.size > 2 * 1024 * 1024) msg = 'Resume is too large (max 2 MB).';
            resume.setCustomValidity(msg);
            feedback.textContent = msg || 'Attach your resume.';
        };
        resume.addEventListener('change', checkResume);

        form.addEventListener('submit', (e) => {
            checkResume();
            if (!form.checkValidity()) {
                e.preventDefault();
                form.classList.add('was-validated');
                return;
            }
            const btn = form.querySelector('button[type=submit]');
            btn.classList.add('btn-loading');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';
        });
        modalEl.addEventListener('hidden.bs.modal', () => form.classList.remove('was-validated'));
    }
})();
