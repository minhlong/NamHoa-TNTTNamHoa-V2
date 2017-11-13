import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';

import { consoleLog } from '../../../_helpers';
import { JwtAuthHttp } from './../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AuthState } from '../../../store/reducers/auth.reducer';
import { AppState } from './../../../store/reducers/index';

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss']
})
export class DanhSachComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/khoa-hoc';

  dataArr = [];
  isLoading = false;
  curAuth: AuthState;
  khoaHienTaiID = 0;

  tableKhoaHoc = {
    // Paging
    id: 'KhoaHoc-List-Page',
    itemsPerPage: 10,
    currentPage: 1,
  }

  sub$: any;
  subAuth$: any;

  constructor(
    private toasterService: ToasterService,
    private store: Store<AppState>,
    private _http: JwtAuthHttp,
  ) {
    consoleLog('Khoa Hoc Danh Sach Component: constructor');

    this.sub$ = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(res => {
      this.khoaHienTaiID = res.id;
    });

    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    this.loadData();
  }

  ngOnDestroy() {
    this.sub$.unsubscribe()
    this.subAuth$.unsubscribe()
  }

  private loadData() {
    this.isLoading = true;
    this._http.get(this.urlAPI).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.dataArr = res.data.sort((a, b) => {
        if (a.id < b.id) {
          return 1;
        } else if (a.id > b.id) {
          return -1;
        } else { return 0; }
      });
    }, error => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  /**
   * Chỉ những ai có quyền 'he-thong'
   * và
   * Chỉ được tạo 1 khóa học cho năm học tới
   */
  hasPermTaoMoi() {
    if (!this.dataArr.length) {
      return false;
    }

    const newKhoaHocID = this.dataArr.find(c => c.id === (this.khoaHienTaiID + 1));
    if (!newKhoaHocID && this.curAuth.phan_quyen.includes('he-thong')) {
      return true;
    }

    return false;
  }

  taoMoi() {
    this.isLoading = true;

    this._http.post(this.urlAPI, null).map(res => res.json()).subscribe(res => {
      this.loadData();
    }, error => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })

  }
}
