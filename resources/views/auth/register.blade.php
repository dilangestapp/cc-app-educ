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
        .wrap{
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

        /* Particles */
        .wrap::before{
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

        /* Gold bokeh wave */
        .wrap::after{
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

        /* Stack */
        .stack{
            width:100%;
            max-width:820px;
            position:relative;
            z-index:2;
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:14px;
        }

        /* Top branding */
        .brand-top{
            text-align:center;
        }
        .brand-top img{
            width:108px;
            height:108px;
            object-fit:contain;
            border-radius:22px;
            background:rgba(0,0,0,.28);
            padding:10px;
            border:1px solid rgba(242,210,122,.45);
            box-shadow:0 14px 40px rgba(242,210,122,.10);
            display:block;
            margin:0 auto 10px;
        }
        .brand-top .appname{
            color:var(--gold);
            font-weight:1000;
            letter-spacing:.14em;
            font-size:18px;
            margin:0;
        }
        .brand-top .tagline{
            color:rgba(255,255,255,.72);
            font-size:12px;
            margin-top:2px;
        }

        /* Card */
        .card{
            width:100%;
            background: rgba(59,59,59,.92);
            border: 1px solid rgba(255,255,255,.08);
            border-radius:26px;
            box-shadow: 0 30px 80px rgba(0,0,0,.65);
            padding:22px 22px 18px;
            backdrop-filter: blur(6px);
        }

        .title{
            text-align:center;
            color:var(--gold);
            font-weight:900;
            letter-spacing:.10em;
            font-size:30px;
            margin:0 0 6px;
        }
        .subtitle{
            text-align:center;
            color:rgba(255,255,255,.70);
            font-size:13px;
            margin:0 0 18px;
        }

        .errors{
            margin:0 0 14px;
            padding:10px 12px;
            border-radius:12px;
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            color:#ffd6d6;
            font-size:13px;
        }

        /* Form layout */
        .grid{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:12px;
        }
        .field{
            display:flex;
            flex-direction:column;
            gap:7px;
        }
        .label{
            color:rgba(255,255,255,.86);
            font-weight:800;
            font-size:13px;
            letter-spacing:.02em;
        }
        .inp, .sel{
            background: rgba(47,47,47,.95);
            border: 1px solid rgba(0,0,0,.35);
            border-radius:12px;
            padding:11px 12px;
            color:var(--text);
            font-size:14px;
            outline:none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.06);
        }
        .inp::placeholder{ color: rgba(255,255,255,.45); }
        .inp:focus, .sel:focus{
            border-color: rgba(242,210,122,.55);
            box-shadow: 0 0 0 4px rgba(242,210,122,.14), inset 0 1px 0 rgba(255,255,255,.06);
        }
        .help{
            color:rgba(255,255,255,.65);
            font-size:12px;
            margin-top:4px;
        }

        .badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 10px;
            border-radius:12px;
            background: rgba(242,210,122,.10);
            border: 1px solid rgba(242,210,122,.18);
            color: rgba(255,255,255,.86);
            font-size:12px;
        }

        /* Full width items */
        .col-span-2{ grid-column: span 2 / span 2; }

        /* Actions */
        .actions{
            margin-top:14px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
        }
        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            padding:12px 16px;
            border:0;
            border-radius:12px;
            background: linear-gradient(180deg, var(--gold) 0%, var(--gold-2) 100%);
            color:#1a1a1a;
            font-weight:900;
            letter-spacing:.06em;
            cursor:pointer;
            box-shadow: 0 12px 30px rgba(242,210,122,.18);
            min-width:220px;
        }
        .btn:hover{ filter: brightness(.98); }

        .link{
            color: rgba(242,210,122,.95);
            font-weight:900;
            letter-spacing:.06em;
            font-size:12px;
            text-decoration:none;
        }
        .link:hover{ text-decoration:underline; }

        /* Mobile */
        @media (max-width: 820px){
            .grid{ grid-template-columns: 1fr; }
            .col-span-2{ grid-column: auto; }
            .title{ font-size:28px; }
            .brand-top img{ width:96px; height:96px; }
            .btn{ width:100%; min-width:0; }
            .actions{ justify-content:center; }
        }
    </style>

    <div class="wrap">
        <div class="stack">

            {{-- LOGO + NOM DU LOGICIEL (SUR LE MÊME FOND) --}}
            <div class="brand-top">
                <img src="{{ asset('images/logo.png') }}" alt="CC-APP-EDUC">
                <div class="appname">CC-APP-EDUC</div>
                <div class="tagline">Inscription par pseudo • Type de compte • Classe obligatoire pour Élève</div>
            </div>

            <div class="card">
                <div class="title">SIGN UP</div>
                <div class="subtitle">Crée un compte avec un pseudo (pas de nom réel).</div>

                @if ($errors->any())
                    <div class="errors">
                        <b>Erreurs :</b>
                        <div style="margin-top:6px;">
                            @foreach ($errors->all() as $e)
                                <div>• {{ $e }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="grid">
                        <div class="field">
                            <div class="label">Pseudo</div>
                            <input class="inp" name="pseudo" value="{{ old('pseudo') }}" required autofocus
                                   placeholder="Ex: user_1, eleve123...">
                            <div class="help">Utilise un pseudo (pas ton nom réel).</div>
                        </div>

                        <div class="field">
                            <div class="label">Type de compte</div>
                            <select class="sel" name="type_compte" id="type_compte" required>
                                <option value="">-- Choisir --</option>
                                <option value="eleve" {{ old('type_compte')==='eleve'?'selected':'' }}>Élève</option>
                                <option value="enseignant" {{ old('type_compte')==='enseignant'?'selected':'' }}>Enseignant</option>
                                <option value="parent" {{ old('type_compte')==='parent'?'selected':'' }}>Parent</option>
                                <option value="admin" {{ old('type_compte')==='admin'?'selected':'' }}>Admin</option>
                            </select>
                            <div class="help">Tu choisis ton rôle dès l’inscription.</div>
                        </div>

                        {{-- CLASSE (obligatoire si élève) --}}
                        <div class="field col-span-2" id="classe_block">
                            <div class="label">Classe (obligatoire pour Élève)</div>
                            <select class="sel" name="classe_id" id="classe_id">
                                <option value="">-- Choisir la classe --</option>

                                @if(!empty($classes) && count($classes) > 0)
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ (string)old('classe_id') === (string)$c->id ? 'selected' : '' }}>
                                            {{ $c->label }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Aucune classe disponible</option>
                                @endif
                            </select>

                            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:6px;">
                                <span class="badge">🔒 Choix unique</span>
                                <span class="badge">💳 Plateforme payante</span>
                                <span class="badge">📚 Cours liés à la classe</span>
                            </div>

                            <div class="help">
                                La classe se choisit une seule fois pendant l’inscription. Pour modifier plus tard, il faut contacter l’administration.
                            </div>
                        </div>

                        <div class="field col-span-2">
                            <div class="label">Email (optionnel)</div>
                            <input class="inp" type="email" name="email" value="{{ old('email') }}"
                                   placeholder="Optionnel (si tu veux garder la connexion par email)">
                            <div class="help">Si tu veux travailler uniquement au pseudo, tu peux laisser vide.</div>
                        </div>

                        <div class="field">
                            <div class="label">Mot de passe</div>
                            <input class="inp" type="password" name="password" required autocomplete="new-password"
                                   placeholder="••••••••">
                        </div>

                        <div class="field">
                            <div class="label">Confirmer le mot de passe</div>
                            <input class="inp" type="password" name="password_confirmation" required autocomplete="new-password"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn" type="submit">
                            <span style="font-size:16px;">✅</span>
                            <span>CRÉER LE COMPTE</span>
                        </button>

                        <a class="link" href="{{ route('login') }}">Déjà inscrit ? Se connecter</a>
                    </div>
                </form>

                <script>
                    (function () {
                        const typeSelect = document.getElementById('type_compte');
                        const classeBlock = document.getElementById('classe_block');
                        const classeSelect = document.getElementById('classe_id');

                        function toggleClasse() {
                            const v = (typeSelect.value || '').toLowerCase();
                            const isEleve = (v === 'eleve');

                            // Afficher/masquer
                            classeBlock.style.display = isEleve ? '' : 'none';

                            // Rendre requis uniquement pour élève
                            if (isEleve) {
                                classeSelect.setAttribute('required', 'required');
                            } else {
                                classeSelect.removeAttribute('required');
                                classeSelect.value = '';
                            }
                        }

                        typeSelect.addEventListener('change', toggleClasse);
                        toggleClasse();
                    })();
                </script>
            </div>

        </div>
    </div>
</x-guest-layout>
