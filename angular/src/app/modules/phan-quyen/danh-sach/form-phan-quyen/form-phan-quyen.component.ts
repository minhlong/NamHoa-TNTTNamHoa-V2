import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { URLSearchParams } from '@angular/http';
import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { ToasterService } from 'angular2-toaster';
import { Subject } from 'rxjs';

import { JwtAuthHttp } from '../../../../services/http-auth.service';
import { AppState } from '../../../../store/reducers';
import { environment } from 'environments/environment';
import { bodauTiengViet } from '../../../../_helpers';

@Component({
  selector: 'app-form-phan-quyen',
  templateUrl: './form-phan-quyen.component.html',
  styleUrls: ['./form-phan-quyen.component.scss']
})
export class FormPhanQuyenComponent implements OnInit, OnDestroy {
  @Input() quyenInfo;
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/phan-quyen';
  private tkAPI = environment.apiURL + '/tai-khoan';
  private nhomAPI = environment.apiURL + '/nhom-tai-khoan';
  private taiKhoanSrcArr = [];

  isLoading = false;

  taiKhoanArr = [];
  nhomArr = [];
  filter$ = new Subject<any>();

  pagingNhom1 = {
    id: 'nhom1-phan-quyen-edit-page',
    itemsPerPage: 5,
    currentPage: 1,
  }
  pagingNhom2 = {
    id: 'nhom2-phan-quyen-edit-page',
    itemsPerPage: 5,
    currentPage: 1,
  }
  pagingHT1 = {
    id: 'ht1-phan-quyen-edit-page',
    itemsPerPage: 5,
    currentPage: 1,
  }
  pagingHT2 = {
    id: 'ht2-phan-quyen-edit-page',
    itemsPerPage: 5,
    currentPage: 1,
  }

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {
    // Xử lý tìm kiếm tài khoản
    this.filter$.debounceTime(400).subscribe((_str) => {
      this.taiKhoanArr = this.taiKhoanSrcArr.filter(el => {
        // Loại bỏ tài khoản đã phân quyền
        return !this.quyenInfo.role_taikhoan.find(c => c.ten === el.id)
      }).filter(el => {
        // Bỏ dấu để lọc theo tên
        const _tmpA = bodauTiengViet(el.ho_va_ten);
        const _tmpB = bodauTiengViet(_str);
        return _tmpA.toLowerCase().indexOf(_tmpB.toLowerCase()) !== -1
      });
    });

    this.loadTaiKhoan();
    this.loadNhom();
  }

  ngOnInit() {
  }

  ngOnDestroy() {
    this.filter$.complete();
  }

  /**
   * Load dữ liệu từ server
   */
  private loadTaiKhoan() {
    this.isLoading = true;

    /**
     * Chỉ lọc tài khoản còn hoạt động và là huynh trưởng
     */
    const search = new URLSearchParams();
    search.set('trang_thai', 'HOAT_DONG');
    search.set('loai_tai_khoan', 'HUYNH_TRUONG,SOEUR,LINH_MUC');

    this._http.get(this.tkAPI, { search }).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.taiKhoanSrcArr = res.data;

      // Sau khi lấy dữ liệu về thì lọc tìm kiếm
      this.filter$.next('');
    }, error => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  /**
   * Load dữ liệu từ server
   */
  private loadNhom() {
    this.isLoading = true;

    this._http.get(this.nhomAPI).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.nhomArr = res.data.filter(el => {
        // Loại bỏ nhóm đã phân quyền
        return !this.quyenInfo.role_nhom.find(c => c.id === el.id)
      });
    }, error => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  /**
   * Click "Check All" -> check tất cả
   * @param _arr Arr
   * @param _event checked
   */
  checkAllTK(_arr: any[], _event) {
    _arr.forEach(_el => {
      _el.checked = _event.target.checked
    });
  }

  /**
   * Lấy những phần tử được chọn
   * @param _arr Arr
   */
  getCheckedTK(_arr: any[]) {
    return _arr.filter((_el) => {
      return _el.checked === true;
    }).length;
  }

  /**
   * Thêm tài khoản vào nhóm quyền
   *    + Thêm từng tài khoản
   *    hoặc
   *    + Thêm nhiều tài khoản 1 lúc
   * @param _tk Tai khoan
   */
  themTK(_tk = null) {
    this.isLoading = true;
    const _tmpArr = [];
    if (_tk) {
      _tmpArr.push({
        id: _tk.id,
        ho_va_ten: _tk.ho_va_ten,
      });
    } else {
      this.taiKhoanArr.filter((_el) => {
        return _el.checked === true;
      }).forEach(_el => {
        _tmpArr.push({
          id: _el.id,
          ho_va_ten: _el.ho_va_ten,
        });
      });
    }

    this._http.post(this.urlAPI + '/' + this.quyenInfo.id + '/tai-khoan', {
      TaiKhoan: _tmpArr
    }).map(res => res.json()).subscribe(_res => {
      this.quyenInfo = _res.data;
      this.loadTaiKhoan();

      this.toasterService.pop('success', 'Đã thêm!');
      this.isLoading = false;
    }, error => {
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoading = false;
    })
  }

  /**
   * Thêm nhóm tài khoản vào nhóm quyền
   *    + Thêm từng nhóm
   *    hoặc
   *    + Thêm nhiều nhóm 1 lúc
   * @param _id Nhóm ID
   */
  themNhom(_id = null) {
    this.isLoading = true;
    const _tmpArr = [];
    if (_id) {
      _tmpArr.push(_id);
    } else {
      this.nhomArr.filter((_el) => {
        return _el.checked === true;
      }).forEach(_el => {
        _tmpArr.push(_el.id);
      });
    }

    this._http.post(this.urlAPI + '/' + this.quyenInfo.id + '/nhom', {
      IDs: _tmpArr
    }).map(res => res.json()).subscribe(_res => {
      this.quyenInfo = _res.data;
      this.loadNhom();

      this.toasterService.pop('success', 'Đã thêm!');
      this.isLoading = false;
    }, error => {
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoading = false;
    })
  }

  /**
   * Xóa tài-khoản/nhóm trong nhóm quyền
   *    + Xóa từng tài-khoản/nhóm
   *    hoặc
   *    + Xóa nhiều tài-khoản/nhóm 1 lúc
   * @param _tk Tai khoan
   */
  xoa(_id = null, _nhom = false, loading) {
    this.isLoading = true;
    const _tmpArr = [];
    if (_id) {
      _tmpArr.push(_id);
    } else if (_nhom) {
      // Xóa Nhóm
      this.quyenInfo.role_nhom.filter((_el) => {
        return _el.checked === true;
      }).forEach(_el => {
        _tmpArr.push(_el.id);
      });
    } else {
      // Xóa Tài Khoản
      this.quyenInfo.role_taikhoan.filter((_el) => {
        return _el.checked === true;
      }).forEach(_el => {
        _tmpArr.push(_el.id);
      });
    }

    this._http.post(this.urlAPI + '/' + this.quyenInfo.id + '/xoa', {
      IDs: _tmpArr
    }).map(res => res.json()).subscribe(_res => {
      this.quyenInfo = _res.data;
      this.loadTaiKhoan();

      this.toasterService.pop('success', 'Đã Xóa!');
      this.isLoading = false;
    }, error => {
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoading = false;
    })
  }

  /**
   * Thoát -> quay lại trang chính
   */
  cancel() {
    this.updateInfo.emit(true);
  }
}
