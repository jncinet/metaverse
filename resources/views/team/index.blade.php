@extends('metaverse::layout')

@section('title', 'Community & recommendation')

@section('content')
    <div class="d-flex m-3 bg-linear-gray p-3 justify-content-between rounded-3">
        <div class="flex-average">
            <div class="small text-white-50 mb-1 lh-1">@lang('metaverse::metaverse.space_rank'):</div>
            <div>@lang('metaverse::metaverse.space_rank_value.' . $user_level->sort)</div>
        </div>
        <div class="flex-average">
            <div class="small text-white-50 mb-1 lh-1">@lang('metaverse::metaverse.gas_fee'):</div>
            <div>{{ $user_level->gas_fee_rate }}%</div>
        </div>
        <div class="flex-average">
            <div class="small text-white-50 mb-1 lh-1">@lang('metaverse::metaverse.community_level'):</div>
            <div>@lang('metaverse::metaverse.community_level_value.' . $team_level)</div>
        </div>
    </div>

    <div class="d-flex mx-3 gap-3">
        <div class="flex-average p-3 bg-linear-gray rounded-3 shadow">
            <div class="mb-3 text-white-50 lh-1">@lang('metaverse::metaverse.my_qr_code')</div>
            <img class="img-fluid shadow" src="{{ $qrcode }}"
                 alt="">
        </div>
        <div class="flex-average d-flex flex-column gap-3">
            <div class="bg-gray flex-fill rounded-3 d-flex flex-column shadow">
                <div class="px-3 pt-3 text-white-50 small lh-1">@lang('metaverse::metaverse.community_ranking')</div>
                <div class="flex-fill d-flex justify-content-center align-items-center">
                    <div class="fs-18 lh-1">{{ $user_ranking }}</div>
                </div>
            </div>
            <div class="bg-gray flex-fill rounded-3 d-flex flex-column shadow">
                <div class="px-3 pt-3 text-white-50 lh-1">@lang('metaverse::metaverse.recommender')</div>
                <div class="flex-fill d-flex justify-content-center align-items-center">
                    <div>{{ $recommender }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex text-center mx-3 mt-3 gap-1">
        <div id="allSpace" class="flex-fill px-2 text-truncate py-3 bg-gray rounded-top">
            @lang('metaverse::metaverse.total_population')
        </div>
        <div id="bigSpace" class="flex-fill px-2 text-truncate py-3 bg-gray rounded-top opacity-50">
            @lang('metaverse::metaverse.big_space')
        </div>
        <div id="smallSpace" class="flex-fill px-2 text-truncate py-3 bg-gray rounded-top opacity-50">
            @lang('metaverse::metaverse.small_space')
        </div>
    </div>

    <div class="mx-3 bg-gray p-3 mb-3">
        <div class="small">
            @lang('metaverse::metaverse.total_power'):
            <span id="spaceTotalPower">{{ $total_addition }}</span>
        </div>
        <div id="bigSpaceList">
            @foreach($big_space as $item)
                <div class="black-line my-3"></div>
                <div class="d-flex align-items-center">
                    <img width="42" height="42" class="rounded-circle me-3" alt="avatar"
                         src="{{ empty($item->user->avatar) ? asset('vendor/metaverse/img/meta_logo.png') : Storage::url($item->user->avatar) }}"/>
                    <div>
                        <div>@lang('metaverse::metaverse.username'): {{ $item->user->nickname }}</div>
                        <div class="small text-white-50">
                            @lang('metaverse::metaverse.total_power'): {{ number_format($item->addition, 4) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="smallSpaceList">
            @foreach($small_space as $item)
                <div class="black-line my-3"></div>
                <div class="d-flex align-items-center">
                    <img width="42" height="42" class="rounded-circle me-3" alt="avatar"
                         src="{{ empty($item->user->avatar) ? asset('vendor/metaverse/img/meta_logo.png') : Storage::url($item->user->avatar) }}"/>
                    <div>
                        <div>@lang('metaverse::metaverse.username'): {{ $item->user->nickname }}</div>
                        <div class="small text-white-50">
                            @lang('metaverse::metaverse.total_power'): {{ number_format($item->addition, 4) }}
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="pb-3">
                {{ $small_space->links('metaverse::pages') }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const allSpace = document.getElementById("allSpace"),
            bigSpace = document.getElementById("bigSpace"),
            smallSpace = document.getElementById("smallSpace"),
            bigSpaceList = document.getElementById("bigSpaceList"),
            smallSpaceList = document.getElementById("smallSpaceList"),
            spaceTotalPower = document.getElementById("spaceTotalPower"),
            allSpaceTotalPower = "{{ $total_addition }}",
            bigSpaceTotalPower = "{{ $big_space_total_addition }}",
            smallSpaceTotalPower = "{{ $small_space_total_addition }}",
            tab_classname_active = 'flex-fill px-2 text-truncate py-3 bg-gray rounded-top',
            tab_classname = 'flex-fill px-2 text-truncate py-3 bg-gray rounded-top opacity-50';
        allSpace.onclick = function () {
            allSpace.className = tab_classname_active;
            bigSpace.className = tab_classname;
            smallSpace.className = tab_classname;
            bigSpaceList.style.display = "unset";
            smallSpaceList.style.display = "unset";
            spaceTotalPower.innerText = allSpaceTotalPower;
        };
        bigSpace.onclick = function () {
            allSpace.className = tab_classname;
            bigSpace.className = tab_classname_active;
            smallSpace.className = tab_classname;
            bigSpaceList.style.display = "unset";
            smallSpaceList.style.display = "none";
            spaceTotalPower.innerText = bigSpaceTotalPower;
        };
        smallSpace.onclick = function () {
            allSpace.className = tab_classname;
            bigSpace.className = tab_classname;
            smallSpace.className = tab_classname_active;
            bigSpaceList.style.display = "none";
            smallSpaceList.style.display = "unset";
            spaceTotalPower.innerText = smallSpaceTotalPower;
        };
    </script>
@endsection
