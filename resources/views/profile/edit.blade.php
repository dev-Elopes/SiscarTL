<x-app-layout>

    <div class="mb-6">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#E11D48]"></span>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Configurações de Perfil & Segurança
            </h1>
        </div>
        <p class="text-xs text-gray-500 mt-1">
            Gerencie seus dados de acesso, senha e preferências da conta
        </p>
    </div>

    <div class="space-y-6 max-w-4xl">
        <div class="p-6 sm:p-8 bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
