@extends('metaverse::layout')

@section('title', 'My stacking')

@section('navbar_right')
    <a href="{{ route('wap.metaverse.powers.index') }}"
       class="d-flex align-items-center bg-linear-gray px-2 rounded-pill shadow">
        <i class="iconfont icon-history me-1 icon-linear-gradient"></i>
        <div class="fs-14 lh-lg text-white-50">@lang('metaverse::metaverse.history')</div>
    </a>
@endsection

@section('content')
    @forelse($machines as $machine)
        <div class="p-3 position-relative bg-linear-black" id="data{{ $machine->id }}"
             data-price="{{ $machine->price }}" data-power="{{ $machine->power }}"
             @if(in_array($machine->id, $free_arr))
             style="opacity: 0.6; pointer-events: none;"
            @endif
        >
            <div class="d-flex align-items-center bg-linear-primary rounded-top p-3 gap-4">
                <div class="d-flex align-items-center px-3 py-2 bg-linear-gray rounded-pill shadow">
                    <img height="22" class="rounded-3 me-2 grayscale" alt="logo ico"
                         src="{{ asset('vendor/metaverse/img/meta_logo.png') }}"/>
                    <div>{{ $machine->name }}</div>
                </div>
                <div class="fs-14">
                    <div>@lang('metaverse::machine.power'):</div>
                    <div class="opacity-75">{!! number_format($machine->power, 4) !!}</div>
                </div>
                <div class="fs-14">
                    <div>@lang('metaverse::machine.count'):</div>
                    <div class="opacity-75">{{ $machine->count }}</div>
                </div>
            </div>
            <div class="bg-linear-gray rounded-bottom p-3">
                <div class="d-flex align-items-center pb-1">
                    <img height="18" class="rounded-3 me-2" alt="logo ico"
                         src="{{ asset('vendor/metaverse/img/meta_logo.png') }}"/>
                    <div>{!! number_format($machine->price) !!}</div>
                    <div class="small opacity-75 ms-2">
                        @lang('metaverse::metaverse.currency_name')
                    </div>
                    <div class="convert-amount-input ms-auto d-flex rounded-pill shadow">
                        <span onclick="subAmount({{ $machine->id }})" class="text-white-50 px-3">-</span>
                        <input id="amount{{ $machine->id }}" class="text-center" type="number" value="1"
                               style="width: 50px;" min="1"/>
                        <span onclick="addAmount({{ $machine->id }})" class="text-white-50 px-3">+</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="small opacity-75 text-capitalize">
                        @lang('metaverse::machine.daily_output'):
                        <span>{{  bcmul($machine->power, config('metaverse.miner_output'), 4) }}</span>
                    </div>
                    <div class="small opacity-75 text-capitalize">
                        @lang('metaverse::machine.total_output'):
                        <span>{{ bcadd(bcmul(bcmul($machine->power, config('metaverse.miner_output'), 8), $machine->count, 4), $machine->price, 4) }}</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="small opacity-75">
                        @lang('metaverse::metaverse.total_power'):
                        <span id="power{{ $machine->id }}">{!! number_format($machine->power, 4) !!}</span>
                    </div>
                    <div onclick="buy({{ $machine->id }})"
                         class="bg-linear-primary ms-auto rounded-pill px-3 py-1 fs-14">
                        @if($machine->price > 0)
                            @lang('metaverse::metaverse.buy') <strong
                                id="price{{ $machine->id }}">{!! round($machine->price) !!}</strong> @lang('metaverse::metaverse.currency_name')
                        @else
                            @lang('metaverse::metaverse.claim')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="height: 80vh;" class="d-flex justify-content-center align-items-center">
            <div class="opacity-50 fs-14">No Content</div>
        </div>
    @endforelse

    <!-- 设置安全密码 -->
    <div id="setSafePassword" style="display: none; max-width:350px;"
         class="position-fixed w-75 bg-black top-50 start-50 translate-middle py-3 rounded-3 shadow-lg">
        <div class="d-flex justify-content-between align-items-center mb-3 ms-3">
            <div>@lang('metaverse::metaverse.set_safe_pwd')</div>
            <div onclick="closeSetSafePassword()" class="text-white-50 px-3">&times;</div>
        </div>
        <div class="d-flex flex-column align-items-center gap-3 mx-3">
            <input id="setSafePasswordOld" class="w-100 meta-input p-2 rounded-3 lh-1" type="password"
                   placeholder="@lang('metaverse::metaverse.old_pwd_placeholder')"
                   maxlength="255"/>
            <input id="setSafePasswordInput" class="w-100 meta-input p-2 rounded-3 lh-1" type="password"
                   placeholder="@lang('metaverse::metaverse.safe_pwd_placeholder')" minlength="6"
                   maxlength="255"/>
            <input id="setSafePasswordInputConfirmation" class="w-100 meta-input p-2 rounded-3 lh-1" type="password"
                   placeholder="@lang('metaverse::metaverse.safe_pwd_confirmed_placeholder')" minlength="6"
                   maxlength="255"/>
            <button onclick="setSafePassword()" type="button"
                    class="w-100 border-none p-2 bg-linear-primary rounded-3 text-white">
                @lang('metaverse::metaverse.submit')
            </button>
        </div>
    </div>

    <!-- 验证安全密码 -->
    <div id="verifySafePassword" style="display: none; max-width:350px;"
         class="position-fixed w-75 bg-black top-50 start-50 translate-middle py-3 rounded-3 shadow-lg">
        <div class="d-flex justify-content-between align-items-center mb-3 ms-3">
            <div>@lang('metaverse::metaverse.verify_safe_pwd')</div>
            <div onclick="closeVerifySafePassword()" class="text-white-50 px-3">&times;</div>
        </div>
        <div class="d-flex flex-column align-items-center gap-3 mx-3">
            <input id="verifySafePasswordInput" class="w-100 meta-input p-2 rounded-3 lh-1" type="password"
                   placeholder="@lang('metaverse::metaverse.safe_pwd_placeholder')" minlength="6"
                   maxlength="255"/>
            <button onclick="verifySafePassword()" type="button"
                    class="w-100 border-none p-2 bg-linear-primary rounded-3 text-white">
                @lang('metaverse::metaverse.submit')
            </button>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        let waiting = false, intId = 0;

        // 显示设置安全密码窗口
        function showSetSafePassword() {
            document.getElementById('setSafePassword').style.display = "unset";
        }

        // 设置安全密码
        function setSafePassword() {
            axios.post("{{ route('wap.metaverse.safe.password') }}", {
                password: document.getElementById('setSafePasswordOld').value,
                safe_password: document.getElementById('setSafePasswordInput').value,
                safe_password_confirmation: document.getElementById('setSafePasswordInputConfirmation').value,
            })
                .then(function () {
                    closeSetSafePassword();
                })
                .catch(function (error) {
                    if (error.response) {
                        const data = error.response.data;
                        toast(data.message, false);
                    }
                });
        }

        // 关闭设置安全密码窗口
        function closeSetSafePassword() {
            document.getElementById('setSafePassword').style.display = "none";
        }

        // 显示验证安全密码窗口
        function showVerifySafePassword() {
            document.getElementById('verifySafePassword').style.display = "unset";
        }

        // 验证安全密码
        function verifySafePassword() {
            const dom = document.getElementById('verifySafePasswordInput');
            axios.post("{{ route('wap.metaverse.verify.safe.password') }}", {
                safe_password: dom.value,
            })
                .then(function () {
                    closeVerifySafePassword();
                    dom.value = "";
                    if (intId > 0) buy(intId);
                })
                .catch(function (error) {
                    if (error.response) {
                        const data = error.response.data;
                        toast(data.message, false);
                    }
                });
        }

        // 关闭验证安全密码窗口
        function closeVerifySafePassword() {
            document.getElementById('verifySafePassword').style.display = "none";
        }

        // 下单
        function buy(id) {
            if (waiting) {
                toast("@lang('metaverse::metaverse.confirmation')", false, 1500);
                return false;
            }

            waiting = true;

            axios.post('{{ route('wap.metaverse.powers.store') }}', {
                metaverse_machine_id: id,
                quantity: document.getElementById('amount' + id).value,
            })
                .then(function (response) {
                    waiting = false;
                    const data = response.data;
                    toast(data.message);
                })
                .catch(function (error) {
                    waiting = false;
                    if (error.response) {
                        const data = error.response.data;
                        switch (data.code) {
                            case 423:
                                intId = id;
                                showVerifySafePassword();
                                break;
                            case 424:
                                showSetSafePassword();
                                break;
                            default:
                                toast(data.message, false);
                        }
                    }
                });
        }

        // 增加数量
        function addAmount(id) {
            const dom = document.getElementById('amount' + id);
            dom.value = parseInt(dom.value) + 1;
            const {price, power} = getData(id);
            updatePower(id, power, dom.value);
            updatePrice(id, price, dom.value);
        }

        // 减少数量
        function subAmount(id) {
            const dom = document.getElementById('amount' + id);
            dom.value = parseInt(dom.value) - 1 > 0 ? parseInt(dom.value) - 1 : 1;
            const {price, power} = getData(id);
            updatePower(id, power, dom.value);
            updatePrice(id, price, dom.value);
        }

        // 获取行数据
        function getData(id) {
            const dom = document.getElementById('data' + id);
            return {
                price: dom.getAttribute('data-price'),
                power: dom.getAttribute('data-power')
            };
        }

        // 更新价格
        function updatePrice(id, price, amount) {
            document.getElementById('price' + id).innerText = (parseFloat(price) * amount).toFixed();
        }

        // 更新算力
        function updatePower(id, power, amount) {
            document.getElementById('power' + id).innerText = (parseFloat(power) * amount).toFixed(4);
        }
    </script>
@endsection
