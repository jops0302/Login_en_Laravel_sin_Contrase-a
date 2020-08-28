@component('mail::message')
# Link de acceso para {{ $user->email }}

Da click en el siguiente boton para acceder a la aplicación

@component('mail::button', ['url' => $link])
Acceder
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
