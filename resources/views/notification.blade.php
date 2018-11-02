<p>{{ $createdDate->format("d.m.Y H:i:s") }} поступила новая заявка!</p>
<br>
<p>Контактные данные пользователя:</p>
<ul>
    <li>Имя: <b>{{ $name }}</b></li>
    <li>Электронная почта: <b><a href="mailto:{{ $email }}">{{ $email }}</a></b></li>
    @if($phone)
        <li>Телефон: <b>{{ $phone }}</b></li>
    @endif
</ul>
@if($hasFile)
    <p>К заявке также был приложен файл.</p>
@endif
