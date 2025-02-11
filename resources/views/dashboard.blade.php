<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard', ['date' => $prev_month]) }}" 
                   class="px-3 py-1 text-sm bg-gray-700 hover:bg-gray-600 rounded-md">
                    &larr; Previous
                </a>
                
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $current_month_name }}
                </h2>
                
                <a href="{{ route('dashboard', ['date' => $next_month]) }}" 
                   class="px-3 py-1 text-sm bg-gray-700 hover:bg-gray-600 rounded-md">
                    Next &rarr;
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Day</th>
                                <th class="px-4 py-2 text-left">Weight</th>
                                <th class="px-4 py-2 text-left">Trend</th>
                                <th class="px-4 py-2 text-left">Variation</th>
                                <th class="px-4 py-2 text-left">Exercise Rung</th>
                                <th class="px-4 py-2 text-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($days as $day)
                                <tr>
                                    <td class="border px-4 py-2">{{ $day['name'] }}</td>
                                    <td class="border px-4 py-2">
                                        <input 
                                            type="number" 
                                            step="0.1"
                                            value="{{ $day['weight'] }}"
                                            data-date="{{ $day['date'] }}"
                                            data-field="weight"
                                            class="day-input w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                                        />
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ $day['trend'] ?? '' }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ $day['variation'] ?? '' }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input 
                                            type="number"
                                            value="{{ $day['exercise_rung'] }}"
                                            data-date="{{ $day['date'] }}"
                                            data-field="exercise_rung"
                                            class="day-input w-24 bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                                        />
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input 
                                            type="text"
                                            value="{{ $day['notes'] }}"
                                            data-date="{{ $day['date'] }}"
                                            data-field="notes"
                                            class="day-input w-full bg-gray-700 text-gray-200 border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
