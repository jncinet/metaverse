@extends('metaverse::layout')

@section('title', 'My addition powers')

@section('content')
    <div class="px-3 pb-3">
        @forelse($items as $item)
            <div class="mt-3 rounded-3 px-3 py-2 bg-linear-gray">
                <div class="d-flex align-items-center">
                    <img width="42" height="42" class="rounded-circle me-3" alt="avatar"
                         src="{{ empty($item->user->avatar) ? asset('vendor/metaverse/img/meta_logo.png') : Storage::url($item->user->avatar) }}"/>
                    <div>{{ $item->user->nickname }}</div>
                    <div class="ms-auto">+{!! number_format($item->basic, 4) !!}</div>
                </div>
            </div>
        @empty
            <div style="height: 80vh;" class="d-flex justify-content-center align-items-center">
                <div class="opacity-50 fs-14">No Content</div>
            </div>
        @endforelse
    </div>
    <div class="px-3 pb-3">
        {{ $items->links('metaverse::pages') }}
    </div>
@endsection
