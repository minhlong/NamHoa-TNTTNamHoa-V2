import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { SharedModule } from '../shared/shared.module';
import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { ChiTietComponent } from './chi-tiet/chi-tiet.component';
import { DiemDanhComponent } from './diem-danh/diem-danh.component';
import { DiemSoComponent } from './diem-so/diem-so.component';
import { TongKetComponent } from './tong-ket/tong-ket.component';
import { TaoMoiComponent } from './tao-moi/tao-moi.component';
import { ThongTinComponent } from './thong-tin/thong-tin.component';
import { FormComponent } from './thong-tin/form/form.component';
import { HuynhTruongComponent } from './thong-tin/huynh-truong/huynh-truong.component';
import { ThieuNhiComponent } from './thong-tin/thieu-nhi/thieu-nhi.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    SharedModule,
    TextMaskModule,
    NgxPaginationModule,

    // Routes
    RouterModule.forChild([
      { path: '', component: DanhSachComponent },
      { path: 'tao-moi', component: TaoMoiComponent },
      {
        path: 'chi-tiet/:id', component: ChiTietComponent,
        children: [
          { path: '', redirectTo: 'thong-tin', pathMatch: 'full' },
          { path: 'thong-tin', component: ThongTinComponent },
          { path: 'diem-danh', component: DiemDanhComponent },
          { path: 'diem-so', component: DiemSoComponent },
          { path: 'tong-ket', component: TongKetComponent },
        ],
      },
    ]),
  ],
  declarations: [
    DanhSachComponent,
    ChiTietComponent, DiemDanhComponent, DiemSoComponent, TongKetComponent,
    ThongTinComponent, FormComponent, HuynhTruongComponent, ThieuNhiComponent,
    TaoMoiComponent,
  ]
})
export class LopHocModule { }
