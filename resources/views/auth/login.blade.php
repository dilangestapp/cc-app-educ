<x-guest-layout>
    <style>
        :root{
            --bg:#050505;
            --card:#3b3b3b;
            --card-2:#2f2f2f;
            --stroke:#1f1f1f;
            --gold:#f2d27a;
            --gold-2:#d8b45b;
            --text:#f7f7f7;
            --muted:#cfcfcf;
        }

        /* Page background */
        .login-wrap{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:28px 16px;
            background: radial-gradient(1200px 700px at 20% 10%, rgba(242,210,122,.10), transparent 60%),
                        radial-gradient(900px 600px at 80% 20%, rgba(216,180,91,.08), transparent 60%),
                        var(--bg);
            position:relative;
            overflow:hidden;
        }

        /* Particles (pure CSS) */
        .login-wrap::before{
            content:"";
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at 10% 20%, rgba(242,210,122,.35) 0 2px, transparent 3px),
                radial-gradient(circle at 30% 10%, rgba(242,210,122,.25) 0 2px, transparent 3px),
                radial-gradient(circle at 65% 25%, rgba(242,210,122,.22) 0 2px, transparent 3px),
                radial-gradient(circle at 80% 15%, rgba(242,210,122,.18) 0 2px, transparent 3px),
                radial-gradient(circle at 55% 5%, rgba(242,210,122,.15) 0 2px, transparent 3px),
                radial-gradient(circle at 15% 55%, rgba(242,210,122,.12) 0 2px, transparent 3px),
                radial-gradient(circle at 90% 60%, rgba(242,210,122,.12) 0 2px, transparent 3px),
                radial-gradient(circle at 40% 70%, rgba(242,210,122,.10) 0 2px, transparent 3px);
            opacity:.9;
            filter: blur(.2px);
            pointer-events:none;
        }

        /* Gold bokeh wave at bottom */
        .login-wrap::after{
            content:"";
            position:absolute;
            left:-10%;
            right:-10%;
            bottom:-40%;
            height:65%;
            background:
                radial-gradient(circle at 15% 20%, rgba(242,210,122,.55) 0 10px, transparent 12px),
                radial-gradient(circle at 22% 40%, rgba(242,210,122,.35) 0 7px, transparent 9px),
                radial-gradient(circle at 35% 30%, rgba(242,210,122,.40) 0 9px, transparent 11px),
                radial-gradient(circle at 48% 45%, rgba(242,210,122,.25) 0 8px, transparent 10px),
                radial-gradient(circle at 60% 35%, rgba(242,210,122,.38) 0 9px, transparent 11px),
                radial-gradient(circle at 72% 50%, rgba(242,210,122,.28) 0 8px, transparent 10px),
                radial-gradient(circle at 85% 40%, rgba(242,210,122,.45) 0 10px, transparent 12px);
            transform: rotate(-10deg);
            opacity:.9;
            filter: blur(.2px);
            pointer-events:none;
        }

        /* Card */
        .card{
            width:100%;
            max-width:420px;
            background: rgba(59,59,59,.92);
            border: 1px solid rgba(255,255,255,.08);
            border-radius:26px;
            box-shadow: 0 30px 80px rgba(0,0,0,.65);
            padding:26px 22px 22px;
            position:relative;
            z-index:2;
            backdrop-filter: blur(6px);
        }

        .title{
            text-align:center;
            color:var(--gold);
            font-weight:900;
            letter-spacing:.10em;
            font-size:34px;
            margin:0 0 18px;
        }

        /* Input rows like screenshot */
        .row{
            display:grid;
            grid-template-columns: 110px 1fr;
            gap:10px;
            align-items:center;
            margin-bottom:12px;
        }

        .lab{
            background: rgba(47,47,47,.95);
            border: 1px solid rgba(0,0,0,.35);
            border-radius:10px;
            padding:10px 12px;
            color:#eaeaea;
            font-weight:800;
            font-size:13px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.06);
            text-align:left;
        }

        .inp{
            background: rgba(47,47,47,.95);
            border: 1px solid rgba(0,0,0,.35);
            border-radius:10px;
            padding:10px 12px;
            color:var(--text);
            font-size:14px;
            outline:none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.06);
        }
        .inp::placeholder{ color: rgba(255,255,255,.45); }
        .inp:focus{
            border-color: rgba(242,210,122,.55);
            box-shadow: 0 0 0 4px rgba(242,210,122,.14), inset 0 1px 0 rgba(255,255,255,.06);
        }

        .remember{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            margin:10px 0 14px;
            color: rgba(255,255,255,.75);
            font-size:13px;
        }

        .btn{
            width:100%;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            padding:12px 14px;
            border:0;
            border-radius:12px;
            background: linear-gradient(180deg, var(--gold) 0%, var(--gold-2) 100%);
            color:#1a1a1a;
            font-weight:900;
            letter-spacing:.06em;
            cursor:pointer;
            box-shadow: 0 12px 30px rgba(242,210,122,.18);
        }
        .btn:hover{ filter: brightness(.98); }

        .actions{
            display:flex;
            justify-content:center;
            margin-top:12px;
        }

        .link{
            color: rgba(242,210,122,.95);
            font-weight:900;
            letter-spacing:.08em;
            font-size:12px;
            text-decoration:none;
        }
        .link:hover{ text-decoration:underline; }

        .errors{
            margin:0 0 12px;
            padding:10px 12px;
            border-radius:12px;
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            color:#ffd6d6;
            font-size:13px;
        }

        @media (max-width:420px){
            .row{ grid-template-columns: 100px 1fr; }
            .title{ font-size:30px; }
        }
    </style>
<div style="display:flex; justify-content:center; margin-bottom:12px;">
    <img src="{{ asset('images/logo.png') }}"
         alt="CC-APP-EDUC"
         style="width:88px; height:88px; object-fit:contain; border-radius:18px; background:rgba(255,255,255,.06); padding:10px; border:1px solid rgba(255,255,255,.10);">
</div>

    <div class="login-wrap">
        <div class="card">

            <div class="title">LOGIN</div>

            @if ($errors->any())
                <div class="errors">
                    @foreach ($errors->all() as $e)
                        <div>• {{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- IMPORTANT:
                   Breeze attend "email". Même si tu veux pseudo, on garde name="email"
                   et tu peux entrer pseudo OU email selon ta logique serveur.
                --}}
                <div class="row">
                    <div class="lab">Name</div>
                    <input class="inp" type="text" name="email" value="{{ old('email') }}"
                           placeholder="Pseudo ou Email" required autofocus autocomplete="username">
                </div>

                <div class="row">
                    <div class="lab">Password</div>
                    <input class="inp" type="password" name="password"
                           placeholder="********" required autocomplete="current-password">
                </div>

                <div class="remember">
                    <input id="remember_me" type="checkbox" name="remember"
                           style="accent-color: var(--gold); width:16px; height:16px;">
                    <label for="remember_me">Remember me</label>
                </div>

                <button class="btn" type="submit">
                    <span style="font-size:16px;">🔒</span>
                    <span>LOGIN</span>
                </button>

                <div class="actions">
                    <a class="link" href="{{ route('register') }}">[ SIGN UP ]</a>
                </div>
            </form>

        </div>
    </div>
</x-guest-layout>
