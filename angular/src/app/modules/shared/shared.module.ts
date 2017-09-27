import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NewlinePipe, LoaiTK, TrangThai, Nganh, Cap, Doi, GioiTinh, HienThiNgay } from './convert-type.pipe';

@NgModule({
  imports: [
    CommonModule,
  ],
  declarations: [
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
