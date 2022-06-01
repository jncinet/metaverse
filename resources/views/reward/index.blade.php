@extends('metaverse::layout')

@section('title', 'My rewards')

@section('content')
    <div class="px-3 pb-3">
        @forelse($rewards as $reward)
            <div class="mt-3 rounded-3 px-3 py-2 bg-linear-gray">
                <div class="d-flex align-items-center">
                    @if($reward->status === 1)
                        <i class="iconfont icon-zhengquewancheng-yuankuang fs-18 text-success me-2"></i>
                    @else
                        <i class="iconfont icon-cuowuguanbiquxiao-yuankuang fs-18 text-danger me-2"></i>
                    @endif
                    <div>
                        <div>@lang('metaverse::reward.type_value.' . $reward->type)</div>
                        <div class="fs-12 text-white-50">{{ $reward->created_at }}</div>
                    </div>
                    <div class="ms-auto">+{!! number_format($reward->amount, 4) !!}</div>
                </div>
            </div>
        @empty
            <div style="height: 80vh;" class="d-flex justify-content-center align-items-center">
                <div class="opacity-50 fs-14">No Content</div>
            </div>
        @endforelse
    </div>
    <div class="px-3 pb-3">
        {{ $rewards->links('metaverse::pages') }}
    </div>
@endsection
