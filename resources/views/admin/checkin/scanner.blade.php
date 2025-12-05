@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Check-in por QR Code</h1>
            <h2 class="text-xl font-semibold text-gray-600 mb-6">{{ $evento->nome }}</h2>

            {{-- Onde a câmera será renderizada --}}
            <div id="reader" class="w-full max-w-sm mx-auto border-4 border-gray-300 rounded-lg overflow-hidden"></div>
            
            {{-- Feedback para o usuário --}}
            <div id="result" class="mt-6 text-center text-lg font-semibold">
                <p class="text-gray-500">Aponte a câmera para o QR Code do participante.</p>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
@push('scripts')
{{-- Biblioteca para ler QR Code --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const resultDiv = document.getElementById('result');
    const eventoId = "{{ $evento->id }}";
    let lastScanTime = 0;
    let isProcessing = false;

    function onScanSuccess(decodedText, decodedResult) {
        const now = Date.now();
        // Prevenir múltiplos scans do mesmo código em um curto intervalo
        if (isProcessing || (now - lastScanTime < 5000)) {
            return;
        }

        isProcessing = true;
        lastScanTime = now;
        
        // `decodedText` contém o ID da inscrição
        const inscricaoId = decodedText;

        resultDiv.innerHTML = `<p class="text-blue-600">Verificando inscrição...</p>`;

        fetch('/api/checkin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                inscricao_id: inscricaoId,
                evento_id: eventoId 
            })
        })
        .then(response => {
            if (!response.ok) {
                // Se a resposta não for OK, tenta ler como JSON para pegar a mensagem de erro
                return response.json().then(err => {
                    throw new Error(err.message || 'Erro de rede ou servidor.');
                });
            }
            return response.json();
        })
        .then(data => {
            let message = '';
            let messageClass = '';

            switch (data.status) {
                case 'success':
                    message = `✅ ${data.message}<br><span class="text-base font-normal">${data.data.participante}</span>`;
                    messageClass = 'text-green-600';
                    playSound('success');
                    break;
                case 'warning':
                    message = `⚠️ ${data.message}<br><span class="text-base font-normal">${data.data.participante}</span>`;
                    messageClass = 'text-yellow-600';
                    playSound('warning');
                    break;
                default: // 'error' e outros
                    message = `❌ ${data.message}`;
                    messageClass = 'text-red-600';
                    playSound('error');
                    break;
            }
            resultDiv.innerHTML = `<p class="${messageClass}">${message}</p>`;
        })
        .catch(error => {
            console.error('Erro no Check-in:', error);
            resultDiv.innerHTML = `<p class="text-red-600">❌ Erro: ${error.message}</p>`;
            playSound('error');
        })
        .finally(() => {
            // Permite um novo scan após o processamento
            setTimeout(() => { isProcessing = false; }, 2000); // um pequeno delay para o usuário ler a msg
        });
    }

    function onScanFailure(error) {
        // não faz nada, apenas continua tentando
    }

    // --- Inicialização do Scanner ---
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { 
            fps: 10, // Frames por segundo
            qrbox: { width: 250, height: 250 }, // Tamanho da caixa de scan
            rememberLastUsedCamera: true, // Lembra a última câmera usada
            supportedScanTypes: [Html5Qrcode.SCAN_TYPE_CAMERA]
        },
        false // verbose
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    // --- Efeitos sonoros para feedback ---
    function playSound(type) {
        // Cria um contexto de áudio (necessário para autoplay em navegadores modernos)
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime); // volume
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        if (type === 'success') {
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(800, audioContext.currentTime + 0.1);
        } else if (type === 'warning') {
            oscillator.type = 'triangle';
            oscillator.frequency.setValueAtTime(400, audioContext.currentTime);
        } else { // error
            oscillator.type = 'square';
            oscillator.frequency.setValueAtTime(200, audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(100, audioContext.currentTime + 0.1);
        }
        
        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.2);
    }
});
</script>
@endpush
