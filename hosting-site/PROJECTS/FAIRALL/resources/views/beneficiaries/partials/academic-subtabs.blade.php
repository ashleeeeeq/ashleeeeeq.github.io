@php
    $isEnrollmentHistory = request()->is("beneficiaries/$beneficiary->id/enrollments*");
    $isAcademicRecords = request()->is("beneficiaries/$beneficiary->id/academic-records*");
@endphp

<div class="overflow-x-auto overflow-y-hidden mt-4">
    <div class="flex gap-2 border-b border-white/20 min-w-max">
        <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 {{ $isEnrollmentHistory ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Enrollment History
        </a>

        <a href="/beneficiaries/{{ $beneficiary->id }}/academic-records"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 {{ $isAcademicRecords ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-accent1' }}">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Academic Records
        </a>
    </div>
</div>