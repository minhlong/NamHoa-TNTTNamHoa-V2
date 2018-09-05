import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { Subject } from 'rxjs';
import { URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from '../../../../services/http-auth.service';
import { AppState } from '../../../../store/reducers';
import { environment } from 'environments/environment';
import { bodauTiengViet } from '../../../../_helpers';

@Component({
  selector: 'app-form-edit',
  templateUrl: './form-edit.component.html',
  styleUrls: ['./form-edit.component.scss']
})
export class FormEditComponent implements OnInit {
  @Input() nhom;
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/nhom-tai-khoan';
  private tkAPI = environment.apiURL + '/tai-khoan';
  private taiKhoanSrcArr = [];

  isLoading = false;
  isLoadingTK = false;

  formGroup: FormGroup;
  error: any;

  taiKhoanArr = [];
  filter$ = new Subject<any>();

  pagingT1 = {
    // Paging
    id: 't1-phan-nhom-edit-page',
    itemsPerPage: 10,
    currentPage: 1,
  }

  pagingT2 = {
    // Paging
    id: 't2-phan-nhom-edit-page',
    itemsPerPage: 10,
    currentPage: 1,
  }

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _fb: FormBuilder,
    private _http: JwtAuthHttp,
  ) {
    // Xử lý tìm kiếm tài khoản
    this.filter$.debounceTime(400).subscribe((_str) => {
      this.taiKhoanArr = this.taiKhoanSrcArr.filter(el => {
        // Bỏ dấu để lọc theo tên
        const _tmpA = bodauTiengViet(el.ho_va_ten);
        const _tmpB = bodauTiengViet(_str);
        return _tmpA.toLowerCase().indexOf(_tmpB.toLowerCase()) !== -1
      });

      if (this.nhom.id) {
        this.taiKhoanArr = this.taiKhoanArr.filter(el => {
          // Loại bỏ tài khoản đã phân quyền
          return !this.nhom.tai_khoan.find(c => c.id === el.id)
        });
      }
    });

    this.loadTaiKhoan();
  }

  ngOnInit() {
    // Tạo form để submit lên server
    this.formGroup = this._fb.group({
      ten_hien_thi: this.nhom.ten_hien_thi,
    });
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

  /**
   * Thêm tài khoản vào nhóm
   *    + Thêm từng tài khoản
   *    hoặc
   *    + Thêm nhiều tài khoản 1 lúc
   * @param _id Tai khoan ID
   */
  them(_id = null) {
    this.isLoadingTK = true;
    const _tmpArr = [];
    if (_id) {
      _tmpArr.push(_id);
    } else {
      this.taiKhoanArr.filter((_el) => {
        return _el.checked === true;
      }).forEach(_el => {
        _tmpArr.push(_el.id);
      });
    }

    this._http.post(this.urlAPI + '/' + this.nhom.id + '/tai-khoan', {
      TaiKhoan: _tmpArr
    }).map(res => res.json()).subscribe(_res => {
      this.nhom = _res.data;
      this.loadTaiKhoan();

      this.toasterService.pop('success', 'Đã thêm!');
      this.isLoadingTK = false;
    }, error => {
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoadingTK = false;
    })
  }

  /**
   * Xóa tài khoản trong  nhóm
   *    + Xóa từng tài khoản
   *    hoặc
   *    + Xóa nhiều tài khoản 1 lúc
   * @param _id Tai khoan ID
   */
  xoa(_id = null) {
    this.isLoadingTK = true;
    const _tmpArr = [];
    if (_id) {
      _tmpArr.push(_id);
    } else {
      this.nhom.tai_khoan.filter((_el) => {
        return _el.checked === true;
      }).forEach(_el => {
        _tmpArr.push(_el.id);
      });
    }

    this._http.post(this.urlAPI + '/' + this.nhom.id + '/xoa-tai-khoan', {
      TaiKhoan: _tmpArr
    }).map(res => res.json()).subscribe(_res => {
      this.nhom = _res.data;
      this.loadTaiKhoan();

      this.toasterService.pop('success', 'Đã Xóa!');
      this.isLoadingTK = false;
    }, error => {
      this.toasterService.pop('error', 'Lỗi!', error);
      this.isLoadingTK = false;
    })
  }

  /**
   *    Nếu OK -> quay lại trang chính
   *    Nếu lỗi -> hiện lỗi
   */
  save() {
    let _url = this.urlAPI;
    if (this.nhom.id) {
      _url += '/' + this.nhom.id;
    }

    this.isLoading = true;

    this._http.post(_url, this.formGroup.value).map(res => res.json()).subscribe(res => {
      this.isLoading = false;

      if (!this.nhom.id) {
        // Nếu tạo mới thì tiếp tục tiến hành thêm tài khoản vào nhóm
        this.nhom = res.data;
      } else {
        // Nếu ở trạng thái Sửa thì sau khi cập nhật sẽ trở lại trang danh sách
        this.cancel();
      }
    }, _err => {
      this.isLoading = false;
      if (typeof _err === 'string') {
        this.toasterService.pop('error', 'Lỗi!', _err);
      } else {
        this.error = _err;
        for (const _field in _err) {
          if (_err.hasOwnProperty(_field)) {
            _err[_field].forEach(_mess => {
              this.toasterService.pop('error', 'Lỗi!', _mess);
            });
          }
        }
      }
    });
  }

  /**
   * Thoát -> quay lại trang chính
   */
  cancel() {
    this.updateInfo.emit(true);
  }
}
