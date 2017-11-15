import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { SharedModule } from '../shared/shared.module';

import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { FormEditComponent } from './danh-sach/form-edit/form-edit.component';
import { FormPhanQuyenComponent } from './danh-sach/form-phan-quyen/form-phan-quyen.component';

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
    ]),
  ],
  declarations: [DanhSachComponent, FormEditComponent, FormPhanQuyenComponent]
})
export class PhanQuyenModule { }
