<div class="overflow-x-auto">
    <table class="table-modern">
        <thead>
            <tr>
                @foreach($cabeceras as $cabecera)
                    <th class="px-5 py-4">{{ $cabecera }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            {{ $filas }}
        </tbody>
    </table>
</div>