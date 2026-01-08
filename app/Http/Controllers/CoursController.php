<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

    /**
     * ✅ Page import (route cours.import)
     */
    public function importForm($matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");

        $matiereId = (int)$matiere;
        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        return view('cours.import', compact('matiereRow'));
    }

    /**
     * ✅ Import store (route cours.import.store)
     * DOCX => HTML structuré + images extraites vers storage/public
     * PDF  => texte => HTML (sans images)
     */
    public function importStore(Request $request, $matiere)
    {
        $matiereId = (int)$matiere;

        $request->validate([
            'fichier' => 'required|file|max:51200', // 50MB
        ]);

        if (!$request->hasFile('fichier')) {
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Aucun fichier reçu.");
        }

        $file = $request->file('fichier');
        if (!$file->isValid()) {
            $err = $file->getError();
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Upload échoué (code $err).");
        }

        $ext = strtolower((string)$file->getClientOriginalExtension());
        if (!in_array($ext, ['docx', 'pdf'], true)) {
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Format non supporté. Utilise DOCX ou PDF.");
        }

        $tmpDir = storage_path('app/tmp_imports');
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }

        $tmpName = uniqid('import_', true) . '.' . $ext;
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;

        try {
            $file->move($tmpDir, $tmpName);
        } catch (\Throwable $e) {
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Impossible de sauvegarder le fichier importé (permissions storage).");
        }

        $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $html = '';

        try {
            if ($ext === 'docx') {
                $html = $this->extractHtmlFromDocxWithImages($tmpPath);
            } else {
                $text = $this->extractTextFromPdf($tmpPath);
                if (trim((string)$text) === '') {
                    @unlink($tmpPath);
                    return redirect()->route('cours.import', $matiereId)
                        ->with('error', "PDF sans texte lisible (souvent scanné). Utilise un DOCX ou un PDF avec texte sélectionnable.");
                }

                $safe = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $safe = preg_replace("/\n{3,}/", "\n\n", $safe) ?? $safe;

                // HTML simple mais propre
                $html = '<div class="pdf-text">' . nl2br($safe) . '</div>';
            }
        } catch (\Throwable $e) {
            @unlink($tmpPath);
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Erreur import : " . $e->getMessage());
        }

        @unlink($tmpPath);

        if (trim((string)$html) === '') {
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Import impossible : contenu vide.");
        }

        return redirect()
            ->route('cours.create', $matiereId)
            ->withInput([
                'titre'   => $title ?: old('titre', ''),
                'contenu' => $html,
                'actif'   => 1,
            ])
            ->with('success', 'Import terminé : Contenu HTML + images (DOCX) pré-remplis.');
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
    // DOCX -> HTML + images
    // ======================================================
    private function extractHtmlFromDocxWithImages(string $path): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException("ZipArchive manquant (ext-zip).");
        }

        $zip = new \ZipArchive();
        $ok = $zip->open($path);
        if ($ok !== true) {
            throw new \RuntimeException("Impossible d'ouvrir le DOCX.");
        }

        $documentXml = $zip->getFromName('word/document.xml');
        if ($documentXml === false) {
            $zip->close();
            throw new \RuntimeException("document.xml introuvable dans le DOCX.");
        }

        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        $ridToTarget = [];
        if ($relsXml !== false) {
            $rels = @simplexml_load_string($relsXml);
            if ($rels) {
                foreach ($rels->Relationship as $rel) {
                    $id = (string)$rel['Id'];
                    $target = (string)$rel['Target'];
                    $type = (string)$rel['Type'];
                    if ($id !== '' && $target !== '') {
                        $ridToTarget[$id] = ['target' => $target, 'type' => $type];
                    }
                }
            }
        }

        // Dossier unique pour les images
        $folder = date('Ymd_His') . '_' . substr(sha1($path . microtime(true)), 0, 8);
        $base = "cours_images/{$folder}";
        Storage::disk('public')->makeDirectory($base);

        $ridToPublicUrl = [];

        // Sauver images référencées
        foreach ($ridToTarget as $rid => $info) {
            $target = str_replace('\\', '/', (string)($info['target'] ?? ''));

            // Souvent: media/image1.png
            if (!str_starts_with($target, 'media/')) continue;

            $insideZip = 'word/' . $target;
            $bin = $zip->getFromName($insideZip);
            if ($bin === false) continue;

            $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION)) ?: 'png';
            if (!in_array($ext, ['png','jpg','jpeg','gif','webp','bmp'], true)) $ext = 'png';

            $name = 'img_' . substr(sha1($rid . $target), 0, 10) . '.' . $ext;
            Storage::disk('public')->put("{$base}/{$name}", $bin);

            $ridToPublicUrl[$rid] = '/storage/' . "{$base}/{$name}";
        }

        // Parse document.xml -> HTML
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        @$dom->loadXML($documentXml);

        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xp->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xp->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xp->registerNamespace('v', 'urn:schemas-microsoft-com:vml');

        $htmlParts = [];

        $paras = $xp->query('//w:body/w:p');
        if ($paras) {
            foreach ($paras as $p) {
                // Style (Heading/Titre)
                $styleVal = '';
                $styleNode = $xp->query('.//w:pPr/w:pStyle', $p);
                if ($styleNode && $styleNode->length > 0) {
                    $styleVal = (string)$styleNode->item(0)->getAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'val');
                    if ($styleVal === '') $styleVal = (string)$styleNode->item(0)->getAttribute('w:val');
                }

                $tag = 'p';
                $sv = strtolower($styleVal);
                if (str_contains($sv, 'heading1') || str_contains($sv, 'titre1') || str_contains($sv, 'title1')) $tag = 'h1';
                if (str_contains($sv, 'heading2') || str_contains($sv, 'titre2') || str_contains($sv, 'title2')) $tag = 'h2';
                if (str_contains($sv, 'heading3') || str_contains($sv, 'titre3') || str_contains($sv, 'title3')) $tag = 'h3';
                if (str_contains($sv, 'heading4') || str_contains($sv, 'titre4') || str_contains($sv, 'title4')) $tag = 'h4';

                // Texte (w:t) + sauts de ligne (w:br)
                $text = '';
                $runs = $xp->query('.//w:r', $p);
                if ($runs) {
                    foreach ($runs as $rnode) {
                        // br
                        $brs = $xp->query('.//w:br', $rnode);
                        if ($brs && $brs->length > 0) {
                            $text .= "\n";
                        }

                        $tNodes = $xp->query('.//w:t', $rnode);
                        if ($tNodes) {
                            foreach ($tNodes as $tn) {
                                $text .= (string)$tn->nodeValue;
                            }
                        }
                    }
                }

                $text = trim((string)$text);

                // Images (a:blip r:embed)
                $imgsHtml = '';
                $blips = $xp->query('.//a:blip', $p);
                if ($blips) {
                    foreach ($blips as $b) {
                        $rid = (string)$b->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
                        if ($rid === '') $rid = (string)$b->getAttribute('r:embed');

                        $src = $ridToPublicUrl[$rid] ?? '';
                        if ($src !== '') {
                            $imgsHtml .= '<div><img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="image"></div>';
                        }
                    }
                }

                // VML fallback (v:imagedata r:id)
                $vImgs = $xp->query('.//v:imagedata', $p);
                if ($vImgs) {
                    foreach ($vImgs as $vimg) {
                        $rid = (string)$vimg->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');
                        if ($rid === '') $rid = (string)$vimg->getAttribute('r:id');

                        $src = $ridToPublicUrl[$rid] ?? '';
                        if ($src !== '') {
                            $imgsHtml .= '<div><img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="image"></div>';
                        }
                    }
                }

                if ($text === '' && $imgsHtml === '') continue;

                if ($text !== '') {
                    $safe = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $safe = nl2br($safe);
                    $htmlParts[] = "<{$tag}>{$safe}</{$tag}>" . $imgsHtml;
                } else {
                    $htmlParts[] = $imgsHtml;
                }
            }
        }

        $zip->close();

        return implode("\n", $htmlParts);
    }

    // ======================================================
    // PDF -> texte (pdftotext)
    // ======================================================
    private function extractTextFromPdf(string $path): string
    {
        if ($this->canUseShellExec()) {
            // tente pdftotext
            $cmd = 'pdftotext -layout -enc UTF-8 ' . escapeshellarg($path) . ' - 2>/dev/null';
            $out = @shell_exec($cmd);
            if (is_string($out) && trim($out) !== '') {
                return $this->cleanText($out);
            }
        }
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
}
