cat > resources/views/dashboard.blade.php <<'EOF'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard — CC APP EDUC
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Message bienvenue -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    👋 Bienvenue <strong>{{ Auth::user()->name }}</strong> sur CC APP EDUC.
                </div>
            </div>

            <!-- Cartes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">👨‍🎓 Élèves</h3>
                    <p class="text-gray-600 mt-2">Gestion des élèves</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">🏫 Classes</h3>
                    <p class="text-gray-600 mt-2">Organisation des classes</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">📊 Notes</h3>
                    <p class="text-gray-600 mt-2">Résultats et évaluations</p>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
EOF
