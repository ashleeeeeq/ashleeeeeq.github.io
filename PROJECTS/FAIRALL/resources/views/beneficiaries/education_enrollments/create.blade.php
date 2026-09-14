<x-dashboardlayout name="{{ $name }}" title="Create Enrollment">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Create Enrollment</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Add school enrollment(s) for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <x-beneficiary-switcher :beneficiary="$beneficiary" switchUrlPattern="/beneficiaries/__ID__/enrollments/create" formId="enrollments-form" />

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/enrollments" id="enrollments-form">
            @csrf
            <div id="enrollments-rows" class="space-y-4"></div>

            <div class="flex justify-start mt-4">
                <button type="button" id="add-enrollment"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Enrollment
                </button>
            </div>
            <x-forms.errors name="enrollments" />

            <div class="flex flex-wrap justify-end gap-3 pt-6 mt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments"
                   class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Save & Go to List
                </button>
                <button type="button" id="save-switch-btn"
                   class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M4 17h12m0 0l-4-4m4 4l-4 4"></path></svg>
                    Save & Switch Beneficiary
                </button>
            </div>
        </form>
        <x-save-switch-dialog :beneficiary="$beneficiary" formId="enrollments-form" />
    </div>

    <template id="enrollment-template">
        <div class="enrollment-card rounded-2xl overflow-hidden transition-all duration-300" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b flex items-center justify-between" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white enrollment-card-title" style="font-family: var(--font-header1);">Enrollment #__NUMBER__</h3>
                        <p class="text-xs text-white/50">Enrollment details</p>
                    </div>
                </div>
                <button type="button" class="remove-enrollment inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg transition-all duration-200" style="font-family: var(--font-body1); color: var(--color-danger); background-color: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Remove
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">School Name <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input name="enrollments[__INDEX__][school_name]" class="enrollment-school_name w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                               style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);" placeholder="e.g., FEU Tech" required>
                        <x-forms.errors name="enrollments.__INDEX__.school_name" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Academic Year <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input name="enrollments[__INDEX__][academic_year]" class="enrollment-academic_year w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                               style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);" placeholder="2025-2026" required>
                        <x-forms.errors name="enrollments.__INDEX__.academic_year" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Grade Level <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        @php $gradeLevels = ['1','2','3','4','5','6','7','8','9','10','11','12','1st Year','2nd Year','3rd Year','4th Year','5th Year']; @endphp
                        <div class="relative">
                            <select name="enrollments[__INDEX__][grade_level]" class="enrollment-grade_level w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" required>
                                <option value="" disabled selected style="color: var(--color-primary1);">Select grade level</option>
                                @foreach ($gradeLevels as $gl)
                                    <option value="{{ $gl }}" style="color: var(--color-primary1);">{{ $gl }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Enrollment Status</label>
                        @php $statuses = ['active','completed','transferred','dropped','withdrawn','repeated']; @endphp
                        <div class="relative">
                            <select name="enrollments[__INDEX__][enrollment_status]" class="enrollment-enrollment_status w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                @foreach ($statuses as $st)
                                    <option value="{{ $st }}" style="color: var(--color-primary1);" @selected($st==='active')>{{ $st }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        (() => {
            const existing = @json(old('enrollments', []));
            const rows = document.getElementById('enrollments-rows');
            const template = document.getElementById('enrollment-template').innerHTML;
            const addBtn = document.getElementById('add-enrollment');

            const reindex = () => {
                rows.querySelectorAll('.enrollment-card').forEach((card, idx) => {
                    card.querySelector('.enrollment-card-title').textContent = `Enrollment #${idx + 1}`;
                    card.querySelectorAll('input, select').forEach(el => {
                        if (el.name) el.name = el.name.replace(/enrollments\[\d+\]/, `enrollments[${idx}]`);
                    });
                    const rm = card.querySelector('.remove-enrollment');
                    if (rm) rm.style.display = rows.querySelectorAll('.enrollment-card').length > 1 ? '' : 'none';
                });
            };

            const addRow = (data = {}) => {
                if (rows.querySelectorAll('.enrollment-card').length >= 20) return;
                const idx = rows.querySelectorAll('.enrollment-card').length;
                let html = template.replaceAll('__INDEX__', idx).replaceAll('__NUMBER__', idx + 1);
                rows.insertAdjacentHTML('beforeend', html);
                const card = rows.lastElementChild;
                if (data.school_name) card.querySelector('.enrollment-school_name').value = data.school_name;
                if (data.academic_year) card.querySelector('.enrollment-academic_year').value = data.academic_year;
                if (data.grade_level) card.querySelector('.enrollment-grade_level').value = data.grade_level;
                if (data.enrollment_status) card.querySelector('.enrollment-enrollment_status').value = data.enrollment_status;
                card.querySelector('.remove-enrollment').addEventListener('click', () => { card.remove(); reindex(); });
                reindex();
            };

            addBtn.addEventListener('click', () => addRow());
            if (existing.length) existing.forEach(row => addRow(row)); else addRow();
        })();
    </script>
</x-dashboardlayout>
