import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { SharedModule } from '../shared/shared.module';
import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { ChiTietComponent } from './chi-tiet/chi-tiet.component';
import { ThongTinComponent } from './thong-tin/thong-tin.component';

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
      {
        path: 'chi-tiet/:id', component: ChiTietComponent,
        children: [
          { path: '', redirectTo: 'thong-tin', pathMatch: 'full' },
          { path: 'thong-tin', component: ThongTinComponent },
        ],
      },
    ]),
  ],
  declarations: [DanhSachComponent, ChiTietComponent, ThongTinComponent]
})
export class LopHocModule { }
