@props([
    'name' => '',
    'type' => 'bank',
    'class' => 'w-10 h-10 rounded-2xl'
])

@php
$normalized = strtolower(trim($name));
$logoType = 'generic';

if (str_contains($normalized, 'bca') && !str_contains($normalized, 'blu')) {
    $logoType = 'bca';
} elseif (str_contains($normalized, 'blu')) {
    $logoType = 'blu';
} elseif (str_contains($normalized, 'mandiri')) {
    $logoType = 'mandiri';
} elseif (str_contains($normalized, 'bri')) {
    $logoType = 'bri';
} elseif (str_contains($normalized, 'bni')) {
    $logoType = 'bni';
} elseif (str_contains($normalized, 'jago')) {
    $logoType = 'jago';
} elseif (str_contains($normalized, 'jenius') || str_contains($normalized, 'btpn')) {
    $logoType = 'jenius';
} elseif (str_contains($normalized, 'seabank') || str_contains($normalized, 'sea bank')) {
    $logoType = 'seabank';
} elseif (str_contains($normalized, 'bsi') || str_contains($normalized, 'syariah')) {
    $logoType = 'bsi';
} elseif (str_contains($normalized, 'permata')) {
    $logoType = 'permata';
} elseif (str_contains($normalized, 'cimb')) {
    $logoType = 'cimb';
} elseif (str_contains($normalized, 'mega')) {
    $logoType = 'mega';
} elseif (str_contains($normalized, 'btn')) {
    $logoType = 'btn';
} elseif (str_contains($normalized, 'gopay') || str_contains($normalized, 'go-pay') || str_contains($normalized, 'gojek')) {
    $logoType = 'gopay';
} elseif (str_contains($normalized, 'ovo')) {
    $logoType = 'ovo';
} elseif (str_contains($normalized, 'dana')) {
    $logoType = 'dana';
} elseif (str_contains($normalized, 'shopee') || str_contains($normalized, 'spay')) {
    $logoType = 'shopeepay';
} elseif (str_contains($normalized, 'linkaja') || str_contains($normalized, 'link aja')) {
    $logoType = 'linkaja';
} elseif (str_contains($normalized, 'paypal')) {
    $logoType = 'paypal';
} elseif (str_contains($normalized, 'wise')) {
    $logoType = 'wise';
} elseif (str_contains($normalized, 'bibit')) {
    $logoType = 'bibit';
} elseif (str_contains($normalized, 'stockbit')) {
    $logoType = 'stockbit';
} elseif (str_contains($normalized, 'cash') || str_contains($normalized, 'dompet') || str_contains($normalized, 'tunai') || $type === 'cash') {
    $logoType = 'cash';
} elseif ($type === 'ewallet') {
    $logoType = 'ewallet_generic';
}
@endphp

