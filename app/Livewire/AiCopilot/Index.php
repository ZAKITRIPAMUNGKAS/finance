<?php

namespace App\Livewire\AiCopilot;

use App\Services\AiFinancialAdvisorService;
use Livewire\Component;

class Index extends Component
{
    public string $userQuery = '';
    public array $conversation = [];
    public bool $isLoading = false;

    public array $quickPrompts = [
        [
            'icon' => 'shield-check',
            'title' => 'Cek Runway & Dana Darurat',
            'query' => 'Berapa bulan Cash Runway saya jika tidak ada pemasukan baru?',
        ],
        [
            'icon' => 'shopping-bag',
            'title' => 'Simulasi Beli Gadget / Barang',
            'query' => 'Apakah aman kalau saya beli barang seharga 5 juta sekarang?',
        ],
        [
            'icon' => 'file-text',
            'title' => 'Audit Piutang & Invoice Klien',
            'query' => 'Bagaimana status piutang dan invoice klien yang belum lunas?',
        ],
        [
            'icon' => 'pie-chart',
            'title' => 'Analisis Pengeluaran & Burn Rate',
            'query' => 'Analisis pengeluaran dan Fixed Burn Rate bulanan saya.',
        ],
    ];

    public function mount(AiFinancialAdvisorService $advisor)
    {
        // Initial welcome message with full diagnostic overview
        $initialDiagnosis = $advisor->ask(auth()->id(), 'diagnosis');
        $this->conversation[] = [
            'role' => 'assistant',
            'content' => $initialDiagnosis,
            'time' => now()->format('H:i'),
        ];
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
        $result = $advisor->ask($userId, $q);

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
