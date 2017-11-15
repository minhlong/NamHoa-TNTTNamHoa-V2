import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';
import { ToasterService } from 'angular2-toaster';
import { FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';

import { JwtAuthHttp } from 'app/services/http-auth.service';
import { AppState } from 'app/store/reducers';
import { environment } from 'environments/environment.prod';

@Component({
  selector: 'app-form-edit',
  templateUrl: './form-edit.component.html',
  styleUrls: ['./form-edit.component.scss']
})
export class FormEditComponent implements OnInit, OnDestroy {
  @Input() quyenInfo;
  @Output() updateInfo = new EventEmitter();

  private urlAPI = environment.apiURL + '/phan-quyen';

  isLoading: boolean;
  formGroup: FormGroup;
  error: any;

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private _fb: FormBuilder,
    private _http: JwtAuthHttp,

  ) { }

  ngOnInit() {
    this.isLoading = false;
    // Tạo form để submit lên server
    this.formGroup = this._fb.group({
      ten_hien_thi: this.quyenInfo.ten_hien_thi,
      ghi_chu: this.quyenInfo.ghi_chu,
    });
  }

  ngOnDestroy() {
  }
  /**
   * Lưu điểm
   *    Nếu OK -> quay lại trang chính
   *    Nếu lỗi -> hiện lỗi
   */
  save() {
    const _url = this.urlAPI + '/' + this.quyenInfo.id;
    this.isLoading = true;

    this._http.post(_url, this.formGroup.value).map(res => res.json()).subscribe(res => {
      this.isLoading = false;
      this.updateInfo.emit(res);
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
    this.updateInfo.emit(null);
  }

}
