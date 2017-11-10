import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  NewlinePipe,
  XepLoai, XepHang, LoaiTK, TrangThai, Nganh, Cap, Doi, GioiTinh,
  HienThiNgay, ObjectKeysPipe, Phieu
} from './convert-type.pipe';

@NgModule({
  imports: [
    CommonModule,
  ],
  declarations: [
    XepLoai,
    XepHang,
    LoaiTK,
    TrangThai,
    Nganh,
    Cap,
    Doi,
    HienThiNgay,
    GioiTinh,
    NewlinePipe,
    Phieu,
    ObjectKeysPipe,
  ],
  exports: [
    XepLoai,
    XepHang,
    LoaiTK,
    TrangThai,
    Nganh,
    Cap,
    Doi,
    HienThiNgay,
    GioiTinh,
    NewlinePipe,
    Phieu,
    ObjectKeysPipe,
  ]
})
export class SharedModule { }
