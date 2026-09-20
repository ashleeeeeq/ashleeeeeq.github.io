@props([
    'name' => 'contact_number',
    'value' => '',
    'required' => false,
])

@php
    $dialCodes = app(\App\Services\PhoneDialCodeService::class)->getDialCodes();
    $storedValue = old($name, $value ?? '');

    $codeName = str_replace('[contact_number]', '[dial_code]', $name);
    if ($codeName === $name) {
        $codeName = $name . '_code';
    }
    $storedCode = old(str_replace(['[', ']'], ['.', ''], $codeName), '');

    $detectedCode = '+63';
    $numberValue = $storedValue;

    if ($storedCode !== '') {
        $detectedCode = $storedCode;
    } elseif ($storedValue !== '') {
        foreach ($dialCodes as $dc) {
            if (str_starts_with($storedValue, $dc['code'])) {
                $detectedCode = $dc['code'];
                $numberValue = substr($storedValue, strlen($dc['code']));
                break;
            }
        }
        if (str_starts_with($storedValue, '0')) {
            $detectedCode = '+63';
            $numberValue = ltrim($storedValue, '0');
        }
    }
@endphp

<div class="flex gap-2 justify-between max-xs:flex-wrap w-full" style="font-family: var(--font-body1);">
    <div class="relative phone-dial-code">
        <select name="{{ $codeName }}" data-choices="true"
            class="w-full shrink-0 px-4 py-2.5 text-sm rounded-xl border appearance-none focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 cursor-pointer"
            style="background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
            @foreach ($dialCodes as $dc)
                <option value="{{ $dc['code'] }}" style="background: var(--color-white); color: var(--color-primary1);"
                    @selected($detectedCode === $dc['code'])>
                    {{ $dc['iso'] }} {{ $dc['code'] }}
                </option>
            @endforeach
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-3 h-3 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5">
                </path>
            </svg>
        </div>
    </div>

    <input type="tel" inputmode="numeric" name="{{ $name }}"
        class="flex-1 px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 text-primary1 phone-number-input"
        style="background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
        value="{{ $numberValue }}" placeholder="9xx xxx xxxx" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
        {{ $required ? 'required' : '' }}>
</div>
<span class="{{ request()->is('register/*') ? 'text-primary1/60' : 'text-warning' }} text-xs">
    +63 dial codes automatically remove 0 from the beginning of the number.
</span>
<style>
    .phone-dial-code .choices {
        min-width: 100px !important;
    }
</style>

<script>
    (function() {
        const container = document.currentScript.parentElement;
        const dialSelect = container.querySelector('select[name="{{ $codeName }}"]');
        const numberInput = container.querySelector('input[name="{{ $name }}"]');

        function stripLeadingZero() {
            if (!dialSelect || !numberInput) return;
            const dialCode = dialSelect.value;
            if (dialCode === '+63' && numberInput.value.startsWith('0')) {
                numberInput.value = numberInput.value.replace(/^0+/, '');
            }
        }

        if (dialSelect) {
            dialSelect.addEventListener('change', stripLeadingZero);
        }
        if (numberInput) {
            numberInput.addEventListener('input', stripLeadingZero);
            numberInput.addEventListener('blur', stripLeadingZero);
        }
    })();
</script>

<x-forms.errors name="{{ $name }}" />
