@props([
    'name' => '',
    'type' => 'bank',
    'class' => 'w-10 h-10 rounded-2xl'
])

@php
$normalized = strtolower(trim($name));
$svgFile = null;
$bgColor = 'bg-white border border-slate-200/80';

if (str_contains($normalized, 'bca') && !str_contains($normalized, 'blu') && !str_contains($normalized, 'digital')) {
    $svgFile = 'vendor/idn-finlogos/bca.svg';
} elseif (str_contains($normalized, 'blu') || str_contains($normalized, 'bca digital')) {
    $svgFile = 'vendor/idn-finlogos/bca-digital.svg';
} elseif (str_contains($normalized, 'mandiri')) {
    $svgFile = 'vendor/idn-finlogos/bank-mandiri.svg';
} elseif (str_contains($normalized, 'bri')) {
    $svgFile = 'vendor/idn-finlogos/bank-rakyat-indonesia.svg';
} elseif (str_contains($normalized, 'bni')) {
    $svgFile = 'vendor/idn-finlogos/bni.svg';
} elseif (str_contains($normalized, 'jago')) {
    $svgFile = 'vendor/idn-finlogos/bank-jago.svg';
} elseif (str_contains($normalized, 'jenius') || str_contains($normalized, 'btpn')) {
    $svgFile = 'vendor/idn-finlogos/jenius.svg';
} elseif (str_contains($normalized, 'seabank') || str_contains($normalized, 'sea bank')) {
    $svgFile = 'vendor/idn-finlogos/seabank.svg';
} elseif (str_contains($normalized, 'bsi') || str_contains($normalized, 'syariah')) {
    $svgFile = 'vendor/idn-finlogos/bank-syariah-indonesia.svg';
} elseif (str_contains($normalized, 'permata')) {
    $svgFile = 'vendor/idn-finlogos/bank-permata.svg';
} elseif (str_contains($normalized, 'cimb')) {
    $svgFile = 'vendor/idn-finlogos/bank-cimb-niaga.svg';
} elseif (str_contains($normalized, 'gopay') || str_contains($normalized, 'go-pay') || str_contains($normalized, 'gojek')) {
    $svgFile = 'vendor/idn-finlogos/gopay.svg';
} elseif (str_contains($normalized, 'ovo')) {
    $svgFile = 'vendor/idn-finlogos/ovo.svg';
} elseif (str_contains($normalized, 'dana')) {
    $svgFile = 'vendor/idn-finlogos/dana.svg';
} elseif (str_contains($normalized, 'shopee') || str_contains($normalized, 'spay')) {
    $svgFile = 'vendor/idn-finlogos/shopeepay.svg';
} elseif (str_contains($normalized, 'linkaja') || str_contains($normalized, 'link aja')) {
    $svgFile = 'vendor/idn-finlogos/linkaja.svg';
} elseif (str_contains($normalized, 'paypal')) {
    $svgFile = 'vendor/idn-finlogos/paypal.svg';
} elseif (str_contains($normalized, 'wise')) {
    $svgFile = 'vendor/idn-finlogos/wise.svg';
}
@endphp

<div class="{{ $class }} {{ $bgColor }} flex items-center justify-center shrink-0 shadow-2xs overflow-hidden p-1.5 transition-all select-none group">
    @if($svgFile && file_exists(public_path($svgFile)))
        <img src="{{ asset($svgFile) }}" alt="{{ $name }}" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
    @elseif($type === 'cash' || str_contains($normalized, 'cash') || str_contains($normalized, 'tunai') || str_contains($normalized, 'dompet'))
        <!-- Dompet Tunai / Cash Official Vector -->
        <svg viewBox="0 0 48 48" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="12" fill="#F59E0B"/>
            <path d="M12 18C12 15.7909 13.7909 14 16 14H32C34.2091 14 36 15.7909 36 18V32C36 34.2091 34.2091 36 32 36H16C13.7909 36 12 34.2091 12 32V18Z" fill="#D97706"/>
            <path d="M10 20C10 17.7909 11.7909 16 14 16H34C36.2091 16 38 17.7909 38 20V32C38 34.2091 36.2091 36 34 36H14C11.7909 36 10 34.2091 10 32V20Z" fill="#FBBF24"/>
            <circle cx="29" cy="26" r="3" fill="#FFFFFF"/>
            <path d="M29 24.5V27.5M27.5 26H30.5" stroke="#D97706" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    @elseif($type === 'investment' || str_contains($normalized, 'bibit') || str_contains($normalized, 'stockbit') || str_contains($normalized, 'invest'))
        <!-- Investasi Generic Official Vector -->
        <svg viewBox="0 0 48 48" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="12" fill="#10B981"/>
            <path d="M14 32L22 24L28 30L34 18" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M28 18H34V24" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    @elseif($type === 'ewallet')
        <!-- E-Wallet Generic Official Vector -->
        <svg viewBox="0 0 48 48" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="12" fill="#6366F1"/>
            <path d="M14 17C14 15.3431 15.3431 14 17 14H31C32.6569 14 34 15.3431 34 17V31C34 32.6569 32.6569 34 31 34H17C15.3431 34 14 32.6569 14 31V17Z" stroke="#FFFFFF" stroke-width="2.5"/>
            <circle cx="24" cy="24" r="3" fill="#FFFFFF"/>
        </svg>
    @else
        <!-- Bank Generic Vector -->
        <svg viewBox="0 0 48 48" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="12" fill="#0F172A"/>
            <path d="M12 20L24 14L36 20V23H12V20Z" fill="#FFFFFF"/>
            <path d="M15 25H18V32H15V25ZM22.5 25H25.5V32H22.5V25ZM30 25H33V32H30V25Z" fill="#C6F24D"/>
            <path d="M12 33H36V35H12V33Z" fill="#FFFFFF"/>
        </svg>
    @endif
</div>
