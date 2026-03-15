<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhoaHoc;
use Illuminate\Http\Request;

class KhoaHocController extends Controller
{
    public function index(Request $request)
    {
        $query = KhoaHoc::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ten_khoa_hoc', 'like', "%$search%")
                  ->orWhere('ma_khoa_hoc', 'like', "%$search%");
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $khoaHocs = $query->paginate(10);
        return view('admin.khoa_hoc.index', compact('khoaHocs'));
    }

    public function create()
    {
        return view('admin.khoa_hoc.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ma_khoa_hoc' => 'required|unique:khoa_hocs',
            'ten_khoa_hoc' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'so_buoi' => 'required|integer|min:1',
            'so_tiet_moi_buoi' => 'required|integer|min:1',
            'hoc_phi' => 'required|numeric|min:0',
            'trang_thai' => 'required|in:dang_mo,da_ket_thuc,tam_dung',
        ]);

        KhoaHoc::create($data);

        return redirect()->route('admin.khoa_hoc.index')->with('success', 'Thêm khóa học thành công!');
    }

    public function show(KhoaHoc $khoa_hoc)
    {
        return view('admin.khoa_hoc.show', compact('khoa_hoc'));
    }

    public function edit(KhoaHoc $khoa_hoc)
    {
        return view('admin.khoa_hoc.edit', compact('khoa_hoc'));
    }

    public function update(Request $request, KhoaHoc $khoa_hoc)
    {
        $data = $request->validate([
            'ma_khoa_hoc' => 'required|unique:khoa_hocs,ma_khoa_hoc,' . $khoa_hoc->id,
            'ten_khoa_hoc' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'so_buoi' => 'required|integer|min:1',
            'so_tiet_moi_buoi' => 'required|integer|min:1',
            'hoc_phi' => 'required|numeric|min:0',
            'trang_thai' => 'required|in:dang_mo,da_ket_thuc,tam_dung',
        ]);

        $khoa_hoc->update($data);

        return redirect()->route('admin.khoa_hoc.index')->with('success', 'Cập nhật khóa học thành công!');
    }

    public function destroy(KhoaHoc $khoa_hoc)
    {
        if ($khoa_hoc->lopHocs()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa khóa học đang có lớp học.');
        }

        $khoa_hoc->delete();
        return redirect()->route('admin.khoa_hoc.index')->with('success', 'Xóa khóa học thành công!');
    }
}
