/**
 * Bulk select helper for table multi-delete.
 * Works with:
 *  - <table data-bulk-table="tableId"> containing:
 *      th > input[data-select-all]
 *      td > input[data-row-checkbox][value=id]
 *  - <div id="bulkBar-tableId" data-bulk-bar>
 *  - <dialog id="bulkConfirm-tableId">
 *  - <form id="bulkForm-tableId"> with <div data-bulk-ids>
 */
export function initBulkSelect() {
    const tables = document.querySelectorAll('[data-bulk-table]');

    tables.forEach((table) => {
        const tableId = table.getAttribute('data-bulk-table');
        if (!tableId) return;

        // avoid double-init when navigating with back/forward cache
        if (table.dataset.bulkInitialized === 'true') return;
        table.dataset.bulkInitialized = 'true';

        const selectAll = table.querySelector('[data-select-all]');
        const getRowBoxes = () => table.querySelectorAll('[data-row-checkbox]');
        const bar = document.getElementById(`bulkBar-${tableId}`);
        // Archive tables have dual forms/dialogs per group
        const isArchive = tableId.startsWith('archive-');
        if (isArchive) {
            const key = tableId.replace('archive-', '');
            const restoreBtn = bar ? bar.querySelector('[data-bulk-restore-btn]') : null;
            const deleteBtnArch = bar ? bar.querySelector('[data-bulk-delete-btn]') : null;
            const clearBtnArch = bar ? bar.querySelector('[data-bulk-clear]') : null;
            const countElArch = bar ? bar.querySelector('[data-selected-count]') : null;
            const restoreForm = document.getElementById(`restoreForm-${key}`);
            const deleteForm = document.getElementById(`deleteForm-${key}`);
            const restoreIdsContainer = restoreForm ? restoreForm.querySelector('[data-restore-ids]') : bar ? bar.querySelector('[data-restore-ids]') : null;
            const deleteIdsContainer = deleteForm ? deleteForm.querySelector('[data-delete-ids]') : bar ? bar.querySelector('[data-delete-ids]') : null;
            const restoreDialog = document.getElementById(`bulkRestoreConfirm-${key}`);
            const deleteDialog = document.getElementById(`bulkDeleteConfirm-${key}`);
            const restoreConfirmCountEl = restoreDialog ? restoreDialog.querySelector('[data-bulk-confirm-count]') : null;
            const deleteConfirmCountEl = deleteDialog ? deleteDialog.querySelector('[data-bulk-confirm-count]') : null;
            const restoreConfirmBtn = document.getElementById(`bulkRestoreBtn-${key}`);
            const deleteConfirmBtn = document.getElementById(`bulkDeleteBtn-${key}`);

            if (!selectAll || !getRowBoxes().length || !bar) return;
            const getEnabledBoxes = () => Array.from(getRowBoxes()).filter((c) => !c.disabled);
            const updateArch = () => {
                const checked = Array.from(getRowBoxes()).filter((c) => c.checked);
                const n = checked.length;
                const enabledCount = getEnabledBoxes().length;
                bar.classList.toggle('hidden', n === 0);
                bar.classList.toggle('flex', n > 0);
                if (restoreBtn) restoreBtn.disabled = n === 0;
                if (deleteBtnArch) deleteBtnArch.disabled = n === 0;
                if (countElArch) countElArch.textContent = String(n);
                if (restoreConfirmCountEl) restoreConfirmCountEl.textContent = String(n);
                if (deleteConfirmCountEl) deleteConfirmCountEl.textContent = String(n);
                selectAll.checked = enabledCount > 0 && n === enabledCount;
                selectAll.indeterminate = n > 0 && n < enabledCount;
            };
            selectAll.addEventListener('change', (e) => {
                const checked = e.target.checked;
                getRowBoxes().forEach((c) => { if (!c.disabled) c.checked = checked; });
                updateArch();
            });
            table.addEventListener('change', (e) => {
                if (e.target.matches('[data-row-checkbox]')) updateArch();
            });
            if (clearBtnArch) {
                clearBtnArch.addEventListener('click', () => {
                    getRowBoxes().forEach((c) => (c.checked = false));
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                    updateArch();
                });
            }
            const populateIds = (container) => {
                if (!container) return 0;
                const checked = Array.from(getRowBoxes()).filter((c) => c.checked);
                if (!checked.length) return 0;
                container.innerHTML = '';
                checked.forEach((c) => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'ids[]';
                    inp.value = c.value;
                    container.appendChild(inp);
                });
                return checked.length;
            };
            if (restoreBtn && restoreDialog) {
                restoreBtn.addEventListener('click', () => {
                    const n = populateIds(restoreIdsContainer);
                    if (!n) return;
                    if (restoreConfirmCountEl) restoreConfirmCountEl.textContent = String(n);
                    if (countElArch) countElArch.textContent = String(n);
                    restoreDialog.showModal();
                });
            }
            if (deleteBtnArch && deleteDialog) {
                deleteBtnArch.addEventListener('click', () => {
                    const n = populateIds(deleteIdsContainer);
                    if (!n) return;
                    if (deleteConfirmCountEl) deleteConfirmCountEl.textContent = String(n);
                    if (countElArch) countElArch.textContent = String(n);
                    deleteDialog.showModal();
                });
            }
            if (restoreConfirmBtn && restoreForm) {
                restoreConfirmBtn.addEventListener('click', () => restoreForm.submit());
            }
            if (deleteConfirmBtn && deleteForm) {
                deleteConfirmBtn.addEventListener('click', () => deleteForm.submit());
            }
            updateArch();
            return;
        }
        const form = document.getElementById(`bulkForm-${tableId}`);
        const idsContainer = bar ? bar.querySelector('[data-bulk-ids]') : null;
        const countEl = bar ? bar.querySelector('[data-selected-count]') : null;
        const deleteBtn = bar ? bar.querySelector('[data-bulk-delete-btn]') : null;
        const clearBtn = bar ? bar.querySelector('[data-bulk-clear]') : null;
        const dialog = document.getElementById(`bulkConfirm-${tableId}`);
        const confirmCountEl = dialog ? dialog.querySelector('[data-bulk-confirm-count]') : null;
        const confirmSubmitBtn = document.getElementById(`bulkConfirmBtn-${tableId}`);

        if (!selectAll || !getRowBoxes().length || !bar || !form) {
            return;
        }

        const getEnabledBoxes = () => Array.from(getRowBoxes()).filter((c) => !c.disabled);

        const update = () => {
            const checked = Array.from(getRowBoxes()).filter((c) => c.checked);
            const n = checked.length;
            const enabledCount = getEnabledBoxes().length;

            bar.classList.toggle('hidden', n === 0);
            bar.classList.toggle('flex', n > 0);
            if (deleteBtn) deleteBtn.disabled = n === 0;
            if (countEl) countEl.textContent = String(n);
            if (confirmCountEl) confirmCountEl.textContent = String(n);

            // select-all state (only count enabled boxes)
            selectAll.checked = enabledCount > 0 && n === enabledCount;
            selectAll.indeterminate = n > 0 && n < enabledCount;
        };

        selectAll.addEventListener('change', (e) => {
            const checked = e.target.checked;
            getRowBoxes().forEach((c) => {
                // skip disabled (e.g. self-delete guard in users table)
                if (!c.disabled) c.checked = checked;
            });
            update();
        });

        table.addEventListener('change', (e) => {
            if (e.target.matches('[data-row-checkbox]')) update();
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                getRowBoxes().forEach((c) => (c.checked = false));
                selectAll.checked = false;
                selectAll.indeterminate = false;
                update();
            });
        }

        if (deleteBtn && dialog) {
            deleteBtn.addEventListener('click', () => {
                const checked = Array.from(getRowBoxes()).filter((c) => c.checked);
                if (!checked.length) return;

                // populate hidden form
                if (idsContainer) {
                    idsContainer.innerHTML = '';
                    checked.forEach((c) => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'ids[]';
                        inp.value = c.value;
                        idsContainer.appendChild(inp);
                    });
                }

                if (confirmCountEl) confirmCountEl.textContent = String(checked.length);
                if (countEl) countEl.textContent = String(checked.length);

                dialog.showModal();
            });
        }

        if (confirmSubmitBtn && form) {
            confirmSubmitBtn.addEventListener('click', () => {
                form.submit();
            });
        }

        // initial state
        update();
    });
}

window.initBulkSelect = initBulkSelect;

// Auto-init on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBulkSelect, { once: true });
} else {
    initBulkSelect();
}

// Re-init after back/forward navigation (bfcache)
window.addEventListener('pageshow', () => {
    // reset init flag and re-init (handles cached DOM)
    document.querySelectorAll('[data-bulk-table]').forEach((t) => {
        delete t.dataset.bulkInitialized;
    });
    initBulkSelect();
});
