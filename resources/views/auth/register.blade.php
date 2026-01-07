<x-guest-layout>
    <style>
        :root{
            --green:#5fb54f;
            --deep-green:#2e8d58;
            --teal:#0f766e;
            --teal-dark:#0b5f68;
            --ink:#0f172a;
            --muted:#64748b;
            --paper:#ffffff;
            --line:#e5e7eb;
        }

        .reg-page{
            min-height:100vh;
            padding:24px;
            display:flex;
            align-items:flex-start;
            justify-content:center;
            background:
                radial-gradient(900px 500px at 10% 0%, rgba(95,181,79,.18), transparent 55%),
                radial-gradient(900px 500px at 90% 10%, rgba(15,118,110,.18), transparent 55%),
                #eef2f7;
        }

        .sheet{
            width:100%;
            max-width:980px;
            background:var(--paper);
            border:1px solid rgba(15,23,42,.08);
            border-radius:18px;
            overflow:hidden;
            box-shadow: 0 20px 50px rgba(15,23,42,.10);
        }

        /* HEADER comme ton template */
        .hero{
            position:relative;
            padding:34px 28px;
            background: linear-gradient(90deg, var(--teal) 0%, var(--green) 70%);
            color:#fff;
        }
        .hero:after{
            content:"";
            position:absolute;
            right:-80px;
            top:-80px;
            width:260px;
            height:260px;
            background: rgba(255,255,255,.14);
            transform: rotate(22deg);
            border-radius:28px;
        }
        .hero:before{
            content:"";
            position:absolute;
            right:40px;
            top:18px;
            width:120px;
            height:120px;
            background: rgba(11,95,104,.35);
            transform: rotate(22deg);
            border-radius:22px;
            border:1px solid rgba(255,255,255,.22);
        }
        .brand{
            font-weight:800;
            letter-spacing:.08em;
            font-size:12px;
            opacity:.95;
        }
        .hero h1{
            margin:10px 0 6px;
            font-size:40px;
            line-height:1.02;
            font-weight:900;
            letter-spacing:.02em;
        }
        .hero p{
            margin:0;
            font-size:14px;
            opacity:.92;
            max-width:620px;
        }

        .body{
            padding:22px 28px 26px;
        }

        .alert{
            border-radius:14px;
            padding:12px 14px;
            margin-bottom:14px;
            font-size:13px;
        }
        .alert.err{ background:rgba(239,68,68,.10); border:1px solid rgba(239,68,68,.25); color:#7f1d1d; }
        .alert.ok{ background:rgba(34,197,94,.10); border:1px solid rgba(34,197,94,.25); color:#14532d; }

        .section{
            padding:18px 0;
            border-top:1px solid var(--line);
        }
        .section:first-of-type{ border-top:0; padding-top:6px; }
        .section h2{
            margin:0 0 14px;
            font-size:18px;
            font-weight:900;
            color: var(--deep-green);
            letter-spacing:.02em;
            text-transform:uppercase;
        }

        /* Lignes (label : champ) comme le template */
        .rows{
            display:grid;
            gap:12px;
        }
        .row{
            display:grid;
            grid-template-columns: 240px 20px 1fr;
            align-items:center;
            gap:12px;
        }
        .lab{
            font-size:14px;
            color: var(--ink);
            font-weight:700;
        }
        .colon{
            font-weight:900;
            color: var(--muted);
            text-align:center;
        }

        input, select{
            width:100%;
            padding:12px 12px;
            border-radius:12px;
            border:1px solid #cbd5e1;
            background:#fff;
            color:var(--ink);
            outline:none;
            font-size:14px;
        }
        input:focus, select:focus{
            border-color: rgba(15,118,110,.55);
            box-shadow: 0 0 0 4px rgba(15,118,110,.12);
        }

        .hint{
            margin-top:10px;
            font-size:12px;
            color:var(--muted);
        }

        .actions{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding-top:18px;
            border-top:1px solid var(--line);
        }
        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:12px 16px;
            border-radius:12px;
            font-weight:900;
            border:0;
            cursor:pointer;
            background: linear-gradient(90deg, var(--teal-dark), var(--green));
            color:#fff;
            min-width:180px;
        }
        .btn:hover{ filter: brightness(.98); }
        .link{
            font-size:13px;
            color: var(--teal-dark);
            text-decoration:none;
            font-weight:800;
        }
        .link:hover{ text-decoration:underline; }

        /* Responsive mobile */
        @media (max-width: 820px){
            .hero h1{ font-size:30px; }
            .row{ grid-template-columns: 1fr; }
            .colon{ display:none; }
            .lab{ margin-bottom:4px; }
        }
    </style>

    <div class="reg-page">
        <div class="sheet">

            <div class="hero">
                <div class="brand">CC-APP-EDUC</div>
                <h1>INSCRIPTION</h1>
                <p>Crée ton compte avec un <b>pseudo</b> (pas de nom réel). Choisis ton type de compte.</p>
            </div>

            <div class="body">

                @if ($errors->any())
                    <div class="alert err">
                        <b>Erreurs :</b>
                        <ul style="margin:8px 0 0 18px;">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert ok">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="section">
                        <h2>INFORMATIONS DU COMPTE</h2>

                        <div class="rows">
                            <div class="row">
                                <div class="lab">Pseudo</div>
                                <div class="colon">:</div>
                                <div>
                                    <input name="pseudo" value="{{ old('pseudo') }}" required autofocus placeholder="Ex: user_1, eleve123...">
                                </div>
                            </div>

                            <div class="row">
                                <div class="lab">Email (optionnel)</div>
                                <div class="colon">:</div>
                                <div>
                                    <input name="email" type="email" value="{{ old('email') }}" placeholder="Optionnel si tu ne veux pas utiliser email">
                                </div>
                            </div>
                        </div>

                        <div class="hint">Astuce : tu peux laisser l’email vide si tu veux travailler uniquement au pseudo.</div>
                    </div>

                    <div class="section">
                        <h2>TYPE DE COMPTE</h2>

                        <div class="rows">
                            <div class="row">
                                <div class="lab">Type de compte</div>
                                <div class="colon">:</div>
                                <div>
                                    <select name="type_compte" required>
                                        <option value="">-- Choisir --</option>
                                        <option value="eleve" {{ old('type_compte')==='eleve'?'selected':'' }}>Élève</option>
                                        <option value="enseignant" {{ old('type_compte')==='enseignant'?'selected':'' }}>Enseignant</option>
                                        <option value="parent" {{ old('type_compte')==='parent'?'selected':'' }}>Parent</option>
                                        <option value="admin" {{ old('type_compte')==='admin'?'selected':'' }}>Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <h2>SÉCURITÉ</h2>

                        <div class="rows">
                            <div class="row">
                                <div class="lab">Mot de passe</div>
                                <div class="colon">:</div>
                                <div>
                                    <input name="password" type="password" required autocomplete="new-password" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="row">
                                <div class="lab">Confirmer le mot de passe</div>
                                <div class="colon">:</div>
                                <div>
                                    <input name="password_confirmation" type="password" required autocomplete="new-password" placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn" type="submit">Créer le compte</button>
                        <a class="link" href="{{ route('login') }}">Déjà inscrit ? Se connecter</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
