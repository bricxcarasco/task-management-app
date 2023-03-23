<script src="{{ asset('js/dist/cordova/platforms/' . $platform .'/cordova.js') }}" defer></script>
<script src="{{ asset('js/dist/cordova/platforms/' . $platform .'/cordova_plugins.js') }}" defer></script>

@foreach ($plugins ?? [] as $plugin)
    <script src="{{ asset('js/dist/cordova/platforms/' . $platform . '/plugins/cordova-plugin-' . $plugin . '/www/' . $plugin . '.js') }}" defer></script>
@endforeach