@extends('metaverse::layout')

@section('title', 'My history')

@section('navbar_right')
    <a href="{{ route('wap.metaverse.machines.index') }}" class="d-flex align-items-center bg-linear-gray px-2 rounded-pill shadow">
        <i class="iconfont icon-tubiao- fs-18 me-1 icon-linear-gradient"></i>
        <div class="fs-14 lh-lg text-white-50">@lang('metaverse::metaverse.pledge_space')</div>
    </a>
@endsection


@section('content')
    <div class="px-3 pt-3">
        <div class="d-flex gap-2 flex-wrap fs-14">
            <a href="{{ route('wap.metaverse.powers.index') }}" class="bg-gray px-2 py-1 text-white rounded-3">
                All
                <small class="text-white-50">({{ $machines->sum('metaverse_powers_count') }})</small>
            </a>
            @foreach($machines as $machine)
                <a href="{{ route('wap.metaverse.powers.index', ['id' => $machine->id]) }}" class="bg-gray px-2 py-1 text-white">
                    {{ $machine->name }}
                    <small class="text-white-50">({{ $machine->metaverse_powers_count }})</small>
                </a>
            @endforeach
        </div>
    </div>
    <div class="px-3 pb-3">
        @forelse($powers as $power)
            <a href="{{ route('wap.metaverse.rewards.index', ['metaverse_power_id' => $power->id]) }}"
               class="mt-3 d-block text-white rounded-3 bg-linear-gray p-3">
                <div class="d-flex justify-content-between align-items-baseline mb-1">
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <div>{{ $power->metaverse_machine->name }}</div>
                            <i class="iconfont icon-xiayiyeqianjinchakangengduo ms-2 opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-center opacity-50">
                            <i class="iconfont fs-12 icon-chuangjianriqi"></i>
                            <div class="fs-12 ms-1">{{ $power->created_at }}</div>
                        </div>
                    </div>
                    @if($power->remaining_count == 0)
                        <div class="rounded-pill bg-gray px-2 py-1 lh-1 small text-white-50 text-capitalize">
                            @lang('metaverse::metaverse.released')
                        </div>
                    @else
                        <div class="rounded-pill bg-linear-primary px-2 py-1 lh-1 small">
                            {{ $power->remaining_count }}<span class="mx-1 text-white-50">/</span>{{ $power->count }}
                        </div>
                    @endif
                </div>
                <div class="d-flex gap-3 fs-14">
                    <div class="flex-average">
                        <span class="opacity-75 me-1 text-capitalize">@lang('metaverse::power.price'):</span>
                        {{ $power->price }}
                        <span class="ms-1 text-white-50">@lang('metaverse::metaverse.currency_name')</span>
                    </div>
                    <div class="flex-average">
                        <span class="opacity-75 me-1 text-capitalize">@lang('metaverse::power.total_price'):</span>
                        {{ $power->total_price }}
                        <span class="ms-1 text-white-50">@lang('metaverse::metaverse.currency_name')</span>
                    </div>
                </div>
                <div class="d-flex gap-3 fs-14">
                    <div class="flex-average">
                        <span class="opacity-75 me-1 text-capitalize">@lang('metaverse::power.power'):</span>
                        {!! number_format($power->power, 4) !!}
                    </div>
                    <div class="flex-average">
                        <span class="opacity-75 me-1 text-capitalize">@lang('metaverse::power.total_power'):</span>
                        {!! number_format($power->total_power, 4) !!}
                    </div>
                </div>
                <div class="d-flex gap-3 fs-14">
                    <div class="flex-average">
                        <span class="opacity-75 me-1 text-capitalize">@lang('metaverse::power.quantity'):</span>
                        {{ $power->quantity }}
                    </div>
                </div>
            </a>
        @empty
            <div style="height: 80vh;" class="d-flex justify-content-center align-items-center">
                <div class="opacity-50 fs-14">No Content</div>
            </div>
        @endforelse
    </div>
    <div class="px-3 pb-3">
        {{ $powers->appends(['id' => $id])->links('metaverse::pages') }}
    </div>

@endsection

@section('script')

@endsection
