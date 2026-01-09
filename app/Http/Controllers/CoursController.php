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

    /*
    |--------------------------------------------------------------------------
    | IMPORT (page)
    |--------------------------------------------------------------------------
    */
    public function importForm($matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");

        $matiereId = (int)$matiere;
        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        return view('cours.import', compact('matiereRow'));
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORT (submit)
    |--------------------------------------------------------------------------
    */
    public function importStore(Request $request, $matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");

        $matiereId = (int)$matiere;

        // ⚠️ Important : ne PAS faire $request->validate('file') ici.
        if (!$request->hasFile('fichier')) {
            return back()->with('error', "Aucun fichier reçu. Vérifie la taille (limites serveur) et réessaie.");
        }

        $file = $request->file('fichier');

        if (!$file->isValid()) {
            $code = $file->getError();
            return back()->with('error', "Upload échoué (code $code). Cause fréquente : fichier trop lourd ou limite serveur.");
        }

        // 50 MB
        if ($file->getSize() > 50 * 1024 * 1024) {
            return back()->with('error', "Fichier trop lourd (> 50MB). Essaie plus petit ou augmente les limites serveur.");
        }

        $ext = strtolower((string)$file->getClientOriginalExtension());
        if (!in_array($ext, ['docx', 'pdf'], true)) {
            return back()->with('error', "Format non supporté. Utilise DOCX ou PDF.");
        }

        // Dossier temporaire
        $tmpDir = storage_path('app/tmp_imports');
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0775, true);
        }

        $tmpName = uniqid('import_', true) . '.' . $ext;
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;

        try {
            $file->move($tmpDir, $tmpName);
        } catch (\Throwable $e) {
            return back()->with('error', "Impossible de sauvegarder le fichier importé (permissions storage).");
        }

        $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'Cours importé';

        try {
            if ($ext === 'docx') {
                $folder = 'cours_imports/' . date('Ymd_His') . '_' . substr(md5($tmpName), 0, 10);
                $html = $this->docxToHtmlWithImages($tmpPath, $folder);

                @unlink($tmpPath);

                if (trim(strip_tags($html)) === '') {
                    return back()->with('error', "DOCX illisible (aucun contenu).");
                }

                return redirect()
                    ->route('cours.create', $matiereId)
                    ->withInput([
                        'titre'   => $title,
                        'contenu' => $html, // ✅ HTML + <img src="/storage/...">
                        'actif'   => 1,
                    ])
                    ->with('success', 'DOCX importé : contenu + images pré-remplis. Clique sur “Créer” pour enregistrer.');

            } else { // PDF
                if (!$this->hasPdftotext()) {
                    @unlink($tmpPath);
                    return back()->with('error', "PDF import impossible : pdftotext n’est pas installé côté serveur. (Railway/Docker : ajoute poppler-utils).");
                }

                $text = $this->extractTextFromPdf($tmpPath);
                @unlink($tmpPath);

                if (trim($text) === '') {
                    return back()->with('error', "PDF illisible (pas de texte récupérable). Ce PDF est probablement scanné (image). ✅ Essaie en DOCX ou utilise un PDF texte.");
                }

                $html = $this->textToHtml($text);

                return redirect()
                    ->route('cours.create', $matiereId)
                    ->withInput([
                        'titre'   => $title,
                        'contenu' => $html,
                        'actif'   => 1,
                    ])
                    ->with('success', 'PDF importé : texte pré-rempli (images PDF non supportées). Clique sur “Créer” pour enregistrer.');
            }
        } catch (\Throwable $e) {
            @unlink($tmpPath);
            return back()->with('error', "Erreur import : " . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD NORMAL
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | DOCX -> HTML + Images (ROBUSTE)
    |--------------------------------------------------------------------------
    */
    private function docxToHtmlWithImages(string $docxPath, string $publicFolder): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException("ZipArchive manquant (zip extension).");
        }

        $zip = new \ZipArchive();
        $ok = $zip->open($docxPath);
        if ($ok !== true) {
            throw new \RuntimeException("Impossible d'ouvrir le DOCX.");
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $relsXml     = $zip->getFromName('word/_rels/document.xml.rels');

        if (!is_string($documentXml) || $documentXml === '') {
            $zip->close();
            throw new \RuntimeException("DOCX invalide (document.xml introuvable).");
        }

        // Map rId -> target (+ external)
        $relMap = [];
        if (is_string($relsXml) && trim($relsXml) !== '') {
            $relsDom = new \DOMDocument();
            $relsDom->preserveWhiteSpace = false;
            if (@$relsDom->loadXML($relsXml)) {
                foreach ($relsDom->getElementsByTagName('Relationship') as $rel) {
                    /** @var \DOMElement $rel */
                    $id = $rel->getAttribute('Id');
                    $target = $rel->getAttribute('Target');
                    $mode = $rel->getAttribute('TargetMode'); // "External" possible

                    if ($id !== '' && $target !== '') {
                        $relMap[$id] = [
                            'target'   => $target,
                            'external' => (strtolower($mode) === 'external'),
                        ];
                    }
                }
            }
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        if (!@$dom->loadXML($documentXml)) {
            $zip->close();
            throw new \RuntimeException("DOCX illisible (XML invalide).");
        }

        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xp->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xp->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xp->registerNamespace('v', 'urn:schemas-microsoft-com:vml');

        $htmlParts = [];
        $inList = false;

        foreach ($xp->query('//w:body//w:p') as $p) {

            // style
            $pStyle = '';
            $styleNode = $xp->query('w:pPr/w:pStyle', $p)->item(0);
            if ($styleNode instanceof \DOMElement) {
                $pStyle = (string)$styleNode->getAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'val');
                if ($pStyle === '') $pStyle = (string)$styleNode->getAttribute('w:val');
            }

            $isList = $xp->query('w:pPr/w:numPr', $p)->length > 0;

            $tag = 'p';
            if (stripos($pStyle, 'Heading1') !== false || stripos($pStyle, 'Titre1') !== false) $tag = 'h2';
            if (stripos($pStyle, 'Heading2') !== false || stripos($pStyle, 'Titre2') !== false) $tag = 'h3';

            $inner = '';

            foreach ($xp->query('.//w:r', $p) as $rNode) {

                // DrawingML images
                $blip = $xp->query('.//a:blip', $rNode)->item(0);
                if ($blip instanceof \DOMElement) {
                    $rid = $blip->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
                    if ($rid === '') $rid = $blip->getAttribute('r:embed');

                    if ($rid !== '') {
                        $src = $this->saveDocxImageFromRid($zip, $relMap, $rid, $publicFolder);
                        if ($src) {
                            $inner .= '<div class="docx-image"><img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt=""></div>';
                        }
                    }
                    continue;
                }

                // VML images (anciens DOCX)
                $vImg = $xp->query('.//v:imagedata', $rNode)->item(0);
                if ($vImg instanceof \DOMElement) {
                    $rid = $vImg->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');
                    if ($rid === '') $rid = $vImg->getAttribute('r:id');

                    if ($rid !== '') {
                        $src = $this->saveDocxImageFromRid($zip, $relMap, $rid, $publicFolder);
                        if ($src) {
                            $inner .= '<div class="docx-image"><img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt=""></div>';
                        }
                    }
                    continue;
                }

                // texte
                $isBold = $xp->query('w:rPr/w:b', $rNode)->length > 0;
                $isItalic = $xp->query('w:rPr/w:i', $rNode)->length > 0;

                $text = '';
                foreach ($xp->query('.//w:t', $rNode) as $tNode) {
                    $text .= $tNode->textContent;
                }
                if ($xp->query('.//w:br', $rNode)->length > 0) {
                    $text .= "\n";
                }

                if ($text === '') continue;

                $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                $safe = nl2br($safe);

                if ($isBold) $safe = '<strong>' . $safe . '</strong>';
                if ($isItalic) $safe = '<em>' . $safe . '</em>';

                $inner .= $safe;
            }

            $inner = trim($inner);
            if ($inner === '') continue;

            if ($isList) {
                if (!$inList) {
                    $htmlParts[] = '<ul>';
                    $inList = true;
                }
                $htmlParts[] = '<li>' . $inner . '</li>';
                continue;
            } else {
                if ($inList) {
                    $htmlParts[] = '</ul>';
                    $inList = false;
                }
            }

            $htmlParts[] = "<{$tag}>{$inner}</{$tag}>";
        }

        if ($inList) $htmlParts[] = '</ul>';

        $zip->close();

        $html = implode("\n", $htmlParts);
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html) ?? $html;

        return '<div class="course-docx">' . $html . '</div>';
    }

    private function normalizeDocxTarget(string $target): string
    {
        $target = str_replace('\\', '/', $target);
        $target = ltrim($target, '/');

        // cible fréquente : ../media/image1.png
        while (str_starts_with($target, '../')) {
            $target = substr($target, 3);
        }

        // parfois déjà préfixé word/
        if (str_starts_with($target, 'word/')) {
            $target = substr($target, 5);
        }

        return $target;
    }

    private function saveDocxImageFromRid(\ZipArchive $zip, array $relMap, string $rid, string $publicFolder): ?string
    {
        if (!isset($relMap[$rid])) return null;
        if (!empty($relMap[$rid]['external'])) return null;

        $target = (string)$relMap[$rid]['target'];
        $target = $this->normalizeDocxTarget($target);

        // chemins possibles
        $bin = $zip->getFromName('word/' . $target);
        if ($bin === false) {
            $bin = $zip->getFromName($target);
        }
        if ($bin === false) return null;

        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION)) ?: 'png';
        $name = 'img_' . substr(md5($rid . microtime(true)), 0, 12) . '.' . $ext;

        $publicFolder = trim($publicFolder, '/');
        $savePath = $publicFolder . '/' . $name;

        $ok = Storage::disk('public')->put($savePath, $bin);
        if (!$ok) {
            throw new \RuntimeException("Impossible d'écrire l'image dans storage/public (permissions).");
        }

        return '/storage/' . ltrim($savePath, '/');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF -> texte (pdftotext)
    |--------------------------------------------------------------------------
    */
    private function hasPdftotext(): bool
    {
        if (!$this->canUseShellExec()) return false;
        $check = @shell_exec('command -v pdftotext 2>/dev/null');
        return is_string($check) && trim($check) !== '';
    }

    private function extractTextFromPdf(string $path): string
    {
        if (!$this->canUseShellExec()) return '';

        $cmd = 'pdftotext -layout -enc UTF-8 ' . escapeshellarg($path) . ' - 2>/dev/null';
        $out = @shell_exec($cmd);

        if (!is_string($out)) return '';
        if ($this->looksGarbled($out)) return '';

        return $this->cleanText($out);
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
        $len = strlen($text);
        if ($len <= 0) return true;

        $rep = substr_count($text, "�");
        if ($rep > 0 && ($rep / max(1, $len)) > 0.03) return true;

        return false;
    }

    private function textToHtml(string $text): string
    {
        $text = trim($text);
        if ($text === '') return '';

        $blocks = preg_split("/\n\s*\n/", $text) ?: [];
        $out = [];

        foreach ($blocks as $b) {
            $b = trim($b);
            if ($b === '') continue;

            $lines = preg_split("/\n+/", $b) ?: [];
            $bulletLines = 0;

            foreach ($lines as $ln) {
                $ln = trim($ln);
                if (preg_match('/^(\-|\•|\*|\d+\.)\s+/', $ln)) $bulletLines++;
            }

            if (count($lines) >= 2 && $bulletLines >= (int)ceil(count($lines) * 0.6)) {
                $out[] = '<ul>';
                foreach ($lines as $ln) {
                    $ln = trim($ln);
                    $ln = preg_replace('/^(\-|\•|\*|\d+\.)\s+/', '', $ln) ?? $ln;
                    $safe = htmlspecialchars($ln, ENT_QUOTES, 'UTF-8');
                    if ($safe !== '') $out[] = "<li>{$safe}</li>";
                }
                $out[] = '</ul>';
            } else {
                $safe = htmlspecialchars($b, ENT_QUOTES, 'UTF-8');
                $safe = nl2br($safe);
                $out[] = "<p>{$safe}</p>";
            }
        }

        return '<div class="course-pdf">' . implode("\n", $out) . '</div>';
    }
}
