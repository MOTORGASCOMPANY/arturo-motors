<div class="mt-4">
    <x-label for="rol" value="{{ __('Rol') }}" />
    <select  id="rol" name="rol" class="bg-gray-50 mx-2 border-indigo-500 rounded-md outline-none ml-1 block w-full " wire:model="rol">
        <option value="">Seleccione</option>
        @foreach ($roles as $item)
        <option value="{{ $item->name }}">{{ $item->name }}</option>
        @endforeach              
    </select>
    <x-input-error for="rol"/>
</div>
