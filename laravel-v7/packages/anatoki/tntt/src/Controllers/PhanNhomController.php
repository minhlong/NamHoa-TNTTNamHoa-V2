<?php

namespace TNTT\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PhanNhomController extends Controller
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
            'data' => Role::with(['users', 'permissions'])->get()->toArray(),
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
        $tableName = config('permission.table_names.roles');
        $request->validate([
            "name" => "required|unique:$tableName,name",
        ]);
        $item = Role::create($request->only(['name', 'note']));

        return response()->json($item->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @param  Role  $nhom_tai_khoan
     * @return JsonResponse
     */
    public function show(Role $nhom_tai_khoan)
    {
        return response()->json([
            'data' => $nhom_tai_khoan->load(['users', 'permissions'])->toArray(),
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Role  $nhom_tai_khoan
     * @return JsonResponse
     */
    public function update(Request $request, Role $nhom_tai_khoan)
    {
        $request->validate([
            "name" => "required",
        ]);
        $nhom_tai_khoan->fill($request->only(['name', 'note']))->save();

        return response()->json($nhom_tai_khoan->toArray());

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role  $nhom_tai_khoan
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Role $nhom_tai_khoan)
    {
        $nhom_tai_khoan->delete();
        return response()->json(['result' => true]);
    }
}
