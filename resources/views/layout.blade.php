<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="//at.alicdn.com/t/font_3114689_1zn97ribv6u.css" rel="stylesheet">
    <link href="{{ asset('vendor/metaverse/css/main.css') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
<!-- 顶部导航 -->
<div class="fixed-top bg-black">
    <div class="d-flex align-items-center justify-content-between mx-3">
        <div>
            <a href="javascript:window.history.back();"
               class="iconfont icon-shangyiyehoutuifanhui text-white fs-18"></a>
        </div>
        <div class="flex-fill mx-2 top-nav">@yield('title')</div>
        <div>
            @section('navbar_right')
                <a href="{{ route('wap.metaverse.index') }}" class="d-block ps-2 text-white">
                    <i class="iconfont icon-tubiao- fs-18"></i>
                </a>
            @show
        </div>
    </div>
</div>
<div class="top-nav-placeholder"></div>
@yield('content')
<!-- 轻提示 -->
<div id="toast-success-wrap" style="display: none; z-index: 9999;"
     class="position-fixed bg-black top-50 start-50 translate-middle p-3 rounded-3 shadow-lg">
    <div class="d-flex align-items-center">
        <i class="iconfont icon-zhengquewancheng-xianxingyuankuang me-2 fs-18 text-success"></i>
        <div class="opacity-75 fs-14" id="toast-success-msg">SUCCESS</div>
    </div>
</div>
<div id="toast-error-wrap" style="display: none; z-index: 9999;"
     class="position-fixed bg-black top-50 start-50 translate-middle p-3 rounded-3 shadow-lg">
    <div class="d-flex align-items-center">
        <i class="iconfont icon-cuowuguanbiquxiao-xianxingyuankuang me-2 fs-18 text-warning"></i>
        <div class="opacity-75 fs-14 lh-sm" id="toast-error-msg">ERROR</div>
    </div>
</div>
</body>
<script>
    /**
     * 轻提示
     *
     * @param msg string 提示内容
     * @param type bool 成功｜失败
     * @param times int 关闭倒计时秒数
     */
    function toast(msg = "", type = true, times = 3000) {
        const status = type === true ? "success" : "error";
        const dom = document.getElementById('toast-' + status + '-wrap');
        dom.style.display = "unset";
        document.getElementById('toast-' + status + '-msg').innerText = msg;
        setTimeout(function () {
            dom.style.display = "none";
        }, times);
    }
</script>
@yield('script')
</html>
