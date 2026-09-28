@props(['placeholder' => ''])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
    <textarea wire:model="notas" rows="2"
        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        placeholder="{{ $placeholder }}"></textarea>
</div>
