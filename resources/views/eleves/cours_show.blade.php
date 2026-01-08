<x-app-layout>
    <style>
        .page-wrap{ padding:18px 14px; }
        .topbar{ display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px; }
        .title{ font-size:22px; font-weight:900; line-height:1.15; margin:0; }
        .muted{ color:#64748b; font-size:13px; margin-top:4px; }
        .btn{ display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:10px; border:1px solid rgba(15,23,42,.12); background:#fff; font-weight:800; font-size:13px; }

        .card{ background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:14px; padding:16px; }

        .course-prose{ font-size:15px; line-height:1.75; color:#0f172a; word-break: break-word; }
        .course-prose h1,.course-prose h2,.course-prose h3,.course-prose h4{ font-weight:900; line-height:1.2; margin:18px 0 10px; }
        .course-prose h1{ font-size:22px; } .course-prose h2{ font-size:19px; } .course-prose h3{ font-size:17px; } .course-prose h4{ font-size:16px; }
        .course-prose p{ margin:10px 0; }
        .course-prose ul,.course-prose ol{ padding-left:20px; margin:10px 0; }
        .course-prose li{ margin:6px 0; }
        .course-prose blockquote{ margin:12px 0; padding:10px 12px; border-left:4px solid rgba(15,23,42,.18); background:rgba(15,23,42,.03); border-radius:10px; }
        .course-prose pre{ white-space:pre-wrap; background:rgba(15,23,42,.03); border:1px solid rgba(15,23,42,.08); padding:12px; border-radius:12px; overflow:auto; }

        .course-prose img{ max-width:100%; height:auto; display:block; margin:12px auto; border-radius:12px; border:1px solid rgba(15,23,42,.08); }
        .course-prose img:not([src]), .course-prose img[src=""]{ display:none !important; }

        .table-wrap{ overflow:auto; border:1px solid rgba(15,23,42,.08); border-radius:12px; margin:12px 0; }
        .course-prose table{ width:100%; border-collapse:collapse; min-width:520px; font-size:14px; }
        .course-prose th,.course-prose td{ padding:10px 10px; border-bottom:1px solid rgba(15,23,42,.08); vertical-align:top; }
        .course-prose th{ background:rgba(15,23,42,.03); font-weight:900; }

        @media (max-width: 640px){
            .title{ font-size:18px; }
            .card{ padding:14px; }
            .course-prose{ font-size:14px; line-height:1.7; }
        }
    </style>

    <div class="page-wrap">
        <div class="topbar">
            <div>
                <h1 class="title">{{ $title }}</h1>
                <div class="muted">Cours #{{ $id }}</div>
            </div>

            <a class="btn" href="{{ route('eleve.cours') }}">← Retour</a>
        </div>

        <div class="card">
            <div class="course-prose">
                @if(!empty($isHtml) && $isHtml)
                    {!! $content !!}
                @else
                    {!! nl2br(e($content)) !!}
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
