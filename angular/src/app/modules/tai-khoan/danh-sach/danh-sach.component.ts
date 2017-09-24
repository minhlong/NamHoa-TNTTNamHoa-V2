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

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss']
})
export class DanhSachComponent implements OnDestroy {
  webAPI = environment.webURL + '/tai-khoan/download';
  urlAPI = environment.apiURL + '/tai-khoan';
  maskOption = {
    mask: [/[0-3]/, /[1-9]/, '-', /[0-1]/, /[1-9]/, '-', /[1-2]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
    placeholderChar: ' ',
  }

  isLoading = true;
  isLoadingExport = false;
  khoaHienTaiID = 0;
  dataArr = [];
  sub: any;

  cookieState: any;

  constructor(
    private toasterService: ToasterService,
    private store: Store<AppState>,
    private _http: JwtAuthHttp,
  ) {
    consoleLog('DashboardComponent: constructor');

    this.sub = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai_id).subscribe(x => {
      this.khoaHienTaiID = x;
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

  updateState() {
    localStorage.setItem(this.cookieState.id, JSON.stringify(this.cookieState));
  }

  resetCookieState() {
    localStorage.removeItem(this.cookieState.id);
    this.resetPageState();
    this.searchData();
  }

  checkHidden(_val): boolean {
    return this.cookieState.FmoRong || _val;
  }

  searchData() {
    this.isLoading = true;
    this.cookieState.FmoRong = false;
    this.updateState();

    const search = this.getFilter();
    this._http.get(this.urlAPI, { search }).map(res => res.json()).subscribe(res => {
      this.dataArr = res.data;
      this.isLoading = false;
    }, error => {
      this.dataArr = [];
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    }, () => {
      this.isLoading = false;
    })
  }

  exportData() {
    this.isLoadingExport = true;
    const search = this.getFilter();
    this.toasterService.pop('info', 'Đang tải');

    this._http.get(this.urlAPI + '/export', { search }).map(res => res.json()).subscribe(res => {
      this.isLoadingExport = false;
      window.open(this.webAPI + '/' + res.data);
    }, error => {
      this.isLoadingExport = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    });
  }

  private getFilter() {
    const search = new URLSearchParams();
    search.set('khoa', this.cookieState.Fkhoa);
    search.set('nganh', this.cookieState.Fnganh);
    search.set('cap', this.cookieState.Fcap);
    search.set('doi', this.cookieState.Fdoi);
    search.set('trang_thai', this.cookieState.Ftrang_thai);
    search.set('loai_tai_khoan', this.cookieState.Floai_tai_khoan);
    search.set('ho_va_ten', this.cookieState.Fho_va_ten);
    search.set('ngay_sinh_tu', ngay(this.cookieState.Fngay_sinh_tu));
    search.set('ngay_sinh_den', ngay(this.cookieState.Fngay_sinh_den));
    search.set('ngay_tao_tu', ngay(this.cookieState.Fngay_tao_tu));
    search.set('ngay_tao_den', ngay(this.cookieState.Fngay_tao_den));
    return search;
  }

  ngOnDestroy() {
    this.sub.unsubscribe()
  }
}
