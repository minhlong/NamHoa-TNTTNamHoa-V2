import { AppState } from './../../store/reducers/index';
import { Component, OnDestroy } from '@angular/core';
import { JwtAuthHttp } from '../../services/http-auth.service';
import { consoleLog } from '../../shared/helpers';
import { environment } from '../../../environments/environment';
import { defaultPageState } from './defaultPageState';
import { ToasterService } from 'angular2-toaster';
import { Store } from '@ngrx/store';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],
})

export class DashboardComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/tai-khoan';
  isLoading = true;
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
  }

  checkHidden(_val): boolean {
    return this.cookieState.FmoRong || _val;
  }

  searchData() {
    this.isLoading = true;
    this.cookieState.FmoRong = false;
    this.updateState();
    this._http.get(this.urlAPI).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.dataArr = res.data;
    }, error => {
      this.isLoading = false;
      this.dataArr = [];
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  view(_taiKhoan) {
    this.toasterService.pop('success', _taiKhoan.ho_va_ten, _taiKhoan.ho_va_ten);
  }

  ngOnDestroy() {
    this.sub.unsubscribe()
  }
}
