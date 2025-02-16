<x-app-layout>
    <x-slot name="header">
        <div class="relative flex items-center justify-center">
            <a href="{{ route('dashboard', ['date' => $prev_month]) }}" 
               class="absolute left-0 px-3 py-1 text-sm bg-gray-700 hover:bg-gray-600 text-white rounded-md">
                &larr; Previous
            </a>
            
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $current_month_name }}
            </h2>
            
            <a href="{{ route('dashboard', ['date' => $next_month]) }}" 
               class="absolute right-0 px-3 py-1 text-sm bg-gray-700 hover:bg-gray-600 text-white rounded-md">
                Next &rarr;
            </a>
        </div>
    </x-slot>

    <div class="bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:weight-chart :chartData="$chartData" />
        </div>
    </div>

    <div class="flex-1 overflow-hidden">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-full">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div id="days-container">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-1">
                                @foreach ($days as $day)
                                    <livewire:day-entry :day="$day" :key="$day['date']" />
                                @endforeach
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
