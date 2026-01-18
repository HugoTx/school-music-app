<h1>Pagamentos do aluno: {{ $student->name }}</h1>

<div style="margin:15px 0;">
    <strong>Total pago:</strong> {{ $totalPaid }} €<br>
    <strong>Por pagar:</strong> {{ $totalDue }} €
</div>

<table border="1" cellpadding="8">
    <tr>
        <th>Mês</th>
        <th>Ano</th>
        <th>Valor</th>
        <th>Estado</th>
    </tr>

    @foreach($payments as $payment)
    <tr>
        <td>{{ $payment->month }}</td>
        <td>{{ $payment->year }}</td>
        <td>{{ $payment->amount }} €</td>
        <td>
            @if($payment->paid)
            ✔ Pago em {{ $payment->paid_at }}
            @else
            ❌ Pendente
            @endif
        </td>
    </tr>
    @endforeach
</table>