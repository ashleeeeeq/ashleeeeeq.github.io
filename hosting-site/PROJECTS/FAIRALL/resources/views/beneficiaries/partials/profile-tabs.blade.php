<div class="overflow-x-auto overflow-y-hidden mb-6">
    <div class="flex gap-2 border-b border-white/20 min-w-max">
        <a href="/beneficiaries/{{ $beneficiary->id }}/profile"
            class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->is("beneficiaries/$beneficiary->id/profile")
                      ? 'text-accent1 border-b-2 border-accent1'
                      : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Profile
        </a>

        @can('manage-education-records')
            @php
                $isAcademicSection =
                    request()->is("beneficiaries/$beneficiary->id/enrollments*") ||
                    request()->is("beneficiaries/$beneficiary->id/academic-records*");
            @endphp
            @if ($beneficiary->programs->contains(fn($p) => $p->program_name === 'Education'))
                <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments"
                    class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                      {{ $isAcademicSection
                          ? 'text-accent1 border-b-2 border-accent1'
                          : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
                    <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    School Enrollments
                </a>

                <a href="/beneficiaries/{{ $beneficiary->id }}/ffa-assessments"
                    class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                      {{ request()->is("beneficiaries/$beneficiary->id/ffa-assessments")
                          ? 'text-accent1 border-b-2 border-accent1'
                          : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
                    <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Youth Center
                </a>
            @endif
        @endcan

        <a href="/beneficiaries/{{ $beneficiary->id }}/guardians"
            class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->is("beneficiaries/$beneficiary->id/guardians")
                      ? 'text-accent1 border-b-2 border-accent1'
                      : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                </path>
            </svg>
            Guardians
        </a>

        @can('manage-sports-records')
            @if ($beneficiary->programs->contains(fn($p) => $p->program_name === 'Sports'))
                <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records"
                    class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                          {{ request()->is("beneficiaries/$beneficiary->id/injury-records")
                              ? 'text-accent1 border-b-2 border-accent1'
                              : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
                    <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                    </svg>
                    Injury
                </a>
            @endif
        @endcan

        <a href="/beneficiaries/{{ $beneficiary->id }}/status-history"
            class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->is("beneficiaries/$beneficiary->id/status-history")
                      ? 'text-accent1 border-b-2 border-accent1'
                      : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
            </svg>
            Status
        </a>

        <a href="/beneficiaries/{{ $beneficiary->id }}/activity-history"
            class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->is("beneficiaries/$beneficiary->id/activity-history")
                      ? 'text-accent1 border-b-2 border-accent1'
                      : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"></path>
            </svg>
            Activities
        </a>

        <a href="/beneficiaries/{{ $beneficiary->id }}/alerts"
            class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->is("beneficiaries/$beneficiary->id/alerts")
                      ? 'text-accent1 border-b-2 border-accent1'
                      : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
            </svg>
            Alerts
        </a>

        @can('manage-education-records')
            @if ($beneficiary->programs->contains(fn($p) => $p->program_name === 'Education'))
                <a href="/beneficiaries/{{ $beneficiary->id }}/education-intake/edit"
                    class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                          {{ request()->is("beneficiaries/$beneficiary->id/education-intake/edit")
                              ? 'text-accent1 border-b-2 border-accent1'
                              : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
                    <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Intake
                </a>
            @endif
        @endcan

        <a href="/beneficiaries/{{ $beneficiary->id }}/documents"
            class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->is("beneficiaries/$beneficiary->id/documents")
                      ? 'text-accent1 border-b-2 border-accent1'
                      : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z">
                </path>
            </svg>
            Documents
        </a>
    </div>
</div>
