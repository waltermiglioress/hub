{{--<div class="progress-bar">--}}
{{--    <div class="progress-bar-value" style="width: {{$getState()}}%;">--}}
{{--        {{ $getState() }}%--}}
{{--    </div>--}}
{{--</div>--}}
<div class="progress-bar rounded-full h-4">
    <div
        class="progress-bar-value h-4 rounded-full"
        style="width: {{ $getState() }}%; background-color: {{ $getBackgroundColor($getState()) }};">
        {{ $getState() }}%
    </div>
</div>
