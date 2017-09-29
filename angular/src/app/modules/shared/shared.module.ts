import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NewlinePipe, XepHang, LoaiTK, TrangThai, Nganh, Cap, Doi, GioiTinh, HienThiNgay } from './convert-type.pipe';

@NgModule({
  imports: [
    CommonModule,
  ],
  declarations: [
    XepHang,
    LoaiTK,
    TrangThai,
    Nganh,
    Cap,
    Doi,
    HienThiNgay,
    GioiTinh,
    NewlinePipe,
  ],
  exports: [
    XepHang,
    LoaiTK,
    TrangThai,
    Nganh,
    Cap,
    Doi,
    HienThiNgay,
    GioiTinh,
    NewlinePipe,
  ]
})
export class SharedModule { }
