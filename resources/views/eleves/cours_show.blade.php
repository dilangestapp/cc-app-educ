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

    <style>
        .course-content img{ max-width:100%; height:auto; border-radius:12px; }
        .course-content h2{ font-size:1.25rem; font-weight:800; margin:18px 0 10px; }
        .course-content h3{ font-size:1.1rem; font-weight:800; margin:16px 0 8px; }
        .course-content p{ margin:10px 0; line-height:1.65; }
        .course-content .docx-image{ margin:14px 0; }
        .course-card{ border:1px solid #eef2f7; }
        @media(max-width:640px){
            .course-card{ padding:14px !important; }
        }
    </style>

    @php
        $content = $content ?? '';
        $isHtml = is_string($content) && preg_match('/<\s*(div|p|h1|h2|h3|ul|ol|li|table|img|br)\b/i', $content);
    @endphp

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl course-card p-6">
                <div class="text-xs text-gray-500 mb-4">Cours #{{ $id }}</div>

                <div class="course-content">
                    @if($isHtml)
                        {!! $content !!}
                    @else
                        {!! nl2br(e($content)) !!}
                    @endif
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
