import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AppState } from './../../../store/reducers/index';

@Component({
  selector: 'app-thong-tin',
  templateUrl: './thong-tin.component.html',
  styleUrls: ['./thong-tin.component.scss']
})
export class ThongTinComponent implements OnDestroy {

  isLoading = true;
  authSub: any;
  lhSub: any;
  htSub: any;
  tnSub: any;

  curAuth: any;
  lopHocInfo: any = {};
  huynhTruongArr = [];
  thieuNhiArr = [];

  pHT = {
    id: 'htTabl',
    itemsPerPage: 3,
    currentPage: 1,
  }

  pTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
    private activatedRoute: ActivatedRoute
  ) {
    this.authSub = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    // this.lhSub = this.store.select((state: AppState) => state.lop_hoc.thong_tin).subscribe(res => {
    //   this.lopHocInfo = res;
    // });

    this.htSub = this.store.select((state: AppState) => state.lop_hoc.huynh_truong).subscribe(res => {
      this.huynhTruongArr = res;
      this.isLoading = false;
    });

    this.tnSub = this.store.select((state: AppState) => state.lop_hoc.thieu_nhi).subscribe(res => {
      this.thieuNhiArr = res;
      this.isLoading = false;
    });
  }

  hasPerm() {
    if (this.curAuth.phan_quyen.includes('lop-hoc')) {
      return true;
    }
    return false;
  }

  ngOnDestroy() {
    this.authSub.unsubscribe();
    // this.lhSub.unsubscribe();
    this.htSub.unsubscribe();
    this.tnSub.unsubscribe();
  }
}
