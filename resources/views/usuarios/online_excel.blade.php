<table>
    <thead>
        <tr>
            <th colspan="6">Atividades Recentes</th>
        </tr>
        <tr>
            <th>Usuário</th>
            <th>Evento</th>
            <th>URL/Modelo</th>
            <th>IP</th>
            <th>Navegador</th>
            <th>Data/Hora</th>
        </tr>
    </thead>
    <tbody>
        @foreach($recentActivities as $log)
            <tr>
                <td>{{ ($log->user) ? $log->user->name : 'Sistema' }}</td>
                <td>{{ ucfirst($log->evento->nome) }}</td>
                <td>
                    @if($log->evento->chave == 'activity')
                        {{ $log->url }}
                    @else
                        {{ $log->auditable_type }}
                    @endif
                </td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->user_agent }}</td>
                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>