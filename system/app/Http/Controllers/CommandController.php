<?php

namespace App\Http\Controllers;

use Database\Seeds\SeederBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use July\Node\Node;
use July\Node\NodeField;

class CommandController extends Controller
{
    /**
     * 修改后台用户密码
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changeAdminPassword(Request $request)
    {
        if (config('app.is_demo')) {
            return response('');
        }

        $user = Auth::guard('admin')->user();

        $valid = Hash::check($request->input('current_password'), $user->getAuthPassword());
        if (! $valid) {
            return response('', 202);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();
        Auth::guard('admin')->login($user);

        return response('');
    }

    /**
     * 搜索后台数据库
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     *
     * @todo 重构
     */
    public function searchDatabase(Request $request)
    {
        $keywords = $request->input('keywords');

        $results = [];
        foreach (NodeField::all() as $field) {
            $results = array_merge($results, $field->searchValue($keywords));
        }
        foreach ($results as &$result) {
            $result['title'] = DB::table('node_index')->where('entity_id',$result['entity_id'])->where('field_id','title')->value('content');
            $result['Type'] = DB::table('nodes')->where('title',$result['title'])->value('mold_id');
            $result['src'] = '/manage/nodes/'.$result['entity_id'].'/edit';
        }

        return view('search', [
            'keywords' => $keywords,
            'results' => $results,
        ]);
    }
}
