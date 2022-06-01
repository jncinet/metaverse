<?php
/**
 * 后台选择
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use App\Models\Authentication;
use App\Models\User;
use Illuminate\Http\Request;
use Jncinet\Metaverse\Models\MetaverseUserLevel;

class ApiController extends Controller
{
    // 选择用户
    public function selectUser(Request $request)
    {
        $q = $request->query('q');
        return User::where('username', 'like', '%' . $q . '%')
            ->paginate(null, ['id', 'username as text']);
    }

    // 选择认证项目
    public function selectAuthentication()
    {
        return Authentication::select('id', 'name as text')->get();
    }

    // 选择会员等级
    public function selectUserLevel()
    {
        return MetaverseUserLevel::select('id', 'name as text')->orderBy('sort')->get();
    }
}
