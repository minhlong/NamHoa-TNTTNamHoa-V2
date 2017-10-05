import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AppState } from './../../../store/reducers/index';
import { GetLopInfoSucc } from '../../../store/actions/lop-hoc.action';
import { ngay } from '../../shared/convert-type.pipe';

@Component({
  selector: 'app-diem-danh',
  templateUrl: './diem-danh.component.html',
  styleUrls: ['./diem-danh.component.scss']
})
export class DiemDanhComponent implements OnDestroy {
  private lhAPI = environment.apiURL + '/lop-hoc';

  isLoading = true;
  ngayHoc: string;
  authSub: any;
  lhSub: any;
  tnSub: any;

  curAuth: any;
  thieuNhiArr = [];
  lopHocInfo: any = {
    khoa_hoc_id: null
  };

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 10,
    currentPage: 1,
  }

  maskOption = {
    mask: [/[0-3]/, /[0-9]/, '-', /[0-1]/, /[0-9]/, '-', /[1-2]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {
    this.ngayHoc = ngay(new Date().toJSON().slice(0, 10));

    this.authSub = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });
    this.lhSub = this.store.select((state: AppState) => state.lop_hoc.thong_tin).filter(c => c.id).subscribe(res => {
      this.lopHocInfo = res;
      this.loadData();
    });
    this.tnSub = this.store.select((state: AppState) => state.lop_hoc.thieu_nhi).subscribe(res => {
      this.thieuNhiArr = res;
    });
  }

  ngOnDestroy() {
    this.authSub.unsubscribe();
    this.lhSub.unsubscribe();
    this.tnSub.unsubscribe();
  }

  loadData() {
    const search = new URLSearchParams();
    search.set('ngay_hoc', ngay(this.ngayHoc));

    this.isLoading = true;
    this._http.get(this.lhAPI + '/' + this.lopHocInfo.id + '/chuyen-can', { search })
      .map(res => res.json()).subscribe(_res => {
        if (_res.data) {

        }
        console.log(_res);
        this.isLoading = false;
      }, error => {
        this.toasterService.pop('error', 'Lỗi!', error);
        this.isLoading = false;
      })
  }

  hasPerm() {
    return true;
  }

}
