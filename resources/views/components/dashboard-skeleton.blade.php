<div class="space-y-6 animate-pulse" aria-hidden="true">
    
    <!-- SKELETON HERO 2-COL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- SKELETON HERO DEBIT CARD (7 COLS) -->
        <div class="lg:col-span-7 bg-slate-900 rounded-3xl p-6 sm:p-7 flex flex-col justify-between min-h-[220px] border border-slate-800">
            <div class="flex items-center justify-between">
                <div class="h-3.5 w-24 bg-slate-800 rounded-md"></div>
                <div class="h-4 w-6 bg-slate-800 rounded-md"></div>
            </div>

            <div class="my-4 space-y-3">
                <div class="h-3 w-40 bg-slate-800 rounded-md"></div>
                <div class="h-9 w-64 bg-slate-700/80 rounded-xl"></div>
                <div class="h-6 w-52 bg-slate-800 rounded-full mt-2"></div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                <div class="space-y-1">
                    <div class="h-2.5 w-16 bg-slate-800 rounded"></div>
                    <div class="h-3.5 w-28 bg-slate-800 rounded"></div>
                </div>
                <div class="space-y-1 text-right">
                    <div class="h-2.5 w-20 bg-slate-800 rounded ml-auto"></div>
                    <div class="h-3.5 w-32 bg-slate-800 rounded ml-auto"></div>
                </div>
            </div>
        </div>

        <!-- SKELETON ACTIONS (5 COLS) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/70 rounded-3xl p-6 flex flex-col justify-between space-y-5">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="h-3 w-32 bg-slate-200 rounded"></div>
                    <div class="h-2.5 w-20 bg-slate-100 rounded"></div>
                </div>

                <div class="grid grid-cols-5 gap-2 text-center">
                    @for($i = 0; $i < 5; $i++)
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-11 sm:w-12 h-11 sm:h-12 rounded-2xl bg-slate-100"></div>
                        <div class="h-2.5 w-10 bg-slate-100 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <div class="h-3 w-28 bg-slate-100 rounded"></div>
                <div class="h-3 w-20 bg-slate-100 rounded"></div>
            </div>
        </div>

    </div>

    <!-- SKELETON 4 METRIC STATS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @for($i = 0; $i < 4; $i++)
        <div class="bg-white border border-slate-200/70 rounded-2xl p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between">
                <div class="h-2.5 w-24 bg-slate-200 rounded"></div>
                <div class="w-7 h-7 bg-slate-100 rounded-xl"></div>
            </div>
            <div class="h-6 w-32 bg-slate-200 rounded-lg"></div>
            <div class="h-2.5 w-20 bg-slate-100 rounded"></div>
        </div>
        @endfor
    </div>

    <!-- SKELETON BOTTOM 2-COL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- SKELETON TRANSACTIONS LIST (7 COLS) -->
        <div class="lg:col-span-7 bg-white border border-slate-200/70 rounded-3xl p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="h-4 w-36 bg-slate-200 rounded"></div>
                <div class="h-3 w-16 bg-slate-100 rounded"></div>
            </div>

            <div class="space-y-3">
                @for($i = 0; $i < 4; $i++)
                <div class="flex items-center justify-between p-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-slate-100 rounded-xl shrink-0"></div>
                        <div class="space-y-1.5">
                            <div class="h-3.5 w-32 bg-slate-200 rounded"></div>
                            <div class="h-2.5 w-20 bg-slate-100 rounded"></div>
                        </div>
                    </div>
                    <div class="h-4 w-24 bg-slate-200 rounded"></div>
                </div>
                @endfor
            </div>
        </div>

        <!-- SKELETON REKENING & CASH (5 COLS) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/70 rounded-3xl p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="h-4 w-32 bg-slate-200 rounded"></div>
                <div class="h-3 w-20 bg-slate-100 rounded"></div>
            </div>

            <div class="space-y-3">
                @for($i = 0; $i < 3; $i++)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-slate-200 rounded-lg"></div>
                        <div class="space-y-1">
                            <div class="h-3 w-20 bg-slate-200 rounded"></div>
                            <div class="h-2 w-14 bg-slate-100 rounded"></div>
                        </div>
                    </div>
                    <div class="h-3.5 w-24 bg-slate-200 rounded"></div>
                </div>
                @endfor
            </div>
        </div>

    </div>

</div>
