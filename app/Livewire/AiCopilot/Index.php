<?php

namespace App\Livewire\AiCopilot;

use App\Services\AiFinancialAdvisorService;
use Livewire\Component;

class Index extends Component
{
    public string $userQuery = '';
    public array $conversation = [];
    public bool $isThinking = false;
    public string $customApiKey = '';
    public bool $showApiKeyModal = false;

    public array $quickPrompts = [
        [
            'icon' => 'shield-check',
            'title' => 'Cek Runway & Cadangan',
            'query' => 'Berapa bulan Cash Runway kas saya jika tidak ada pemasukan baru sama sekali?',
        ],
        [
            'icon' => 'shopping-bag',
            'title' => 'Simulasi Beli Gadget / Kamera',
            'query' => 'Apakah aman kalau saya beli barang impian seharga 5 juta sekarang?',
        ],
        [
            'icon' => 'file-text',
            'title' => 'Audit Piutang & Invoice',
            'query' => 'Bagaimana status piutang dan invoice klien yang belum lunas?',
        ],
        [
            'icon' => 'pie-chart',
            'title' => 'Analisis Pengeluaran & Burn Rate',
            'query' => 'Analisis pengeluaran dan Fixed Burn Rate bulanan saya.',
        ],
        [
            'icon' => 'trending-up',
            'title' => 'Cek Pemasukan & Tabungan',
            'query' => 'Berapa total pemasukan dan persentase tabungan bersih saya bulan ini?',
        ],
        [
            'icon' => 'activity',
            'title' => 'Diagnosa Kesehatan Lengkap',
            'query' => 'Berikan diagnosa lengkap dan skor kesehatan finansial saya.',
        ],
    ];

    public function mount(AiFinancialAdvisorService $advisor)
    {
        $this->customApiKey = session('user_gemini_api_key', '');

        // Initial welcome message with full diagnostic overview
        $initialDiagnosis = $advisor->ask(auth()->id(), 'diagnosis');
        $this->conversation[] = [
            'role' => 'assistant',
            'content' => $initialDiagnosis,
            'time' => now()->format('H:i'),
        ];
    }

    public function saveApiKey()
    {
        session(['user_gemini_api_key' => trim($this->customApiKey)]);
        $this->showApiKeyModal = false;
        session()->flash('message', 'API Key Google Gemini berhasil disimpan!');
    }

    public function clearHistory(AiFinancialAdvisorService $advisor)
    {
        $this->conversation = [];
        $initialDiagnosis = $advisor->ask(auth()->id(), 'diagnosis');
        $this->conversation[] = [
            'role' => 'assistant',
            'content' => $initialDiagnosis,
            'time' => now()->format('H:i'),
        ];
        $this->dispatch('scroll-to-bottom');
    }

    public function selectPrompt(string $prompt)
    {
        $this->userQuery = $prompt;
        $this->sendQuery();
    }

    public function sendQuery()
    {
        $q = trim($this->userQuery);
        if (empty($q)) {
            return;
        }

        $userId = auth()->id();

        // 1. Append user question
        $this->conversation[] = [
            'role' => 'user',
            'content' => $q,
            'time' => now()->format('H:i'),
        ];

        $this->userQuery = '';

        // 2. Generate AI Advisor Response
        $advisor = app(AiFinancialAdvisorService::class);
        $result = $advisor->ask($userId, $q, $this->conversation);

        // 3. Append assistant response
        $this->conversation[] = [
            'role' => 'assistant',
            'content' => $result,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('scroll-to-bottom');
    }

    public function render(AiFinancialAdvisorService $advisor)
    {
        $snapshot = $advisor->getSnapshot(auth()->id());

        return view('livewire.ai-copilot.index', compact('snapshot'))
            ->layout('components.layouts.app', [
                'title' => 'AI Financial Copilot — PortoFinance'
            ]);
    }
}
