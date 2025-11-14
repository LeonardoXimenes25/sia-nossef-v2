<table class="table-auto w-full border">
    <thead>
        <tr>
            <th class="border px-2 py-1">Estudante</th>
            <th class="border px-2 py-1">Valor</th>
            <th class="border px-2 py-1">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getState() ?? [] as $i => $student)
<tr>
    <td>{{ $student['name'] }}</td>
    <td>
        <input type="number" min="0" max="10" step="0.1"
               wire:model.defer="students.{{ $i }}.score"
               class="w-full border rounded px-2 py-1" />
    </td>
    <td>
        <input type="text"
               wire:model.defer="students.{{ $i }}.remarks"
               class="w-full border rounded px-2 py-1" />
    </td>
</tr>
@endforeach

    </tbody>
</table>
