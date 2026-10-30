<x-app-layout>

    <!-- Top Navigation Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#E11D48]"></span>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    IPVA, Licenciamento & Taxas Parceladas
                </h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Controle financeiro de tributos, parcelamentos com quitação individual e cronograma fiscal
            </p>
        </div>

        <!-- Filtro Rápido de Veículo -->
        <form method="GET" action="{{ route('obligations.index') }}" class="flex items-center gap-2">
            <select name="vehicle_id" onchange="this.form.submit()" 
                    class="bg-white border border-gray-200 text-xs font-semibold text-gray-800 rounded-xl px-3 py-1.5 focus:outline-none focus:border-[#E11D48] shadow-sm">
                <option value="">Todos os Veículos</option>
                @foreach ($vehicles as $v)
                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                        {{ $v->nickname }} ({{ $v->license_plate ?? 'S/ Placa' }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- 2 Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tributos & Parcelas Pendentes</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">
                R$ {{ number_format($totalPending, 2, ',', '.') }}
            </div>
            <span class="text-xs text-amber-600 font-medium mt-1 block">A vencer ou aguardando liquidação</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Quitado / Liquidado</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-bold text-emerald-600 tracking-tight">
                R$ {{ number_format($totalPaid, 2, ',', '.') }}
            </div>
            <span class="text-xs text-gray-400 mt-1 block">Tributos e parcelas pagas</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Formulário de Cadastro com Parcelamento (4 cols) -->
        <div class="lg:col-span-4">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden sticky top-20">
                <div class="h-1 bg-[#E11D48] w-full"></div>

                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Nova Obrigação Fiscal</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Cadastrar tributo, IPVA ou taxa parcelada</p>
                        </div>
                        <span class="p-1.5 bg-gray-50 text-gray-500 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"/></svg>
                        </span>
                    </div>

                    <form method="POST" action="{{ route('obligations.store') }}" class="space-y-4"
                          x-data="{
                              paymentMode: 'single',
                              installmentsCount: 3,
                              amount: '',
                              get installmentValue() {
                                  const val = parseFloat(this.amount) || 0;
                                  const count = parseInt(this.installmentsCount) || 1;
                                  return (val / count).toFixed(2).replace('.', ',');
                              }
                          }">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Veículo</label>
                            <select name="vehicle_id" required class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                                @foreach ($vehicles as $v)
                                    <option value="{{ $v->id }}">
                                        {{ $v->nickname }} ({{ $v->license_plate ?? 'S/ Placa' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tipo de Taxa / Documento</label>
                            <select name="type" required class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                                <option value="ipva">IPVA</option>
                                <option value="licensing">Licenciamento Anual</option>
                                <option value="insurance">Seguro do Veículo</option>
                                <option value="dpvat">SPVAT / DPVAT</option>
                                <option value="fine">Multa de Trânsito</option>
                                <option value="inspection">Vistoria / Inspeção Veicular</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Descrição / Título</label>
                            <input type="text" name="title" placeholder="Ex: IPVA 2026, Seguro Auto" required
                                   class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ano Referência</label>
                                <input type="number" name="reference_year" value="{{ date('Y') }}" required
                                       class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">1º Vencimento</label>
                                <input type="date" name="due_date" required value="{{ date('Y-m-d') }}"
                                       class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                            </div>
                        </div>

                        <!-- Opção de Parcelamento -->
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-2.5">
                            <label class="block text-xs font-bold text-gray-800">Forma de Pagamento:</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 cursor-pointer text-xs font-medium text-gray-700 hover:border-gray-300">
                                    <input type="radio" name="payment_mode" value="single" x-model="paymentMode" class="text-[#E11D48] focus:ring-0">
                                    <span>À Vista (1x)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 cursor-pointer text-xs font-medium text-gray-700 hover:border-gray-300">
                                    <input type="radio" name="payment_mode" value="installments" x-model="paymentMode" class="text-[#E11D48] focus:ring-0">
                                    <span>Parcelado</span>
                                </label>
                            </div>

                            <div x-show="paymentMode === 'installments'" class="pt-2 border-t border-gray-200 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-xs font-semibold text-gray-700">Qtd. de Parcelas:</label>
                                    <select name="installments_count" x-model="installmentsCount" 
                                            class="bg-white border border-gray-200 rounded-lg px-2.5 py-1 text-xs font-semibold text-gray-800 focus:outline-none focus:border-[#E11D48]">
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                        <option value="4">4x</option>
                                        <option value="5">5x</option>
                                        <option value="6">6x</option>
                                        <option value="10">10x</option>
                                        <option value="12">12x</option>
                                    </select>
                                </div>
                                <div class="text-[11px] text-gray-500 bg-rose-50/50 p-2 rounded-lg border border-rose-100/60 text-[#E11D48]">
                                    O sistema criará as parcelas no banco e você poderá quitar cada uma individualmente (Parcela 1, 2, 3...).
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    <span x-text="paymentMode === 'installments' ? 'Valor Total (R$)' : 'Valor (R$)'"></span>
                                </label>
                                <input type="number" step="0.01" name="amount" x-model="amount" required placeholder="0,00"
                                       class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Situação Inicial</label>
                                <select name="status" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all">
                                    <option value="pending">A Vencer / Pendente</option>
                                    <option value="paid" x-show="paymentMode === 'single'">Já Quitado</option>
                                </select>
                            </div>
                        </div>

                        <!-- Card Informativo do Cálculo das Parcelas -->
                        <div x-show="paymentMode === 'installments' && amount > 0" class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs space-y-1">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Valor de cada parcela:</span>
                                <strong class="text-gray-900 font-bold"><span x-text="installmentsCount"></span>x de R$ <span x-text="installmentValue"></span></strong>
                            </div>
                            <span class="text-[11px] text-gray-400 block">Vencimentos mensais calculados automaticamente.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Observações (Opcional)</label>
                            <textarea name="notes" rows="2" placeholder="Código de barras, chave PIX, notas..."
                                      class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:border-[#E11D48] focus:ring-1 focus:ring-[#E11D48] transition-all"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-xs rounded-xl shadow-sm transition-colors flex items-center justify-center gap-1.5">
                            <span>Gravar Obrigação Fiscal</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Obrigações e Parcelamentos Detalhados (8 cols) -->
        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Tributos e Parcelamentos</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Clique em uma obrigação parcelada para ver e quitar cada parcela individual</p>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                        {{ $obligations->total() }} obrigações
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($obligations as $ob)
                        @php
                            $isPaid = $ob->status === 'paid';
                            $hasInstallments = $ob->installments->count() > 1;
                            $paidCount = $ob->installments->where('status', 'paid')->count();
                            $totalCount = $ob->installments->count() ?: 1;
                            $percent = round(($paidCount / $totalCount) * 100);
                        @endphp
                        <div x-data="{ open: {{ $hasInstallments && !$isPaid ? 'true' : 'false' }} }" class="p-4 hover:bg-gray-50/50 transition-colors">
                            
                            <!-- Linha Principal da Obrigação -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-gray-900">{{ $ob->title }}</span>
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">
                                            {{ $ob->type_label }} • {{ $ob->reference_year }}
                                        </span>
                                        @if ($hasInstallments)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isPaid ? 'bg-emerald-100 text-emerald-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $paidCount }}/{{ $totalCount }} Parcelas Pagas ({{ $percent }}%)
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-3">
                                        <span>Veículo: <strong class="text-gray-800">{{ $ob->vehicle->nickname }}</strong> ({{ $ob->vehicle->license_plate ?? 'S/ Placa' }})</span>
                                        <span>•</span>
                                        <span>1º Vencimento: <strong class="text-gray-800">{{ date('d/m/Y', strtotime($ob->due_date)) }}</strong></span>
                                        <span>•</span>
                                        <span>Total: <strong class="text-gray-900 font-bold">R$ {{ number_format($ob->amount, 2, ',', '.') }}</strong></span>
                                        @if ($ob->amount_paid > 0 && $ob->amount_paid < $ob->amount)
                                            <span class="text-emerald-600">(Pago: R$ {{ number_format($ob->amount_paid, 2, ',', '.') }})</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Ações & Status Geral -->
                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                    @if ($hasInstallments)
                                        <button type="button" @click="open = !open" 
                                                class="px-2.5 py-1 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors flex items-center gap-1">
                                            <span x-text="open ? 'Ocultar Parcelas' : 'Ver Parcelas ({{ $totalCount }})'"></span>
                                            <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    @endif

                                    @if (!$isPaid && !$hasInstallments)
                                        <form method="POST" action="{{ route('obligations.pay', $ob) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-lg transition-colors shadow-sm">
                                                Quitar
                                            </button>
                                        </form>
                                    @elseif ($isPaid)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            100% Quitado
                                        </span>
                                    @endif

                                    <form method="POST" action="{{ route('obligations.destroy', $ob) }}" onsubmit="return confirm('Excluir esta obrigação e todas as parcelas?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-rose-600 transition-colors p-1" title="Excluir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Grade de Parcelas Associadas (Quando Aberto) -->
                            @if ($ob->installments->count() > 0)
                                <div x-show="open" class="mt-3 pt-3 border-t border-gray-100 bg-gray-50/75 rounded-xl p-3 space-y-2">
                                    <div class="text-xs font-bold text-gray-700 flex items-center justify-between pb-1.5 border-b border-gray-200">
                                        <span>Detalhamento das Parcelas:</span>
                                        <span class="text-gray-500 font-normal">Quitação individual com atualização automática de saldo</span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 pt-1">
                                        @foreach ($ob->installments as $inst)
                                            @php
                                                $instPaid = $inst->status === 'paid';
                                            @endphp
                                            <div class="p-2.5 rounded-lg border {{ $instPaid ? 'bg-emerald-50/50 border-emerald-200' : 'bg-white border-gray-200' }} flex items-center justify-between gap-2 shadow-xs">
                                                <div>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs font-bold text-gray-900">Parcela {{ $inst->installment_number }}/{{ $inst->total_installments }}</span>
                                                        @if ($instPaid)
                                                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Paga</span>
                                                        @else
                                                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800">Pendente</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                                        Venc: <strong class="text-gray-700">{{ date('d/m/Y', strtotime($inst->due_date)) }}</strong>
                                                    </div>
                                                    <div class="text-xs font-semibold text-gray-900 mt-0.5">
                                                        R$ {{ number_format($inst->amount, 2, ',', '.') }}
                                                    </div>
                                                </div>

                                                <!-- Botão de Quitar/Reabrir Parcela -->
                                                <div>
                                                    @if (!$instPaid)
                                                        <form method="POST" action="{{ route('obligations.installments.pay', $inst) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg transition-colors shadow-xs flex items-center gap-1 whitespace-nowrap">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                                <span>Quitar</span>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('obligations.installments.reopen', $inst) }}" onsubmit="return confirm('Reabrir esta parcela como pendente?');">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 font-medium text-[11px] rounded transition-colors" title="Desfazer quitação">
                                                                Desfazer
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-xs">
                            Nenhum tributo ou imposto cadastrado.
                        </div>
                    @endforelse
                </div>

                @if ($obligations->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $obligations->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

</x-app-layout>
