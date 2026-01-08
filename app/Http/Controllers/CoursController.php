<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * ✅ Page dédiée à l’import (route('cours.import'))
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
     * ✅ POST import (route('cours.import.store'))
     * Remplit automatiquement titre + contenu (HTML propre) + images (DOCX).
     */
    public function importStore(Request $request, $matiere)
    {
        $matiereId = (int)$matiere;

        $request->validate([
            'fichier' => 'required|file|max:51200', // 50MB
        ]);

        if (!$request->hasFile('fichier')) {
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Aucun fichier reçu. Vérifie upload_max_filesize/post_max_size.");
        }

        $file = $request->file('fichier');
        if (!$file->isValid()) {
            $err = $file->getError();
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Upload échoué (code $err). Souvent dû à la taille/limites PHP.");
        }

        $ext = strtolower((string)$file->getClientOriginalExtension());
        if (!in_array($ext, ['docx', 'pdf'], true)) {
            return redirect()->route('cours.import', $matiereId)
                ->with('error', "Format non supporté. Utilise DOCX ou PDF.");
        }

        // Stockage temporaire (local disk)
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

        $originalTitle = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $title = $originalTitle ?: ('Cours_' . date('Ymd_His'));

        // ✅ Extraction
        $html = '';
        if ($ext === 'docx') {
            $folder = 'cours_images/' . date('Ymd_His') . '_' . Str::lower(Str::random(6));
            $html = $this->docxToHtmlWithImages($tmpPath, $folder);
        } else { // pdf
            $text = $this->extractTextFromPdf($tmpPath);
            $text = $this->cleanText((string)$text);
            if ($text !== '') {
                $html = $this->plainTextToHtml($text);
            }
        }

        // Nettoyage fichier temp
        @unlink($tmpPath);

        if (trim($html) === '') {
            $msg = ($ext === 'pdf')
                ? "PDF illisible (pas de texte récupérable). ✅ Essaie en DOCX, ou installe pdftotext côté serveur."
                : "Impossible de lire le DOCX. (ZipArchive manquant ou docx invalide).";
            return redirect()->route('cours.import', $matiereId)->with('error', $msg);
        }

        return redirect()
            ->route('cours.create', $matiereId)
            ->withInput([
                'titre'   => $title,
                'contenu' => $html,   // ✅ HTML prêt à enregistrer
                'actif'   => 1,
            ])
            ->with('success', 'Import OK : Titre + Contenu (formaté) pré-remplis.');
    }

    public function store(Request $request, $matiere)
    {
        // ✅ si jamais un vieux formulaire post encore ici avec _action=import
        if ($request->input('_action') === 'import') {
            return $this->importStore($request, $matiere);
        }

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
    // ✅ DOCX -> HTML + images
    // ======================================================

    private function docxToHtmlWithImages(string $path, string $publicFolder): string
    {
        if (!class_exists(\ZipArchive::class)) return '';

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return '';

        $docXml = $zip->getFromName('word/document.xml');
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');

        if ($docXml === false) { $zip->close(); return ''; }

        $relsMap = [];
        if ($relsXml !== false) {
            $relsDom = new \DOMDocument();
            $relsDom->loadXML($relsXml);

            $relsXpath = new \DOMXPath($relsDom);
            $relsXpath->registerNamespace('rels', 'http://schemas.openxmlformats.org/package/2006/relationships');

            foreach ($relsXpath->query('//rels:Relationship') as $rel) {
                /** @var \DOMElement $rel */
                $id = $rel->getAttribute('Id');
                $target = $rel->getAttribute('Target');
                if ($id && $target) {
                    $relsMap[$id] = $target; // ex: media/image1.png
                }
            }
        }

        // Prépare dossier public
        if (!Storage::disk('public')->exists($publicFolder)) {
            Storage::disk('public')->makeDirectory($publicFolder);
        }

        $extractedImages = []; // rid => url

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($docXml);

        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xp->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xp->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        $htmlParts = [];

        // Parcours body: paragraphes + tableaux
        $body = $xp->query('//w:body')->item(0);
        if (!$body) { $zip->close(); return ''; }

        foreach ($body->childNodes as $node) {
            if (!$node instanceof \DOMElement) continue;

            if ($node->namespaceURI === 'http://schemas.openxmlformats.org/wordprocessingml/2006/main' && $node->localName === 'p') {
                $htmlParts[] = $this->renderDocxParagraph($xp, $node, $zip, $relsMap, $publicFolder, $extractedImages);
            }

            if ($node->namespaceURI === 'http://schemas.openxmlformats.org/wordprocessingml/2006/main' && $node->localName === 'tbl') {
                $htmlParts[] = $this->renderDocxTable($xp, $node, $zip, $relsMap, $publicFolder, $extractedImages);
            }
        }

        $zip->close();

        $out = trim(implode("\n", array_filter($htmlParts)));
        return $out;
    }

    private function renderDocxParagraph(\DOMXPath $xp, \DOMElement $p, \ZipArchive $zip, array $relsMap, string $publicFolder, array &$extractedImages): string
    {
        $style = '';
        $styleNode = $xp->query('./w:pPr/w:pStyle', $p)->item(0);
        if ($styleNode instanceof \DOMElement) {
            $style = $styleNode->getAttribute('w:val') ?: $styleNode->getAttribute('val') ?: '';
        }

        $tag = 'p';
        if (preg_match('/Heading1|Titre1|Title/i', $style)) $tag = 'h1';
        elseif (preg_match('/Heading2|Titre2/i', $style)) $tag = 'h2';
        elseif (preg_match('/Heading3|Titre3/i', $style)) $tag = 'h3';

        $inner = [];

        foreach ($xp->query('./w:r', $p) as $r) {
            /** @var \DOMElement $r */

            // image ?
            $blip = $xp->query('.//a:blip', $r)->item(0);
            if ($blip instanceof \DOMElement) {
                $rid = $blip->getAttribute('r:embed') ?: $blip->getAttribute('embed');
                if ($rid) {
                    $src = $this->extractDocxImageIfNeeded($zip, $rid, $relsMap, $publicFolder, $extractedImages);
                    if ($src) $inner[] = '<img src="'.e($src).'" alt="" />';
                    continue;
                }
            }

            // texte
            $text = '';
            foreach ($xp->query('.//w:t', $r) as $t) {
                $text .= $t->textContent;
            }

            // saut de ligne
            if ($xp->query('.//w:br', $r)->length > 0) {
                $text .= "\n";
            }

            $text = $this->escapeText($text);
            if ($text === '') continue;

            $isBold = $xp->query('./w:rPr/w:b', $r)->length > 0;
            $isItalic = $xp->query('./w:rPr/w:i', $r)->length > 0;

            if ($isItalic) $text = '<em>'.$text.'</em>';
            if ($isBold) $text = '<strong>'.$text.'</strong>';

            $inner[] = $text;
        }

        $content = trim(implode('', $inner));
        if ($content === '') return '';

        // si le paragraphe contient des "\n", on garde <br>
        $content = nl2br($content, false);

        return "<{$tag}>{$content}</{$tag}>";
    }

    private function renderDocxTable(\DOMXPath $xp, \DOMElement $tbl, \ZipArchive $zip, array $relsMap, string $publicFolder, array &$extractedImages): string
    {
        $rowsHtml = [];

        foreach ($xp->query('./w:tr', $tbl) as $tr) {
            $cellsHtml = [];
            foreach ($xp->query('./w:tc', $tr) as $tc) {
                $cellParts = [];
                foreach ($xp->query('.//w:p', $tc) as $p) {
                    if ($p instanceof \DOMElement) {
                        $cellParts[] = $this->renderDocxParagraph($xp, $p, $zip, $relsMap, $publicFolder, $extractedImages);
                    }
                }
                $cell = trim(implode('', array_filter($cellParts)));
                if ($cell === '') $cell = '&nbsp;';
                $cellsHtml[] = '<td>'.$cell.'</td>';
            }
            $rowsHtml[] = '<tr>'.implode('', $cellsHtml).'</tr>';
        }

        if (!$rowsHtml) return '';
        return '<div class="table-wrap"><table>'.implode('', $rowsHtml).'</table></div>';
    }

    private function extractDocxImageIfNeeded(\ZipArchive $zip, string $rid, array $relsMap, string $publicFolder, array &$extractedImages): ?string
    {
        if (isset($extractedImages[$rid])) {
            return $extractedImages[$rid];
        }

        $target = $relsMap[$rid] ?? null; // ex media/image1.png
        if (!$target) return null;

        $target = ltrim($target, '/');
        $zipPath = str_starts_with($target, 'word/') ? $target : 'word/'.$target;

        $data = $zip->getFromName($zipPath);
        if ($data === false) return null;

        $base = basename($target);
        $ext = pathinfo($base, PATHINFO_EXTENSION);
        if (!$ext) $ext = 'png';

        $filename = 'img_'.$rid.'.'.$ext;
        $storePath = $publicFolder.'/'.$filename;

        Storage::disk('public')->put($storePath, $data);

        $url = '/storage/'.$storePath;
        $extractedImages[$rid] = $url;

        return $url;
    }

    private function escapeText(string $t): string
    {
        $t = str_replace("\0", '', $t);
        return htmlspecialchars($t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function plainTextToHtml(string $text): string
    {
        $text = trim($text);
        if ($text === '') return '';

        $paras = preg_split("/\n{2,}/", $text) ?: [];
        $out = [];
        foreach ($paras as $p) {
            $p = trim($p);
            if ($p === '') continue;
            $out[] = '<p>'.nl2br($this->escapeText($p), false).'</p>';
        }
        return implode("\n", $out);
    }

    // ======================================================
    // ✅ PDF -> texte (si pdftotext dispo)
    // ======================================================

    private function extractTextFromPdf(string $path): string
    {
        if ($this->canUseShellExec()) {
            // Linux: nécessite poppler-utils
            $cmd = 'pdftotext -layout -enc UTF-8 ' . escapeshellarg($path) . ' - 2>/dev/null';
            $out = @shell_exec($cmd);
            if (is_string($out) && !$this->looksGarbled($out)) {
                return $out;
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

    private function looksGarbled(string $text): bool
    {
        $text = (string)$text;
        if (trim($text) === '') return true;

        $len = strlen($text);
        if ($len <= 0) return true;

        $rep = substr_count($text, "�");
        return ($rep > 0 && ($rep / max(1, $len)) > 0.03);
    }
}
