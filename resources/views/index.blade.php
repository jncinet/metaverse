@extends('metaverse::layout')

@section('title', 'N3S Mine')

@section('navbar_right')
    <a href="/" class="d-block ps-2 icon-linear-gradient">
        <i class="iconfont icon-home fs-18"></i>
    </a>
@endsection

@section('content')
    <!-- 账户概况 -->
    <div class="d-flex mx-3 py-3">
        <img onclick="location.href='{{ route('member.wap.index') }}'" width="60" height="60"
             class="rounded-circle" alt="avatar"
             src="{{ empty($user->avatar) ? asset('vendor/metaverse/img/meta_logo.png') : Storage::url($user->avatar) }}"/>
        <div onclick="location.href='{{ route('member.wap.wallet') }}'"
             class="me-auto ms-2 fs-14 d-flex flex-column justify-content-center">
            <div class="mb-1">{{ $user->nickname }}</div>
            <div class="small text-white-50 lh-sm">
                @lang('metaverse::metaverse.currency_name'):
                {{ number_format($user->account->integral, 4) }}
            </div>
            <div class="small text-white-50 lh-sm">
                @lang('metaverse::metaverse.recharge_currency_name'):
                {{ number_format($user->account->balance, 2) }}
            </div>
        </div>
        <div class="d-flex flex-column justify-content-between fs-14">
            <a href="javascript:showPopup();" role="button"
               class="bg-linear-gray text-white rounded-pill px-2 d-flex align-items-center">
                <i class="iconfont icon-convert me-1"></i>
                <span class="text-capitalize lh-1">@lang('metaverse::metaverse.convert')</span>
            </a>
            <a href="javascript:void(0);" role="button"
               class="bg-linear-gray opacity-50 text-white rounded-pill px-2 d-flex align-items-center">
                <i class="iconfont icon-rebate me-1"></i>
                <span class="text-capitalize lh-1">@lang('metaverse::metaverse.rebate')</span>
            </a>
        </div>
    </div>

    <!-- 我的算力 -->
    <div class="d-flex align-items-center justify-content-around bg-gray mx-3 p-3 rounded-3 shadow-lg">
        <div onclick="location.href='{{ route('wap.metaverse.powers.index') }}'">
            <div>{!! number_format($basic_energy_value, 4) !!}</div>
            <small class="text-white-50">@lang('metaverse::metaverse.basic_energy_value')</small>
        </div>
        <div class="vertical-line-white-3"></div>
        <div onclick="location.href='{{ route('wap.metaverse.team.addition') }}'">
            <div>{!! number_format($addition_energy_value, 4) !!}</div>
            <small class="text-white-50">@lang('metaverse::metaverse.addition_energy_value')</small>
        </div>
    </div>

    <!-- 跳转链接 -->
    <div class="d-flex flex-column gap-3 m-3">
        <a href="{{ route('wap.metaverse.machines.index') }}"
           class="d-flex py-2 text-white align-items-center bg-linear-gray rounded-3 pe-3">
            <i class="iconfont icon-zhiyaliang icon-linear-gradient me-2 ms-3 fs-18"></i>
            <div class="fs-14">@lang('metaverse::metaverse.view_pledge_space')</div>
            <i class="iconfont icon-xiayiyeqianjinchakangengduo ms-auto"></i>
        </a>
        <a href="{{ route('wap.metaverse.team.index') }}"
           class="d-flex py-2 text-white align-items-center bg-linear-gray rounded-3 pe-3">
            <i class="iconfont icon-shequ icon-linear-gradient me-2 ms-3 fs-18"></i>
            <div class="fs-14">@lang('metaverse::metaverse.community_and_recommendation')</div>
            <i class="iconfont icon-xiayiyeqianjinchakangengduo ms-auto"></i>
        </a>
    </div>

    @if($task_count > 0)
        <!-- 今日任务 -->
        <div class="d-flex align-items-center mb-3 pt-1 lh-1 mx-3">
            <i class="iconfont icon-renwu me-2 ms-1 text-white-50"></i>
            <div class="text-capitalize">
                @lang('metaverse::metaverse.today_mission')
            </div>
        </div>
        <div class="p-3 mx-3 bg-gray rounded-3 shadow-lg mb-3">
            <div class="text-capitalize fs-12 text-white-50 lh-1">
                @lang('metaverse::metaverse.today_mission_subtitle')
            </div>
            <div class="task-progress-bar my-2">
                <div style="width: {{ $task_progress }}%"></div>
            </div>
            <div class="d-flex justify-content-between fs-12 lh-1">
                <div>0</div>
                <div>{{ $task_count }}</div>
            </div>
        </div>

        <!-- 任务列表 -->
        <div class="mx-3 p-2">
            @foreach($task_items as $item)
                <div class="mb-3 d-flex align-items-center">
                    <img width="36" height="36" class="rounded-3 me-3 bg-gray" alt="task ico"
                         src="{{ Storage::url($item->thumbnail) }}"/>
                    <div class="flex-fill">
                        <div class="fs-14 mb-1">
                            {{ $item->name }}
                            <small class="ms-2 text-white-50">({{ $item->task_reward_log_count }}
                                /{{ $item->attribute[0] }})</small>
                        </div>
                        <div class="task-progress-bar-min">
                            <div
                                style="width: {{ \Jncinet\Metaverse\Support::taskProgress($item->task_reward_log_count, $item->attribute[0]) }}%"></div>
                        </div>
                    </div>
                    @if($item->task_reward_log_count < $item->attribute[0])
                        <a href="{{ empty($item->attribute[2]) ? '/' : $item->attribute[2] }}"
                           class="btn-do-task shadow bg-linear-gray text-white fs-14 ms-3 text-capitalize">
                            @lang('metaverse::metaverse.incomplete')
                        </a>
                    @else
                        <div class="btn-do-task shadow bg-gray text-white fs-14 ms-3 text-capitalize">
                            @lang('metaverse::metaverse.completed')
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- 获取奖励 -->
    <div onclick="getTodayReward()" class="d-flex align-items-center bg-gray shadow-lg mx-3 mb-3 rounded-3 p-3 lh-1">
        <img class="me-3" height="30" src="{{ asset('vendor/metaverse/img/meta_logo.png') }}" alt="LOGO"/>
        <div>@lang('metaverse::metaverse.click_get')</div>
        @if($task_progress === '100.00')
            <i class="iconfont icon-zhengquewancheng-xianxingyuankuang ms-auto icon-linear-gradient"
               style="font-size: 30px"></i>
        @else
            <i class="iconfont icon-cuowuguanbiquxiao-xianxingyuankuang ms-auto text-white-50"
               style="font-size: 30px"></i>
        @endif
    </div>

    <!-- 兑出弹窗 -->
    <div class="popup" id="popup" style="display: none;">
        <div class="mask" style="display: none;">
            <div onclick="hidePopup()" class="mask-aria-button" role="button" aria-label="mask"></div>
            <div class="mask-content"></div>
        </div>
        <div class="popup-body popup-body-position-bottom bg-gray rounded-top">
            <div class="d-flex align-items-center justify-content-center py-3">
                <div class="text-capitalize">
                    @lang('metaverse::metaverse.convert') @lang('metaverse::metaverse.currency_name')
                </div>
                <img class="ms-3" height="24" src="{{ asset('vendor/metaverse/img/meta_logo.png') }}" alt="LOGO"/>
            </div>
            <div class="black-line mb-3"></div>
            <div class="mx-3">@lang('metaverse::metaverse.convert_tips')</div>
            <div class="black-line my-2 opacity-50 mx-3"></div>
            <div class="d-flex mx-3 justify-content-between">
                <div>
                    <div class="mb-1">@lang('metaverse::metaverse.my_balance')</div>
                    <div class="d-flex align-items-center">
                        <img height="18" src="{{ asset('vendor/metaverse/img/meta_logo.png') }}"
                             alt="LOGO"/>
                        <div class="text-white mx-2">{{ number_format($user->account->integral, 4) }}</div>
                        <small class="text-white-50">@lang('metaverse::metaverse.currency_name')</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="mb-1">@lang('metaverse::metaverse.gas_fee')</div>
                    <div>{{ $user_gas_fee_rate }} %</div>
                </div>
            </div>
            <div class="black-line my-2 opacity-50 mx-3"></div>
            <div class="mx-3">@lang('metaverse::metaverse.convert_amount')</div>
            <div class="black-line mt-2 mb-3 opacity-50 mx-3"></div>
            <label class="d-flex mx-3 rounded-3 py-1 px-2 align-items-center convert-amount-input">
                <img class="me-2" height="18" src="{{ asset('vendor/metaverse/img/meta_logo.png') }}"
                     alt="LOGO"/>
                <input id="convertAmount" type="number" class="flex-fill" max="{{ $user->account->integral }}"/>
                <small class="text-white-50 ms-2">@lang('metaverse::metaverse.currency_name')</small>
            </label>
            <div class="black-line mb-2 mt-3 opacity-50 mx-3"></div>
            <div class="mx-3 d-flex justify-content-between">
                <div>@lang('metaverse::metaverse.will_receive')</div>
                <div><span id="willReceive">0</span> <small
                        class="text-white-50">@lang('metaverse::metaverse.currency_name')</small></div>
            </div>
            <div class="d-flex convert-submit gap-3 py-3 mx-3">
                <div onclick="hidePopup()"
                     class="flex-fill bg-linear-gray py-2 rounded-pill d-flex align-items-center justify-content-center">
                    <i class="iconfont icon-cuowuguanbiquxiao-yuankuang me-1"></i>
                    <div class="text-capitalize">@lang('metaverse::metaverse.cancel')</div>
                </div>
                <div onclick="submitConvert()"
                     class="flex-fill bg-linear-primary py-2 rounded-pill d-flex align-items-center justify-content-center">
                    <i class="iconfont icon-zhengquewancheng-yuankuang me-1"></i>
                    <div class="text-capitalize">@lang('metaverse::metaverse.convert')</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 绑定接口用户信息 -->
    <div id="setApiUser" style="display: none; z-index: 1001;"
         class="position-fixed w-75 bg-black top-50 start-50 translate-middle py-3 rounded-3 shadow-lg">
        <div class="d-flex justify-content-between align-items-center mb-3 ms-3">
            <div>@lang('metaverse::ranking.bind_user_account')</div>
            <div onclick="closeBindApiUser()" class="text-white-50 px-3">&times;</div>
        </div>
        <div class="d-flex flex-column align-items-center gap-3 mx-3">
            <input id="verifySafePassword" class="w-100 meta-input p-2 rounded-3 lh-1" type="password"
                   placeholder="@lang('metaverse::ranking.safe_pwd_placeholder')"
                   maxlength="255"/>
            <input id="apiUserId" class="w-100 meta-input p-2 rounded-3 lh-1" type="text"
                   placeholder="@lang('metaverse::ranking.api_user_id')" minlength="1"
                   maxlength="255"/>
            <input id="apiUsername" class="w-100 meta-input p-2 rounded-3 lh-1" type="text"
                   placeholder="@lang('metaverse::ranking.api_username')" minlength="1"
                   maxlength="255"/>
            <button onclick="bindApiUser()" type="button"
                    class="w-100 border-none p-2 bg-linear-primary rounded-3 text-white">
                @lang('metaverse::metaverse.submit')
            </button>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        window.onload = function () {
            // 解决首次打开时CSS无动效
            document.getElementById('popup').style.display = "unset";
        }

        // 计算实际获得的金额
        document.getElementById('convertAmount').onchange = function (e) {
            const rate = parseFloat("{{ $user_gas_fee_rate }}");
            let amount = parseFloat(e.target.value);
            amount = amount - amount * rate / 100;
            document.getElementById('willReceive').innerText = amount.toFixed(4);
        }

        // 显示绑定账户窗口
        function showBindApiUser() {
            document.getElementById('setApiUser').style.display = "unset";
        }

        // 关闭绑定用户窗口
        function closeBindApiUser() {
            document.getElementById('setApiUser').style.display = "none";
        }

        // 提交绑定信息
        function bindApiUser() {
            axios.post("{{ route('wap.metaverse.convert.bind.user') }}", {
                safe_password: document.getElementById('verifySafePassword').value,
                username: document.getElementById('apiUsername').value,
                user_id: document.getElementById('apiUserId').value,
            })
                .then(function (response) {
                    const data = response.data;
                    console.log(data);
                    // toast(data.message + " +" + data.data.amount);
                })
                .catch(function (error) {
                    if (error.response) {
                        const data = error.response.data;
                        console.log(data);
                        // toast(data.message, false);
                    }
                });
        }

        // 请求兑出
        function submitConvert() {
            axios.post("{{ route('wap.metaverse.convert') }}", {
                amount: document.getElementById('convertAmount').value,
            })
                .then(function (response) {
                    const data = response.data;

                    console.log(data);
                    // toast(data.message + " +" + data.data.amount);
                })
                .catch(function (error) {
                    if (error.response) {
                        const data = error.response.data;
                        switch (data.code) {
                            case 423:
                                showBindApiUser();
                                break;
                            default:
                                toast(data.message, false);
                        }
                        console.log(data);
                    }
                });
        }

        /**
         * 获取当日奖励
         */
        function getTodayReward() {
            axios.get("{{ route('wap.metaverse.rewards.update') }}")
                .then(function (response) {
                    const data = response.data;
                    toast(data.message + " +" + data.data.amount);
                })
                .catch(function (error) {
                    if (error.response) {
                        const data = error.response.data;
                        toast(data.message, false);
                    }
                });
        }

        /**
         * 打开窗口
         */
        function showPopup() {
            const dom = document.getElementById('popup');
            dom.firstElementChild.style.display = "unset";
            dom.firstElementChild.style.opacity = 1;
            dom.lastElementChild.style.transform = "translate(0, 0%)";
        }

        /**
         * 关闭窗口
         */
        function hidePopup() {
            const dom = document.getElementById('popup');
            dom.firstElementChild.style.display = "none";
            dom.firstElementChild.style.opacity = 0;
            dom.lastElementChild.style.transform = "translate(0, 100%)";
        }
    </script>
@endsection
