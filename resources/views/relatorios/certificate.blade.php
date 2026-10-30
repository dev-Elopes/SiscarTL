<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laudo de Procedência e Prontuário de Manutenções - {{ $vehicle->nickname }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                font-size: 11pt !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-[#F9FAFB] text-gray-800 font-sans p-4 sm:p-10">

    <!-- Top Action Bar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white px-4 py-2.5 rounded-xl shadow-sm border border-gray-200 transition-colors">
            &larr; Voltar para Relatórios
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-semibold shadow-sm transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            <span>Imprimir / Salvar em PDF</span>
        </button>
    </div>

    <!-- Official Document Container (Clean SaaS & Printable A4) -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 p-8 sm:p-12 overflow-hidden">
        
        <!-- Document Header with Crimson Accent -->
        <div class="flex items-start justify-between border-b border-gray-200 pb-8 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#E11D48] text-white flex items-center justify-center font-bold text-xl shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-[#E11D48]">Certificado de Histórico Veicular</span>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">PRONTUÁRIO TÉCNICO DE REVISÕES</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Comprovação de manutenções preventivas e procedência mecânica</p>
                </div>
            </div>

            <!-- Selo Oficial -->
            <div class="flex items-center gap-4 text-right">
                <div class="inline-block p-3 border border-gray-200 rounded-xl bg-gray-50 text-center">
                    <span class="block text-[10px] font-semibold uppercase text-[#E11D48] tracking-wider">Histórico Emitido</span>
                    <span class="text-xs font-bold text-gray-900">{{ date('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Dados Cadastrais do Veículo -->
        <div class="bg-gray-50/70 rounded-xl p-6 border border-gray-200/80 mb-8">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-4">Dados de Identificação do Veículo</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Veículo / Modelo</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Placa do Veículo</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $vehicle->license_plate ?? 'Não informada' }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Ano Fab. / Modelo</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $vehicle->year_manufacture }} / {{ $vehicle->year_model }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Quilometragem Verificada</span>
                    <span class="font-bold text-[#E11D48] text-sm">{{ number_format($vehicle->current_mileage, 0, ',', '.') }} km</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Combustível</span>
                    <span class="font-medium text-gray-800">{{ $vehicle->fuel_type_label }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Cor</span>
                    <span class="font-medium text-gray-800">{{ $vehicle->color ?? 'Não informada' }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Câmbio</span>
                    <span class="font-medium text-gray-800">{{ ucfirst($vehicle->transmission_type ?? 'Padrão') }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-medium">Chassi</span>
                    <span class="text-gray-800 font-medium">{{ $vehicle->chassis ?? 'Protegido' }}</span>
                </div>
            </div>
        </div>

        <!-- Histórico Cronológico de Manutenções -->
        <div class="mb-8">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-4">Linha do Tempo de Intervenções & Revisões Realizadas</h2>
            
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-semibold tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4">Data</th>
                            <th class="py-3 px-4">Quilometragem</th>
                            <th class="py-3 px-4">Serviço / Peças</th>
                            <th class="py-3 px-4">Oficina</th>
                            <th class="py-3 px-4 text-center">Garantia</th>
                            <th class="py-3 px-4 text-right">Investimento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($vehicle->maintenanceRecords as $record)
                            <tr>
                                <td class="py-3 px-4 font-semibold whitespace-nowrap text-gray-900">{{ $record->performed_at->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-medium whitespace-nowrap">{{ number_format($record->mileage, 0, ',', '.') }} km</td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-gray-900 block">{{ $record->title }}</span>
                                    <span class="text-[11px] text-gray-500">{{ $record->type_label }} &bull; {{ $record->category_label }}</span>
                                    @if ($record->oil_viscosity)
                                        <span class="block text-[11px] text-[#E11D48] font-medium">Óleo: {{ $record->oil_brand }} {{ $record->oil_viscosity }}</span>
                                    @endif
                                    @if ($record->items->isNotEmpty())
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            Peças: {{ $record->items->pluck('item_name')->join(', ') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $record->workshop_name ?? 'Particular' }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap text-gray-500">
                                    {{ $record->warranty_days ? $record->warranty_days . ' dias' : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-900 whitespace-nowrap">
                                    R$ {{ number_format($record->total_cost, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">Nenhum registro de manutenção arquivado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold text-gray-900">
                        <tr>
                            <td colspan="5" class="py-3 px-4 text-right uppercase text-[10px] tracking-wider text-gray-500">Total Acumulado em Revisões:</td>
                            <td class="py-3 px-4 text-right font-bold text-sm text-gray-900">
                                R$ {{ number_format($vehicle->maintenanceRecords->sum('total_cost'), 2, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Termo de Procedência & Assinaturas -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-xs text-gray-500 leading-relaxed text-justify mb-8">
                Este laudo reúne o histórico técnico de manutenções registradas pelo proprietário através da plataforma <strong>AutoCare</strong>. As informações acima atestam os cuidados preventivos tomados com o veículo, agregando transparência e valor de mercado para fins de comprovação e transferência de titularidade.
            </p>

            <div class="grid grid-cols-2 gap-12 text-center text-xs">
                <div>
                    <div class="border-b border-gray-300 pb-1 mb-2"></div>
                    <span class="font-semibold text-gray-800 block">Proprietário / Vendedor</span>
                    <span class="text-gray-400 text-xs">{{ $vehicle->user->name }}</span>
                </div>

                <div>
                    <div class="border-b border-gray-300 pb-1 mb-2"></div>
                    <span class="font-semibold text-gray-800 block">Comprador / Novo Proprietário</span>
                    <span class="text-gray-400 text-xs">Ciente do Histórico</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
