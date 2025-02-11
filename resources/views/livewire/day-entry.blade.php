<tr>
    <td class="border px-4 py-2">
        {{ \Carbon\Carbon::parse($date)->format('D j') }}
    </td>
    <td class="border px-4 py-2">
        <input 
            type="text" 
            wire:model.live.blur="weight"
            class="w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
        />
    </td>
    <td class="border px-4 py-2">{{ $trend ?? '' }}</td>
    <td class="border px-4 py-2">{{ $variation ?? '' }}</td>
    <td class="border px-4 py-2">
        <input 
            type="text"
            wire:model.live.blur="exercise_rung"
            class="w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
        />
    </td>
    <td class="border px-4 py-2">
        <input 
            type="text"
            wire:model.live.blur="notes"
            class="w-full bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
        />
    </td>
</tr>

