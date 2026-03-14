<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Movimiento</th>
            <th>Módulo</th>
            <th>Acción</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transacciones as $t)
            <tr>
                <td>{{ $t->user->full_name ?? 'N/A' }}</td>
                <td>{{ $t->cat_tipos_transaccion->name ?? 'N/A' }}</td>
                <td>{{ $t->cat_modulo->name ?? 'N/A' }}</td>
                <td>{{ $t->action ?? 'N/A' }}</td>
                <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
