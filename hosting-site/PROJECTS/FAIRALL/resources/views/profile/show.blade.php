<x-dashboardlayout title="My Profile">
    <div class="mb-8 space-y-6">
        <!-- Page Header -->
        <div>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white" style="font-family: var(--font-header1);">My
                        Profile</h1>
                    <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Manage your personal
                        information, account settings, and security preferences.</p>
                </div>
            </div>
            <div class="w-16 h-1 mt-3 rounded-full" style="background-color: var(--color-accent1);"></div>
        </div>

        <!-- Status Messages -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3"
                style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3);">
                <span class="text-sm" style="color: #10b981;">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Avatar Section -->
        <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="w-28 h-28 rounded-full overflow-hidden border border-white/20 shrink-0">
                    <img id="profile_avatar_preview" src="{{ $user->avatar_url }}"
                        alt="{{ $displayName }} profile picture" class="w-full h-full object-cover">
                </div>

                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white">Profile Picture</h2>
                    <p class="text-sm mt-1" style="color: rgba(255,255,255,0.5);">Upload a JPG, PNG, or WebP image up to
                        4 MB. Crop and zoom before saving.</p>

                    @if($errors->has('avatar'))
                        @foreach($errors->get('avatar') as $error)
                            <div class="mb-6 p-4 rounded-xl flex items-center gap-3 mt-4"
                                style="background-color: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3);">
                                <span class="text-sm" style="color: var(--color-danger);">{{ $error }}</span>
                            </div>
                        @endforeach
                    @endif

                    <form id="avatar_upload_form" method="POST" action="/profile/avatar" enctype="multipart/form-data"
                        class="mt-4 flex flex-col md:flex-row gap-3 md:items-end">
                        @csrf
                        <input type="hidden" name="avatar_cropped" id="avatar_cropped" value="">

                        <div class="w-full max-w-md">
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Choose an
                                image</label>
                            <input id="avatar_input" type="file" name="avatar"
                                accept="image/jpeg,image/png,image/webp"
                                class="file-input file-input-bordered w-full bg-white/95 text-slate-800">
                        </div>
                    </form>

                    @if ($user->avatar_path)
                        <div class="mt-3">
                            <button type="button"
                                onclick="document.getElementById('removeAvatarModal').showModal()"
                                class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                                style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                                onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                Remove Picture
                            </button>
                        </div>

                        <x-confirm-dialog id="removeAvatarModal" title="Remove Profile Picture" message="Are you sure you want to remove your profile picture? This action cannot be undone.">
                            <form method="POST" action="/profile/avatar">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                                    style="font-family: var(--font-body1); background-color: var(--color-danger); color: white; border: none;">
                                    Remove
                                </button>
                            </form>
                        </x-confirm-dialog>
                    @endif

                    <p id="crop_status" class="text-xs mt-2" style="color: rgba(255,255,255,0.4);">Tip: selecting a file
                        opens the crop editor automatically. Confirm in the modal to save.</p>
                </div>
            </div>
        </div>

        <!-- Avatar Crop Modal -->
        <dialog id="avatar_crop_modal" class="modal">
            <div class="modal-box max-w-3xl" style="background-color: var(--color-primary1); color: white;">
                <h3 class="font-bold text-lg text-white">Crop Profile Picture</h3>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.5);">Drag to reposition and zoom with the
                    slider. Final output is 512x512.</p>

                <div
                    class="mt-4 rounded-xl overflow-hidden border border-white/10 bg-white/5 min-h-80 flex items-center justify-center">
                    <img id="avatar_crop_image" alt="Crop preview" class="max-w-full block">
                </div>

                <div class="mt-4">
                    <label for="avatar_zoom" class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Zoom</label>
                    <input id="avatar_zoom" type="range" min="0" max="3" step="0.01" value="0"
                        class="range range-accent mt-2">
                </div>

                <div class="modal-action">
                    <button id="cancel_crop" type="button"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center gap-2"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">Cancel</button>
                    <button id="apply_crop" type="button"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center gap-2"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">Confirm</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Personal Information -->
        <div>
            <h2 class="text-2xl font-semibold text-white">Personal Information</h2>
            <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl p-5 bg-white/5 border border-white/10 only:col-span-2">
                <p class="text-sm" style="color: rgba(255,255,255,0.5);">Name</p>
                <p class="text-lg font-medium text-white">{{ $user->display_name }}</p>
            </div>

            @if ($user->user_type == 'staff')
                <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                    <p class="text-sm" style="color: rgba(255,255,255,0.5);">Department</p>
                    <p class="text-lg font-medium text-white">{{ $user->staff?->department->name }}</p>
                </div>

                <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                    <p class="text-sm" style="color: rgba(255,255,255,0.5);">Position</p>
                    <p class="text-lg font-medium text-white">{{ $user->staff?->position->name }}</p>
                </div>
            @endif
        </div>

        <!-- Account Information -->
        <div>
            <h2 class="text-2xl font-semibold text-white">Account Information</h2>
            <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
        </div>

        <div class="space-y-4">
            <!-- Email -->
            <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm" style="color: rgba(255,255,255,0.5);">Email Address</p>
                        <p class="text-lg font-medium text-white">{{ $user->email }}</p>
                    </div>
                    <a href="/profile/email"
                        class="px-4 py-2 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-sm"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                        Edit
                    </a>
                </div>
            </div>

            <!-- Email Verification Status -->
            <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm" style="color: rgba(255,255,255,0.5);">Email Verification</p>
                        @if ($user->email_verified_at)
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    class="inline-block rounded-full px-3 py-1 text-xs font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-300/20">Verified</span>
                                <span class="text-sm"
                                    style="color: rgba(255,255,255,0.5);">{{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                            </div>
                        @else
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold mt-1"
                                style="background-color: rgba(255,255,255,0.06); color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.1);">Pending</span>
                        @endif
                    </div>
                    @if (!$user->email_verified_at)
                        <form action="/profile/resend-verification" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-sm"
                                style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                                Resend
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                    <p class="text-sm" style="color: rgba(255,255,255,0.5);">Account Type</p>
                    <p class="text-lg font-medium capitalize text-white">{{ $user->user_type }}</p>
                </div>

                @if ($user->user_type == 'staff')
                    <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                        <p class="text-sm" style="color: rgba(255,255,255,0.5);">System Role</p>
                        <p class="text-lg font-medium capitalize text-white">{{ $user->staff?->role }}</p>
                    </div>
                @endif

                <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                    <p class="text-sm" style="color: rgba(255,255,255,0.5);">Login ID</p>
                    <p class="text-lg font-mono font-medium text-white">{{ $user->login_id ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Account Status Section -->
        <div>
            <h2 class="text-2xl font-semibold text-white">Account Status</h2>
            <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <!-- Active Status -->
            <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full"
                        style="background-color: {{ $user->is_active ? '#10b981' : '#ef4444' }};"></div>
                    <p class="font-medium text-white">{{ $user->is_active ? 'Account Active' : 'Account Inactive' }}
                    </p>
                </div>
            </div>

            <!-- Member Since -->
            <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                <p class="text-sm" style="color: rgba(255,255,255,0.5);">Member Since</p>
                <p class="font-medium text-white">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Security Section -->
        <div>
            <h2 class="text-2xl font-semibold text-white">Security</h2>
            <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
        </div>

        <div class="rounded-2xl p-5 bg-white/5 border border-white/10 flex justify-end">
            <a href="/profile/password"
                class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                Change Password
            </a>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <style>
        #avatar_crop_modal .cropper-view-box,
        #avatar_crop_modal .cropper-face {
            border-radius: 9999px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        (() => {
            const uploadForm = document.getElementById('avatar_upload_form');
            const fileInput = document.getElementById('avatar_input');
            const hiddenCroppedInput = document.getElementById('avatar_cropped');
            const modal = document.getElementById('avatar_crop_modal');
            const image = document.getElementById('avatar_crop_image');
            const cancelCropButton = document.getElementById('cancel_crop');
            const applyCropButton = document.getElementById('apply_crop');
            const zoomSlider = document.getElementById('avatar_zoom');
            const cropStatus = document.getElementById('crop_status');
            const profilePreview = document.getElementById('profile_avatar_preview');
            let cropper = null;
            let objectUrl = null;

            const cleanupCropper = () => {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
            };

            const openCropper = () => {
                const selectedFile = fileInput?.files?.[0];

                if (!selectedFile) {
                    cropStatus.textContent = 'Select an image first, then open the cropper.';
                    return;
                }

                cleanupCropper();

                objectUrl = URL.createObjectURL(selectedFile);
                image.src = objectUrl;

                image.onload = () => {
                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                        guides: false,
                        movable: true,
                        zoomable: true,
                        rotatable: false,
                        scalable: false,
                    });

                    zoomSlider.value = '0';
                };

                modal.showModal();
            };

            const closeCropper = () => {
                modal.close();
                cleanupCropper();
            };

            fileInput?.addEventListener('change', () => {
                hiddenCroppedInput.value = '';
                cropStatus.textContent = 'Image selected. Adjust the round crop, then click Confirm.';
                openCropper();
            });

            cancelCropButton?.addEventListener('click', closeCropper);

            zoomSlider?.addEventListener('input', () => {
                if (!cropper) {
                    return;
                }

                cropper.zoomTo(Number(zoomSlider.value));
            });

            applyCropButton?.addEventListener('click', () => {
                if (!cropper) {
                    return;
                }

                const canvas = cropper.getCroppedCanvas({
                    width: 512,
                    height: 512,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                    fillColor: '#ffffff'
                });

                hiddenCroppedInput.value = canvas.toDataURL('image/png', 0.92);
                cropStatus.textContent = 'Uploading your new profile picture...';

                if (profilePreview) {
                    profilePreview.src = hiddenCroppedInput.value;
                }

                closeCropper();
                uploadForm?.requestSubmit();
            });

            modal?.addEventListener('close', cleanupCropper);
        })();
    </script>
</x-dashboardlayout>
