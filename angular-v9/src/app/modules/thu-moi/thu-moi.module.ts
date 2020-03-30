import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { SharedModule } from '../shared/shared.module';

import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { FormEditComponent } from './danh-sach/form-edit/form-edit.component';
import { FormTaoMoiComponent } from './danh-sach/form-tao-moi/form-tao-moi.component';

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
  declarations: [DanhSachComponent, FormEditComponent, FormTaoMoiComponent]
})
export class ThuMoiModule { }
