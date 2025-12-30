<!-- Cartes principales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">👨‍🎓 Élèves</h3>
        <p class="text-gray-600 mt-2">Gestion des élèves</p>
    </div>

    {{-- Carte Classes --}}
    @if (Route::has('classes.index'))
        <a href="{{ route('classes.index') }}" class="block hover:bg-gray-50 transition">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold">🏫 Classes</h3>
                <p class="text-gray-600 mt-2">Organisation des classes</p>
            </div>
        </a>
    @else
        <div class="bg-white p-6 rounded-lg shadow opacity-50 cursor-not-allowed">
            <h3 class="text-lg font-semibold">🏫 Classes</h3>
            <p class="text-gray-600 mt-2">Organisation des classes (bientôt)</p>
        </div>
    @endif

    {{-- Carte Matières --}}
    @if (Route::has('classes.index'))
        <a href="{{ route('classes.index') }}" class="block hover:bg-gray-50 transition">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold">📘 Matières</h3>
                <p class="text-gray-600 mt-2">
                    Matières par classe
                </p>
            </div>
        </a>
    @else
        <div class="bg-white p-6 rounded-lg shadow opacity-50 cursor-not-allowed">
            <h3 class="text-lg font-semibold">📘 Matières</h3>
            <p class="text-gray-600 mt-2">Dépend des classes</p>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">👨‍🏫 Enseignants</h3>
        <p class="text-gray-600 mt-2">Gestion du personnel enseignant</p>
    </div>

</div>
