import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from 'environments/environment';
import { AuthState } from '../../../store/reducers/auth.reducer';
import { AppState } from '../../../store/reducers';

@Component({
  selector: 'app-danh-sach',
  templateUrl: './danh-sach.component.html',
  styleUrls: ['./danh-sach.component.scss']
})
export class DanhSachComponent implements OnDestroy {
  urlAPI = environment.apiURL + '/nhom-tai-khoan';

  tab = 'danh-sach';
  dataArr = [];
  isLoading = false;
  curAuth: AuthState;
  itemSelected;

  paging = {
    // Paging
    id: 'phan-nhom-List-Page',
    itemsPerPage: 10,
    currentPage: 1,
  }

  subAuth$: any;

  constructor(
    private toasterService: ToasterService,
    private store: Store<AppState>,
    private _http: JwtAuthHttp,
  ) {
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    this.loadData();
  }

  ngOnDestroy() {
    this.subAuth$.unsubscribe()
  }

  private loadData() {
    this.isLoading = true;
    this._http.get(this.urlAPI).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.dataArr = res.data;
    }, error => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  /**
   * Chỉ những ai có quyền 'phan-quyen' mới được cập nhật
   */
  hasPerm() {
    if (this.curAuth.phan_quyen.includes('phan-quyen')) {
      return true;
    }

    return false;
  }

  /**
   * Xóa 1 Nhóm Tài Khoản
   * @param _item Nhom
   */
  xoa(_item) {
    this.isLoading = true;
    const _url = this.urlAPI + '/' + _item.id;
    this._http.delete(_url, null).map(res => res.json()).subscribe(res => {
      this.toasterService.pop('success', 'Đã xóa ' + _item.ten_hien_thi);
      this.loadData();
    }, _err => {
      this.toasterService.pop('error', 'Lỗi!', _err);
      this.isLoading = false;
    })
  }

  /**
   * Xử lý thông tin sau khi cập nhật thành-công/thất-bại
   * @param _info dữ liệu trả về từ cập nhật
   */
  update(_info) {
    this.tab = 'danh-sach';
    if (_info) {
      this.loadData();
    }
  }
}
