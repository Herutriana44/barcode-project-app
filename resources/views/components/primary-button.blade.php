<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-egg-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-egg-700 focus:bg-egg-700 active:bg-egg-800 focus:outline-none focus:ring-2 focus:ring-egg-500 focus:ring-offset-2 transition ease-in-out duration-150 lg:px-8 lg:py-3.5 lg:text-sm lg:min-h-[3rem]']) }}>
    {{ $slot }}
</button>
