import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { URLSearchParams } from '@angular/http';
import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';
import { Subject } from 'rxjs/Rx';

import { JwtAuthHttp } from 'app/services/http-auth.service';
import { AppState } from 'app/store/reducers';
import { environment } from 'environments/environment.prod';
import { bodauTiengViet } from '../../../../_helpers';

@Component({
  selector: 'app-form-phan-quyen',
  templateUrl: './form-phan-quyen.component.html',
  styleUrls: ['./form-phan-quyen.component.scss']
})
export class FormPhanQuyenComponent implements OnInit, OnDestroy {
  @Input() quyenInfo;
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/phan-quyen/nhom';
  private tkAPI = environment.apiURL + '/tai-khoan';
  private taiKhoanSrcArr = [];

  isLoadingTK: boolean;

  huynhTruongArr = [];
  taiKhoanArr = [];
  filter$ = new Subject<any>();

  pagingHT = {
    // Paging
    id: 'phan-quyen-edit-page',
    itemsPerPage: 10,
    currentPage: 1,
  }

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {
    this.loadTaiKhoan();

    // Xử lý tìm kiếm tài khoản
    this.filter$.debounceTime(400).subscribe((_str) => {
      this.taiKhoanArr = this.taiKhoanSrcArr.filter(el => {
        // Loại bỏ tài khoản đã phân quyền
        return !this.huynhTruongArr.find(c => c.ten === el.id)
      }).filter(el => {
        // Bỏ dấu để lọc theo tên
        const _tmpA = bodauTiengViet(el.ho_va_ten);
        const _tmpB = bodauTiengViet(_str);
        return _tmpA.toLowerCase().indexOf(_tmpB.toLowerCase()) !== -1
      });
    });
  }

  ngOnInit() {
    this.huynhTruongArr = this.quyenInfo.role_taikhoan;
  }

  ngOnDestroy() {
    this.filter$.complete();
  }

  /**
   * Load dữ liệu từ server
   */
  private loadTaiKhoan() {
    this.isLoadingTK = true;

    /**
     * Chỉ lọc tài khoản còn hoạt động và là huynh trưởng
     */
    const search = new URLSearchParams();
    search.set('trang_thai', 'HOAT_DONG');
    search.set('loai_tai_khoan', 'HUYNH_TRUONG,SOEUR,LINH_MUC');

    this._http.get(this.tkAPI, { search }).map(res => res.json()).subscribe(res => {
      this.isLoadingTK = false;
      this.taiKhoanSrcArr = res.data;

      // Sau khi lấy dữ liệu về thì lọc tìm kiếm
      this.filter$.next('');
    }, error => {
      this.isLoadingTK = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  checkAll(_arr: any[], _event) {
    _arr.forEach(_el => {
      _el.checked = _event.target.checked
    });
  }

  getChecked(_arr: any[]) {
    return _arr.filter((_el) => {
      return _el.checked === true;
    }).length;
  }

  them() {

  }

  xoa() {

  }

  /**
   * Thoát -> quay lại trang chính
   */
  cancel() {
    this.updateInfo.emit(null);
  }
}
