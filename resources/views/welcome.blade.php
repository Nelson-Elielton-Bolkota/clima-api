<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open-Meteo | Consulta de Clima</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-white min-h-screen flex items-center justify-center p-4">

    <main class="w-full max-w-lg glass rounded-3xl p-8 shadow-2xl border border-white/10">
        
        <div class="text-center mb-6">
            <span class="inline-block bg-sky-500/10 text-sky-400 text-xs font-semibold px-3 py-1 rounded-full border border-sky-500/20 mb-2">
                Open-Meteo API
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-white">Previsão em Tempo Real</h1>
            <p class="text-slate-400 text-sm mt-1">Consulte as condições meteorológicas de qualquer cidade</p>
        </div>

        <form onsubmit="buscarClima(event)" class="relative mb-4">
            <input 
                type="text" 
                id="inputCidade" 
                placeholder="Ex: Guarapuava, Curitiba, Tóquio..." 
                value="Guarapuava"
                class="w-full bg-slate-900/90 border border-slate-700/80 rounded-2xl pl-5 pr-28 py-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition shadow-inner"
                required
            >
            <button 
                type="submit" 
                class="absolute right-2 top-2 bottom-2 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-semibold px-5 rounded-xl text-sm transition shadow-lg flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Buscar
            </button>
        </form>

        <div class="flex flex-wrap items-center justify-center gap-2 mb-6 text-xs">
            <span class="text-slate-500">Rápido:</span>
            <button onclick="atalhoCidade('Guarapuava')" class="bg-slate-800/80 hover:bg-slate-700 text-slate-300 px-3 py-1 rounded-full border border-slate-700 transition">Guarapuava</button>
            <button onclick="atalhoCidade('Curitiba')" class="bg-slate-800/80 hover:bg-slate-700 text-slate-300 px-3 py-1 rounded-full border border-slate-700 transition">Curitiba</button>
            <button onclick="atalhoCidade('São Paulo')" class="bg-slate-800/80 hover:bg-slate-700 text-slate-300 px-3 py-1 rounded-full border border-slate-700 transition">São Paulo</button>
            <button onclick="atalhoCidade('Londrina')" class="bg-slate-800/80 hover:bg-slate-700 text-slate-300 px-3 py-1 rounded-full border border-slate-700 transition">Londrina</button>
        </div>

        <div id="cardResultado" class="transition-all duration-300">
        </div>

    </main>

    <script>
        function atalhoCidade(nome) {
            document.getElementById('inputCidade').value = nome;
            buscarClima();
        }

        async function buscarClima(event) {
            if (event) event.preventDefault();
            const cidade = document.getElementById('inputCidade').value.trim();
            const card = document.getElementById('cardResultado');

            card.innerHTML = `
                <div class="bg-slate-900/60 rounded-2xl p-8 text-center border border-slate-800 animate-pulse">
                    <div class="inline-block w-8 h-8 border-4 border-sky-400 border-t-transparent rounded-full animate-spin mb-3"></div>
                    <p class="text-slate-400 text-sm">Consultando satélite da Open-Meteo...</p>
                </div>
            `;

            try {
                const res = await fetch(`/api/weather?city=${encodeURIComponent(cidade)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok) {
                    card.innerHTML = `
                        <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-6 text-center">
                            <p class="text-2xl mb-1">⚠️</p>
                            <p class="text-red-400 font-semibold text-sm">${data.error || 'Cidade não encontrada.'}</p>
                            <p class="text-slate-500 text-xs mt-1">Verifique a grafia e tente novamente.</p>
                        </div>
                    `;
                    return;
                }

                const ehDia = data.clima_atual.eh_dia;
                const icone = ehDia ? '☀️' : '🌙';
                const periodoTexto = ehDia ? 'Período Diurno' : 'Período Noturno';
                const badgeCor = ehDia 
                    ? 'bg-amber-500/10 text-amber-300 border-amber-500/20' 
                    : 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20';

                const dataHora = data.clima_atual.atualizado_em.replace('T', ' às ');

                card.innerHTML = `
                    <div class="bg-gradient-to-b from-slate-800/80 to-slate-900/90 rounded-2xl p-6 border border-white/10 shadow-inner">
                        
                        <!-- Topo: Cidade e Badge Dia/Noite -->
                        <div class="flex justify-between items-start gap-4 mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-white leading-tight">${data.localizacao.cidade}</h2>
                                <p class="text-slate-400 text-sm">${data.localizacao.estado} • ${data.localizacao.pais}</p>
                            </div>
                            <span class="text-xs font-medium px-3 py-1 rounded-full border ${badgeCor} flex items-center gap-1.5 shrink-0">
                                <span>${icone}</span> ${periodoTexto}
                            </span>
                        </div>

                        <!-- Centro: Temperatura Principal -->
                        <div class="flex items-center justify-between my-6 bg-slate-950/50 p-5 rounded-2xl border border-white/5">
                            <div>
                                <span class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Temperatura Atual</span>
                                <div class="text-6xl font-extrabold tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-white to-sky-300 mt-1">
                                    ${data.clima_atual.temperatura_c}<span class="text-3xl font-bold text-sky-400">°C</span>
                                </div>
                            </div>
                            <div class="text-6xl select-none filter drop-shadow-lg">
                                ${icone}
                            </div>
                        </div>

                        <!-- Rodapé de Métricas -->
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-slate-900/70 p-3.5 rounded-xl border border-slate-800 flex items-center gap-3">
                                <span class="text-2xl">💨</span>
                                <div>
                                    <p class="text-slate-400 text-xs">Velocidade do Vento</p>
                                    <p class="font-semibold text-white">${data.clima_atual.velocidade_vento_kmh} km/h</p>
                                </div>
                            </div>

                            <div class="bg-slate-900/70 p-3.5 rounded-xl border border-slate-800 flex items-center gap-3">
                                <span class="text-2xl">📍</span>
                                <div>
                                    <p class="text-slate-400 text-xs">Coordenadas</p>
                                    <p class="font-semibold text-white text-xs">${Number(data.localizacao.latitude).toFixed(2)}, ${Number(data.localizacao.longitude).toFixed(2)}</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-center text-slate-500 text-[11px] mt-5">
                            Última leitura sincronizada: ${dataHora}
                        </p>
                    </div>
                `;
            } catch (err) {
                card.innerHTML = `
                    <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-6 text-center">
                        <p class="text-red-400 font-semibold text-sm">Erro ao conectar com a rota /api/weather.</p>
                    </div>
                `;
            }
        }

        buscarClima();
    </script>
</body>
</html>