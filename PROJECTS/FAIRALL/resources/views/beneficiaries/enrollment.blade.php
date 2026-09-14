<x-dashboardlayout name="{{ $name }}" title="Enrollment Management">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Enrollment Management</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Manage beneficiary enrollments across programs</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3" style="background-color: var(--color-success-light); border: 1px solid var(--color-success);">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm" style="color: var(--color-success-dark); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="rounded-2xl mb-6 p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" placeholder="Search by name, login ID, or email..." value="{{ request('search') }}"
                               class="w-full rounded-xl pl-10 pr-4 py-2 text-sm text-primary1"
                               style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: var(--color-accent1); color: var(--color-primary1);">
                        Filter
                    </button>
                    <a href="/beneficiaries/enrollment"
                       class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                       style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabs -->
        <div class="w-full">
            <!-- Tab Headers -->
            <div class="flex gap-2 border-b border-white/20 mb-6 overflow-x-auto">
                @foreach ($programs as $program)
                    @php
                        $iconPaths = [
                            'Education' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
                            'Sports' => 'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0',
                        ];
                        $iconPath = $iconPaths[$program->program_name] ?? 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
                    @endphp
                    <button type="button" onclick="switchProgramTab({{ $program->id }})" 
                            id="tabBtn{{ $program->id }}"
                            class="tab-btn-inactive px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.6); border-bottom: 2px solid transparent; background-color: transparent;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                        </svg>
                        {{ $program->program_name }}
                    </button>
                @endforeach
            </div>

            <!-- Tab Content -->
            @foreach ($programs as $program)
                <div id="programTab{{ $program->id }}" class="program-tab-content" style="display: {{ $loop->first ? 'block' : 'none' }};">
                    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                        @php
                                            $programIcons = [
                                                'Education' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
                                                'Sports' => 'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0',
                                            ];
                                            $iconPath = $programIcons[$program->program_name] ?? 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
                                        @endphp
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">{{ $program->program_name }}</h2>
                                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Beneficiary roster and enrollment status</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm sm:text-basepx-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full whitespace-nowrap"
                                          style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                                        {{ $totalBeneficiaries }} total
                                    </span>
                                    @if (!in_array((int) $program->id, $manageableProgramIds, true))
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">View only</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Login ID</th>
                                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Name</th>
                                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Email</th>
                                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Action</th>
                                         </tr>
                                    </thead>
                                    <tbody>
                                        @forelse (($beneficiariesByProgram[$program->id] ?? collect()) as $index => $row)
                                            @php
                                                $beneficiary = $row['beneficiary'];
                                                $isActive = $row['is_active'];
                                                $canManage = in_array((int) $program->id, $manageableProgramIds, true);
                                            @endphp
                                            <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                                <td class="px-3 sm:px-4 py-3 align-middle">
                                                    <span class="text-xs sm:text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $beneficiary?->user?->login_id }}</span>
                                                </td>
                                                <td class="px-3 sm:px-4 py-3 align-middle">
                                                    <a href="/beneficiaries/{{ $beneficiary->id }}/profile" 
                                                       class="text-xs sm:text-sm text-white font-medium hover:text-yellow-400 transition-colors"
                                                       style="font-family: var(--font-body1);">
                                                        {{ $beneficiary->display_name }}
                                                    </a>
                                                </td>
                                                <td class="px-3 sm:px-4 py-3 align-middle">
                                                    <span class="text-xs sm:text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $beneficiary?->user?->email }}</span>
                                                </td>                                                <td class="px-3 sm:px-4 py-3 align-middle">
                                                    @if ($canManage)
                                                        @if ($isActive)
                                                            <form method="POST" action="/beneficiaries/enrollment/unenroll" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->id }}" />
                                                                <input type="hidden" name="program_id" value="{{ $program->id }}" />
                                                                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                                        style="font-family: var(--font-body1); background-color: var(--color-danger-light); color: var(--color-danger-dark);"
                                                                        onclick="document.getElementById('unenrollModal_{{ $beneficiary->id }}_{{ $program->id }}').showModal()">
                                                                    Unenroll
                                                                </button>

                                                                <x-confirm-dialog id="unenrollModal_{{ $beneficiary->id }}_{{ $program->id }}" title="Unenroll Beneficiary" message="Unenroll {{ $beneficiary->display_name }} from {{ $program->program_name }}?">
                                                                    <button type="submit" form="unenrollForm_{{ $beneficiary->id }}_{{ $program->id }}" class="px-4 py-2 font-semibold rounded-lg transition-all duration-200" style="background-color: var(--color-danger); color: white;">Unenroll</button>
                                                                </x-confirm-dialog>
                                                            </form>
                                                            <form method="POST" action="/beneficiaries/enrollment/unenroll" id="unenrollForm_{{ $beneficiary->id }}_{{ $program->id }}" class="hidden">
                                                                @csrf
                                                                <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->id }}" />
                                                                <input type="hidden" name="program_id" value="{{ $program->id }}" />
                                                            </form>
                                                        @else
                                                            <form method="POST" action="/beneficiaries/enrollment/enroll" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->id }}" />
                                                                <input type="hidden" name="program_id" value="{{ $program->id }}" />
                                                                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                                                                        onclick="document.getElementById('enrollModal_{{ $beneficiary->id }}_{{ $program->id }}').showModal()">
                                                                    Enroll
                                                                </button>

                                                                <x-confirm-dialog id="enrollModal_{{ $beneficiary->id }}_{{ $program->id }}" title="Enroll Beneficiary" message="Enroll {{ $beneficiary->display_name }} to {{ $program->program_name }}?">
                                                                    <button type="submit" form="enrollForm_{{ $beneficiary->id }}_{{ $program->id }}" class="px-4 py-2 font-semibold rounded-lg transition-all duration-200" style="background-color: var(--color-accent1); color: var(--color-primary1);">Enroll</button>
                                                                </x-confirm-dialog>
                                                            </form>
                                                            <form method="POST" action="/beneficiaries/enrollment/enroll" id="enrollForm_{{ $beneficiary->id }}_{{ $program->id }}" class="hidden">
                                                                @csrf
                                                                <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->id }}" />
                                                                <input type="hidden" name="program_id" value="{{ $program->id }}" />
                                                            </form>
                                                        @endif
                                                    @else
                                                        <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg cursor-not-allowed opacity-50"
                                                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5);" disabled>
                                                            Not Allowed
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-3 sm:px-4 py-12 text-center">
                                                    <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.3);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                    </svg>
                                                    <p class="text-sm" style="color: rgba(255,255,255,0.4);">No beneficiaries found.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-6">
                {{ $beneficiaries->links('pagination.custom') }}
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-end">
            <a href="/beneficiaries"
                class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10 text-sm sm:text-base"
                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Beneficiaries
            </a>
        </div>
    </div>

    <script>
        function switchProgramTab(programId) {
            // Hide all tab contents
            document.querySelectorAll('.program-tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Show selected tab content
            const selectedTab = document.getElementById('programTab' + programId);
            if (selectedTab) {
                selectedTab.style.display = 'block';
            }
            
            // Update tab button styles
            document.querySelectorAll('[id^="tabBtn"]').forEach(btn => {
                btn.style.color = 'rgba(255,255,255,0.6)';
                btn.style.borderBottom = '2px solid transparent';
            });
            
            const activeBtn = document.getElementById('tabBtn' + programId);
            if (activeBtn) {
                activeBtn.style.color = 'var(--color-accent1)';
                activeBtn.style.borderBottom = '2px solid var(--color-accent1)';
            }
        }
        
        // Set active tab on load
        document.addEventListener('DOMContentLoaded', function() {
            const firstTab = document.querySelector('[id^="tabBtn"]');
            if (firstTab) {
                const programId = firstTab.id.replace('tabBtn', '');
                switchProgramTab(programId);
            }
        });
    </script>
</x-dashboardlayout>