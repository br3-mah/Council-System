<button {{ $attributes->merge(['type' => 'button', 'class' => 'flex justify-center rounded bg-secondary py-2 px-6 font-medium text-gray hover:bg-opacity-90']) }}>
    {{ $slot }}
</button>
