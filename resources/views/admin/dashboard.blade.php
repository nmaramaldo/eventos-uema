<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Bem-vindo ao painel de administração!</p>
                    <ul class="mt-4">
                        <li><a href="{{ route('admin.usuarios.index') }}" class="text-blue-500 hover:underline">Gerenciar Usuários</a></li>
                        <li><a href="{{ route('eventos.index') }}" class="text-blue-500 hover:underline">Gerenciar Eventos</a></li>
                        <li><a href="{{ route('palestrantes.index') }}" class="text-blue-500 hover:underline">Gerenciar Palestrantes</a></li>
                        <li><a href="{{ route('certificados.index') }}" class="text-blue-500 hover:underline">Gerenciar Certificados</a></li>
                        <li><a href="{{ route('relatorios.index') }}" class="text-blue-500 hover:underline">Relatórios</a></li>
                        <li><a href="{{ route('audit-logs.index') }}" class="text-blue-500 hover:underline">Logs de Auditoria</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
