<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Your Request - NU Registrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --nu-header-height: 60px;
            --nu-footer-height: 48px;
        }
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1 0 auto;
            padding-top: calc(var(--nu-header-height) + 1rem + 4px);
            padding-bottom: calc(var(--nu-footer-height) + 1rem);
        }
        .nu-header {
            background: #2c2f92;
            color: #fff;
            height: var(--nu-header-height);
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: 600;
            font-size: 1.15rem;
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 1050;
        }
        .nu-header-bar {
            position: fixed;
            top: var(--nu-header-height);
            left: 0; right: 0;
            height: 4px;
            background: #ffd600;
            z-index: 1040;
        }
        .nu-footer {
            background: #2c2f92;
            color: #fff;
            height: var(--nu-footer-height);
            padding: 0.4rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            font-family: 'Segoe UI', Arial, sans-serif;
            position: fixed; bottom: 0; left: 0; right: 0;
            z-index: 1050;
        }
        @media (max-width: 768px) {
            .nu-footer { flex-direction: column; align-items: flex-start; gap: .5rem; }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <header class="nu-header">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Shield" class="nu-shield" style="height:1.8rem;">
            <span class="fw-bold fs-5">NU LIPA</span>
        </div>
        <span>Welcome to <b>NU Lipa</b></span>
    </header>
    <div class="nu-header-bar"></div>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="container py-5">
            <h3 class="text-center mb-4 text-primary">Track Your Request</h3>

            {{-- Flash Messages --}}
            @foreach (['success','error','info'] as $msg)
                @if(session($msg))
                    <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} text-center">
                        {{ session($msg) }}
                    </div>
                @endif
            @endforeach

            {{-- Timeline --}}
            @php
                $steps = [
                    ['label'=>'Start','icon'=>'📝','step'=>'start'],
                    ['label'=>'Payment','icon'=>'💸','step'=>'payment'],
                    ['label'=>'Window','icon'=>'🪟','step'=>'window'],
                    ['label'=>'Processing','icon'=>'⚙️','step'=>'processing'],
                    ['label'=>'Release','icon'=>'📦','step'=>'release'],
                    ['label'=>'Completed','icon'=>'✅','step'=>'completed'],
                ];
                $currentIndex = array_search($onsiteRequest->current_step, array_column($steps,'step'));
                $ticketNumber = 'ticket-no:' . $onsiteRequest->created_at->format('Ymd') . '-i' . $onsiteRequest->id;
            @endphp

            <div class="d-flex justify-content-center mb-5">
                <div class="d-flex align-items-center justify-content-between w-100" style="max-width:1100px; padding:0 30px;">
                    @foreach ($steps as $index=>$step)
                        @if ($index>0)
                            <div style="flex:1; height:4px; background:#ced4da; margin:0 -10px; z-index:0;"></div>
                        @endif
                        <div class="text-center position-relative" style="width:90px; z-index:1;">
                            <div class="mb-2 d-flex justify-content-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                     style="width:55px; height:55px;
                                            background-color: {{ $index < $currentIndex ? '#198754' : ($index === $currentIndex ? '#0d6efd' : '#dee2e6') }};
                                            color:#fff; font-size:24px;">
                                    {{ $step['icon'] }}
                                </div>
                            </div>
                            <div class="fw-semibold small {{ $index < $currentIndex ? 'text-success' : ($index === $currentIndex ? 'text-primary' : 'text-muted') }}">
                                {{ $step['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Step-specific Details --}}
            <div class="text-center">
                @include('onsite.partials.step-details', ['onsiteRequest' => $onsiteRequest, 'ticketNumber' => $ticketNumber])
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="nu-footer">
        <div>
            <div class="fw-bold">NU ONLINE SERVICES</div>
            <div>All Rights Reserved. National University</div>
        </div>
        <div class="text-end">
            CONTACT US<br>
            <span class="fw-normal">NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if ($onsiteRequest->current_step === 'completed')
    <script>
        let count=3; const countdownEl=document.getElementById('countdown');
        const interval=setInterval(()=>{count--; if(count>0){countdownEl.textContent=`Redirecting to login in ${count}...`}else{clearInterval(interval);window.location.href="{{ route('login') }}" }},1000);
    </script>
    @endif
</body>
</html>
