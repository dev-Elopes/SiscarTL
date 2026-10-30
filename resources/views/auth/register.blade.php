<x-guest-layout>
    <div class="w-full max-w-[440px] bg-white rounded-2xl border border-[#E5E7EB] shadow-[0_12px_40px_rgba(0,0,0,0.06)] overflow-hidden">
        
        <!-- Linha Superior de Destaque Automotivo Sutil -->
        <div class="h-1 bg-[#E11D48] w-full"></div>

        <div class="p-8 sm:p-9">
            
            <!-- Topo do Card -->
            <div class="mb-7">
                <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center text-[#E11D48] mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                
                <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">
                    Criar Garagem
                </h1>
                
                <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                    Comece a controlar a manutenção e os custos dos seus veículos.
                </p>
            </div>

            <!-- Formulário de Cadastro -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Nome -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#1F2937] mb-1.5">
                        Nome do Proprietário
                    </label>
                    <input id="name" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Seu Nome Completo" 
                           required 
                           autofocus 
                           autocomplete="name"
                           class="w-full px-3.5 py-3 bg-white border border-[#D1D5DB] rounded-lg text-sm text-[#1F2937] placeholder-gray-400 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 transition-all" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-[#E11D48]" />
                </div>

                <!-- E-mail -->
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
                           autocomplete="username"
                           class="w-full px-3.5 py-3 bg-white border border-[#D1D5DB] rounded-lg text-sm text-[#1F2937] placeholder-gray-400 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 transition-all" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-[#E11D48]" />
                </div>

                <!-- Senha -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#1F2937] mb-1.5">
                        Senha
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           placeholder="Mínimo 8 caracteres" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-3.5 py-3 bg-white border border-[#D1D5DB] rounded-lg text-sm text-[#1F2937] placeholder-gray-400 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 transition-all" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-[#E11D48]" />
                </div>

                <!-- Confirmar Senha -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#1F2937] mb-1.5">
                        Confirmar Senha
                    </label>
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           placeholder="Repita sua senha" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-3.5 py-3 bg-white border border-[#D1D5DB] rounded-lg text-sm text-[#1F2937] placeholder-gray-400 focus:outline-none focus:border-[#E11D48] focus:ring-2 focus:ring-[#E11D48]/15 transition-all" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-[#E11D48]" />
                </div>

                <!-- Botão Principal -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full h-12 bg-[#E11D48] hover:bg-[#BE123C] text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center cursor-pointer">
                        Cadastrar Garagem
                    </button>
                </div>
            </form>

            <!-- Rodapé do Card -->
            <div class="mt-6 pt-5 border-t border-[#F3F4F6] text-center">
                <p class="text-xs text-gray-500">
                    Já possui conta cadastrada? 
                    <a href="{{ route('login') }}" class="font-medium text-[#E11D48] hover:text-[#BE123C] transition-colors ml-1">
                        Fazer Login
                    </a>
                </p>
            </div>

        </div>
    </div>
</x-guest-layout>
