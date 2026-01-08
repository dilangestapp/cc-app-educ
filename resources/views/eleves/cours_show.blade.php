<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">{{ $title }}</h2>
            <a href="{{ route('eleve.cours') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour cours
            </a>
        </div>
    </x-slot>

    @php
        $raw = (string) ($content ?? '');
        $looksHtml = preg_match('/<\s*(p|h\d|ul|ol|li|table|img|br|div|span|strong|em|blockquote|pre|code)\b/i', $raw) === 1;
        $rendered = $looksHtml ? $raw : nl2br(e($raw));
    @endphp

    <style>
        .course-wrap { background:#fff; border:1px solid #eef2f7; border-radius: 16px; }
        .course-meta { color:#64748b; font-size:12px; }

        .course-content { font-size: 15px; line-height: 1.75; color:#0f172a; }
        .course-content h1 { font-size: 24px; font-weight: 800; margin: 18px 0 10px; }
        .course-content h2 { font-size: 20px; font-weight: 800; margin: 16px 0 10px; }
        .course-content h3 { font-size: 17px; font-weight: 800; margin: 14px 0 8px; }
        .course-content p { margin: 10px 0; }
        .course-content img { max-width: 100%; height: auto; border-radius: 12px; margin: 12px 0; border:1px solid #e5e7eb; }
        .course-content .table-wrap { width: 100%; overflow-x: auto; margin: 12px 0; }
        .course-content table { width: 100%; border-collapse: collapse; min-width: 520px; }
        .course-content td, .course-content th { border: 1px solid #e5e7eb; padding: 8px 10px; vertical-align: top; }
        .course-content strong { font-weight: 800; }

        @media (max-width: 640px) {
            .course-content { font-size: 14px; }
            .course-content h1 { font-size: 20px; }
            .course-content h2 { font-size: 18px; }
        }
    </style>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="course-wrap shadow-sm p-5 sm:p-6">
                <div class="course-meta mb-4">Cours #{{ $id }}</div>

                <div class="course-content">
                    {!! $rendered !!}
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('eleve.quiz') }}"
                       class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Aller aux quiz
                    </a>
                    <a href="{{ route('eleve.questions') }}"
                       class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                        Poser une question
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
