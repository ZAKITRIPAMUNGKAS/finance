@props([
    'name' => '',
    'type' => 'bank',
    'class' => 'w-10 h-10 rounded-2xl'
])

@php
$normalized = strtolower($name);
$logoType = 'generic';

if (str_contains($normalized, 'bca')) {
    $logoType = 'bca';
} elseif (str_contains($normalized, 'mandiri')) {
    $logoType = 'mandiri';
} elseif (str_contains($normalized, 'gopay')) {
    $logoType = 'gopay';
} elseif (str_contains($normalized, 'ovo')) {
    $logoType = 'ovo';
} elseif (str_contains($normalized, 'dana')) {
    $logoType = 'dana';
} elseif (str_contains($normalized, 'bri')) {
    $logoType = 'bri';
} elseif (str_contains($normalized, 'bni')) {
    $logoType = 'bni';
} elseif (str_contains($normalized, 'jago')) {
    $logoType = 'jago';
} elseif (str_contains($normalized, 'seabank')) {
    $logoType = 'seabank';
} elseif (str_contains($normalized, 'cash') || str_contains($normalized, 'dompet') || $type === 'cash') {
    $logoType = 'cash';
}
@endphp

<div class="{{ $class }} flex items-center justify-center shrink-0 shadow-sm overflow-hidden p-1.5 {{ 
    $logoType === 'bca' ? 'bg-[#003B70]' : (
    $logoType === 'mandiri' ? 'bg-[#002D62]' : (
    $logoType === 'gopay' ? 'bg-[#00AA13]' : (
    $logoType === 'ovo' ? 'bg-[#4C3494]' : (
    $logoType === 'dana' ? 'bg-[#118EEA]' : (
    $logoType === 'bri' ? 'bg-[#00529C]' : (
    $logoType === 'bni' ? 'bg-[#F15A24]' : (
    $logoType === 'jago' ? 'bg-[#8235F4]' : (
    $logoType === 'seabank' ? 'bg-[#F26422]' : 'bg-slate-900'
    ))))))))
}}">
    @if($logoType === 'bca')
        <!-- BCA Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="28" letter-spacing="-1" text-anchor="middle">BCA</text>
        </svg>
    @elseif($logoType === 'mandiri')
        <!-- Bank Mandiri Real Brand Logo with Yellow Wave -->
        <svg viewBox="0 0 100 50" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M72 12C78 12 84 15 88 20C82 17 76 18 72 20C68 22 66 26 62 26C58 26 55 24 53 22C57 20 64 21 68 18C71 15 72 12 72 12Z" fill="#FDB813"/>
            <text x="44" y="34" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="800" font-size="19" letter-spacing="-0.5" text-anchor="middle">mandırı</text>
        </svg>
    @elseif($logoType === 'gopay')
        <!-- GoPay Real Brand Logo -->
        <svg viewBox="0 0 100 100" class="w-full h-full p-0.5" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="38" stroke="#FFFFFF" stroke-width="12"/>
            <circle cx="50" cy="50" r="14" fill="#FFFFFF"/>
        </svg>
    @elseif($logoType === 'ovo')
        <!-- OVO Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="26" letter-spacing="1" text-anchor="middle">OVO</text>
        </svg>
    @elseif($logoType === 'dana')
        <!-- DANA Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="26" letter-spacing="0.5" text-anchor="middle">DANA</text>
        </svg>
    @elseif($logoType === 'bri')
        <!-- BRI Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="28" letter-spacing="-0.5" text-anchor="middle">BRI</text>
        </svg>
    @elseif($logoType === 'bni')
        <!-- BNI Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="28" letter-spacing="-0.5" text-anchor="middle">BNI</text>
        </svg>
    @elseif($logoType === 'cash')
        <!-- Cash / Dompet Icon -->
        <div class="w-full h-full rounded-xl bg-amber-500 flex items-center justify-center text-slate-950">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="5" rx="2"/>
                <line x1="2" x2="22" y1="10" y2="10"/>
            </svg>
        </div>
    @else
        <!-- Generic Wallet / Bank -->
        <div class="w-full h-full rounded-xl bg-slate-900 text-[#C6F24D] flex items-center justify-center font-extrabold text-xs">
            {{ strtoupper(substr($name, 0, 2)) }}
        </div>
    @endif
</div>
