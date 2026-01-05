<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MatiereController extends Controller
{
    public function manage()
    {
        if (!Schema::hasTable('matieres')) {
            return view('matieres.manage', [
                'matieres' => collect(),
                'error' => "La table 'matieres' n'existe pas encore en base. Lance les migrations : php artisan migrate --force",
            ]);
        }

        $matieres = DB::table('matieres')->orderBy('nom')->get();

        return view('matieres.manage', [
            'matieres' => $matieres,
            'error' => null,
        ]);
    }

    public function indexByClasse($classe)
    {
        $classeId = (int) $classe;

        if (!Schema::hasTable('classes')) {
            abort(500, "La table 'classes' n'existe pas.");
        }

        $classeRow = DB::table('classes')->where('id', $classeId)->first();
        abort_if(!$classeRow, 404);

        if (!Schema::hasTable('classe_matiere') || !Schema::hasTable('matieres')) {
            return view('matieres.classe', [
                'classeRow' => $classeRow,
                'matieres'  => collect(),
                'error'     => "Tables manquantes (matieres / classe_matiere). Lance les migrations.",
            ]);
        }

        $matieres = DB::table('matieres')
            ->join('classe_matiere', 'matieres.id', '=', 'classe_matiere.matiere_id')
            ->where('classe_matiere.classe_id', $classeId)
            ->select('matieres.*')
            ->orderBy('matieres.nom')
            ->get();

        return view('matieres.classe', [
            'classeRow' => $classeRow,
            'matieres'  => $matieres,
            'error'     => null,
        ]);
    }

    public function create()
    {
        return view('matieres.create');
    }

    public function store(Request $request)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");

        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom',
        ]);

        $data = ['nom' => trim((string)$request->nom)];

        // Compat legacy : si colonne classe_id existe et NOT NULL, on met 0.
        if (Schema::hasColumn('matieres', 'classe_id')) {
            $data['classe_id'] = $this->isNullableColumn('matieres', 'classe_id') ? null : 0;
        }

        if (Schema::hasColumn('matieres', 'created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('matieres', 'updated_at')) $data['updated_at'] = now();

        DB::table('matieres')->insert($data);

        return redirect()->route('matieres.manage')->with('success', 'Matière créée.');
    }

    public function edit($matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");

        $matiereRow = DB::table('matieres')->where('id', (int)$matiere)->first();
        abort_if(!$matiereRow, 404);

        return view('matieres.edit', compact('matiereRow'));
    }

    public function update(Request $request, $matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");

        $matiereId = (int)$matiere;

        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom,' . $matiereId,
        ]);

        $data = ['nom' => trim((string)$request->nom)];
        if (Schema::hasColumn('matieres', 'updated_at')) $data['updated_at'] = now();

        DB::table('matieres')->where('id', $matiereId)->update($data);

        return redirect()->route('matieres.manage')->with('success', 'Matière mise à jour.');
    }

    public function destroy($matiere)
    {
        $matiereId = (int)$matiere;

        DB::transaction(function () use ($matiereId) {
            if (Schema::hasTable('classe_matiere')) {
                DB::table('classe_matiere')->where('matiere_id', $matiereId)->delete();
            }
            if (Schema::hasTable('matieres')) {
                DB::table('matieres')->where('id', $matiereId)->delete();
            }
        });

        return redirect()->route('matieres.manage')->with('success', 'Matière supprimée.');
    }

    public function affecter($matiere)
    {
        $matiereId = (int)$matiere;

        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");
        abort_if(!Schema::hasTable('classes'), 500, "Table 'classes' manquante.");
        abort_if(!Schema::hasTable('classe_matiere'), 500, "Table 'classe_matiere' manquante. Lance les migrations.");

        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        $classes = DB::table('classes')->orderBy('nom')->get();

        $classesAffectees = DB::table('classe_matiere')
            ->where('matiere_id', $matiereId)
            ->pluck('classe_id')
            ->toArray();

        return view('matieres.affecter', compact('matiereRow', 'classes', 'classesAffectees'));
    }

    public function storeAffectation(Request $request, $matiere)
    {
        $matiereId = (int)$matiere;

        abort_if(!Schema::hasTable('classe_matiere'), 500, "Table 'classe_matiere' manquante. Lance les migrations.");

        $classes = $request->input('classes', []);
        if (!is_array($classes)) $classes = [];

        $classes = array_values(array_unique(array_map('intval', $classes)));
        $classes = array_filter($classes, fn($id) => $id > 0);

        DB::transaction(function () use ($matiereId, $classes) {
            DB::table('classe_matiere')->where('matiere_id', $matiereId)->delete();

            if (!count($classes)) return;

            $hasCreated = Schema::hasColumn('classe_matiere', 'created_at');
            $hasUpdated = Schema::hasColumn('classe_matiere', 'updated_at');
            $now = now();

            $rows = [];
            foreach ($classes as $classeId) {
                $row = [
                    'matiere_id' => $matiereId,
                    'classe_id'  => (int)$classeId,
                ];
                if ($hasCreated) $row['created_at'] = $now;
                if ($hasUpdated) $row['updated_at'] = $now;
                $rows[] = $row;
            }

            DB::table('classe_matiere')->insert($rows);
        });

        return redirect()->route('matieres.manage')->with('success', 'Affectations mises à jour.');
    }

    // ======================================================
    // ✅ IMPORT MATIERES (Word/PDF)
    // ======================================================
    public function importForm()
    {
        return view('matieres.import');
    }

    public function importStore(Request $request)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");

        $request->validate([
            'fichier' => 'required|file|max:15360|mimes:docx,pdf',
        ]);

        $file = $request->file('fichier');
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        $text = $this->extractTextFromFile($path, $ext);
        $names = $this->parseMatieresFromText($text);

        if (!count($names)) {
            return redirect()->route('matieres.import')
                ->with('error', "Aucune matière détectée dans le fichier. Mets une matière par ligne (ou liste à puces).");
        }

        $hasCreated = Schema::hasColumn('matieres', 'created_at');
        $hasUpdated = Schema::hasColumn('matieres', 'updated_at');

        $added = 0;
        $updated = 0;

        DB::transaction(function () use ($names, $hasCreated, $hasUpdated, &$added, &$updated) {
            foreach ($names as $nom) {
                $exists = DB::table('matieres')->where('nom', $nom)->exists();

                $data = ['nom' => $nom];

                // Compat legacy
                if (Schema::hasColumn('matieres', 'classe_id')) {
                    $data['classe_id'] = $this->isNullableColumn('matieres', 'classe_id') ? null : 0;
                }

                if ($hasUpdated) $data['updated_at'] = now();

                if ($exists) {
                    DB::table('matieres')->where('nom', $nom)->update($data);
                    $updated++;
                } else {
                    if ($hasCreated) $data['created_at'] = now();
                    DB::table('matieres')->insert($data);
                    $added++;
                }
            }
        });

        return redirect()->route('matieres.manage')
            ->with('success', "Import terminé : {$added} ajoutée(s), {$updated} déjà existante(s) (mise à jour).");
    }

    private function extractTextFromFile(string $path, string $ext): string
    {
        if ($ext === 'docx') {
            return $this->extractTextFromDocx($path);
        }

        if ($ext === 'pdf') {
            return $this->extractTextFromPdf($path);
        }

        return '';
    }

    private function extractTextFromDocx(string $path): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException("ZipArchive non disponible sur ce serveur (impossible de lire DOCX).");
        }

        $zip = new \ZipArchive();
        $ok = $zip->open($path);

        if ($ok !== true) return '';

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) return '';

        // Ajouter des retours ligne sur fins de paragraphes
        $xml = str_replace(['</w:p>', '</w:tr>', '</w:tab>'], ["\n", "\n", "\t"], $xml);

        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return (string)$text;
    }

    private function extractTextFromPdf(string $path): string
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            // PDF optionnel : on donne message clair
            return '';
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            return (string)$pdf->getText();
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function parseMatieresFromText(string $text): array
    {
        $text = trim((string)$text);
        if ($text === '') return [];

        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Convertir puces en lignes
        $text = preg_replace("/[•·●▪]+/u", "\n", $text);

        $lines = preg_split("/\n+/u", $text);
        $out = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            // enlever numérotation: "1.", "1)", "01 -", "-"
            $line = preg_replace('/^\s*(\d{1,3}\s*[\.\)\-:])\s*/u', '', $line);
            $line = preg_replace('/^\s*[-–—]\s*/u', '', $line);

            $line = trim($line, " \t\n\r\0\x0B-–—•.,;:|");
            $line = preg_replace('/\s{2,}/u', ' ', $line);

            if (mb_strlen($line, 'UTF-8') < 2) continue;

            // Normaliser: "mathematique" => "Mathematique"
            $line = mb_convert_case($line, MB_CASE_TITLE, 'UTF-8');

            $out[] = $line;
        }

        $out = array_values(array_unique($out));
        return $out;
    }

    private function isNullableColumn(string $table, string $column): bool
    {
        try {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                $dbName = DB::getDatabaseName();
                $row = DB::selectOne(
                    "SELECT IS_NULLABLE FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
                     LIMIT 1",
                    [$dbName, $table, $column]
                );
                return isset($row->IS_NULLABLE) && strtoupper((string)$row->IS_NULLABLE) === 'YES';
            }

            if ($driver === 'pgsql') {
                $row = DB::selectOne(
                    "SELECT is_nullable FROM information_schema.columns
                     WHERE table_schema = 'public' AND table_name = ? AND column_name = ?
                     LIMIT 1",
                    [$table, $column]
                );
                return isset($row->is_nullable) && strtoupper((string)$row->is_nullable) === 'YES';
            }
        } catch (\Throwable $e) {}

        return false;
    }
}
