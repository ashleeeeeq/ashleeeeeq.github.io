@props([
    'name'
])

@if($errors->has($name))
    @foreach($errors->get($name) as $error)
        <p class="error text-red-500">{{ $error }}</p>
    @endforeach
@endif