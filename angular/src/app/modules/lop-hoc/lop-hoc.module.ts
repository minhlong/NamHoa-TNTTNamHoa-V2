import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { SharedModule } from '../shared/shared.module';

// Chi Tiet
import { ChiTietComponent } from './chi-tiet/chi-tiet.component';
// Thong Tin
import { ThongTinComponent } from './chi-tiet/thong-tin/thong-tin.component';
import { FormThongTinComponent } from './chi-tiet/thong-tin/form/form.component';
import { HuynhTruongComponent } from './chi-tiet/thong-tin/huynh-truong/huynh-truong.component';
import { ThieuNhiComponent } from './chi-tiet/thong-tin/thieu-nhi/thieu-nhi.component';
// Diem Danh
import { DiemDanhComponent } from './chi-tiet/diem-danh/diem-danh.component';
import { FormDiemDanhComponent } from './chi-tiet/diem-danh/form/form.component';
// Diem So
import { DiemSoComponent } from './chi-tiet/diem-so/diem-so.component';
import { FormDiemSoComponent } from './chi-tiet/diem-so/form-diem-so/form-diem-so.component';
// Tong Ket
import { TongKetComponent } from './chi-tiet/tong-ket/tong-ket.component';
import { FormXepHangComponent } from './chi-tiet/tong-ket/form-xep-hang/form-xep-hang.component';
import { FormNhanXetComponent } from './chi-tiet/tong-ket/form-nhan-xet/form-nhan-xet.component';
import { PhieuLienLacComponent } from './chi-tiet/tong-ket/phieu-lien-lac/phieu-lien-lac.component';

import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { TaoMoiComponent } from './tao-moi/tao-moi.component';

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
    TaoMoiComponent,
    ChiTietComponent,
    ThongTinComponent, FormThongTinComponent, HuynhTruongComponent, ThieuNhiComponent,
    DiemDanhComponent, FormDiemDanhComponent,
    DiemSoComponent, FormDiemSoComponent,
    TongKetComponent, FormXepHangComponent, FormNhanXetComponent, PhieuLienLacComponent,
  ]
})
export class LopHocModule { }
