<div class="overflow-x-auto bg-base-100 rounded-box shadow">
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>Login ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Account Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($staffUsers as $user)
                <tr>
                    <td>{{ $user->login_id }}</td>
                    <td>{{ $user->display_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->staff?->roleLabel() }}</td>
                    <td>{{ $user->staff?->department?->name ?? '-' }}</td>
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
                        <a href="/users/{{ $user->id }}/edit" class="btn btn-sm btn-outline">Edit</a>
                        @if (auth()->id() !== $user->id)
                            <button type="button" class="btn btn-sm btn-error" onclick="document.getElementById('deleteStaffUserModal_{{ $user->id }}').showModal()">Delete</button>

                            <x-confirm-dialog id="deleteStaffUserModal_{{ $user->id }}" title="Delete User" message="Are you sure you want to delete <strong>{{ $user->display_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                deleteUrl="/users/{{ $user->id }}" />
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No staff users yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $staffUsers->links('pagination.custom') }}
</div>
