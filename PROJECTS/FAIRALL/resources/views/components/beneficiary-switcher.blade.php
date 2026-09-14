@props([
    'beneficiary',
    'switchUrlPattern',
    'formId' => null,
])

@php
    $currentLabel = $beneficiary->display_name . ' — ' . ($beneficiary->user->login_id ?? '');
@endphp

<div class="mb-6 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-3 relative" style="background-color: rgba(255,204,51,0.1); border: 1px solid rgba(255,204,51,0.3);" id="beneficiary-switcher-root" data-current-id="{{ $beneficiary->id }}" data-pattern="{{ $switchUrlPattern }}" data-form-id="{{ $formId }}">
    <div class="flex items-center gap-2 shrink-0">
        <svg class="w-5 h-5 shrink-0" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="text-sm font-semibold whitespace-nowrap" style="color: var(--color-accent1); font-family: var(--font-body1);">Beneficiary:</span>
    </div>

    <div class="relative flex-none w-full sm:w-[256px] sm:max-w-[256px]">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none text-primary1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                id="beneficiary-switcher-input"
                type="text"
                autocomplete="off"
                placeholder="Search name, login ID or email to switch…"
                value="{{ $currentLabel }}"
                class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all text-primary1"
                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.18);"
            />
            <button type="button" id="beneficiary-switcher-clear" class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-md flex items-center justify-center transition-colors hidden" style="color: rgba(255,255,255,0.5);" title="Clear">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <ul id="beneficiary-switcher-results" class="hidden absolute z-50 w-full mt-2 rounded-xl overflow-hidden shadow-2xl overflow-y-auto overscroll-contain" style="background-color: var(--color-white, #ffffff); border: 1px solid rgba(0,0,0,0.08); max-height: min(320px, calc(100vh - 200px)); margin-bottom: 12px;"></ul>
    </div>

    <span class="hidden lg:inline text-xs whitespace-nowrap" style="color: rgba(255,255,255,0.45); font-family: var(--font-body1);">Type to switch — unsaved changes will prompt</span>
</div>

<dialog id="beneficiary-switcher-confirm" class="fixed inset-0 m-auto rounded-2xl overflow-hidden backdrop:backdrop-blur-sm p-0" style="background: transparent; border: none;">
    <div class="rounded-2xl p-6 max-w-md mx-auto text-center" style="background-color: var(--color-white); border: 1px solid rgba(0,0,0,0.08);">
        <h3 class="text-lg font-bold mb-2" style="font-family: var(--font-header1); color: var(--color-primary1);">Discard unsaved changes?</h3>
        <p class="text-sm leading-relaxed mb-6" style="color: var(--color-primary1); font-family: var(--font-body1);">You have unsaved changes. Switching beneficiaries will discard them.</p>
        <div class="flex justify-center gap-3">
            <button type="button" id="beneficiary-switcher-confirm-go" class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]" style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">Discard &amp; Switch</button>
            <button type="button" id="beneficiary-switcher-confirm-cancel" class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]" style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">Stay</button>
        </div>
    </div>
</dialog>

