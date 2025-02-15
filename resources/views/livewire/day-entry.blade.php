<div class="p-4 rounded-lg border {{ \Carbon\Carbon::parse($date)->isToday() ? 'border-gray-500' : 'border-gray-700' }} {{ !$is_editable ? 'bg-slate-700' : 'bg-gray-800' }}"
     data-date="{{ $date }}"
     @if(\Carbon\Carbon::parse($date)->isToday())
         id="current-day"
     @endif>
    <div class="text-lg font-semibold mb-3">
        <i class="fa-regular fa-calendar mr-2 text-gray-400"></i>{{ \Carbon\Carbon::parse($date)->format('D j') }}
    </div>
    
    <div class="space-y-3">
        <div class="flex items-center">
            <span class="w-32 text-gray-400">Weight:</span>
            @if ($is_editable)
                <input 
                    type="text" 
                    wire:model.live.blur="weight"
                    @click="$event.target.select()"
                    class="flex-1 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                />
            @endif
        </div>

        <div class="flex items-center">
            <span class="w-24 sm:w-32 text-gray-400">Trend:</span>
            <span class="flex-1">{{ $trend ?? '' }}</span>
        </div>

        <div class="flex items-center">
            <span class="w-24 sm:w-32 text-gray-400">Variation:</span>
            @if($formattedVariation)
                <span class="{{ $formattedVariation['color'] }} flex-1">
                    {{ $formattedVariation['icon'] }} {{ number_format($formattedVariation['value'], 2) }}
                </span>
            @endif
        </div>

        <div class="flex items-center">
            <span class="w-24 sm:w-32 text-gray-400">Exercise Rung:</span>
            @if ($is_editable)
                <input 
                    type="text"
                    wire:model.live.blur="exercise_rung"
                    @click="$event.target.select()"
                    class="flex-1 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                />
            @endif
        </div>

        <div class="flex items-start">
            <span class="w-24 sm:w-32 text-gray-400">Notes:</span>
            @if ($is_editable)
                <input 
                    type="text"
                    wire:model.live.blur="notes"
                    class="flex-1 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                />
            @endif
        </div>
    </div>
</div>

