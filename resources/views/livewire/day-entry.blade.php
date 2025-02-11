<tr>
    <td class="border px-4 py-2 {{ !$is_editable ? 'bg-slate-700' : '' }}">
        {{ \Carbon\Carbon::parse($date)->format('D j') }}
    </td>
    <td class="border px-4 py-2 {{ !$is_editable ? 'bg-slate-700' : '' }}">
        @if ($is_editable)
            <input 
                type="text" 
                wire:model.live.blur="weight"
                @click="$event.target.select()"
                class="w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
            />
        @endif
    </td>
    <td class="border px-4 py-2 {{ !$is_editable ? 'bg-slate-700' : '' }}" wire:model="trend">
        {{ $trend ?? '' }}
    </td>
    <td class="border px-4 py-2 {{ !$is_editable ? 'bg-slate-700' : '' }}" wire:model="variation">
        {{ $variation ?? '' }}
    </td>
    <td class="border px-4 py-2 {{ !$is_editable ? 'bg-slate-700' : '' }}">
        @if ($is_editable)
            <input 
                type="text"
                wire:model.live.blur="exercise_rung"
                @click="$event.target.select()"
                class="w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
            />
        @endif
    </td>
    <td class="border px-4 py-2 {{ !$is_editable ? 'bg-slate-700' : '' }}">
        @if ($is_editable)
            <input 
                type="text"
                wire:model.live.blur="notes"
                class="w-full bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
            />
        @endif
    </td>
</tr>

