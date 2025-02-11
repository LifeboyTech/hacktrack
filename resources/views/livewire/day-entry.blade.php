<div>
    <tr>
        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($date)->format('D j') }}</td>
        <td class="border px-4 py-2">
            <input 
                type="number" 
                step="0.1"
                wire:model="weight"
                wire:change="updateField('weight')"
                class="w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
            />
        </td>
        <td class="border px-4 py-2">{{ $trend ?? '' }}</td>
        <td class="border px-4 py-2">{{ $variation ?? '' }}</td>
        <td class="border px-4 py-2">
            <input 
                type="number"
                wire:model="exercise_rung"
                wire:change="updateField('exercise_rung')"
                class="w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
            />
        </td>
        <td class="border px-4 py-2">
            <input 
                type="text"
                wire:model="notes"
                wire:change="updateField('notes')"
                class="w-full bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
            />
        </td>
    </tr>
</div>
