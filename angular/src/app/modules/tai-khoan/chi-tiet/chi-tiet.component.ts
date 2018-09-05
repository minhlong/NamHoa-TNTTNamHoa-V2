import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from 'environments/environment';
import { AppState } from '../../../store/reducers';

@Component({
  selector: 'app-chi-tiet',
  templateUrl: './chi-tiet.component.html',
  styleUrls: ['./chi-tiet.component.scss']
})
export class ChiTietComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/tai-khoan';
  tab = 'chi-tiet';
  itemSelected = null;
  isLoading = true;

  pState = {
    // Paging
    id: 'TaiKhoan-ChiTiet-Page',
    itemsPerPage: 3,
    currentPage: 1,
  }

  taiKhoanID: string;
  taiKhoanInfo: any;

  sub$: any;
  subAuth$: any;
  curAuth: any;

  constructor(
    private store: Store<AppState>,
    private router: Router,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
    private activatedRoute: ActivatedRoute
  ) {
    this.sub$ = this.activatedRoute.params.subscribe(params => {
      this.taiKhoanID = params['id'];

      // Lấy thông tin từ server
      this._http.get(this.urlAPI + '/' + this.taiKhoanID)
        .map(res => res.json()).subscribe(res => {
          this.isLoading = false;
          this.taiKhoanInfo = res;
        }, error => {
          this.isLoading = false;
          this.toasterService.pop('error', 'Lỗi!', error);
        });
    })

    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
    this.subAuth$.unsubscribe();
  }

  /**
   * Kiểm tra phân quyền
   *    Tài khoản có quyền 'tai-khoan'
   *    Hoặc
   *    Tài khoản này chính là tài khoản của người dùng đăng nhập
   */
  hasPerm() {
    if (this.curAuth.phan_quyen.includes('tai-khoan') || this.taiKhoanID.toUpperCase() === this.curAuth.tai_khoan.id) {
      return true;
    }
    return false;
  }

  // Xem tài khoản khác
  xemTaiKhoan(taiKhoan) {
    this.router.navigate(['/tai-khoan/chi-tiet/', taiKhoan.id]);
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
    this.taiKhoanInfo = _info;
  }
}
