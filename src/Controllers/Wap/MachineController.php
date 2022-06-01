<?php
/**
 * 矿机
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Jncinet\Metaverse\Models\MetaverseMachine;

class MachineController extends Controller
{
    public function __construct()
    {
        App::setLocale('en');
    }

    // 矿机列表
    public function index(Request $request)
    {
        $machines = MetaverseMachine::where('status', 1)
            ->orderBy('sort')
            ->orderBy('id', 'desc')
            ->get();
        return view('metaverse::machine.index', compact('machines'));
    }

    // 选择矿机
    public function selectMachine()
    {
        return MetaverseMachine::select('id', 'name as text')->get();
    }
}
