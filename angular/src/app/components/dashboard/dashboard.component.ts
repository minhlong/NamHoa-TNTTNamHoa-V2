import { Component } from '@angular/core';
import { JwtAuthHttp } from '../../services/http-auth.service';
import { consoleLog } from '../../shared/helpers';
import { environment } from '../../../environments/environment';
import { defaultPageState } from './defaultPageState';
import { ToasterService } from 'angular2-toaster';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],
})

export class DashboardComponent {
  urlAPI = environment.apiURL + '/tai-khoan';
  dataArr = [];

  cookieState = JSON.parse(JSON.stringify(defaultPageState))

  constructor(
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {
    consoleLog('DashboardComponent: constructor');

    // Update current page state
    Object.assign(this.cookieState, JSON.parse(localStorage.getItem(this.cookieState.id)));

    this.loadData();
  }

  private loadData() {
    this._http.get(this.urlAPI).map(res => res.json()).subscribe(res => {
      this.dataArr = res.data;
    }, error => {
      console.log(error);
    })
  }

  updatePageChange(_page) {
    this.cookieState.currentPage = _page;
    this.updateState();
  }

  updateState() {
    localStorage.setItem(this.cookieState.id, JSON.stringify(this.cookieState));
  }

  resetCookieState() {
    localStorage.removeItem(this.cookieState.id);
    this.cookieState = JSON.parse(JSON.stringify(defaultPageState));
  }

  view(_taiKhoan) {
    this.toasterService.pop('success', _taiKhoan.ho_va_ten, _taiKhoan.ho_va_ten);
  }
}
