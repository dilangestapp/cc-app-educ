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

        // ======================================================
        // ✅ IMPORT depuis la page create (sans routes supplémentaires)
        // ======================================================
        if ($request->input('_action') === 'import') {
            $request->validate([
                'fichier' => 'required|file|max:15360|mimes:docx,pdf',
            ]);

            $file = $request->file('fichier');
            $ext  = strtolower($file->getClientOriginalExtension());
            $path = $file->getRealPath();

            $text = $this->extractTextFromFile($path, $ext);

            if (trim((string)$text) === '') {
                $msg = ($ext === 'pdf')
                    ? "PDF illisible (police encodée ou PDF scanné). ✅ Solution: exporte un PDF avec texte sélectionnable, ou utilise DOCX."
                    : "Impossible de lire le fichier Word. (DOCX uniquement)";

                return redirect()
                    ->route('cours.create', $matiereId)
                    ->with('error', $msg);
            }

            $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            return redirect()
                ->route('cours.create', $matiereId)
                ->withInput([
                    'titre'   => $title ?: old('titre', ''),
                    'contenu' => $text,
                    'actif'   => 1,
                ])
                ->with('success', 'Fichier importé : Titre et Contenu pré-remplis.');
        }

        // ======================================================
        // ✅ Création normale
        // ======================================================
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
    // Extraction (DOCX/PDF)
    // ======================================================
    private function extractTextFromFile(string $path, string $ext): string
    {
        if ($ext === 'docx') return $this->cleanText($this->extractTextFromDocx($path));
        if ($ext === 'pdf')  return $this->cleanText($this->extractTextFromPdf($path));
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
        $text = '';

        // 1) ✅ Meilleur: pdftotext (poppler-utils)
        if (function_exists('shell_exec')) {
            $cmd = 'pdftotext -layout -enc UTF-8 ' . escapeshellarg($path) . ' - 2>/dev/null';
            $out = @shell_exec($cmd);
            if (is_string($out)) {
                $text = $out;
                if ($this->looksGarbled($text)) {
                    $text = '';
                }
            }
        }

        // 2) Fallback: smalot/pdfparser (si dispo)
        if ($text === '' && class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                $t = (string)$pdf->getText();
                if (!$this->looksGarbled($t)) {
                    $text = $t;
                }
            } catch (\Throwable $e) {
                $text = '';
            }
        }

        return (string)$text;
    }

    private function cleanText(string $text): string
    {
        $text = str_replace("\0", '', $text);

        // Force UTF-8 (ignore invalid bytes)
        if (function_exists('iconv')) {
            $fixed = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
            if (is_string($fixed)) $text = $fixed;
        }

        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Remove weird control chars (except \n and \t)
        $text = preg_replace('/[^\P{C}\n\t]+/u', '', $text) ?? $text;

        // Clean extra spaces/newlines
        $text = preg_replace("/[ \t]+\n/", "\n", $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function looksGarbled(string $text): bool
    {
        $text = (string)$text;
        if (trim($text) === '') return true;

        // Trop de "�" = encodage foireux
        $len = strlen($text);
        if ($len <= 0) return true;

        $rep = substr_count($text, "�");
        if ($rep > 0 && ($rep / max(1, $len)) > 0.03) return true;

        // Beaucoup de caractères non imprimables
        $bad = preg_match_all('/[^\p{L}\p{N}\p{P}\p{Zs}\n\r\t]/u', $text, $m);
        if ($bad === false) return true;

        // Si trop de "bad chars", on considère que c’est du charabia
        if ($bad > 200) return true;

        return false;
    }
}
