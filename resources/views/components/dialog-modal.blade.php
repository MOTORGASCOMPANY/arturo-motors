@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="text-lg bg-slate-700 text-white p-4">
        {{ $title }}
    </div>

    <div class="px-6 py-4">
        <div class="mt-2">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
        {{ $footer }}
    </div>
</x-modal>
