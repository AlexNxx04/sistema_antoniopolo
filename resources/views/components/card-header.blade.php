<div class="modern-card overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            @if(isset($icon))
                <i class="fas fa-{{ $icon }} text-emerald-500"></i>
            @endif
            {{ $title }}
        </h3>
    </div>
    <div class="p-5">
        {{ $slot }}
    </div>
</div>