<div class="{{ $class }} flex items-center justify-center shrink-0 shadow-xs overflow-hidden p-1.5 transition-all select-none {{ 
    $logoType === 'bca' ? 'bg-[#003B70]' : (
    $logoType === 'blu' ? 'bg-[#0060FF]' : (
    $logoType === 'mandiri' ? 'bg-[#002D62]' : (
    $logoType === 'bri' ? 'bg-[#00529C]' : (
    $logoType === 'bni' ? 'bg-[#005E6A]' : (
    $logoType === 'jago' ? 'bg-[#8235F4]' : (
    $logoType === 'jenius' ? 'bg-[#00A3E0]' : (
    $logoType === 'seabank' ? 'bg-[#F26422]' : (
    $logoType === 'bsi' ? 'bg-[#00A39D]' : (
    $logoType === 'permata' ? 'bg-[#004F71]' : (
    $logoType === 'cimb' ? 'bg-[#EE1D23]' : (
    $logoType === 'mega' ? 'bg-[#F7941D]' : (
    $logoType === 'btn' ? 'bg-[#003087]' : (
    $logoType === 'gopay' ? 'bg-[#00AA13]' : (
    $logoType === 'ovo' ? 'bg-[#4C3494]' : (
    $logoType === 'dana' ? 'bg-[#118EEA]' : (
    $logoType === 'shopeepay' ? 'bg-[#EE4D2D]' : (
    $logoType === 'linkaja' ? 'bg-[#ED1C24]' : (
    $logoType === 'paypal' ? 'bg-[#003087]' : (
    $logoType === 'wise' ? 'bg-[#163300]' : (
    $logoType === 'bibit' ? 'bg-[#00C250]' : (
    $logoType === 'stockbit' ? 'bg-[#00C853]' : (
    $logoType === 'cash' ? 'bg-[#F59E0B]' : 'bg-slate-900'
    ))))))))))))))))))))))
}}">
    @if($logoType === 'bca')
        <!-- BCA Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="29" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="28" letter-spacing="-1" text-anchor="middle">BCA</text>
        </svg>
    @elseif($logoType === 'blu')
        <!-- Blu by BCA Digital -->
        <div class="flex items-center justify-center font-black text-white text-xs tracking-tighter">
            <span class="text-white">blu</span><span class="text-[#D4F66C] text-[10px] ml-0.5">●</span>
        </div>
    @elseif($logoType === 'mandiri')
        <!-- Bank Mandiri Real Brand Logo with Yellow Wave -->
        <svg viewBox="0 0 100 50" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M72 12C78 12 84 15 88 20C82 17 76 18 72 20C68 22 66 26 62 26C58 26 55 24 53 22C57 20 64 21 68 18C71 15 72 12 72 12Z" fill="#FDB813"/>
            <text x="44" y="34" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="800" font-size="19" letter-spacing="-0.5" text-anchor="middle">mandırı</text>
        </svg>
    @elseif($logoType === 'bri')
        <!-- BRI Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="44" y="29" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="27" letter-spacing="-0.5" text-anchor="middle">BRI</text>
            <circle cx="82" cy="22" r="5" fill="#F37021"/>
        </svg>
    @elseif($logoType === 'bni')
        <!-- BNI Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="36" y="29" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="26" letter-spacing="-0.5" text-anchor="middle">BNI</text>
            <rect x="68" y="12" width="22" height="18" rx="4" fill="#F15A24"/>
            <text x="79" y="26" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="13" text-anchor="middle">46</text>
        </svg>
    @elseif($logoType === 'jago')
        <!-- Bank Jago Real Brand Logo -->
        <div class="flex items-center justify-center font-black text-white text-xs tracking-tight">
            <span>jago</span><span class="text-[#FFD000] text-sm ml-0.5">✦</span>
        </div>
    @elseif($logoType === 'jenius')
        <!-- Jenius by BTPN -->
        <div class="flex items-center justify-center font-black text-white text-xs tracking-tight">
            <span>jenius</span><span class="text-[#F37021] text-[10px] ml-0.5">●</span>
        </div>
    @elseif($logoType === 'seabank')
        <!-- SeaBank Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 26 C15 16, 25 14, 30 20 C35 26, 45 24, 45 14" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round"/>
            <text x="65" y="27" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="17" letter-spacing="-0.5" text-anchor="middle">Sea</text>
        </svg>
    @elseif($logoType === 'bsi')
        <!-- BSI (Bank Syariah Indonesia) Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="18,10 22,17 30,17 24,22 26,29 20,24 14,29 16,22 10,17 18,17" fill="#F1A900"/>
            <text x="64" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="24" letter-spacing="0.5" text-anchor="middle">BSI</text>
        </svg>
    @elseif($logoType === 'permata')
        <!-- Permata Bank Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="20,12 28,12 34,20 28,28 20,28 14,20" fill="#84BD00"/>
            <text x="64" y="27" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="800" font-size="16" letter-spacing="-0.5" text-anchor="middle">Permata</text>
        </svg>
    @elseif($logoType === 'cimb')
        <!-- CIMB Niaga Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="12,12 30,20 12,28" fill="#FFFFFF"/>
            <text x="65" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="22" letter-spacing="-0.5" text-anchor="middle">CIMB</text>
        </svg>
    @elseif($logoType === 'mega')
        <!-- Bank Mega -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="22" letter-spacing="0.5" text-anchor="middle">MEGA</text>
        </svg>
    @elseif($logoType === 'btn')
        <!-- Bank BTN -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="24" letter-spacing="0.5" text-anchor="middle">BTN</text>
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
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="26" letter-spacing="1.5" text-anchor="middle">OVO</text>
        </svg>
    @elseif($logoType === 'dana')
        <!-- DANA Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="28" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="26" letter-spacing="0.5" text-anchor="middle">DANA</text>
        </svg>
    @elseif($logoType === 'shopeepay')
        <!-- ShopeePay Real Brand Logo -->
        <svg viewBox="0 0 100 100" class="w-full h-full p-1" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M30 35 C30 20, 70 20, 70 35" stroke="#FFFFFF" stroke-width="8" stroke-linecap="round"/>
            <rect x="20" y="35" width="60" height="50" rx="10" fill="#FFFFFF"/>
            <path d="M56 50 C54 46, 46 46, 44 50 C42 54, 58 56, 56 62 C54 68, 44 68, 42 64" stroke="#EE4D2D" stroke-width="5" stroke-linecap="round" fill="none"/>
        </svg>
    @elseif($logoType === 'linkaja')
        <!-- LinkAja Real Brand Logo -->
        <div class="flex items-center justify-center font-black text-white text-[11px] tracking-tight">
            <span>Link</span><span class="text-[#FFD000]">Aja!</span>
        </div>
    @elseif($logoType === 'paypal')
        <!-- PayPal Real Brand Logo -->
        <svg viewBox="0 0 100 40" class="w-full h-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50" y="29" fill="#0079C1" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-weight="900" font-size="24" letter-spacing="-1" text-anchor="middle">Pay<tspan fill="#00457C">Pal</tspan></text>
        </svg>
    @elseif($logoType === 'wise')
        <!-- Wise Real Brand Logo -->
        <div class="flex items-center justify-center font-black text-[#9FE870] text-xs tracking-tighter">
            <span>// wise</span>
        </div>
    @elseif($logoType === 'bibit')
        <!-- Bibit Investment Real Brand Logo -->
        <div class="flex items-center justify-center font-black text-white text-xs tracking-tight">
            <span>🌱 bibit</span>
        </div>
    @elseif($logoType === 'stockbit')
        <!-- Stockbit Real Brand Logo -->
        <div class="flex items-center justify-center font-black text-white text-[10px] tracking-tight">
            <span>stockbit</span>
        </div>
    @elseif($logoType === 'cash')
        <!-- Cash / Dompet Fisik -->
        <div class="w-full h-full rounded-xl flex items-center justify-center text-slate-950">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="5" rx="2" fill="#FEF3C7" stroke="#D97706"/>
                <line x1="2" x2="22" y1="10" y2="10" stroke="#D97706"/>
                <circle cx="12" cy="14" r="2" fill="#D97706"/>
            </svg>
        </div>
    @else
        <!-- Generic Wallet / Bank -->
        <div class="w-full h-full rounded-xl bg-slate-900 text-[#C6F24D] flex items-center justify-center font-extrabold text-xs">
            {{ strtoupper(substr($name, 0, 2)) }}
        </div>
    @endif
</div>
