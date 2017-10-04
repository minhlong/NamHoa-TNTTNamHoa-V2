import { Component, OnInit, OnDestroy } from '@angular/core';
import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';
import { URLSearchParams } from '@angular/http';

import { defaultPageState } from './defaultPageState';
import { AppState } from './../../../store/reducers/index';
import { environment } from '../../../../environments/environment';
import { JwtAuthHttp } from '../../../services/http-auth.service';
import { consoleLog } from '../../../_helpers';
import { ngay } from '../../shared/convert-type.pipe';
import { AuthState } from './../../../store/reducers/auth.reducer';
import { Router } from '@angular/router';

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss']
})
export class DanhSachComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/lop-hoc';
  isLoading = true;
  khoaHienTaiID = 0;
  dataArr = [];

  itemSelected: any;
  curAuth: AuthState;
  authSub: any;
  sub: any;

  cookieState: any;

  constructor(
    private router: Router,
    private toasterService: ToasterService,
    private store: Store<AppState>,
    private _http: JwtAuthHttp,
  ) {
    consoleLog('Lop Hoc Danh Sach: constructor');

    this.sub = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(x => {
      this.khoaHienTaiID = x.id;
    });


    this.authSub = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    // Update current page state
    this.resetPageState();

    this.searchData();
  }

  private resetPageState() {
    this.cookieState = JSON.parse(JSON.stringify(defaultPageState));
    this.cookieState.Fkhoa = this.khoaHienTaiID;

    Object.assign(this.cookieState, JSON.parse(localStorage.getItem(this.cookieState.id)));
  }

  searchData() {
    this.isLoading = true;
    this.updateState();

    const search = this.getFilter();
    this._http.get(this.urlAPI + '/khoa-' + this.cookieState.Fkhoa, { search }).map(res => res.json()).subscribe(res => {
      this.dataArr = res.data;
      this.isLoading = false;
    }, error => {
      this.dataArr = [];
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoading = false;
    })
  }

  updateState() {
    localStorage.setItem(this.cookieState.id, JSON.stringify(this.cookieState));
  }

  private getFilter() {
    const search = new URLSearchParams();
    search.set('nganh', this.cookieState.Fnganh);
    search.set('cap', this.cookieState.Fcap);
    search.set('doi', this.cookieState.Fdoi);
    return search;
  }

  resetCookieState() {
    localStorage.removeItem(this.cookieState.id);
    this.resetPageState();
    this.searchData();
  }

  hasPermTaoMoi() {
    if (this.curAuth.phan_quyen.includes('lop-hoc')) {
      return true;
    }
    return false;
  }

  hasPermXoa(_item) {
    if (this.curAuth.phan_quyen.includes('lop-hoc')) {
      return true;
    }
    return false;
  }

  xoa(_item) {
    this.isLoading = true;
    const _url = this.urlAPI + '/' + _item.id;
    this._http.delete(_url, null).map(res => res.json()).subscribe(res => {
      this.toasterService.pop('success', 'Đã xóa ' + _item.ten);
      this.searchData();
    }, _err => {
      this.toasterService.pop('error', 'Lỗi!', _err);
      this.isLoading = false;
    })
  }

  xemTaiKhoan(taiKhoan) {
    this.router.navigate(['/tai-khoan/chi-tiet/', taiKhoan.id]);
  }

  ngOnDestroy() {
    this.sub.unsubscribe()
    this.authSub.unsubscribe()
  }
}
