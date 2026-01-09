<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EleveQuizController extends Controller
{
    public function index()
    {
        abort_if(!Schema::hasTable('quizzes'), 500, "Table 'quizzes' manquante. Lance les migrations Quiz.");
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");
        abort_if(!Schema::hasTable('classes'), 500, "Table 'classes' manquante.");

        $user = Auth::user();
        $classeId = (int)($user->classe_id ?? 0);

        if ($classeId <= 0) {
            return redirect()->route('eleve.classe.edit')->with('error', "Ta classe n'est pas définie. Choisis ta classe pour accéder aux quiz.");
        }

        $quizzes = DB::table('quizzes')
            ->join('matieres', 'matieres.id', '=', 'quizzes.matiere_id')
            ->join('classes', 'classes.id', '=', 'quizzes.classe_id')
            ->select(
                'quizzes.*',
                'matieres.nom as matiere_nom',
                'classes.nom as classe_nom'
            )
            ->where('quizzes.classe_id', $classeId)
            ->where('quizzes.is_published', 1)
            ->orderByDesc('quizzes.published_at')
            ->orderByDesc('quizzes.id')
            ->get();

        $quizIds = $quizzes->pluck('id')->map(fn($v) => (int)$v)->all();

        $lastAttempts = [];
        if (!empty($quizIds) && Schema::hasTable('quiz_attempts')) {
            $uid = (int)Auth::id();
            $sql = '(SELECT quiz_id, MAX(id) AS max_id FROM quiz_attempts WHERE user_id = '.$uid.' GROUP BY quiz_id) AS la';

            $rows = DB::table('quiz_attempts as a')
                ->join(DB::raw($sql), 'a.id', '=', DB::raw('la.max_id'))
                ->get();

            foreach ($rows as $r) {
                $lastAttempts[(int)$r->quiz_id] = $r;
            }
        }

        return view('eleve.quiz.index', compact('quizzes', 'lastAttempts'));
    }

    public function show($quiz)
    {
        $quizId = (int)$quiz;

        abort_if(!Schema::hasTable('quizzes'), 500, "Table 'quizzes' manquante.");
        abort_if(!Schema::hasTable('quiz_questions'), 500, "Table 'quiz_questions' manquante.");
        abort_if(!Schema::hasTable('quiz_options'), 500, "Table 'quiz_options' manquante.");
        abort_if(!Schema::hasTable('quiz_attempts'), 500, "Table 'quiz_attempts' manquante.");

        $user = Auth::user();
        $uid = (int)Auth::id();
        $classeId = (int)($user->classe_id ?? 0);

        if ($classeId <= 0) {
            return redirect()->route('eleve.classe.edit')->with('error', "Ta classe n'est pas définie. Choisis ta classe pour accéder aux quiz.");
        }

        $quizRow = DB::table('quizzes')
            ->join('matieres', 'matieres.id', '=', 'quizzes.matiere_id')
            ->select('quizzes.*', 'matieres.nom as matiere_nom')
            ->where('quizzes.id', $quizId)
            ->where('quizzes.classe_id', $classeId)
            ->where('quizzes.is_published', 1)
            ->first();

        abort_if(!$quizRow, 404);

        // Respect max_attempts
        $maxAttempts = (int)($quizRow->max_attempts ?? 0);
        if ($maxAttempts > 0) {
            $finishedCount = (int)DB::table('quiz_attempts')
                ->where('quiz_id', $quizId)
                ->where('user_id', $uid)
                ->where('status', 'finished')
                ->count();

            if ($finishedCount >= $maxAttempts) {
                return redirect()->route('eleve.quiz')->with('error', "Nombre maximum de tentatives atteint pour ce quiz.");
            }
        }

        // Questions + options
        $questions = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        if ($questions->count() === 0) {
            return redirect()->route('eleve.quiz')->with('error', "Ce quiz n'a pas encore de questions.");
        }

        $qIds = $questions->pluck('id')->map(fn($v) => (int)$v)->all();

        $optionsRows = DB::table('quiz_options')
            ->whereIn('question_id', $qIds)
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $optionsByQuestion = [];
        foreach ($optionsRows as $o) {
            $optionsByQuestion[(int)$o->question_id][] = $o;
        }

        // Total points
        $total = 0;
        foreach ($questions as $q) $total += (int)$q->points;

        // Reprendre tentative started si existe
        $attempt = DB::table('quiz_attempts')
            ->where('quiz_id', $quizId)
            ->where('user_id', $uid)
            ->where('status', 'started')
            ->orderByDesc('id')
            ->first();

        if (!$attempt) {
            $attemptNo = (int)DB::table('quiz_attempts')
                ->where('quiz_id', $quizId)
                ->where('user_id', $uid)
                ->count() + 1;

            $attemptId = DB::table('quiz_attempts')->insertGetId([
                'quiz_id' => $quizId,
                'user_id' => $uid,
                'attempt_no' => $attemptNo,
                'started_at' => now(),
                'status' => 'started',
                'score' => 0,
                'total' => $total,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $attempt = DB::table('quiz_attempts')->where('id', $attemptId)->first();
        }

        // Anciennes réponses si tentative en cours
        $prevAnswers = [];
        if (Schema::hasTable('quiz_answers')) {
            $rows = DB::table('quiz_answers')
                ->where('attempt_id', (int)$attempt->id)
                ->get();
            foreach ($rows as $a) {
                $prevAnswers[(int)$a->question_id] = (int)($a->option_id ?? 0);
            }
        }

        return view('eleve.quiz.show', [
            'quiz' => $quizRow,
            'attempt' => $attempt,
            'questions' => $questions,
            'optionsByQuestion' => $optionsByQuestion,
            'prevAnswers' => $prevAnswers,
            'total' => $total,
        ]);
    }

    public function submit(Request $request, $quiz)
    {
        $quizId = (int)$quiz;
        $uid = (int)Auth::id();
        $user = Auth::user();
        $classeId = (int)($user->classe_id ?? 0);

        abort_if(!Schema::hasTable('quizzes'), 500, "Table 'quizzes' manquante.");
        abort_if(!Schema::hasTable('quiz_questions'), 500, "Table 'quiz_questions' manquante.");
        abort_if(!Schema::hasTable('quiz_options'), 500, "Table 'quiz_options' manquante.");
        abort_if(!Schema::hasTable('quiz_attempts'), 500, "Table 'quiz_attempts' manquante.");
        abort_if(!Schema::hasTable('quiz_answers'), 500, "Table 'quiz_answers' manquante.");

        if ($classeId <= 0) {
            return redirect()->route('eleve.classe.edit')->with('error', "Ta classe n'est pas définie.");
        }

        $quizRow = DB::table('quizzes')
            ->where('id', $quizId)
            ->where('classe_id', $classeId)
            ->where('is_published', 1)
            ->first();
        abort_if(!$quizRow, 404);

        $attemptId = (int)($request->input('attempt_id') ?? 0);
        if ($attemptId <= 0) {
            return redirect()->route('eleve.quiz.show', $quizId)->with('error', "Tentative introuvable.");
        }

        $attempt = DB::table('quiz_attempts')
            ->where('id', $attemptId)
            ->where('quiz_id', $quizId)
            ->where('user_id', $uid)
            ->first();

        if (!$attempt || (string)$attempt->status !== 'started') {
            return redirect()->route('eleve.quiz')->with('error', "Cette tentative n'est plus active.");
        }

        // Questions
        $questions = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $qIds = $questions->pluck('id')->map(fn($v) => (int)$v)->all();

        // Options correctes
        $correctOptions = DB::table('quiz_options')
            ->whereIn('question_id', $qIds)
            ->where('is_correct', 1)
            ->get();

        $correctByQuestion = [];
        foreach ($correctOptions as $o) {
            $correctByQuestion[(int)$o->question_id] = (int)$o->id;
        }

        // Nettoyer anciennes réponses (si resubmit)
        DB::table('quiz_answers')->where('attempt_id', $attemptId)->delete();

        $answers = $request->input('answers', []);
        if (!is_array($answers)) $answers = [];

        $score = 0;
        $total = 0;

        foreach ($questions as $q) {
            $qid = (int)$q->id;
            $points = (int)$q->points;
            $total += $points;

            $chosen = isset($answers[$qid]) ? (int)$answers[$qid] : 0;
            $correctId = $correctByQuestion[$qid] ?? 0;

            $isCorrect = ($chosen > 0 && $correctId > 0 && $chosen === $correctId);
            $awarded = $isCorrect ? $points : 0;
            $score += $awarded;

            DB::table('quiz_answers')->insert([
                'attempt_id' => $attemptId,
                'question_id' => $qid,
                'option_id' => $chosen > 0 ? $chosen : null,
                'is_correct' => $isCorrect ? 1 : 0,
                'points_awarded' => $awarded,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $startedAt = $attempt->started_at ? strtotime((string)$attempt->started_at) : null;
        $duration = $startedAt ? max(0, time() - $startedAt) : null;

        DB::table('quiz_attempts')->where('id', $attemptId)->update([
            'finished_at' => now(),
            'status' => 'finished',
            'score' => $score,
            'total' => $total,
            'duration_seconds' => $duration,
            'updated_at' => now(),
        ]);

        return redirect()->route('eleve.quiz.result', ['quiz' => $quizId, 'attempt' => $attemptId]);
    }

    public function result($quiz, $attempt)
    {
        $quizId = (int)$quiz;
        $attemptId = (int)$attempt;
        $uid = (int)Auth::id();
        $user = Auth::user();
        $classeId = (int)($user->classe_id ?? 0);

        abort_if(!Schema::hasTable('quiz_attempts'), 500, "Table 'quiz_attempts' manquante.");
        abort_if(!Schema::hasTable('quiz_answers'), 500, "Table 'quiz_answers' manquante.");
        abort_if(!Schema::hasTable('quiz_questions'), 500, "Table 'quiz_questions' manquante.");
        abort_if(!Schema::hasTable('quiz_options'), 500, "Table 'quiz_options' manquante.");
        abort_if(!Schema::hasTable('quizzes'), 500, "Table 'quizzes' manquante.");

        if ($classeId <= 0) {
            return redirect()->route('eleve.classe.edit')->with('error', "Ta classe n'est pas définie.");
        }

        $quizRow = DB::table('quizzes')
            ->join('matieres', 'matieres.id', '=', 'quizzes.matiere_id')
            ->select('quizzes.*', 'matieres.nom as matiere_nom')
            ->where('quizzes.id', $quizId)
            ->where('quizzes.classe_id', $classeId)
            ->first();
        abort_if(!$quizRow, 404);

        $attemptRow = DB::table('quiz_attempts')
            ->where('id', $attemptId)
            ->where('quiz_id', $quizId)
            ->where('user_id', $uid)
            ->where('status', 'finished')
            ->first();
        abort_if(!$attemptRow, 404);

        $questions = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $qIds = $questions->pluck('id')->map(fn($v) => (int)$v)->all();

        $optionsRows = DB::table('quiz_options')
            ->whereIn('question_id', $qIds)
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $optionsByQuestion = [];
        $correctByQuestion = [];
        foreach ($optionsRows as $o) {
            $optionsByQuestion[(int)$o->question_id][] = $o;
            if ((int)$o->is_correct === 1) {
                $correctByQuestion[(int)$o->question_id] = (int)$o->id;
            }
        }

        $answersRows = DB::table('quiz_answers')
            ->where('attempt_id', $attemptId)
            ->get();

        $chosenByQuestion = [];
        foreach ($answersRows as $a) {
            $chosenByQuestion[(int)$a->question_id] = (int)($a->option_id ?? 0);
        }

        return view('eleve.quiz.result', [
            'quiz' => $quizRow,
            'attempt' => $attemptRow,
            'questions' => $questions,
            'optionsByQuestion' => $optionsByQuestion,
            'chosenByQuestion' => $chosenByQuestion,
            'correctByQuestion' => $correctByQuestion,
        ]);
    }
}
