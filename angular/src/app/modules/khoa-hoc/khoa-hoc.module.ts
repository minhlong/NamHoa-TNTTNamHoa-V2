import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { SharedModule } from '../shared/shared.module';

import { DanhSachComponent } from './danh-sach/danh-sach.component';

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
      // { path: 'tao-moi', component: TaoMoiComponent },
      // {
      //   path: 'chi-tiet/:id', component: ChiTietComponent,
      //   children: [
      //     { path: '', redirectTo: 'thong-tin', pathMatch: 'full' },
      //     { path: 'thong-tin', component: ThongTinComponent },
      //     { path: 'diem-danh', component: DiemDanhComponent },
      //     { path: 'diem-so', component: DiemSoComponent },
      //     { path: 'tong-ket', component: TongKetComponent },
      //   ],
      // },
    ]),
  ],
  declarations: [DanhSachComponent]
})
export class KhoaHocModule { }
