@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-#f97316-500 dark:focus:border-#f97316-600 focus:ring-#f97316-500 dark:focus:ring-#f97316-600 rounded-md shadow-sm']) }}>
