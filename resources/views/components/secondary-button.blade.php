<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-white border border-egg-300 rounded-md font-semibold text-xs text-egg-800 uppercase tracking-widest shadow-sm hover:bg-egg-50 focus:outline-none focus:ring-2 focus:ring-egg-400 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 lg:px-8 lg:py-3.5 lg:text-sm lg:min-h-[3rem]']) }}>
    {{ $slot }}
</button>
