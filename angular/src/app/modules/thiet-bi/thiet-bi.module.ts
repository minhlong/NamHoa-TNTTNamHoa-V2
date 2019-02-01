import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TextMaskModule } from 'angular2-text-mask';
import { NgxPaginationModule } from 'ngx-pagination';

import { SharedModule } from '../shared/shared.module';
import { DanhSachComponent } from './danh-sach/danh-sach.component';
import { CapNhatComponent } from './cap-nhat/cap-nhat.component';

import { DataService } from './data.service';

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
      { path: 'cap-nhat', component: CapNhatComponent },
    ]),
  ],
  declarations: [
    DanhSachComponent,
    CapNhatComponent,
  ],
  providers: [
    DataService
  ]
})
export class ThietBiModule { }
