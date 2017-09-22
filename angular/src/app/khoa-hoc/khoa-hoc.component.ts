import { ToasterService } from 'angular2-toaster';
import { Component, OnInit } from '@angular/core';
import { JwtAuthHttp } from '../services/http-auth.service';
import { environment } from './../../environments/environment';
import { consoleLog } from '../shared/helpers';
import { defaultPageState } from './defaultPageState';

@Component({
  selector: 'app-khoa-hoc',
  templateUrl: './khoa-hoc.component.html',
  styleUrls: ['./khoa-hoc.component.scss']
})
export class KhoaHocComponent {
  urlAPI = environment.apiURL + '/khoa-hoc';
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
      this.dataArr = res.data.sort((a, b) => {
        if (a.id < b.id) {
          return 1;
        } else if (a.id > b.id) {
          return -1;
        } else { return 0; }
      });
    }, error => {
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  updateState() {
    localStorage.setItem(this.cookieState.id, JSON.stringify(this.cookieState));
  }

  resetCookieState() {
    localStorage.removeItem(this.cookieState.id);
    this.cookieState = JSON.parse(JSON.stringify(defaultPageState))
  }
}
