<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ml-0 w-100 items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-lg font-bold hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
