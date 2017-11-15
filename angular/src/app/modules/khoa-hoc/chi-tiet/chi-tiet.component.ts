import { ToasterService } from 'angular2-toaster';
import { Component, ViewEncapsulation, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from './../../../../environments/environment';
import { AppState } from './../../../store/reducers/index';

@Component({
  selector: 'app-chi-tiet',
  templateUrl: './chi-tiet.component.html',
  styleUrls: ['./chi-tiet.component.scss'],
  encapsulation: ViewEncapsulation.None
})
export class ChiTietComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/khoa-hoc';
  tab = 'chi-tiet'; // form
  isLoading = true;

  khoaID: number;
  khoaInfo: any;

  sub$: any;
  subAuth: any;
  curAuth: any;

  constructor(
    private store: Store<AppState>,
    private router: Router,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
    private activatedRoute: ActivatedRoute
  ) {
    this.sub$ = this.activatedRoute.params.subscribe(params => {
      this.khoaID = +params['id'];

      this._http.get(this.urlAPI + '/' + this.khoaID)
        .map(res => res.json().data).subscribe(res => {
          this.isLoading = false;
          this.khoaInfo = res;
        }, error => {
          this.isLoading = false;
          this.toasterService.pop('error', 'Lỗi!', error);
        });
    })

    this.subAuth = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
    this.subAuth.unsubscribe();
  }

  /**
   * Kiểm tra phân quyền
   *    Tài khoản có quyền 'he-thong'
   *    và
   *    Khóa học này chính là khóa khọc hiện tại hoặc khóa học tiếp theo
   */
  hasPerm() {
    if (
      this.curAuth.phan_quyen.includes('he-thong') &&
      this.curAuth.khoa_hoc_hien_tai &&
      this.curAuth.khoa_hoc_hien_tai.id <= this.khoaID) {
      return true;
    }
    return false;
  }

  /**
   * Phản hồi thông tin từ form
   * @param _info Thông tin khoa học sau khi update
   */
  update(_info) {
    this.tab = 'chi-tiet';

    if (!_info) {
      return; // Click Thoát
    }
    this.khoaInfo = _info;
  }
}
