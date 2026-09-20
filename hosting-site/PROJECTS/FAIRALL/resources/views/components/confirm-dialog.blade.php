@props([
    'id',
    'title',
    'message',
    'boxClass' => '',
    'titleClass' => '',
    'messageClass' => '',
    'actionsClass' => '',
    'cancelLabel' => 'Cancel',
    'cancelButtonClass' => '',
    'cancelButtonStyle' => '',
    'deleteUrl' => null,
    'deleteButtonText' => 'Delete',
])

<dialog id="{{ $id }}" class="fixed inset-0 m-auto rounded-2xl overflow-hidden backdrop:backdrop-blur-sm">  <div class="rounded-2xl p-6 max-w-md mx-auto text-center {{ $boxClass }}" style="background-color: var(--color-white); border: 1px solid rgba(0,0,0,0.08); width: 100%;">
        <div class="flex flex-col items-center gap-3 mb-5 {{ $titleClass }}">
            <h3 class="text-xl font-bold" style="font-family: var(--font-header1); color: var(--color-primary1);">{{ $title }}</h3>
        </div>
        
        <p class="text-sm leading-relaxed mb-6 {{ $messageClass }}" style="color: var(--color-primary1); font-family: var(--font-body1);">
            {!! $message !!}
        </p>

        <div class="flex justify-center gap-3 {{ $actionsClass }}">
            @if ($deleteUrl)
                <form method="POST" action="{{ $deleteUrl }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2"
                        style="font-family: var(--font-body1); background-color: var(--color-danger); color: white;"
                        onmouseover="this.style.backgroundColor='var(--color-danger-dark)'; this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.backgroundColor='var(--color-danger)';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                        </svg>
                        {{ $deleteButtonText }}
                    </button>
                </form>
            @else
                {{ $slot }}
            @endif

            <button type="button"
                    onclick="document.getElementById('{{ $id }}').close()"
                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group {{ $cancelButtonClass }}"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger); {{ $cancelButtonStyle }}"
                    onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ $cancelLabel }}
            </button>
        </div>
    </div>
</dialog>