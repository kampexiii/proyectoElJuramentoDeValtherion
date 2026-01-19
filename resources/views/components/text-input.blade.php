@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-zinc-950 text-white border-zinc-700 placeholder:text-zinc-500 focus:border-zinc-400 focus:ring-zinc-400 rounded-md shadow-sm']) }}>
