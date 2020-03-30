import { Component, OnInit, OnDestroy } from '@angular/core';
import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';

import { defaultPageState } from './defaultPageState';
import { AppState } from '../../../store/reducers';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpParams } from '@angular/common/http';
import { ngay } from '../../shared/utities.pipe';
import { AuthState } from '../../../store/reducers/auth.reducer';

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss']
})
export class DanhSachComponent implements OnDestroy {
  webAPI = environment.webURL + '/download';
  urlAPI = environment.apiURL + '/tai-khoan';
  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  };

  isLoading = true;
  isLoadingExport = false;
  khoaHienTaiID = 0;
  dataArr = [];

  itemSelected: any;
  curAuth: AuthState;
  subAuth$: any;
  sub$: any;

  cookieState: any;

  constructor(
    private toasterService: ToasterService,
    private store: Store<AppState>,
    private http: HttpClient,
  ) {
    this.sub$ = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(res => {
      this.khoaHienTaiID = res.id;
    });

    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    // Update current page state
    this.resetPageState();
    this.searchData();
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
    this.subAuth$.unsubscribe();
  }

  private resetPageState() {
    this.cookieState = JSON.parse(JSON.stringify(defaultPageState));
    this.cookieState.Fkhoa = this.khoaHienTaiID;

    Object.assign(this.cookieState, JSON.parse(localStorage.getItem(this.cookieState.id)));
  }

  searchData() {
    this.isLoading = true;
    this.cookieState.FmoRong = false;
    this.updateState();

    const params = this.getFilter();
    this.http.get(this.urlAPI, { params }).subscribe((res: any) => {
      this.dataArr = res.data;
      this.isLoading = false;
    }, error => {
      this.dataArr = [];
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoading = false;
    });
  }

  updateState() {
    localStorage.setItem(this.cookieState.id, JSON.stringify(this.cookieState));
  }

  private getFilter() {
    return new HttpParams()
      .set('khoa', this.cookieState.Fkhoa)
      .set('nganh', this.cookieState.Fnganh)
      .set('cap', this.cookieState.Fcap)
      .set('doi', this.cookieState.Fdoi)
      .set('trang_thai', this.cookieState.Ftrang_thai)
      .set('loai_tai_khoan', this.cookieState.Floai_tai_khoan)
      .set('id', this.cookieState.Fid)
      .set('ho_va_ten', this.cookieState.Fho_va_ten)
      .set('ngay_sinh_tu', ngay(this.cookieState.Fngay_sinh_tu))
      .set('ngay_sinh_den', ngay(this.cookieState.Fngay_sinh_den))
      .set('ngay_tao_tu', ngay(this.cookieState.Fngay_tao_tu))
      .set('ngay_tao_den', ngay(this.cookieState.Fngay_tao_den));
  }

  hasPermTaoMoi() {
    if (this.curAuth.phan_quyen.includes('tai-khoan')) {
      return true;
    }
    return false;
  }

  hasPermXoa(item) {
    if (this.curAuth.phan_quyen.includes('tai-khoan') && item.trang_thai === 'TAM_NGUNG') {
      return true;
    }
    return false;
  }

  xoa(item) {
    this.isLoading = true;
    const url = this.urlAPI + '/' + item.id;

    this.http.delete(url, null).subscribe(res => {
      this.toasterService.pop('success', 'Đã xóa ' + item.id + ' ' + item.ten_thanh + ' ' + item.ho_va_ten);
      this.searchData();
    }, err => {
      this.toasterService.pop('error', 'Lỗi!', err);
      this.isLoading = false;
    });
  }

  resetCookieState() {
    localStorage.removeItem(this.cookieState.id);
    this.resetPageState();
    this.searchData();
  }

  checkHidden(val): boolean {
    return this.cookieState.FmoRong || val;
  }

  exportData() {
    this.isLoadingExport = true;
    const params = this.getFilter();
    this.toasterService.pop('info', 'Đang tải');

    this.http.get(this.urlAPI + '/export', { params }).subscribe((res: any) => {
      this.isLoadingExport = false;
      window.open(this.webAPI + '/' + res.data);
    }, error => {
      this.isLoadingExport = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    });
  }
}
