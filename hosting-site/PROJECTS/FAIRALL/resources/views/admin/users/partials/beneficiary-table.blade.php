<div class="overflow-x-auto bg-base-100 rounded-box shadow">
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>Login ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Grade</th>
                <th>Programs</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($beneficiaryUsers as $user)
                <tr>
                    <td>{{ $user->login_id }}</td>
                    <td>{{ $user->beneficiary?->display_name ?? '-' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->beneficiary?->grade_level ?? '-' }}</td>
                    <td>{{ $user->beneficiary?->programs->pluck('program_name')->join(', ') ?? '-' }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="badge {{ $user->email_verified_at ? 'badge-info' : 'badge-ghost' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                            </span>
                        </div>
                    </td>
                    <td class="flex gap-2">
                        <a href="/beneficiaries/{{ $user->beneficiary->id }}/edit" class="btn btn-sm btn-outline">Edit</a>
                        <button type="button" class="btn btn-sm btn-error" onclick="document.getElementById('deleteBeneficiaryModal_{{ $user->id }}').showModal()">Delete</button>

                        <x-confirm-dialog
                            id="deleteBeneficiaryModal_{{ $user->id }}"
                            title="Delete Beneficiary"
                            message="Are you sure you want to delete <strong>{{ $user->display_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                            deleteUrl="/beneficiaries/{{ $user->id }}" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No beneficiary users yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $beneficiaryUsers->links('pagination.custom') }}
</div>
