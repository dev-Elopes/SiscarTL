<x-guest-layout>
    <div class="w-full max-w-[430px] bg-white rounded-2xl border border-[#E5E7EB] shadow-[0_12px_40px_rgba(0,0,0,0.06)] overflow-hidden">
        
        <!-- Linha Superior de Destaque Automotivo Sutil -->
        <div class="h-1 bg-[#E11D48] w-full"></div>

        <div class="p-8 sm:p-9">
            
            <!-- Topo do Card: Ícone Minimalista de Velocímetro & Título -->
            <div class="mb-7">
                <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center text-[#E11D48] mb-4">
                    <!-- Vetor Minimalista de Velocímetro em Linhas Finas -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l3-3m-3 3a2 2 0 11-2.828-2.828A2 2 0 0112 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a9 9 0 10-14.856 0" />
                    </svg>
                </div>
                
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">
                    Acessar Garagem
                </h1>
                
                <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                    Gerencie as revisões e o histórico dos seus veículos.
                </p>
            </div>

            <!-- Status de Sessão -->
            <x-auth-session-status class="mb-4 text-xs font-medium text-emerald-600" :status="session('status')" />

            <!-- Formulário de Login -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Campo E-mail -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#1F2937] mb-1.5">
                        E-mail
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="seu@email.com" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="w-full px-3.5 py-3 bg-white border border-[#D1D5DB] rounded-lg text-sm text-[#1F2937] placeholder-gray-400 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 transition-all" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-[#E11D48]" />
                </div>

                <!-- Campo Senha com Link 'Esqueci a senha' -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-[#1F2937]">
                            Senha
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-gray-500 hover:text-[#E11D48] transition-colors">
                                Esqueci a senha
                            </a>
                        @endif
                    </div>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           placeholder="••••••••" 
                           required 
                           autocomplete="current-password"
                           class="w-full px-3.5 py-3 bg-white border border-[#D1D5DB] rounded-lg text-sm text-[#1F2937] placeholder-gray-400 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 transition-all" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-[#E11D48]" />
                </div>

                <!-- Lembrar-me -->
                <div class="flex items-center pt-0.5">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input id="remember_me" 
                               type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 rounded border-gray-300 text-[#E11D48] focus:ring-[#E11D48]/20 focus:ring-offset-0">
                        <span class="text-xs text-gray-600">Lembrar de mim</span>
                    </label>
                </div>

                <!-- Botão Principal -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full h-12 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center cursor-pointer">
                        Entrar no Sistema
                    </button>
                </div>
            </form>

            <!-- Rodapé do Card -->
            <div class="mt-6 pt-5 border-t border-[#F3F4F6] text-center">
                <p class="text-xs text-gray-500">
                    Ainda não tem conta? 
                    <a href="{{ route('register') }}" class="font-medium text-[#E11D48] hover:text-[#BE123C] transition-colors ml-1">
                        Cadastre-se
                    </a>
                </p>
            </div>

        </div>
    </div>
</x-guest-layout>
