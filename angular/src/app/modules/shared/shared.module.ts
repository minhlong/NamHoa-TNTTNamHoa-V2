import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LoaiTK, TrangThai, Nganh, Cap, Doi, HienThiNgay } from './convert-type.pipe';

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
  ],
  exports: [
    LoaiTK,
    TrangThai,
    Nganh,
    Cap,
    Doi,
    HienThiNgay,
  ]
})
export class SharedModule { }
