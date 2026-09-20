@props(['token', 'printRoute' => null, 'regenerateRoute' => null])

<div class="rounded-xl p-4 mt-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-white/60">QR Token</div>
            <div class="font-mono text-sm text-white mt-1">{{ $token ?? 'Not generated' }}</div>
        </div>

        <div class="flex items-center gap-2">
            @if ($printRoute)
                <a href="{{ $printRoute }}" target="_blank"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02] inline-block whitespace-nowrap"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                   onmouseover="this.style.backgroundColor='rgba(59,130,246,0.15)'; this.style.color='#60a5fa'; this.style.borderColor='#60a5fa'"
                   onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'; this.style.borderColor='var(--color-info)'">
                    Show QR
                </a>
            @endif

            @if ($regenerateRoute)
                <form method="POST" action="{{ $regenerateRoute }}" class="inline">
                    @csrf
                    <button type="button"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02] whitespace-nowrap"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-warning); border: 1px solid var(--color-warning);"
                            onmouseover="this.style.backgroundColor='rgba(245,158,11,0.15)'; this.style.color='var(--color-accent1)'; this.style.borderColor='var(--color-accent1)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-warning)'; this.style.borderColor='var(--color-warning)'"
                            onclick="document.getElementById('regenerateQrModal').showModal()">
                        Regenerate QR
                    </button>

                    <x-confirm-dialog id="regenerateQrModal" title="Regenerate QR" message="Regenerate this QR code? This will create a new code.">
                        <button type="submit" class="px-4 py-2 font-semibold rounded-lg transition-all duration-200" style="background-color: var(--color-warning); color: var(--color-primary1);">Regenerate</button>
                    </x-confirm-dialog>
                </form>
            @endif
        </div>
    </div>
</div>
