import { Component, OnInit, OnDestroy } from '@angular/core';
import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';
import { URLSearchParams } from '@angular/http';

import { defaultPageState } from './defaultPageState';
import { AppState } from '../../../store/reducers';
import { environment } from 'environments/environment';
import { JwtAuthHttp } from '../../../services/http-auth.service';
import { ngay } from '../../shared/utities.pipe';
import { AuthState } from '../../../store/reducers/auth.reducer';

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss']
})
export class DanhSachComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/thu-moi';
  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  tab = 'danh-sach';
  isLoading = true;
  khoaHienTai: any;
  dataArr = [];

  itemSelected: any;
  curAuth: AuthState;
  subAuth$: any;
  sub$: any;

  cookieState: any;

  constructor(
    private toasterService: ToasterService,
    private store: Store<AppState>,
    private _http: JwtAuthHttp,
  ) {
    this.sub$ = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(res => {
      this.khoaHienTai = res;
    });

    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    // Update current page state
    this.resetPageState();
    this.searchData();
  }

  ngOnDestroy() {
    this.sub$.unsubscribe()
    this.subAuth$.unsubscribe()
  }

  private resetPageState() {
    this.cookieState = JSON.parse(JSON.stringify(defaultPageState));
    this.cookieState.Ftu_ngay = ngay(this.khoaHienTai.ngay_bat_dau);

    Object.assign(this.cookieState, JSON.parse(localStorage.getItem(this.cookieState.id)));
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
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoading = false;
    })
  }

  updateState() {
    localStorage.setItem(this.cookieState.id, JSON.stringify(this.cookieState));
  }

  private getFilter() {
    const search = new URLSearchParams();
    search.set('tai_khoan_id', this.cookieState.Ftai_khoan_id);
    search.set('ho_va_ten', this.cookieState.Fho_va_ten);
    search.set('tu_ngay', ngay(this.cookieState.Ftu_ngay));
    search.set('den_ngay', ngay(this.cookieState.Fden_ngay));
    return search;
  }

  hasPerm(_item) {
    if (this.curAuth.phan_quyen.includes('lop-hoc')) {
      return true;
    }
    return false;
  }

  xoa(_item) {
    this.isLoading = true;
    const _url = this.urlAPI + '/' + _item.id;
    this._http.delete(_url, null).map(res => res.json()).subscribe(res => {
      this.toasterService.pop('success', 'Đã xóa');
      this.searchData();
    }, _err => {
      this.toasterService.pop('error', 'Lỗi!', _err);
      this.isLoading = false;
    })
  }

  resetCookieState() {
    localStorage.removeItem(this.cookieState.id);
    this.resetPageState();
    this.searchData();
  }

  /**
   * Xử lý thông tin sau khi cập nhật thành-công/thất-bại
   * @param _info dữ liệu trả về từ cập nhật
   */
  update(_info) {
    this.tab = 'danh-sach';
    if (_info) {
      this.searchData();
    }
  }
}
