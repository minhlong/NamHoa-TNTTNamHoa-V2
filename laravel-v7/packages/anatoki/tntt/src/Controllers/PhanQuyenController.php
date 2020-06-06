<?php

namespace TNTT\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PhanQuyenController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
        $this->middleware(['bindings'])->only([
            'show',
            'update',
            'destroy',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Permission::with(['users', 'roles'])->get()->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $tableName = config('permission.table_names.permissions');
        $request->validate([
            "name" => "required|unique:$tableName,name",
        ]);
        $item = Permission::create($request->only(['name', 'note']));

        return response()->json($item->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @param  Permission  $phan_quyen
     * @return JsonResponse
     */
    public function show(Permission $phan_quyen)
    {
        return response()->json([
            'data' => $phan_quyen->load(['users', 'roles'])->toArray(),
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Permission  $phan_quyen
     * @return JsonResponse
     */
    public function update(Request $request, Permission $phan_quyen)
    {
        $request->validate([
            "name" => "required",
        ]);
        $phan_quyen->fill($request->only(['name', 'note']))->save();

        return response()->json($phan_quyen->toArray());

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Permission  $phan_quyen
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Permission $phan_quyen)
    {
        $phan_quyen->delete();
        return response()->json(['result' => true]);
    }
}
