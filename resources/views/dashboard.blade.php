cat > resources/views/dashboard.blade.php <<'EOF'
@extends('layouts.app')

@section('content')
<div style="padding:40px">
    <h1>Dashboard — CC APP EDUC</h1>
    <p>Bienvenue {{ auth()->user()->name }}.</p>

    <ul>
        <li>Élèves</li>
        <li>Classes</li>
        <li>Notes</li>
    </ul>
</div>
@endsection
EOF