<script>
(() => {
    const root = document.getElementById('beneficiary-switcher-root');
    if (!root) return;
    const input = document.getElementById('beneficiary-switcher-input');
    const resultsEl = document.getElementById('beneficiary-switcher-results');
    const clearBtn = document.getElementById('beneficiary-switcher-clear');
    const confirmDialog = document.getElementById('beneficiary-switcher-confirm');
    const confirmGo = document.getElementById('beneficiary-switcher-confirm-go');
    const confirmCancel = document.getElementById('beneficiary-switcher-confirm-cancel');
    const currentId = parseInt(root.dataset.currentId, 10);
    const pattern = root.dataset.pattern;
    const formId = root.dataset.formId;
    const STORAGE_KEY = 'fairall_recent_beneficiaries';
    const currentLabel = @json($currentLabel);
    let pendingSwitchId = null;
    let activeIndex = -1;
    let lastResults = [];
    let debounceTimer = null;
    let abortController = null;
    let initialSnapshot = '';
    let initialSnapshotTaken = false;

    function serializeForm() {
        const form = formId ? document.getElementById(formId) : document.querySelector('form[method="POST"]');
        if (!form) return '';
        const fd = new FormData(form);
        // Exclude _token and file
        const entries = [];
        for (const [k, v] of fd.entries()) {
            if (k === '_token') continue;
            if (v instanceof File) continue;
            entries.push(k + '=' + v);
        }
        // Also include dynamic empty inputs that may not be in FormData due to being empty? FormData includes them if they have name
        return entries.sort().join('&');
    }

    function captureSnapshot() {
        // Delay to allow dynamic rows JS to inject first card
        setTimeout(() => {
            initialSnapshot = serializeForm();
            initialSnapshotTaken = true;
        }, 700);
    }

    function isDirty() {
        if (!initialSnapshotTaken) return false;
        return serializeForm() !== initialSnapshot;
    }

    // Recents via localStorage
    function getRecents() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch { return []; }
    }
    function saveRecent(id, label, loginId) {
        try {
            let recents = getRecents().filter(r => r.id !== id);
            recents.unshift({ id, label, login_id: loginId });
            recents = recents.slice(0, 5);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(recents));
        } catch {}
    }
    // Save current on load
    saveRecent(currentId, @json($beneficiary->display_name), @json($beneficiary->user->login_id ?? ''));

    function attemptSwitch(id) {
        pendingSwitchId = id;
        if (isDirty()) {
            if (typeof confirmDialog.showModal === 'function') confirmDialog.showModal();
            else if (!confirm('You have unsaved changes. Switch beneficiary and discard them?')) { pendingSwitchId = null; return; } else doSwitch();
        } else {
            doSwitch();
        }
    }
    function doSwitch() {
        if (!pendingSwitchId) return;
        const url = pattern.replace('__ID__', String(pendingSwitchId));
        window.location.href = url;
    }

    confirmGo?.addEventListener('click', () => { confirmDialog.close(); doSwitch(); });
    confirmCancel?.addEventListener('click', () => { confirmDialog.close(); pendingSwitchId = null; });
    confirmDialog?.addEventListener('click', (e) => {
        const rect = confirmDialog.getBoundingClientRect();
        if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
            confirmDialog.close(); pendingSwitchId = null;
        }
    });

    function highlightMatch(text, q) {
        if (!q) return text;
        const esc = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp('(' + esc + ')', 'ig'), '<mark style="background: rgba(255,204,51,0.35); color: inherit; padding:0 1px; border-radius:2px;">$1</mark>');
    }

    function renderList(items, q, header) {
        if (!items.length) {
            resultsEl.innerHTML = '<li class="px-4 py-4 text-sm text-center" style="color: var(--color-primary1); opacity:0.6;">No results for “' + escapeHtml(q) + '”</li>';
            resultsEl.classList.remove('hidden');
            return;
        }
        let html = '';
        if (header) html += '<li class="px-3 pt-3 pb-1 text-sm font-semibold uppercase tracking-widest" style="color: var(--color-primary1); opacity:0.5;">' + header + '</li>';
        items.forEach((item, idx) => {
            const isActive = idx === activeIndex;
            html += '<li data-id="' + item.id + '" data-index="' + idx + '" class="beneficiary-switcher-option px-3 py-2.5 flex items-center gap-3 cursor-pointer transition-colors" style="background-color:' + (isActive ? 'rgba(255,204,51,0.12)' : 'transparent') + ';">'
                + '<div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold" style="background-color: rgba(255,204,51,0.18); color: var(--color-accent1);">' + escapeHtml((item.label||'?').charAt(0).toUpperCase()) + '</div>'
                + '<div class="min-w-0 flex-1">'
                + '<p class="text-sm font-medium truncate text-primary1" style="color: var(--color-primary1); font-family: var(--font-body1);">' + highlightMatch(escapeHtml(item.label), q) + '</p>'
                + '<p class="text-xs truncate" style="color: var(--color-primary1); opacity:0.6;">' + escapeHtml(item.login_id || '') + (item.email ? ' · ' + escapeHtml(item.email) : '') + '</p>'
                + '</div>'
                + '<svg class="w-4 h-4 shrink-0 ' + (isActive ? '' : 'opacity-0') + '" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>'
                + '</li>';
        });
        resultsEl.innerHTML = html;
        resultsEl.classList.remove('hidden');
        resultsEl.querySelectorAll('.beneficiary-switcher-option').forEach(li => {
            li.addEventListener('click', () => attemptSwitch(parseInt(li.dataset.id, 10)));
            li.addEventListener('mouseenter', () => { activeIndex = parseInt(li.dataset.index, 10); updateActive(); });
        });
    }

    function escapeHtml(s) {
        const d = document.createElement('div'); d.textContent = s; return d.innerHTML;
    }
    function updateActive() {
        const opts = resultsEl.querySelectorAll('.beneficiary-switcher-option');
        opts.forEach((li, idx) => {
            li.style.backgroundColor = idx === activeIndex ? 'rgba(255,204,51,0.12)' : 'transparent';
            const chev = li.querySelector('svg'); if (chev) chev.style.opacity = idx === activeIndex ? '1' : '0';
        });
    }

    function renderRecents() {
        activeIndex = -1;
        const recents = getRecents().filter(r => r.id !== currentId);
        if (!recents.length) {
            resultsEl.innerHTML = '<li class="px-4 py-6 text-sm text-center" style="color: var(--color-primary1); opacity:0.6;">No recent beneficiaries<br><span style="color: var(--color-primary1); opacity:0.45; font-size:12px;">Search by name, login ID or email (min 2 characters)</span></li>';
            resultsEl.classList.remove('hidden');
            lastResults = [];
            return;
        }
        lastResults = recents;
        renderList(recents, '', 'Recent beneficiaries');
    }

    async function fetchSearch(q) {
        if (abortController) abortController.abort();
        abortController = new AbortController();
        try {
            const res = await fetch('/beneficiaries/search?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' }, signal: abortController.signal });
            if (!res.ok) throw new Error('search failed');
            const data = await res.json();
            const filtered = (Array.isArray(data) ? data : []).filter(r => r.id !== currentId);
            lastResults = filtered;
            activeIndex = -1;
            if (!filtered.length) renderList([], q, null);
            else renderList(filtered, q, 'Search results');
        } catch (e) {
            if (e.name === 'AbortError') return;
            resultsEl.innerHTML = '<li class="px-4 py-3 text-sm text-center" style="color: var(--color-danger);">Search failed. Try again.</li>';
            resultsEl.classList.remove('hidden');
        }
    }

    // Input handlers
    input.addEventListener('focus', () => {
        const v = input.value.trim();
        // If input still equals current label, select all for quick overwrite
        if (v === currentLabel) input.select();
        if (v.length >= 2 && v !== currentLabel) {
            fetchSearch(v);
        } else {
            renderRecents();
        }
        if (v !== '' && v !== currentLabel) clearBtn.classList.remove('hidden');
    });

    input.addEventListener('input', () => {
        const v = input.value.trim();
        activeIndex = -1;
        if (v === '') { clearBtn.classList.add('hidden'); renderRecents(); return; }
        if (v === currentLabel) { clearBtn.classList.add('hidden'); renderRecents(); return; }
        clearBtn.classList.remove('hidden');
        clearTimeout(debounceTimer);
        if (v.length < 2) {
            resultsEl.classList.add('hidden');
            return;
        }
        debounceTimer = setTimeout(() => fetchSearch(v), 250);
    });

    clearBtn.addEventListener('click', () => {
        input.value = '';
        clearBtn.classList.add('hidden');
        input.focus();
        renderRecents();
    });

    input.addEventListener('keydown', (e) => {
        const visible = !resultsEl.classList.contains('hidden');
        if (!visible) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); activeIndex = Math.min(activeIndex + 1, lastResults.length - 1); updateActive(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); activeIndex = Math.max(activeIndex - 1, 0); updateActive(); }
        else if (e.key === 'Enter') {
            if (activeIndex >= 0 && lastResults[activeIndex]) { e.preventDefault(); attemptSwitch(lastResults[activeIndex].id); }
        } else if (e.key === 'Escape') { resultsEl.classList.add('hidden'); activeIndex = -1; }
    });

    document.addEventListener('click', (e) => {
        if (!root.contains(e.target)) resultsEl.classList.add('hidden');
    });

    // Hide recents when input is not focused
    input.addEventListener('blur', () => {
        setTimeout(() => {
            const active = document.activeElement;
            const hovering = resultsEl.matches(':hover');
            if (active === input || hovering || resultsEl.contains(active)) return;
            resultsEl.classList.add('hidden');
            if (input.value.trim() === '') { input.value = currentLabel; clearBtn.classList.add('hidden'); }
        }, 180);
    });
    resultsEl.addEventListener('mouseleave', () => {
        setTimeout(() => { if (document.activeElement !== input) resultsEl.classList.add('hidden'); }, 120);
    });

    captureSnapshot();
})();
</script>
