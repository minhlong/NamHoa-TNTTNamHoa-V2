import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AppState } from './../../../store/reducers/index';
import { GetLopInfoSucc } from '../../../store/actions/lop-hoc.action';

@Component({
  selector: 'app-thong-tin',
  templateUrl: './thong-tin.component.html',
  styleUrls: ['./thong-tin.component.scss']
})
export class ThongTinComponent implements OnDestroy {

  tab = 'thong-tin'
  isLoading = true;
  authSub: any;
  htSub: any;
  tnSub: any;

  curAuth: any;
  huynhTruongArr = [];
  thieuNhiArr = [];

  pagingTN = {
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

    this.htSub = this.store.select((state: AppState) => state.lop_hoc.huynh_truong).subscribe(res => {
      this.huynhTruongArr = res;
      this.isLoading = false;
    });

    this.tnSub = this.store.select((state: AppState) => state.lop_hoc.thieu_nhi).subscribe(res => {
      this.thieuNhiArr = res;
      this.isLoading = false;
    });
  }

  ngOnDestroy() {
    this.authSub.unsubscribe();
    this.htSub.unsubscribe();
    this.tnSub.unsubscribe();
  }

  hasPerm() {
    if (this.curAuth.phan_quyen.includes('lop-hoc')) {
      return true;
    }
    return false;
  }

  update(_info) {
    this.tab = 'thong-tin';
    if (_info) {
      this.store.dispatch(new GetLopInfoSucc(_info));
    }
  }
}
