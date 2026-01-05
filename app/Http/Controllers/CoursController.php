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
        // ✅ IMPORT depuis la page create
        // ======================================================
        if ($request->input('_action') === 'import') {

            // 1) ✅ Validation simple + tolérante (mimes parfois faux côté serveur)
            $request->validate([
                'fichier' => 'required|file|max:51200', // 50MB
            ]);

            // 2) ✅ Si l’upload PHP a échoué (taille / proxy / etc.)
            if (!$request->hasFile('fichier')) {
                return redirect()->route('cours.create', $matiereId)
                    ->with('error', "Aucun fichier reçu. Si c’est un PDF lourd, augmente la taille ou réessaie après le redeploy (upload_max_filesize/post_max_size).");
            }

            $file = $request->file('fichier');
            if (!$file->isValid()) {
                $err = $file->getError();
                return redirect()->route('cours.create', $matiereId)
                    ->with('error', "Upload échoué (code $err). Souvent dû à la taille du PDF. Réessaie après le redeploy.");
            }

            $ext = strtolower((string)$file->getClientOriginalExtension());
            if (!in_array($ext, ['docx','pdf'], true)) {
                return redirect()->route('cours.create', $matiereId)
                    ->with('error', "Format non supporté. Utilise DOCX ou PDF.");
            }

            // 3) ✅ On copie le fichier dans storage (chemin stable)
            $tmpDir = storage_path('app/tmp_imports');
            if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }

            $tmpName = uniqid('import_', true) . '.' . $ext;
            $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;

            try {
                $file->move($tmpDir, $tmpName);
            } catch (\Throwable $e) {
                return redirect()->route('cours.create', $matiereId)
                    ->with('error', "Impossible de sauvegarder le fichier importé (permissions storage).");
            }

            $text = $this->extractTextFromFile($tmpPath, $ext);

            // Nettoyage du fichier temporaire
            @unlink($tmpPath);

            if (trim((string)$text) === '') {
                $msg = ($ext === 'pdf')
                    ? "PDF sans texte lisible (souvent scanné ou police encodée). ✅ Solution: utilise un PDF avec texte sélectionnable, ou un DOCX."
                    : "Impossible de lire le fichier Word. (DOCX uniquement)";

                return redirect()->route('cours.create', $matiereId)->with('error', $msg);
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
        // ✅ On privilégie pdftotext (poppler-utils)
        if ($this->canUseShellExec()) {
            $cmd = 'pdftotext -layout -enc UTF-8 ' . escapeshellarg($path) . ' - 2>/dev/null';
            $out = @shell_exec($cmd);
            if (is_string($out) && !$this->looksGarbled($out)) {
                return $out;
            }
        }

        // Fallback : rien (tu peux garder pdfparser si tu veux, mais pdftotext suffit)
        return '';
    }

    private function canUseShellExec(): bool
    {
        if (!function_exists('shell_exec')) return false;

        $disabled = (string)ini_get('disable_functions');
        if ($disabled !== '') {
            $list = array_map('trim', explode(',', $disabled));
            if (in_array('shell_exec', $list, true)) return false;
        }

        return true;
    }

    private function cleanText(string $text): string
    {
        $text = str_replace("\0", '', $text);

        if (function_exists('iconv')) {
            $fixed = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
            if (is_string($fixed)) $text = $fixed;
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[^\P{C}\n\t]+/u', '', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function looksGarbled(string $text): bool
    {
        $text = (string)$text;
        if (trim($text) === '') return true;

        $len = strlen($text);
        if ($len <= 0) return true;

        $rep = substr_count($text, "�");
        if ($rep > 0 && ($rep / max(1, $len)) > 0.03) return true;

        return false;
    }
}
