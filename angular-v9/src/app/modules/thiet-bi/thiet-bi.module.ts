import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';
import {SelectModule} from 'ng2-select';

import { SharedModule } from '../shared/shared.module';
import { DanhSachComponent } from './danh-sach/danh-sach.component';

import { DataService } from './data.service';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    SharedModule,
    TextMaskModule,
    NgxPaginationModule,
    SelectModule,

    RouterModule.forChild([
      { path: '', component: DanhSachComponent },
    ]),
  ],
  declarations: [
    DanhSachComponent,
  ],
  providers: [
    DataService
  ]
})
export class ThietBiModule { }
