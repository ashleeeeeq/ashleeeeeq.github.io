@props([
    'beneficiary',
    'formId',
])

@php
    $currentId = $beneficiary->id;
@endphp

<dialog id="save-switch-dialog" class="fixed inset-0 m-auto rounded-2xl backdrop:backdrop-blur-sm p-0" style="background: transparent; border: none; max-width: 560px; width: calc(100% - 32px); overflow: visible;">
    <div class="rounded-2xl p-6 flex flex-col" style="background-color: var(--color-white, #ffffff); border: 1px solid rgba(0,0,0,0.08); height: 40vh; min-height: 40vh; max-height: 40vh; overflow: visible;">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background-color: rgba(255,204,51,0.15);">
                <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-bold" style="font-family: var(--font-header1); color: var(--color-primary1);">Save & create for another beneficiary?</h3>
                <p class="text-sm mt-1" style="color: var(--color-primary1); opacity:0.65; font-family: var(--font-body1);">Your current work will be saved first, then you'll be taken to the create page for the selected beneficiary.</p>
            </div>
            <button type="button" id="save-switch-dialog-close" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-black/5 transition-colors" style="color: var(--color-primary1); opacity:0.5;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1 flex flex-col min-h-0">
            <div class="relative">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color: var(--color-primary1); opacity:0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input id="save-switch-input" type="text" autocomplete="off" placeholder="Search name, login ID or email…" class="w-full pl-10 pr-9 py-2.5 text-sm rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all" style="font-family: var(--font-body1); background-color: #ffffff; border-color: rgba(0,0,0,0.12); color: var(--color-primary1);" />
                    <button type="button" id="save-switch-clear" class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-md hidden items-center justify-center transition-colors" style="color: var(--color-primary1); opacity:0.5;" title="Clear">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <ul id="save-switch-results" class="hidden absolute z-50 w-full mt-2 rounded-xl overflow-hidden shadow-xl overflow-y-auto overscroll-contain" style="background-color: var(--color-white, #ffffff); border: 1px solid rgba(0,0,0,0.08); max-height: min(280px, calc(40vh - 160px)); margin-bottom: 16px;"></ul>
            </div>
            <div id="save-switch-selected" class="hidden mt-3 px-3 py-2.5 rounded-xl flex items-center gap-3" style="background-color: rgba(255,204,51,0.12); border: 1px solid rgba(255,204,51,0.3);">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold" style="background-color: var(--color-accent1); color: var(--color-primary1);" id="save-switch-selected-avatar">?</div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold truncate" style="color: var(--color-primary1); font-family: var(--font-body1);" id="save-switch-selected-label"></p>
                    <p class="text-xs truncate" style="color: var(--color-primary1); opacity:0.6;" id="save-switch-selected-sub"></p>
                </div>
                <button type="button" id="save-switch-selected-remove" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-black/10 transition-colors" style="color: var(--color-primary1); opacity:0.6;" title="Remove selection">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-auto pt-4 border-t" style="border-color: rgba(0,0,0,0.08);">
            <button type="button" id="save-switch-cancel" class="px-5 py-2.5 text-sm font-semibold rounded-xl transition-all hover:scale-[1.02]" style="font-family: var(--font-body1); background-color: transparent; color: var(--color-primary1); border: 1px solid rgba(0,0,0,0.15);">Cancel</button>
            <button type="button" id="save-switch-confirm" disabled class="px-6 py-2.5 text-sm font-semibold rounded-xl transition-all hover:scale-[1.02] disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100" style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">Save & Switch</button>
        </div>
    </div>
</dialog>

<script>
(() => {
    const formId = @json($formId);
    const currentId = @json($currentId);
    const form = document.getElementById(formId);
    const openBtn = document.getElementById('save-switch-btn');
    const dialog = document.getElementById('save-switch-dialog');
    const closeBtn = document.getElementById('save-switch-dialog-close');
    const cancelBtn = document.getElementById('save-switch-cancel');
    const confirmBtn = document.getElementById('save-switch-confirm');
    const input = document.getElementById('save-switch-input');
    const clearBtn = document.getElementById('save-switch-clear');
    const resultsEl = document.getElementById('save-switch-results');
    const selectedWrap = document.getElementById('save-switch-selected');
    const selectedLabel = document.getElementById('save-switch-selected-label');
    const selectedSub = document.getElementById('save-switch-selected-sub');
    const selectedAvatar = document.getElementById('save-switch-selected-avatar');
    const selectedRemove = document.getElementById('save-switch-selected-remove');

    if (!form || !openBtn || !dialog) return;

    const STORAGE_KEY = 'fairall_recent_beneficiaries';
    let pending = null; // {id,label,login_id,email}
    let lastResults = [];
    let activeIndex = -1;
    let debounceTimer = null;
    let abortController = null;

    function getRecents() { try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch { return []; } }
    function escapeHtml(s) { const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
    function highlightMatch(text,q){ if(!q) return escapeHtml(text); const esc=q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); return escapeHtml(text).replace(new RegExp('('+esc+')','ig'), '<mark style="background: rgba(255,204,51,0.35); color: inherit; padding:0 1px; border-radius:2px;">$1</mark>'); }

    function setSelected(item) {
        pending = item;
        selectedLabel.innerHTML = highlightMatch(item.label, '');
        selectedSub.textContent = (item.login_id || '') + (item.email ? ' · ' + item.email : '');
        selectedAvatar.textContent = (item.label||'?').charAt(0).toUpperCase();
        selectedWrap.classList.remove('hidden');
        resultsEl.classList.add('hidden');
        input.value = item.label + ' — ' + (item.login_id || '');
        clearBtn.classList.remove('hidden');
        clearBtn.style.display = 'flex';
        confirmBtn.disabled = false;
        input.blur();
    }
    function clearSelected() {
        pending = null;
        selectedWrap.classList.add('hidden');
        confirmBtn.disabled = true;
        input.value = '';
        clearBtn.classList.add('hidden');
        clearBtn.style.display = '';
        resultsEl.classList.add('hidden');
        lastResults = [];
        activeIndex = -1;
    }

    function updateActive() {
        const opts = resultsEl.querySelectorAll('[data-index]');
        opts.forEach((li, idx) => {
            li.style.backgroundColor = idx === activeIndex ? 'rgba(255,204,51,0.12)' : 'transparent';
        });
    }

    function renderList(items, q, header) {
        if (!items.length) {
            resultsEl.innerHTML = '<li class="px-4 py-4 text-sm text-center" style="color: var(--color-primary1); opacity:0.6;">No results for “' + escapeHtml(q) + '”</li>';
            resultsEl.classList.remove('hidden'); lastResults = []; activeIndex=-1; return;
        }
        let html='';
        if (header) html += '<li class="px-3 pt-3 pb-1 text-[10px] font-semibold uppercase tracking-widest" style="color: var(--color-primary1); opacity:0.5;">' + header + '</li>';
        items.forEach((item, idx) => {
            html += '<li data-id="'+item.id+'" data-index="'+idx+'" class="px-3 py-2.5 flex items-center gap-3 cursor-pointer hover:bg-black/5" style="background-color:'+(idx===activeIndex?'rgba(255,204,51,0.12)':'transparent')+';">'
                + '<div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold" style="background-color: rgba(255,204,51,0.18); color: var(--color-accent1);">'+escapeHtml((item.label||'?').charAt(0).toUpperCase())+'</div>'
                + '<div class="min-w-0 flex-1"><p class="text-sm font-medium truncate" style="color: var(--color-primary1);">'+highlightMatch(item.label, q)+'</p><p class="text-xs truncate" style="color: var(--color-primary1); opacity:0.6;">'+escapeHtml(item.login_id||'')+(item.email?' · '+escapeHtml(item.email):'')+'</p></div></li>';
        });
        resultsEl.innerHTML = html;
        resultsEl.classList.remove('hidden');
        lastResults = items;
        activeIndex = -1;
        resultsEl.querySelectorAll('[data-id]').forEach(li => {
            li.addEventListener('click', () => {
                const id = parseInt(li.dataset.id,10);
                const found = lastResults.find(r=>r.id===id);
                if(found) setSelected(found);
            });
            li.addEventListener('mouseenter', () => { activeIndex = parseInt(li.dataset.index,10); updateActive(); });
        });
    }

    function renderRecents() {
        const recents = getRecents().filter(r=>r.id!==currentId);
        if (!recents.length) {
            resultsEl.innerHTML = '<li class="px-4 py-6 text-sm text-center" style="color: var(--color-primary1); opacity:0.6;">No recent beneficiaries<br><span style="opacity:0.45; font-size:12px;">Search by name, login ID or email (min 2 characters)</span></li>';
            resultsEl.classList.remove('hidden'); lastResults=[]; return;
        }
        renderList(recents, '', 'Recent beneficiaries');
    }

    async function fetchSearch(q){
        if(abortController) abortController.abort();
        abortController = new AbortController();
        try{
            const res = await fetch('/beneficiaries/search?q='+encodeURIComponent(q), {headers:{'Accept':'application/json'}, signal: abortController.signal});
            if(!res.ok) throw new Error('fail');
            const data = await res.json();
            const filtered = (Array.isArray(data)?data:[]).filter(r=>r.id!==currentId);
            if(!filtered.length) renderList([], q, null); else renderList(filtered, q, 'Search results');
        }catch(e){ if(e.name==='AbortError') return; resultsEl.innerHTML='<li class="px-4 py-3 text-sm text-center" style="color: var(--color-danger);">Search failed. Try again.</li>'; resultsEl.classList.remove('hidden'); }
    }

    openBtn.addEventListener('click', () => {
        if (typeof dialog.showModal === 'function') dialog.showModal(); else dialog.setAttribute('open','');
        clearSelected();
        // Do not auto-show recents — only when input is clicked, so buttons stay unobstructed
        setTimeout(()=> input.focus(), 50);
    });
    function closeDialog(){ if(typeof dialog.close==='function') try{ dialog.close(); }catch{} dialog.removeAttribute('open'); resultsEl.classList.add('hidden'); }
    closeBtn.addEventListener('click', closeDialog);
    cancelBtn.addEventListener('click', closeDialog);
    selectedRemove.addEventListener('click', clearSelected);
    dialog.addEventListener('click', (e)=>{
        const rect = dialog.getBoundingClientRect();
        if(e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom){ closeDialog(); }
    });

    // Only show recents/results when input is clicked — keeps buttons unobstructed on open
    input.addEventListener('click', ()=>{
        if(pending) return;
        const v = input.value.trim();
        if(v.length>=2) fetchSearch(v); else renderRecents();
    });
    // Hide recents when input is not focused — prevents floating dropdown
    input.addEventListener('blur', ()=>{
        setTimeout(()=>{
            const active = document.activeElement;
            const hovering = resultsEl.matches(':hover');
            if (active === input || (resultsEl.contains(active)) || hovering) return;
            resultsEl.classList.add('hidden');
        }, 180);
    });
    resultsEl.addEventListener('mouseleave', ()=>{
        // if input is not focused anymore, hide after leave
        setTimeout(()=>{
            if (document.activeElement !== input) resultsEl.classList.add('hidden');
        }, 120);
    });
    input.addEventListener('focus', ()=>{
        if(pending) return;
        const v = input.value.trim();
        if(v) { clearBtn.classList.remove('hidden'); clearBtn.style.display='flex'; }
        // Do not auto-show recents on focus — wait for click
    });
    input.addEventListener('input', ()=>{
        if(pending){ // if user edits after picking, clear selection
            pending=null; selectedWrap.classList.add('hidden'); confirmBtn.disabled=true;
        }
        const v = input.value.trim();
        if(!v){ clearBtn.classList.add('hidden'); clearBtn.style.display=''; resultsEl.classList.add('hidden'); return; }
        clearBtn.classList.remove('hidden'); clearBtn.style.display='flex';
        clearTimeout(debounceTimer);
        if(v.length<2){ resultsEl.classList.add('hidden'); return; }
        debounceTimer=setTimeout(()=>fetchSearch(v),250);
    });
    clearBtn.addEventListener('click', ()=>{ clearSelected(); input.focus(); });
    input.addEventListener('keydown', (e)=>{
        const visible = !resultsEl.classList.contains('hidden');
        if(!visible) return;
        if(e.key==='ArrowDown'){ e.preventDefault(); activeIndex=Math.min(activeIndex+1, lastResults.length-1); updateActive(); }
        else if(e.key==='ArrowUp'){ e.preventDefault(); activeIndex=Math.max(activeIndex-1,0); updateActive(); }
        else if(e.key==='Enter'){
            if(activeIndex>=0 && lastResults[activeIndex]){ e.preventDefault(); setSelected(lastResults[activeIndex]); }
        }else if(e.key==='Escape'){ resultsEl.classList.add('hidden'); activeIndex=-1; }
    });
    document.addEventListener('click', (e)=>{
        const pickerRoot = input.closest('div');
        if(!pickerRoot) return;
        // handled via dialog click, but keep results open when interacting inside
    });

    confirmBtn.addEventListener('click', ()=>{
        if(!pending) return;
        // inject hidden
        let hidden = form.querySelector('input[name="switch_beneficiary_id"]');
        if(!hidden){ hidden=document.createElement('input'); hidden.type='hidden'; hidden.name='switch_beneficiary_id'; form.appendChild(hidden); }
        hidden.value = String(pending.id);
        closeDialog();
        // submit form
        if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit();
    });
})();
</script>
