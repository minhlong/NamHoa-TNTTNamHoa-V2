import { SharedModule } from '../shared/shared.module';
import { FormsModule } from '@angular/forms';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { ChiTietComponent } from './chi-tiet/chi-tiet.component';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    SharedModule,
    TextMaskModule,
    NgxPaginationModule,

    // Routes
    RouterModule.forChild([
      { path: '', component: DanhSachComponent },
      { path: ':id', component: ChiTietComponent },
    ]),
  ],
  declarations: [
    DanhSachComponent,
    ChiTietComponent,
  ]
})
export class TaiKhoanModule { }
