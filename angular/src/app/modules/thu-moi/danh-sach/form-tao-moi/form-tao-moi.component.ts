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
import { ngay } from '../../../shared/utities.pipe';

@Component({
  selector: 'app-form-tao-moi',
  templateUrl: './form-tao-moi.component.html',
  styleUrls: ['./form-tao-moi.component.scss']
})
export class FormTaoMoiComponent implements OnInit, OnDestroy {
  @Output() updateInfo = new EventEmitter();

  private tkAPI = environment.apiURL + '/tai-khoan';
  private urlAPI = environment.apiURL + '/thu-moi';
  private taiKhoanSrcArr = [];
  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  isLoading = false;
  selectedItem = null;

  formGroup: FormGroup;
  error: any;

  khoaHienTaiID: any;
  taiKhoanArr = [];
  filter$ = new Subject<any>();

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 5,
    currentPage: 1,
  }

  subKhoa$: any;

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _fb: FormBuilder,
    private _http: JwtAuthHttp,
  ) {
    this.subKhoa$ = this.store.select((state: AppState) => state.auth.khoa_hoc_hien_tai).subscribe(_khoa => {
      this.khoaHienTaiID = _khoa.id;
      this.loadTaiKhoan();
    });

    this.filter$.debounceTime(400).subscribe((_str) => {
      this.taiKhoanArr = this.taiKhoanSrcArr.filter(el => {
        const _tmpA = bodauTiengViet(el.ho_va_ten);
        const _tmpB = bodauTiengViet(_str);
        return _tmpA.toLowerCase().indexOf(_tmpB.toLowerCase()) !== -1
      });
    });
  }

  ngOnDestroy() {
    this.filter$.complete();
    this.subKhoa$.unsubscribe();
  }

  ngOnInit() {
    // Tạo form để submit lên server
    this.formGroup = this._fb.group({
      ngay: null,
      tai_khoan_id: null,
      ghi_chu: null,
    });
  }

  private loadTaiKhoan() {
    this.isLoading = true;
    const search = new URLSearchParams();
    search.set('khoa', this.khoaHienTaiID);
    search.set('trang_thai', 'HOAT_DONG');
    search.set('loai_tai_khoan', 'THIEU_NHI');
    search.set('loadLopHoc', 'true');
    this._http.get(this.tkAPI, { search }).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.taiKhoanSrcArr = res.data;
      this.filter$.next(''); // Trigger Search
    }, error => {
      this.isLoading = false;
      this.toasterService.pop('error', 'Lỗi!', error);
    })
  }

  /**
   *    Nếu OK -> quay lại trang chính
   *    Nếu lỗi -> hiện lỗi
   */
  save() {
    this.isLoading = true;
    const _par = Object.assign({}, this.formGroup.value, {
      ngay: ngay(this.formGroup.value.ngay),
      tai_khoan_id: this.selectedItem.id,
      ghi_chu: this.formGroup.value.ghi_chu,
    });

    this._http.post(this.urlAPI, _par).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.updateInfo.emit(true);
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
    this.updateInfo.emit(false);
  }
}
