import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { TaoMoiComponent } from './tao-moi/tao-moi.component';
import { MatKhauComponent } from './mat-khau/mat-khau.component';
import { SharedModule } from '../shared/shared.module';
import { ChiTietComponent } from './chi-tiet/chi-tiet.component';
import { FormComponent } from './chi-tiet/form/form.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    SharedModule,
    TextMaskModule,
    NgxPaginationModule,

    RouterModule.forChild([
      { path: '', component: DanhSachComponent },
      { path: 'chi-tiet/:id', component: ChiTietComponent },
      { path: 'mat-khau', component: MatKhauComponent },
      { path: 'tao-moi', component: TaoMoiComponent },
    ]),
  ],
  declarations: [
    DanhSachComponent,
    ChiTietComponent,
    TaoMoiComponent,
    MatKhauComponent,
    FormComponent,
  ]
})
export class TaiKhoanModule { }
