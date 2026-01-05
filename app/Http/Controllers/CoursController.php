<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CoursController extends Controller
{
    public function index($matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante.");

        $matiereId = (int)$matiere;
        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        $cours = DB::table('cours')
            ->where('matiere_id', $matiereId)
            ->orderByDesc('id')
            ->get();

        return view('cours.index', compact('matiereRow', 'cours'));
    }

    public function create($matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");
        abort_if(!Schema::hasTable('classes'), 500, "Table 'classes' manquante.");

        $matiereId = (int)$matiere;
        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        $classes = DB::table('classes')->orderBy('nom')->get();

        return view('cours.create', compact('matiereRow', 'classes'));
    }

    public function store(Request $request, $matiere)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante.");

        $matiereId = (int)$matiere;

        $request->validate([
            'classe_id' => 'required|integer|min:1',
            'titre'     => 'required|string|max:255',
            'contenu'   => 'nullable|string',
            'actif'     => 'nullable',
        ]);

        $data = [
            'matiere_id' => $matiereId,
            'classe_id'  => (int)$request->classe_id,
            'titre'      => trim((string)$request->titre),
            'contenu'    => (string)($request->contenu ?? ''),
            'actif'      => $request->has('actif') ? 1 : 0,
        ];

        if (Schema::hasColumn('cours', 'created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('cours', 'updated_at')) $data['updated_at'] = now();

        DB::table('cours')->insert($data);

        return redirect()->route('cours.index', $matiereId)->with('success', 'Cours créé.');
    }

    public function edit($cours)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante.");
        abort_if(!Schema::hasTable('classes'), 500, "Table 'classes' manquante.");

        $coursId = (int)$cours;
        $coursRow = DB::table('cours')->where('id', $coursId)->first();
        abort_if(!$coursRow, 404);

        $matiereRow = Schema::hasTable('matieres')
            ? DB::table('matieres')->where('id', (int)$coursRow->matiere_id)->first()
            : null;

        $classes = DB::table('classes')->orderBy('nom')->get();

        return view('cours.edit', compact('coursRow', 'matiereRow', 'classes'));
    }

    public function update(Request $request, $cours)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante.");

        $coursId = (int)$cours;

        $request->validate([
            'classe_id' => 'required|integer|min:1',
            'titre'     => 'required|string|max:255',
            'contenu'   => 'nullable|string',
            'actif'     => 'nullable',
        ]);

        $data = [
            'classe_id' => (int)$request->classe_id,
            'titre'     => trim((string)$request->titre),
            'contenu'   => (string)($request->contenu ?? ''),
            'actif'     => $request->has('actif') ? 1 : 0,
        ];

        if (Schema::hasColumn('cours', 'updated_at')) $data['updated_at'] = now();

        DB::table('cours')->where('id', $coursId)->update($data);

        return back()->with('success', 'Cours mis à jour.');
    }

    public function destroy($cours)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante.");

        $coursId = (int)$cours;
        DB::table('cours')->where('id', $coursId)->delete();

        return back()->with('success', 'Cours supprimé.');
    }

    // ======================================================
    // ✅ IMPORT COURS (Word/PDF → préremplit create)
    // ======================================================
    public function importForm($matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");

        $matiereId = (int)$matiere;
        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        return view('cours.import', compact('matiereRow'));
    }

    public function importStore(Request $request, $matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");

        $matiereId = (int)$matiere;

        $request->validate([
            'fichier' => 'required|file|max:15360|mimes:docx,pdf',
        ]);

        $file = $request->file('fichier');
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        $text = $this->extractTextFromFile($path, $ext);

        if (trim($text) === '') {
            return redirect()->route('cours.import', $matiereId)
                ->with('error', $ext === 'pdf'
                    ? "Impossible de lire le PDF. Installe : composer require smalot/pdfparser (ou utilise DOCX)."
                    : "Impossible de lire le fichier Word. (DOCX uniquement)");
        }

        $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // ✅ Redirige vers create avec contenu déjà rempli (si ta vue utilise old())
        return redirect()
            ->route('cours.create', $matiereId)
            ->withInput([
                'titre'   => $title,
                'contenu' => $text,
                'actif'   => 1,
            ])
            ->with('success', 'Fichier importé : contenu pré-rempli.');
    }

    private function extractTextFromFile(string $path, string $ext): string
    {
        if ($ext === 'docx') return $this->extractTextFromDocx($path);
        if ($ext === 'pdf')  return $this->extractTextFromPdf($path);
        return '';
    }

    private function extractTextFromDocx(string $path): string
    {
        if (!class_exists(\ZipArchive::class)) return '';

        $zip = new \ZipArchive();
        $ok = $zip->open($path);
        if ($ok !== true) return '';

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) return '';

        $xml = str_replace(['</w:p>', '</w:tr>'], ["\n", "\n"], $xml);

        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return (string)$text;
    }

    private function extractTextFromPdf(string $path): string
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) return '';

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            return (string)$pdf->getText();
        } catch (\Throwable $e) {
            return '';
        }
    }
}
